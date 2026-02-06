<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shared_batches', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('uploader_email');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedInteger('max_downloads')->nullable();
            $table->timestamp('notified_72h_at')->nullable();
            $table->timestamp('notified_24h_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_batches');
    }
};
