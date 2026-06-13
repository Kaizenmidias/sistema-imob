<?php

namespace App\Services\Images;

use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use Imagick;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PropertyImageProcessor
{
    public function optimizeMainImage(PropertyPhoto $photo, PropertyImageUpload $upload): array
    {
        $image = $this->loadImage($upload);

        $this->autoOrient($image);
        $this->stripMetadata($image);
        $image = $this->resizeToFit(
            $image,
            (int) config('image_uploads.processing.main_max_width', 1920),
            (int) config('image_uploads.processing.main_max_height', 1080)
        );

        $quality = (int) config('image_uploads.processing.webp_quality', 82);
        $mainPath = $this->mainOutputPath($photo);

        $this->saveAsWebp($image, $mainPath, $quality);

        $disk = Storage::disk((string) config('image_uploads.final_disk', 'public'));
        $absolutePath = $disk->path($mainPath);

        return [
            'path' => $mainPath,
            'url' => $disk->url($mainPath),
            'width' => $this->imageWidth($image),
            'height' => $this->imageHeight($image),
            'size' => is_file($absolutePath) ? filesize($absolutePath) : null,
            'mime_type' => 'image/webp',
        ];
    }

    public function generateThumbs(PropertyPhoto $photo, PropertyImageUpload $upload): array
    {
        $quality = (int) config('image_uploads.processing.webp_quality', 82);

        $small = $this->loadImage($upload);
        $this->autoOrient($small);
        $this->stripMetadata($small);
        $small = $this->resizeToFit(
            $small,
            (int) config('image_uploads.processing.thumb_small_width', 400),
            (int) config('image_uploads.processing.thumb_small_height', 300)
        );
        $smallPath = $this->thumbOutputPath($photo, 'small');
        $this->saveAsWebp($small, $smallPath, $quality);

        $medium = $this->loadImage($upload);
        $this->autoOrient($medium);
        $this->stripMetadata($medium);
        $medium = $this->resizeToFit(
            $medium,
            (int) config('image_uploads.processing.thumb_medium_width', 800),
            (int) config('image_uploads.processing.thumb_medium_height', 600)
        );
        $mediumPath = $this->thumbOutputPath($photo, 'medium');
        $this->saveAsWebp($medium, $mediumPath, $quality);

        return [
            'thumb_small_path' => $smallPath,
            'thumb_medium_path' => $mediumPath,
        ];
    }

    public function deleteDerivedFiles(PropertyPhoto $photo): void
    {
        $disk = Storage::disk((string) config('image_uploads.final_disk', 'public'));
        $disk->delete(array_filter([
            $photo->arquivo,
            $photo->thumb_small_path,
            $photo->thumb_medium_path,
        ]));
    }

    private function loadImage(PropertyImageUpload $upload): Imagick|\GdImage
    {
        $disk = Storage::disk($upload->disk);
        $path = $disk->path($upload->temp_path);

        if (!is_file($path)) {
            throw new RuntimeException('Arquivo temporario nao encontrado para processamento.');
        }

        if (class_exists(Imagick::class)) {
            $image = new Imagick();
            $image->readImage($path);
            if ($image->getNumberImages() > 1) {
                throw new RuntimeException('Animacoes nao sao permitidas no upload de imagens.');
            }

            return $image;
        }

        if (function_exists('imagecreatefromstring')) {
            $binary = file_get_contents($path);
            $image = @imagecreatefromstring($binary ?: '');
            if ($image === false) {
                throw new RuntimeException('Falha ao abrir a imagem enviada.');
            }

            return $image;
        }

        Log::error('Nenhum driver de imagem disponivel para processar upload.', [
            'upload_id' => $upload->id,
            'mime_type' => $upload->mime_type,
        ]);

        throw new RuntimeException('Nenhum driver de imagem disponivel no servidor.');
    }

    private function autoOrient(Imagick|\GdImage $image): void
    {
        if ($image instanceof Imagick) {
            $image->autoOrient();
        }
    }

    private function stripMetadata(Imagick|\GdImage $image): void
    {
        if ($image instanceof Imagick) {
            $image->stripImage();
        }
    }

    private function resizeToFit(Imagick|\GdImage $image, int $maxWidth, int $maxHeight): Imagick|\GdImage
    {
        if ($image instanceof Imagick) {
            $image->thumbnailImage($maxWidth, $maxHeight, true, true);
            return $image;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return $image;
        }

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = max(1, (int) floor($width * $ratio));
        $newHeight = max(1, (int) floor($height * $ratio));
        $canvas = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        imagedestroy($image);
        return $canvas;
    }

    private function saveAsWebp(Imagick|\GdImage $image, string $path, int $quality): void
    {
        $disk = Storage::disk((string) config('image_uploads.final_disk', 'public'));
        $absolutePath = $disk->path($path);
        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0775, true);
        }

        if ($image instanceof Imagick) {
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($quality);
            $image->writeImage($absolutePath);
            return;
        }

        if (!function_exists('imagewebp')) {
            throw new RuntimeException('O servidor nao possui suporte a WEBP no driver GD.');
        }

        imagewebp($image, $absolutePath, $quality);
    }

    private function imageWidth(Imagick|\GdImage $image): int
    {
        return $image instanceof Imagick ? $image->getImageWidth() : imagesx($image);
    }

    private function imageHeight(Imagick|\GdImage $image): int
    {
        return $image instanceof Imagick ? $image->getImageHeight() : imagesy($image);
    }

    private function mainOutputPath(PropertyPhoto $photo): string
    {
        return sprintf(
            '%s/%d/%d/main.webp',
            trim((string) config('image_uploads.final_directory', 'properties'), '/'),
            $photo->property_id,
            $photo->id
        );
    }

    private function thumbOutputPath(PropertyPhoto $photo, string $size): string
    {
        return sprintf(
            '%s/%d/%d/thumb-%s.webp',
            trim((string) config('image_uploads.final_directory', 'properties'), '/'),
            $photo->property_id,
            $photo->id,
            $size
        );
    }
}
