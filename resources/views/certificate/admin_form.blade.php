@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Form Tambahan Admin</h3>
    <form method="POST" action="{{ route('certificate.final.generate') }}">
        @csrf

        <p><strong>Peserta:</strong> {{ $participant['participant_name'] }}</p>

        <div class="mb-3">
            <label for="test_date" class="form-label">Tanggal Tes</label>
            <input type="date" class="form-control" name="test_date" required>
        </div>

        <div class="mb-3">
            <label for="validity" class="form-label">Validitas Sertifikat</label>
            <input type="text" class="form-control" name="validity" required placeholder="cth: 1 Tahun">
        </div>

        <div class="mb-3">
            <label for="certificate_number" class="form-label">Nomor Sertifikat</label>
            <input type="text" class="form-control" name="certificate_number" required>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary btn-lg">Generate Sertifikat</button>
        </div>
    </form>
</div>
@endsection
