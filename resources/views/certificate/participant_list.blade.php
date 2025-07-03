@extends('layouts.app') {{-- sesuaikan dengan layout kamu --}}

@section('content')
<div class="container">
    <h2>Daftar Peserta</h2>
    <input type="text" id="searchInput" class="form-control mb-3" placeholder="Cari nama atau institusi...">
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-{{ $participant->id }}">
                            <img src="{{ asset('storage/' . $participant->payment_proof) }}" alt="Bukti" width="100">
                        </a>

                        <!-- Modal -->
                        <div class="modal fade" id="modal-{{ $participant->id }}" tabindex="-1" aria-hidden="true">
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
                        <a href="{{ route('certificate.score.form', $participant->id) }}">Input Skor</a>
                    @endif


                    <form action="{{ route('certificate.delete', $participant->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin mau hapus data ini?')">
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
