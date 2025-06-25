<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat untuk {{ $certificateData->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .certificate-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .certificate-img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .info-text {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container certificate-container">
        <h2 class="mb-4">Sertifikat Berhasil Dibuat!</h2>
        <p class="info-text">Untuk **{{ $certificateData->name }}**</p>
        <p class="info-text">Diterbitkan pada: {{ $certificateData->issueDate }} (ID: {{ $certificateData->certificateId }})</p>

        @if (isset($fileName))
            <img src="{{ asset('generated_certificates/' . $fileName) }}" alt="Sertifikat" class="certificate-img">
            <div class="mt-4">
                <a href="{{ route('certificate.download.pdf', ['id' => $certificateData->id]) }}" class="btn btn-success me-2">Unduh PDF Sertifikat</a>
                <a href="{{ route('certificate.form') }}" class="btn btn-secondary">Buat Sertifikat Lain</a>
            </div>
        @else
            <div class="alert alert-warning">Sertifikat tidak dapat ditampilkan.</div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
