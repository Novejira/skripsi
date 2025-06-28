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
    <div class="row">
    <div class="col-md-6">
        <p><strong>Nomor Sertifikat:</strong> {{ $certificate->certificate_number }}</p>
        <p><strong>Nama Peserta:</strong> {{ $certificate->participant_name }}</p>
        <p><strong>Nama Kegiatan:</strong> {{ $certificate->activity_name }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Tanggal Tes:</strong> {{ $certificate->test_date }}</p>
        <p><strong>Tempat Tes:</strong> {{ $certificate->test_location }}</p>
    </div>
</div>
<hr>
<h4>Rincian Skor:</h4>
<div class="row">
    <div class="col-md-4">
        <p><strong>Listening Comprehension:</strong> {{ $certificate->listening_score }}</p>
    </div>
    <div class="col-md-4">
        <p><strong>Structure & Written Expression:</strong> {{ $certificate->written_score }}</p>
    </div>
    <div class="col-md-4">
        <p><strong>Reading Comprehension:</strong> {{ $certificate->reading_score }}</p>
    </div>
</div>
<h3 class="text-center mt-4">Total Score: {{ $certificate->total_score }}</h3>
</body>
</html>
