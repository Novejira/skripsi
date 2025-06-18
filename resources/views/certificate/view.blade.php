<!DOCTYPE html>
<html>
<head>
    <title>Detail Sertifikat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .justify-text {
            text-align: justify;
        }
    </style>
</head>
<body class="p-5">
    <div class="container">
        <h2 class="text-center mb-4">Detail Sertifikat</h2>
        <p class="justify-text"><strong>Nama Peserta:</strong> {{ $certificate->name }}</p>
    </div>
</body>
</html>
