<?php

namespace App\Jobs;

use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use App\Services\Images\PropertyImageProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GeneratePropertyThumbnailsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 300;

    public function __construct(
        public readonly int $photoId,
        public readonly int $uploadId,
    ) {
    }

    public function handle(PropertyImageProcessor $processor): void
    {
        $photo = PropertyPhoto::findOrFail($this->photoId);
        $upload = PropertyImageUpload::findOrFail($this->uploadId);

        try {
            $thumbs = $processor->generateThumbs($photo, $upload);

            $photo->update([
                'thumb_small_path' => $thumbs['thumb_small_path'],
                'thumb_medium_path' => $thumbs['thumb_medium_path'],
                'processed_at' => now(),
                'processing_status' => 'processed',
                'processing_error' => null,
            ]);

            Storage::disk($upload->disk)->delete($upload->temp_path);
            $upload->update([
                'status' => 'processed',
                'processed_at' => now(),
                'validation_error' => null,
            ]);
        } catch (Throwable $e) {
            $photo->update([
                'processing_status' => 'failed',
                'processing_error' => $e->getMessage(),
            ]);
            $upload->update([
                'status' => 'failed',
                'validation_error' => $e->getMessage(),
            ]);

            Log::error('Falha ao gerar thumbnails do imovel.', [
                'photo_id' => $photo->id,
                'upload_id' => $upload->id,
                'property_id' => $photo->property_id,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
