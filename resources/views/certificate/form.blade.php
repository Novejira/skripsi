@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Formulir Pendaftaraan TOEFL</h4>
                </div>
                <div class="card-body">

                    {{-- Tampilkan pesan error jika ada --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('certificate.to-admin') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="participant_name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="participant_name" name="participant_name" value="{{ old('participant_name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="birth_place" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" id="birth_place" name="birth_place" value="{{ old('birth_place') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Nomor Induk Mahasiswa (NIM)</label>
                            <input type="text" class="form-control" id="student_id" name="student_id" value="{{ old('student_id') }}" required pattern="[0-9]+" title="NIM harus berupa angka">
                        </div>

                        <div class="mb-3">
                            <label for="institution" class="form-label">Asal Institusi</label>
                            <input type="text" class="form-control" id="institution" name="institution" value="{{ old('institution') }}" required>
                        </div>

                        {{-- Dropdown Batch TOEFL --}}
                        <div class="mb-3">
                            <label for="batch" class="form-label">Batch TOEFL</label>
                            <select class="form-select" id="batch" name="batch" required>
                                <option value="" disabled selected>Pilih Batch</option>
                                <option value="03" {{ old('batch') == '03' ? 'selected' : '' }}>Batch 03</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="payment_proof" class="form-label">Bukti Pembayaran (JPG/PNG)</label>
                            <input type="file" class="form-control" id="payment_proof" name="payment_proof" accept="image/*" required>
                        </div>

                        <div class="alert alert-info">
                            <strong>Rekening Resmi Pembayaran TOEFL:</strong><br>
                            BNI<br>
                            YAYASAN JAKARTA GLOBAL EDUCARE CEDEC<br>
                            <strong>3355300077</strong><br><br>

                            <strong>Biaya:</strong> TOEFL Rp300.000<br>
                            <em>Dihimbau untuk tidak melakukan transaksi melalui rekening pribadi siapapun selain rekening resmi kampus.</em>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">DAFTAR</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
