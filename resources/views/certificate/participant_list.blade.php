@extends('layouts.app') {{-- sesuaikan dengan layout kamu --}}

@section('content')
<div class="container">
    <h2>Daftar Peserta</h2>

    {{-- FORM ADMIN: Tanggal Tes & Validitas --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Pengaturan Tes TOEFL</h5>
            <form method="POST" action="{{ route('certificate.participants.settings') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="test_date" class="form-label">Tanggal Tes</label>
                        <input type="date" class="form-control" name="test_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="validity" class="form-label">Masa Berlaku Sertifikat</label>
                        <input type="date" class="form-control" name="validity" required>
                    </div>
                </div>

                <button class="btn btn-primary">Simpan Pengaturan untuk Semua Peserta</button>
            </form>
        </div>
    </div>

    <input type="text" uuid="searchInput" class="form-control mb-3" placeholder="Cari nama atau institusi...">
    <table class="table table-bordered table-striped align-middle text-center table-thick-border">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Institusi</th>
                <th>Bukti Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($participants as $index => $participant)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $participant->name }}</td>
                <td>{{ $participant->student_id }}</td>
                <td>{{ $participant->institution }}</td>
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
                        <span style="color: green; font-weight: bold;">Done</span>
                    @else
                        <a href="{{ route('certificate.score.form', $participant->uuid) }}">Input Skor</a>
                    @endif


                    <form action="{{ route('certificate.delete', $participant->uuid) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin mau hapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="color:red; background:none; border:none; padding:0;">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>
@endsection

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
