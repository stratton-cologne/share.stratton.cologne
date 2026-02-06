<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
            $table->boolean('mfa_totp_enabled')->default(false)->after('is_admin');
            $table->text('mfa_totp_secret')->nullable()->after('mfa_totp_enabled');
            $table->boolean('mfa_email_enabled')->default(false)->after('mfa_totp_secret');
            $table->string('mfa_email_code_hash')->nullable()->after('mfa_email_enabled');
            $table->timestamp('mfa_email_expires_at')->nullable()->after('mfa_email_code_hash');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_admin',
                'mfa_totp_enabled',
                'mfa_totp_secret',
                'mfa_email_enabled',
                'mfa_email_code_hash',
                'mfa_email_expires_at',
            ]);
        });
    }
};
