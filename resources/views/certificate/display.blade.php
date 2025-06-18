<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat untuk {{ $certificate->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body { background-color: #f8f9fa; }
        .certificate-card {
            background-color: #ffffff;
            border: 2px solid #007bff;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 800px;
            width: 100%;
            margin: 50px auto;
        }
        .certificate-title {
            font-family: 'Georgia', serif;
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .certificate-subtitle {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 30px;
        }
        .participant-name {
            font-family: 'Times New Roman', serif;
            font-size: 3.5em;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
            text-transform: capitalize;
            border-bottom: 3px solid #ccc;
            display: inline-block;
            padding-bottom: 5px;
        }
        .certificate-details {
            font-size: 1.1em;
            color: #666;
            line-height: 1.8;
        }
        .certificate-id {
            font-size: 0.9em;
            color: #888;
            margin-top: 30px;
        }
        .btn-back {
            margin-top: 30px;
        }
        pre {
            white-space: pre-wrap; /* Mempertahankan spasi dan baris baru */
            font-family: inherit; /* Menggunakan font dari parent */
            text-align: center;
            border: none;
            background-color: transparent;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate-card">
            <div class="certificate-title">Sertifikat Penghargaan</div>
            <div class="certificate-subtitle">Dengan bangga dipersembahkan kepada:</div>
            <div class="participant-name">{{ $certificate->name }}</div>
            <div class="certificate-details">
                Atas partisipasinya yang luar biasa dalam acara kami.
                <br><br>
                Diterbitkan pada: <strong>{{ $certificate->issueDate }}</strong>
            </div>
            <div class="certificate-id">ID Sertifikat: {{ $certificate->certificateId }}</div>
            <a href="{{ route('certificate.form') }}" class="btn btn-secondary btn-back">Kembali ke Form</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
