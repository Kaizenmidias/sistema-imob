<?php

namespace App\Actions\Properties;

use App\Models\PropertyImageUpload;
use App\Models\User;
use App\Services\Images\PropertyImageSecurityService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StagePropertyImageUploadAction
{
    public function __construct(
        private readonly PropertyImageSecurityService $securityService,
    ) {
    }

    public function execute(User $user, UploadedFile $file): PropertyImageUpload
    {
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
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $pendingUploadsCount = PropertyImageUpload::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        Log::info('Upload temporario de imagem criado.', [
            'upload_id' => $upload->id,
            'user_id' => $user->id,
            'mime_type' => $upload->mime_type,
            'size' => $upload->size,
            'pending_uploads_for_user' => $pendingUploadsCount,
        ]);

        return $upload;
    }

    public function destroy(PropertyImageUpload $upload): void
    {
        Storage::disk($upload->disk)->delete($upload->temp_path);
        $upload->delete();
    }
}
