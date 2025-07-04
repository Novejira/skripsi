@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Riwayat Scan Sertifikat</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Waktu Scan</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Nomor Sertifikat</th>
                <th>Nama</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ Str::limit($log->user_agent, 30) }}</td>
                <td>{{ $log->certificate->certificate_number ?? '-' }}</td>
                <td>{{ $log->certificate->name ?? '-' }}</td>
                <td>
                    @if($log->is_valid)
                        <span class="badge bg-success">Valid ✅</span>
                    @else
                        <span class="badge bg-danger">Tidak Valid ❌</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $logs->links() }}
</div>
@endsection
