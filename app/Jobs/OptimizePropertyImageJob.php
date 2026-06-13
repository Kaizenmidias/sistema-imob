<?php

namespace App\Jobs;

use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use App\Services\Images\PropertyImageProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class OptimizePropertyImageJob implements ShouldQueue
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
            $result = $processor->optimizeMainImage($photo, $upload);

            $photo->update([
                'arquivo' => $result['path'],
                'url' => $result['url'],
                'width' => $result['width'],
                'height' => $result['height'],
                'size' => $result['size'],
                'mime_type' => $result['mime_type'],
                'optimized' => true,
                'processing_status' => 'optimized',
                'processing_error' => null,
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

            Log::error('Falha ao otimizar imagem do imovel.', [
                'photo_id' => $photo->id,
                'upload_id' => $upload->id,
                'property_id' => $photo->property_id,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
