<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Tidak Ditemukan</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fff;
            height: 100vh;
            display: flex;
            justify-content: center; /* Horizontal center */
            align-items: center;     /* Vertical center */
            font-family: Arial, sans-serif;
        }
        .container {
            text-align: center;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .warning-text {
            color: #d9534f;
            font-size: 18px;
            font-weight: bold;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/logo-universitas.png') }}" alt="Logo JGU" class="logo">
        <div class="warning-text">
            DATA PESERTA TIDAK DITEMUKAN<br>
            WASPADAI E-SERTIFIKAT YANG ANDA TERIMA ITU PALSU
        </div>
    </div>
</body>
</html>
