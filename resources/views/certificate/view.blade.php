<!DOCTYPE html>
<html>
<head>
    <h3>Detail Sertifikat</h3>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .justify-text {
            text-align: justify;
        }
    </style>
</head>
<body class="p-5">
    <div class="row">
        <div class="col-md-6">
            <p><strong>Nomor Sertifikat:</strong> {{ $certificate->certificate_number }}</p>
            <p><strong>Nama Peserta:</strong> {{ $certificate->name }}</p>
            <p><strong>Nama Kegiatan:</strong> TOEFL Prediction Test</p>
            <p><strong>Tanggal Tes:</strong> {{ $certificate->test_date }}</p>
            <p><strong>Validitas:</strong> {{ $certificate->validity }}</p>
        </div>
    </div>
    <hr>
    <h3>Rincian Skor</h3>
    <div class="row">
        <div class="col-md-4">
            <p><strong>Listening Comprehension:</strong> {{ $certificate->listening }}</p>
            <p><strong>Structure & Written Expression:</strong> {{ $certificate->structure }}</p>
            <p><strong>Reading Comprehension:</strong> {{ $certificate->reading }}</p>
        </div>
    </div>
    <h3 class="text-center mt-4">Total Score: {{ $certificate->score }}</h3>
</body>
</html>
