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
        Schema::create('codisec_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wordpress_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->date('session_date')->nullable()->index();
            $table->text('description')->nullable();
            $table->text('document_path')->nullable();
            $table->text('external_url')->nullable();
            $table->string('status')->default('published')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codisec_sessions');
    }
};
