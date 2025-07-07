<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_scan_logs', function (Blueprint $table) {
            $table->uuid('certificate_id'); // ✅ gunakan UUID
            $table->foreign('certificate_id')
                  ->references('uuid')
                  ->on('create_certificates')
                  ->onDelete('cascade');

            $table->timestamp('scanned_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_valid');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scan_logs');
    }
};
