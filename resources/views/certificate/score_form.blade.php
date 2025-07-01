@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Input Skor TOEFL untuk: {{ $participant->name }}</h3>
    <form method="POST" action="{{ route('certificate.score.store', $participant->id) }}">
        @csrf

        <div class="mb-3">
            <label for="listening" class="form-label">Listening Comprehension:</label>
            <input type="number" name="listening" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="structure" class="form-label">Structure & Written Expression:</label>
            <input type="number" name="structure" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="reading" class="form-label">Reading Comprehension:</label>
            <input type="number" name="reading" class="form-control" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Generate Sertifikat</button>
        </div>
    </form>
</div>
@endsection
