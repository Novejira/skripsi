<form action="{{ route('certificate.generate') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="participant_name" class="form-label">Nama:</label>
        <input type="text" class="form-control" id="participant_name" name="participant_name" value="{{ old('participant_name') }}" required>
    </div>

    <div class="mb-3">
        <label for="birth_place" class="form-label">Lahir di:</label>
        <input type="text" class="form-control" id="birth_place" name="birth_place" value="{{ old('birth_place') }}">
    </div>

    <div class="mb-3">
        <label for="birth_date" class="form-label">Tanggal Lahir:</label>
        <input type="text" class="form-control" id="birth_date" name="birth_date" placeholder="cth: 26 Juni 2002" value="{{ old('birth_date') }}">
    </div>

    <div class="mb-3">
        <label for="student_id" class="form-label">Nomor Induk Mahasiswa:</label>
        <input type="number" class="form-control" id="student_id" name="student_id" value="{{ old('student_id') }}" required>
    </div>

    <div class="mb-3">
        <label for="institution" class="form-label">Institusi:</label>
        <input type="text" class="form-control" id="institution" name="institution" value="{{ old('institution') }}">
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">Generate Sertifikat</button>
    </div>
</form>
