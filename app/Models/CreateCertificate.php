<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreateCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
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
    'order_number',
    'encrypted_name',
    'data_hash',

];
}
