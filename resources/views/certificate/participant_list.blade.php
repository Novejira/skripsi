@extends('layouts.app') {{-- sesuaikan dengan layout kamu --}}

@section('content')
<div class="container">
    <h2>Daftar Peserta</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM</th>
                <th>Institusi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($participants as $participant)
            <tr>
                <td>{{ $participant->name }}</td>
                <td>{{ $participant->student_id }}</td>
                <td>{{ $participant->institution }}</td>
                <td>
                    <a href="{{ route('certificate.score.form', $participant->id) }}">Input Skor</a>
                    |
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
