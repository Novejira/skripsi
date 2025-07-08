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
    'participant_name',
    'file_name',
    'birth_place',
    'birth_date',
    'student_id',
    'institution',
    'validity',
    'test_date',
    'certificate_number',
    'listening',
    'reading',
    'toefl',
    'toeic',
    'score',
    'payment_proof',
    'encrypted_name',
    'data_hash',

];
}
