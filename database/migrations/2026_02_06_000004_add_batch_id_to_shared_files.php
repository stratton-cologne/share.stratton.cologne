<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            $table->foreignId('shared_batch_id')->nullable()->after('id')->constrained('shared_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('shared_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shared_batch_id');
        });
    }
};
