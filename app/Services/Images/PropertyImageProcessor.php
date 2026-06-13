<?php

namespace App\Services\Images;

use App\Models\PropertyImageUpload;
use App\Models\PropertyPhoto;
use Imagick;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver as InterventionImagickDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

class PropertyImageProcessor
{
    public function __construct(
        private readonly PropertyImageSecurityService $securityService,
    ) {
    }

    public function process(PropertyPhoto $photo, PropertyImageUpload $upload): array
    {
        $validated = $this->securityService->assertStoredUploadIsSafe(
            $upload->disk,
            $upload->temp_path,
            $upload->extension
        );

        $disk = Storage::disk((string) config('image_uploads.final_disk', 'public'));
        $tempPath = Storage::disk($upload->disk)->path($upload->temp_path);

        $originalPath = $this->versionOutputPath($photo, 'original', strtolower($upload->extension));
        $this->ensureDirectory($disk->path($originalPath));
        $disk->put($originalPath, file_get_contents($tempPath));

        $quality = (int) config('image_uploads.processing.webp_quality', 80);
        $fullMaxWidth = (int) config('image_uploads.processing.full_max_width', 1920);
        $fullMaxHeight = (int) config('image_uploads.processing.full_max_height', 1080);
        $mediumMaxWidth = (int) config('image_uploads.processing.medium_max_width', 1200);
        $mediumMaxHeight = (int) config('image_uploads.processing.medium_max_height', 1200);
        $thumbMaxWidth = (int) config('image_uploads.processing.thumb_max_width', 400);
        $thumbMaxHeight = (int) config('image_uploads.processing.thumb_max_height', 400);

        $fullPath = $this->versionOutputPath($photo, 'full', 'webp');
        $mediumPath = $this->versionOutputPath($photo, 'medium', 'webp');
        $thumbPath = $this->versionOutputPath($photo, 'thumb', 'webp');

        if (class_exists(ImageManager::class) && class_exists(InterventionImagickDriver::class)) {
            $full = $this->processWithIntervention($tempPath, $fullMaxWidth, $fullMaxHeight, $quality);
            $this->saveBinary($disk, $fullPath, $full['binary']);

            $medium = $this->processWithIntervention($tempPath, $mediumMaxWidth, $mediumMaxHeight, $quality);
            $this->saveBinary($disk, $mediumPath, $medium['binary']);

            $thumb = $this->processWithIntervention($tempPath, $thumbMaxWidth, $thumbMaxHeight, $quality);
            $this->saveBinary($disk, $thumbPath, $thumb['binary']);

            gc_collect_cycles();

            return [
                'original_path' => $originalPath,
                'path' => $fullPath,
                'url' => $disk->url($fullPath),
                'medium_path' => $mediumPath,
                'thumb_path' => $thumbPath,
                'width' => $full['width'],
                'height' => $full['height'],
                'size' => $disk->size($fullPath),
                'mime_type' => 'image/webp',
                'source_size' => (int) ($validated['size'] ?? 0),
                'source_mime_type' => (string) ($validated['mime_type'] ?? $upload->mime_type),
            ];
        }

        if (!class_exists(Imagick::class)) {
            Log::error('Processamento de imagem indisponivel: Intervention v3 e Imagick ausentes.', [
                'photo_id' => $photo->id,
                'upload_id' => $upload->id,
            ]);
            throw new RuntimeException('O servidor precisa de Intervention Image v3 com driver Imagick.');
        }

        $full = $this->processWithImagick($tempPath, $fullMaxWidth, $fullMaxHeight, $quality);
        $this->saveBinary($disk, $fullPath, $full['binary']);

        $medium = $this->processWithImagick($tempPath, $mediumMaxWidth, $mediumMaxHeight, $quality);
        $this->saveBinary($disk, $mediumPath, $medium['binary']);

        $thumb = $this->processWithImagick($tempPath, $thumbMaxWidth, $thumbMaxHeight, $quality);
        $this->saveBinary($disk, $thumbPath, $thumb['binary']);

        gc_collect_cycles();

        return [
            'original_path' => $originalPath,
            'path' => $fullPath,
            'url' => $disk->url($fullPath),
            'medium_path' => $mediumPath,
            'thumb_path' => $thumbPath,
            'width' => $full['width'],
            'height' => $full['height'],
            'size' => $disk->size($fullPath),
            'mime_type' => 'image/webp',
            'source_size' => (int) ($validated['size'] ?? 0),
            'source_mime_type' => (string) ($validated['mime_type'] ?? $upload->mime_type),
        ];
    }

    public function deleteDerivedFiles(PropertyPhoto $photo): void
    {
        $disk = Storage::disk((string) config('image_uploads.final_disk', 'public'));
        $disk->delete(array_filter([
            $photo->original_path,
            $photo->arquivo,
            $photo->thumb_small_path,
            $photo->thumb_medium_path,
        ]));
    }

    private function processWithIntervention(string $path, int $maxWidth, int $maxHeight, int $quality): array
    {
        $manager = new ImageManager(new InterventionImagickDriver());
        $image = $manager->read($path);
        $native = $this->nativeImage($image);
        if ($native instanceof Imagick && $native->getNumberImages() > 1) {
            throw new RuntimeException('Animacoes nao sao permitidas no upload de imagens.');
        }

        if ($native instanceof Imagick) {
            $native->autoOrient();
            $native->stripImage();
        }

        $image->scaleDown($maxWidth, $maxHeight);

        $binary = (string) $image->toWebp($quality);
        $width = $this->nativeImage($image)?->getImageWidth();
        $height = $this->nativeImage($image)?->getImageHeight();

        $this->cleanupImage($image);

        return [
            'binary' => $binary,
            'width' => $width,
            'height' => $height,
        ];
    }

    private function processWithImagick(string $path, int $maxWidth, int $maxHeight, int $quality): array
    {
        $image = new Imagick();
        $image->readImage($path);
        if ($image->getNumberImages() > 1) {
            throw new RuntimeException('Animacoes nao sao permitidas no upload de imagens.');
        }

        $image->autoOrient();
        $image->stripImage();
        $image->thumbnailImage($maxWidth, $maxHeight, true, true);
        $image->setImageFormat('webp');
        $image->setImageCompressionQuality($quality);

        $result = [
            'binary' => (string) $image,
            'width' => $image->getImageWidth(),
            'height' => $image->getImageHeight(),
        ];

        $image->clear();
        $image->destroy();

        return $result;
    }

    private function saveBinary($disk, string $path, string $binary): void
    {
        $absolutePath = $disk->path($path);
        $this->ensureDirectory($absolutePath);
        $disk->put($path, $binary);
    }

    private function versionOutputPath(PropertyPhoto $photo, string $version, string $extension): string
    {
        return sprintf(
            '%s/%d/%s/%d.%s',
            trim((string) config('image_uploads.final_directory', 'properties'), '/'),
            $photo->property_id,
            $version,
            $photo->id,
            ltrim($extension, '.')
        );
    }

    private function ensureDirectory(string $absolutePath): void
    {
        $directory = dirname($absolutePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }

    private function nativeImage(object $image): ?Imagick
    {
        if (!method_exists($image, 'core')) {
            return null;
        }

        $core = $image->core();
        if (is_object($core) && method_exists($core, 'native')) {
            $native = $core->native();
            if ($native instanceof Imagick) {
                return $native;
            }
        }

        return null;
    }

    private function cleanupImage(object $image): void
    {
        $native = $this->nativeImage($image);
        if ($native instanceof Imagick) {
            $native->clear();
            $native->destroy();
        }

        unset($image);
    }
}
