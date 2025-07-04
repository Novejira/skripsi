<?php

namespace App\Helpers;

use App\Helpers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class SecurityHelper
{
    public static function encryptAES($plainText)
    {
        return Crypt::encryptString($plainText); // Laravel built-in AES
    }

    public static function decryptAES($encryptedText)
    {
        return Crypt::decryptString($encryptedText);
    }

    public static function createSHA256Hash($dataArray)
    {
        $dataString = implode('|', $dataArray); // Gabungkan data penting
        return hash('sha256', $dataString);
    }
}
