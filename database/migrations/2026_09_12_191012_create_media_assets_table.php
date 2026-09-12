<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('alt_text')->nullable();
            $table->text('file_path')->nullable();
            $table->text('original_url')->nullable();
            $table->string('mime_type')->nullable()->index();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('wordpress_parent_id')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
