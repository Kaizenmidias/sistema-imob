<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_photos', function (Blueprint $table) {
            $table->unsignedInteger('width')->nullable()->after('url');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->unsignedBigInteger('size')->nullable()->after('height');
            $table->string('mime_type', 100)->nullable()->after('size');
            $table->string('thumb_small_path', 500)->nullable()->after('mime_type');
            $table->string('thumb_medium_path', 500)->nullable()->after('thumb_small_path');
            $table->boolean('optimized')->default(false)->after('thumb_medium_path');
            $table->timestamp('processed_at')->nullable()->after('optimized');
            $table->string('processing_status', 30)->default('queued')->after('processed_at');
            $table->text('processing_error')->nullable()->after('processing_status');
        });
    }

    public function down(): void
    {
        Schema::table('property_photos', function (Blueprint $table) {
            $table->dropColumn([
                'width',
                'height',
                'size',
                'mime_type',
                'thumb_small_path',
                'thumb_medium_path',
                'optimized',
                'processed_at',
                'processing_status',
                'processing_error',
            ]);
        });
    }
};
