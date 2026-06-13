<?php

namespace App\Jobs;

use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPropertyImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly int $photoId,
        public readonly int $uploadId,
    ) {
    }

    public function handle(): void
    {
        $photo = PropertyPhoto::findOrFail($this->photoId);
        $upload = PropertyImageUpload::findOrFail($this->uploadId);

        $upload->update(['status' => 'processing']);
        $photo->update(['processing_status' => 'processing']);

        Log::info('Processamento de imagem iniciado.', [
            'photo_id' => $photo->id,
            'upload_id' => $upload->id,
            'property_id' => $photo->property_id,
        ]);
    }
}
