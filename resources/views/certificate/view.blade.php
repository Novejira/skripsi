<!DOCTYPE html>
<html>
<head>
    <title>Detail Sertifikat</title>
    <h1>DETAIL OF CERTIFICATE</h1>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        table {
            width: 100%;
        }
        td {
            padding: 4px 8px;
            vertical-align: top;
        }
        td:first-child {
            white-space: nowrap;
            width: 220px;
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
        <div class="col-md-8">
            <table>
                <tr>
                    <td><strong>Nomor Sertifikat</strong></td>
                    <td>: {{ $certificate->certificate_number }}</td>
                </tr>
                <tr>
                    <td><strong>Nama Peserta</strong></td>
                    <td>: {{ isset($valid) && $valid ? $decrypted_name : 'Tidak dapat ditampilkan' }}</td>
                </tr>
                <tr>
                    <td><strong>Nama Kegiatan</strong></td>
                    <td>: TOEFL Prediction Test</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Tes</strong></td>
                    <td>: {{ \Carbon\Carbon::parse($certificate->test_date)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Validitas</strong></td>
                    <td>: {{ $certificate->validity }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if (isset($valid) && $valid)
        <hr>
        <h3>DETAIL SCORE</h3>
        <div class="row">
            <div class="col-md-6">
                <table>
                    <tr>
                        <td><strong>Listening Comprehension</strong></td>
                        <td>: {{ $certificate->listening }}</td>
                    </tr>
                    <tr>
                        <td><strong>Reading Comprehension</strong></td>
                        <td>: {{ $certificate->reading }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Score</strong></td>
                        <td>: {{ $certificate->score }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOEFL</strong></td>
                        <td>: {{ $certificate->toefl }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOEIC</strong></td>
                        <td>: {{ $certificate->toeic }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-4 p-4 bg-success bg-opacity-10 border border-success rounded shadow-sm text-center">
        <h2 class="text-success fw-bold mb-1">CEdEC Verified</h2>
        <p class="text-muted mb-0">
            Sertifikat ini telah diverifikasi dan dinyatakan valid oleh sistem CEdEC.
        </p>
    </div>

</body>
</html>
