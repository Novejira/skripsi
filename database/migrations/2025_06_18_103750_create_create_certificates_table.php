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
        Schema::create('create_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_name')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('student_id')->nullable();
            $table->string('institution')->nullable();
            $table->integer('listening')->nullable();
            $table->integer('structure')->nullable();
            $table->integer('reading')->nullable();
            $table->string('test_date')->nullable();
            $table->integer('score')->nullable(); // total
            $table->string('validity')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('payment_proof')->nullable();
            $table->integer('order_number')->nullable(); // nomor urut pendaftar
            $table->text('encrypted_name')->nullable();
            $table->string('data_hash')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_certificates');
    }
};
