<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateCertificate extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    protected $fillable = [
       // Data pribadi peserta terenkripsi
        'encrypted_name',
        'encrypted_student_id',
        'encrypted_birth_place',
        'encrypted_birth_date',
        'encrypted_institution',
        'encrypted_email',

        // Sertifikat
        'encrypted_certificate_number',
        'file_name',
        'test_date',
        'validity',
        'batch',
        'payment_proof',

        // Skor-skor terenkripsi
        'enc_listening',
        'enc_reading',
        'enc_score',
        'enc_toefl',
        'enc_toeic',

        // Keamanan
        'data_hash',

];
}
