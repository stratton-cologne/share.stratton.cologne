<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            $table->timestamp('notified_72h_at')->nullable()->after('notified_at');
            $table->timestamp('notified_24h_at')->nullable()->after('notified_72h_at');
        });
    }

    public function down(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            $table->dropColumn(['notified_72h_at', 'notified_24h_at']);
        });
    }
};
