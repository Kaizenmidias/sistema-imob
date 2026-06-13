<?php

return [
    'max_files_per_property' => env('PROPERTY_IMAGES_MAX_FILES'),
    'max_file_size_bytes' => (int) env('PROPERTY_IMAGES_MAX_FILE_SIZE', 10 * 1024 * 1024),
    'request_max_body_hint' => (int) env('PROPERTY_IMAGES_REQUEST_MAX_BODY', 256 * 1024 * 1024),
    'parallel_uploads' => (int) env('PROPERTY_IMAGES_PARALLEL_UPLOADS', 8),
    'poll_interval_ms' => (int) env('PROPERTY_IMAGES_POLL_INTERVAL_MS', 4000),
    'processing' => [
        'full_max_width' => 1920,
        'full_max_height' => 1080,
        'medium_max_width' => 1200,
        'medium_max_height' => 1200,
        'thumb_max_width' => 400,
        'thumb_max_height' => 400,
        'webp_quality' => (int) env('PROPERTY_IMAGES_WEBP_QUALITY', 80),
    ],
    'temporary_disk' => env('PROPERTY_IMAGES_TEMP_DISK', 'local'),
    'temporary_directory' => 'tmp/property-images',
    'final_disk' => env('PROPERTY_IMAGES_FINAL_DISK', 'public'),
    'final_directory' => 'properties',
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'heic', 'heif'],
    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/heic',
        'image/heif',
    ],
];
