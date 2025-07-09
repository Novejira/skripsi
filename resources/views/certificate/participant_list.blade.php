@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Daftar Peserta TOEFL</h2>

    {{-- FORM ADMIN: Pengaturan Tes TOEFL Berdasarkan Batch --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Pengaturan Tes TOEFL per Batch</h5>
            <form method="POST" action="{{ route('certificate.participants.settings') }}">
                @csrf

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="batch" class="form-label">Pilih Batch</label>
                        <select name="batch" class="form-select" required>
                            <option value="">-- Pilih Batch --</option>
                            @foreach($participants->pluck('batch')->unique()->sort() as $batch)
                                <option value="{{ $batch }}">{{ $batch }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="test_date" class="form-label">Tanggal Tes</label>
                        <input type="date" class="form-control" name="test_date" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="validity" class="form-label">Masa Berlaku Sertifikat</label>
                        <input type="date" class="form-control" name="validity" required>
                    </div>
                </div>

                <button class="btn btn-primary">Simpan Pengaturan Batch</button>
            </form>
        </div>
    </div>


        <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau institusi...">
        </div>
        <div class="col-md-6">
            <select id="batchFilter" class="form-select">
                <option value="">Semua Batch</option>
                @php
                    $batches = $participants->pluck('batch')->unique()->sort();
                @endphp
                @foreach($batches as $batch)
                    <option value="{{ $batch }}">{{ $batch }}</option>
                @endforeach
            </select>
        </div>
    </div>


    <table class="table table-bordered table-striped align-middle text-center table-thick-border">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Institusi</th>
                <th>Batch TOEFL</th>
                <th>Bukti Pembayaran</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($participants as $index => $participant)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $participant->participant_name }}</td>
                <td>{{ $participant->student_id }}</td>
                <td>{{ $participant->institution }}</td>
                <td>{{ $participant->batch }}</td>
                <td>
                    @if($participant->payment_proof)
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-{{ $participant->uuid }}">
                            <img src="{{ asset('storage/' . $participant->payment_proof) }}" alt="Bukti" width="100">
                        </a>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-{{ $participant->uuid }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-body text-center">
                                        <img src="{{ asset('storage/' . $participant->payment_proof) }}" class="img-fluid" alt="Zoom Bukti">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        Tidak ada
                    @endif
                </td>

                <td>
                    @if ($participant->score !== null)
                        <span class="badge bg-success">Done</span>
                    @else
                        <span class="badge bg-secondary">Belum</span>
                    @endif
                </td>

                <td>
                    <div class="d-flex flex-column align-items-center gap-1">
                        <a href="{{ route('certificate.score.form', $participant->uuid) }}" class="btn btn-sm btn-{{ $participant->score !== null ? 'warning' : 'primary' }}">
                            <i class="bi bi-pencil-square"></i> {{ $participant->score !== null ? 'Edit Skor' : 'Input Skor' }}
                        </a>

                        <form action="{{ route('certificate.delete', $participant->uuid) }}" method="POST" onsubmit="return confirm('Yakin mau hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </td>

            </tr>
        @endforeach

        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const query = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const name = row.children[1].textContent.toLowerCase(); // kolom Nama
            const institution = row.children[3].textContent.toLowerCase(); // kolom Institusi
            row.style.display = (name.includes(query) || institution.includes(query)) ? '' : 'none';
        });
    });
</script>
@endsection
