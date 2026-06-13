<?php

return [
    'max_files_per_property' => (int) env('PROPERTY_IMAGES_MAX_FILES', 50),
    'max_file_size_bytes' => (int) env('PROPERTY_IMAGES_MAX_FILE_SIZE', 20 * 1024 * 1024),
    'request_max_body_hint' => (int) env('PROPERTY_IMAGES_REQUEST_MAX_BODY', 100 * 1024 * 1024),
    'processing' => [
        'main_max_width' => 1920,
        'main_max_height' => 1080,
        'thumb_small_width' => 400,
        'thumb_small_height' => 300,
        'thumb_medium_width' => 800,
        'thumb_medium_height' => 600,
        'webp_quality' => (int) env('PROPERTY_IMAGES_WEBP_QUALITY', 82),
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
