<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            $table->string('uploader_email')->nullable()->after('token');
            $table->timestamp('notified_at')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            $table->dropColumn(['uploader_email', 'notified_at']);
        });
    }
};
