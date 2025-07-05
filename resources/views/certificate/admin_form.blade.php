@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Form Tambahan Admin</h3>
    <form method="POST" action="{{ route('certificate.admin.to-participant-list') }}">
        @csrf

        <div class="mb-3">
            <label for="test_date" class="form-label">Tanggal Tes</label>
            <input type="date" class="form-control" name="test_date" required>
        </div>

        <div class="mb-3">
            <label for="validity" class="form-label">Validitas Sertifikat</label>
            <input type="date" class="form-control" id="validity" name="validity" placeholder="cth: 26 Juni 2002" value="{{ old('validity') }}"required>
        </div>

        <div class="d-grid">
            <button class="btn btn-primary btn-lg">SUBMIT</button>
        </div>
    </form>
</div>
@endsection
