<?php

namespace App\Actions\Properties;

use App\Jobs\ProcessPropertyImageJob;
use App\Models\Property;
use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AttachPropertyImageUploadsAction
{
    public function execute(
        Property $property,
        User $user,
        ?string $featuredUploadToken,
        array $galleryUploadTokens,
    ): void {
        $rawGalleryCount = count($galleryUploadTokens);
        $uniqueGalleryTokenCount = count(array_values(array_unique(array_filter($galleryUploadTokens))));
        $featuredUpload = $this->resolveUpload($featuredUploadToken, $user);
        $galleryUploads = collect($galleryUploadTokens)
            ->filter()
            ->map(fn (string $token) => $this->resolveUpload($token, $user))
            ->unique('id')
            ->values();

        Log::info('Anexando uploads ao imovel.', [
            'property_id' => $property->id,
            'user_id' => $user->id,
            'featured_upload_present' => (bool) $featuredUpload,
            'gallery_tokens_received' => $rawGalleryCount,
            'gallery_tokens_unique' => $uniqueGalleryTokenCount,
            'gallery_uploads_resolved' => $galleryUploads->count(),
        ]);

        DB::transaction(function () use ($property, $featuredUpload, $galleryUploads): void {
            $createdPhotos = 0;

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
                    'processing_status' => 'pending',
                ]);

                $this->dispatchProcessJob($featuredPhoto, $featuredUpload);
                $createdPhotos++;
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
                    'processing_status' => 'pending',
                ]);

                $this->dispatchProcessJob($photo, $upload);
                $createdPhotos++;
            }

            Log::info('Uploads anexados ao imovel com sucesso.', [
                'property_id' => $property->id,
                'user_id' => $user->id,
                'property_photos_created' => $createdPhotos,
            ]);
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
            ->where('status', 'pending')
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

    private function dispatchProcessJob(PropertyPhoto $photo, PropertyImageUpload $upload): void
    {
        ProcessPropertyImageJob::dispatch($photo->id, $upload->id);
    }
}
