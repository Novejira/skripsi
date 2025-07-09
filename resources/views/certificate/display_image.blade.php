<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat untuk {{ $certificateData->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"> <!-- Bootstrap Icons -->

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
            position: relative;
        }
        .certificate-img-wrapper {
            position: relative;
            display: inline-block;
        }
        .certificate-img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .download-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 40px;
            color: #ff0202;
            background-color: #ffffffcc;
            padding: 8px;
            border-radius: 50%;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
        }
        .download-icon:hover {
            background-color: #e6ffe6;
        }
    </style>
</head>
<body>
    <div class="container certificate-container">
        <h2 class="mb-4">Sertifikat Berhasil Dibuat!</h2>

        @if (isset($fileName))
            <div class="certificate-img-wrapper">
                <a href="{{ route('certificate.download.pdf', ['uuid' => $certificateData->uuid]) }}" class="download-icon" title="Unduh PDF">
                    <i class="bi bi-download"></i>
                </a>
                <img src="{{ asset('generated_certificates/' . $fileName) }}" alt="Sertifikat" class="certificate-img">
            </div>
        @else
            <div class="alert alert-warning">Sertifikat tidak dapat ditampilkan.</div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
