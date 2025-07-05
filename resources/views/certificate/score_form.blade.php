@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Input Skor TOEFL untuk: {{ $participant->name }}</h3>
    <form method="POST" action="{{ route('certificate.score.store', $participant->id) }}">
        @csrf

        <div class="mb-3">
            <label for="listening" class="form-label">Listening Comprehension:</label>
            <input type="number" name="listening" class="form-control score-input" max="500" required>
            <div class="warning-text">⚠ Nilai melebihi batas maksimum 500</div>
        </div>

        <div class="mb-3">
            <label for="reading" class="form-label">Reading Comprehension:</label>
            <input type="number" name="reading" class="form-control score-input" max="500" required>
            <div class="warning-text">⚠ Nilai melebihi batas maksimum 500</div>
        </div>

        <div class="mb-3">
            <label for="toefl" class="form-label">EXAM COMPARATIVE SCORE</label>
        </div>

        <div class="mb-3">
            <label for="toefl" class="form-label">TOEFL:</label>
            <input type="number" name="toefl" class="form-control score-input" max="500" required>
            <div class="warning-text">⚠ Nilai melebihi batas maksimum 500</div>
        </div>

        <div class="mb-3">
            <label for="toeic" class="form-label">TOEIC:</label>
            <input type="number" name="toeic" class="form-control score-input" max="500" required>
            <div class="warning-text">⚠ Nilai melebihi batas maksimum 500</div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Generate Sertifikat</button>
        </div>
    </form>
</div>

{{-- CSS dan JavaScript --}}
<style>
    .score-input {
        background-image: linear-gradient(to right, #c8e6c9 0%, #c8e6c9 0%, transparent 0%, transparent 100%);
        background-repeat: no-repeat;
        background-size: 0% 100%;
        transition: background-size 0.3s ease, border-color 0.3s ease;
    }

    .score-input.invalid {
        border-color: #e53935 !important;
        background-image: linear-gradient(to right, #ffcdd2 0%, #ffcdd2 100%);
    }

    .warning-text {
        color: #e53935;
        font-size: 0.875rem;
        display: none;
        margin-top: 4px;
    }

    .warning-text.active {
        display: block;
    }
</style>

<script>
    const maxScore = 400;
    const hardLimit = 500;

    function updateScoreInput(input) {
        const val = parseInt(input.value) || 0;
        const percent = Math.min((val / maxScore) * 100, 100);
        const warning = input.nextElementSibling;

        if (val > hardLimit) {
            input.classList.add('invalid');
            if (warning) warning.classList.add('active');
        } else {
            input.classList.remove('invalid');
            if (warning) warning.classList.remove('active');
        }

        if (val <= hardLimit) {
            input.style.backgroundImage = `linear-gradient(to right, #c8e6c9 ${percent}%, transparent ${percent}%)`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.score-input').forEach(input => {
            input.addEventListener('input', () => updateScoreInput(input));
            updateScoreInput(input); // inisialisasi saat load
        });
    });
</script>
@endsection
