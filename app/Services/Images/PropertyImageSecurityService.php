<?php

namespace App\Services\Images;

use App\DataTransferObjects\PropertyImageUploadData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PropertyImageSecurityService
{
    public function inspectUploadedFile(UploadedFile $file): PropertyImageUploadData
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $mimeType = (string) $file->getMimeType();
        $size = (int) $file->getSize();
        $hash = hash_file('sha256', $file->getRealPath()) ?: Str::uuid()->toString();

        return new PropertyImageUploadData(
            file: $file,
            originalName: $originalName,
            sanitizedName: $this->sanitizeFileName(pathinfo($originalName, PATHINFO_FILENAME), $extension),
            extension: $extension,
            mimeType: $mimeType,
            size: $size,
            sha256: $hash,
        );
    }

    public function sanitizeFileName(string $name, string $extension): string
    {
        $slug = Str::of($name)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '-')->trim('-')->value();
        if ($slug === '') {
            $slug = 'image';
        }

        return $slug . '.' . strtolower($extension);
    }

    public function safeWebpFileName(int $propertyId, string $uuid): string
    {
        return sprintf('property-%d-%s.webp', $propertyId, $uuid);
    }
}
