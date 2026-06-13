<?php

namespace App\DataTransferObjects;

use Illuminate\Http\UploadedFile;

readonly class PropertyImageUploadData
{
    public function __construct(
        public UploadedFile $file,
        public string $originalName,
        public string $sanitizedName,
        public string $extension,
        public string $mimeType,
        public int $size,
        public string $sha256,
    ) {
    }
}
