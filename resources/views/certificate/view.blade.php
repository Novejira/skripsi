<!DOCTYPE html>
<html>
<head>
    <title>Detail Sertifikat</title>
    <h3>Detail Sertifikat</h3>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .justify-text {
            text-align: justify;
        }
    </style>
</head>
<body class="p-5">

    {{-- ✅ Pesan validitas --}}
    @if (isset($valid) && $valid)
        <div class="alert alert-success">
            Sertifikat ini <strong>valid</strong> ✅
        </div>

    @else
        <div class="alert alert-danger">
            Sertifikat ini <strong>tidak valid</strong> ❌ — data kemungkinan telah diubah atau rusak.
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <p><strong>Nomor Sertifikat:</strong> {{ $certificate->certificate_number }}</p>

            {{-- 🔐 Gunakan nama hasil dekripsi jika valid --}}
            <p><strong>Nama Peserta:</strong>
                {{ isset($valid) && $valid ? $decrypted_name : 'Tidak dapat ditampilkan' }}
            </p>

            <p><strong>Nama Kegiatan:</strong> TOEFL Prediction Test</p>
            <p><strong>Tanggal Tes:</strong> {{ \Carbon\Carbon::parse($certificate->test_date)->format('d/m/Y') }}</p>
            <p><strong>Validitas:</strong> {{ $certificate->validity }}</p>
        </div>
    </div>

    @if (isset($valid) && $valid)
    <hr>
    <h3>Rincian Skor</h3>
    <div class="row">
        <div class="col-md-4">
            <p><strong>Listening Comprehension:</strong> {{ $certificate->listening }}</p>
            <p><strong>Structure & Written Expression:</strong> {{ $certificate->structure }}</p>
            <p><strong>Reading Comprehension:</strong> {{ $certificate->reading }}</p>
            <p><strong>Total Score:</strong> {{ $certificate->score }}</p>
        </div>
    </div>
    @endif

     <div class="mt-4 p-4 bg-success bg-opacity-10 border border-success rounded shadow-sm text-center">
            <h2 class="text-success fw-bold mb-1">
                CEdEC Verified
            </h2>
            <p class="text-muted mb-0">
                Sertifikat ini telah diverifikasi dan dinyatakan valid oleh sistem CEdEC.
            </p>
        </div>

</body>
</html>
