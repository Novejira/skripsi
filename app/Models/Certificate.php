<?php

namespace App\Models;

// Tidak perlu meng-extend Eloquent\Model karena ini bukan model database
// Jika ingin disimpan ke database, maka akan extend Illuminate\Database\Eloquent\Model
// dan memiliki properti fillable, tabel, dll.

class Certificate
{
    public $name;
    public $issueDate;
    public $certificateId; // Contoh properti lain

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->issueDate = now()->format('d F Y'); // Tanggal saat ini
        $this->certificateId = uniqid('CERT-'); // ID unik sederhana
    }

    /**
     * Mendapatkan teks lengkap sertifikat.
     *
     * @return string
     */
    public function getCertificateText(): string
    {
        return "Dengan bangga dipersembahkan kepada:\n\n" .
               strtoupper($this->name) . "\n\n" .
               "Atas partisipasinya yang luar biasa dalam acara kami.\n\n" .
               "Diterbitkan pada: " . $this->issueDate . "\n" .
               "ID Sertifikat: " . $this->certificateId;
    }
}
