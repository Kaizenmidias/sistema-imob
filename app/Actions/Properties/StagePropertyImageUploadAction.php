<?php

namespace App\Actions\Properties;

use App\Models\PropertyImageUpload;
use App\Models\User;
use App\Services\Images\PropertyImageSecurityService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StagePropertyImageUploadAction
{
    public function __construct(
        private readonly PropertyImageSecurityService $securityService,
    ) {
    }

    public function execute(User $user, UploadedFile $file): PropertyImageUpload
    {
        $activeUploads = PropertyImageUpload::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['staged', 'processing'])
            ->count();

        if ($activeUploads >= (int) config('image_uploads.max_files_per_property', 50)) {
            throw ValidationException::withMessages([
                'file' => 'Voce atingiu o limite de uploads temporarios para este imovel.',
            ]);
        }

        $data = $this->securityService->inspectUploadedFile($file);
        $diskName = (string) config('image_uploads.temporary_disk', 'local');
        $directory = trim((string) config('image_uploads.temporary_directory', 'tmp/property-images'), '/');
        $token = (string) Str::uuid();
        $path = $file->storeAs(
            sprintf('%s/%d/%s', $directory, $user->id, now()->format('Ymd')),
            $token . '.' . $data->extension,
            $diskName
        );

        $upload = PropertyImageUpload::create([
            'user_id' => $user->id,
            'token' => $token,
            'disk' => $diskName,
            'temp_path' => $path,
            'original_name' => $data->originalName,
            'sanitized_name' => $data->sanitizedName,
            'extension' => $data->extension,
            'mime_type' => $data->mimeType,
            'size' => $data->size,
            'sha256' => $data->sha256,
            'status' => 'staged',
            'expires_at' => now()->addDay(),
        ]);

        Log::info('Upload temporario de imagem criado.', [
            'upload_id' => $upload->id,
            'user_id' => $user->id,
            'mime_type' => $upload->mime_type,
            'size' => $upload->size,
        ]);

        return $upload;
    }

    public function destroy(PropertyImageUpload $upload): void
    {
        Storage::disk($upload->disk)->delete($upload->temp_path);
        $upload->delete();
    }
}
