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
            $table->uuid('uuid')->primary(); // ✅ UUID sebagai primary key
            $table->text('encrypted_name')->nullable();
            $table->text('encrypted_student_id')->nullable();
            $table->text('encrypted_birth_place')->nullable();
            $table->text('encrypted_birth_date')->nullable();
            $table->text('encrypted_institution')->nullable();
            $table->text('encrypted_email')->nullable();
            $table->text('encrypted_certificate_number')->nullable();
            $table->text('enc_listening')->nullable();
            $table->text('enc_reading')->nullable();
            $table->text('enc_score')->nullable();
            $table->text('enc_toefl')->nullable();
            $table->text('enc_toeic')->nullable();

            $table->string('test_date')->nullable();
            $table->string('validity')->nullable();

            $table->string('payment_proof')->nullable();

            $table->string('data_hash')->nullable();

            $table->string('batch')->nullable();
            $table->string('file_name')->nullable();

            // $table->string('participant_name');
            // $table->string('birth_place')->nullable();
            // $table->string('birth_date')->nullable();
            // $table->string('student_id')->nullable();
            // $table->string('institution')->nullable();
            // $table->integer('listening')->nullable();
            // $table->integer('toefl')->nullable();
            // $table->integer('toeic')->nullable();
            // $table->integer('reading')->nullable();
            // $table->integer('score')->nullable(); // total
            // $table->string('certificate_number')->nullable();

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
