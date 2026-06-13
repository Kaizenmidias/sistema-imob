<?php

namespace App\Actions\Properties;

use App\Jobs\GeneratePropertyThumbnailsJob;
use App\Jobs\OptimizePropertyImageJob;
use App\Jobs\ProcessPropertyImageJob;
use App\Models\Property;
use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttachPropertyImageUploadsAction
{
    public function execute(
        Property $property,
        User $user,
        ?string $featuredUploadToken,
        array $galleryUploadTokens,
    ): void {
        $featuredUpload = $this->resolveUpload($featuredUploadToken, $user);
        $galleryUploads = collect($galleryUploadTokens)
            ->filter()
            ->map(fn (string $token) => $this->resolveUpload($token, $user))
            ->unique('id')
            ->values();

        $incomingCount = ($featuredUpload ? 1 : 0) + $galleryUploads->count();
        $totalAfterAttach = $property->photos()->count() + $incomingCount;
        if ($totalAfterAttach > (int) config('image_uploads.max_files_per_property', 50)) {
            throw ValidationException::withMessages([
                'gallery_upload_tokens' => 'Cada imovel pode ter no maximo 50 imagens.',
            ]);
        }

        DB::transaction(function () use ($property, $featuredUpload, $galleryUploads): void {
            if ($featuredUpload) {
                $featuredPhoto = PropertyPhoto::create([
                    'property_id' => $property->id,
                    'arquivo' => '',
                    'url' => '',
                    'principal' => true,
                    'ordem' => 0,
                    'size' => $featuredUpload->size,
                    'mime_type' => $featuredUpload->mime_type,
                    'optimized' => false,
                    'processing_status' => 'queued',
                ]);

                $this->dispatchChain($featuredPhoto, $featuredUpload);
            }

            $currentMaxOrder = (int) ($property->photos()->where('principal', false)->max('ordem') ?? 0);
            foreach ($galleryUploads as $index => $upload) {
                $photo = PropertyPhoto::create([
                    'property_id' => $property->id,
                    'arquivo' => '',
                    'url' => '',
                    'principal' => false,
                    'ordem' => $currentMaxOrder + $index + 1,
                    'size' => $upload->size,
                    'mime_type' => $upload->mime_type,
                    'optimized' => false,
                    'processing_status' => 'queued',
                ]);

                $this->dispatchChain($photo, $upload);
            }
        });
    }

    private function resolveUpload(?string $token, User $user): ?PropertyImageUpload
    {
        if (!$token) {
            return null;
        }

        $upload = PropertyImageUpload::query()
            ->where('token', $token)
            ->where('user_id', $user->id)
            ->where('status', 'staged')
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$upload) {
            throw ValidationException::withMessages([
                'gallery_upload_tokens' => 'Uma ou mais imagens temporarias sao invalidas ou expiraram.',
            ]);
        }

        return $upload;
    }

    private function dispatchChain(PropertyPhoto $photo, PropertyImageUpload $upload): void
    {
        Bus::chain([
            new ProcessPropertyImageJob($photo->id, $upload->id),
            new OptimizePropertyImageJob($photo->id, $upload->id),
            new GeneratePropertyThumbnailsJob($photo->id, $upload->id),
        ])->dispatch();
    }
}
