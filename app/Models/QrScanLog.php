<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CreateCertificate;

class QrScanLog extends Model
{
  protected $fillable = [
        'certificate_id',
        'scanned_at',
        'ip_address',
        'user_agent',
        'is_valid',
    ];
    public function certificate()
    {
        return $this->belongsTo(CreateCertificate::class, 'certificate_id');
    }
}
