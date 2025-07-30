@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">DETAIL OF CERTIFICATE</h1>

    <div class="row">
        {{-- Kolom Kiri: Data Sertifikat --}}
        <div class="col-md-6">
            <table class="table">
                <tr>
                    <th>Certificate Number</th>
                    <td>{{ $decrypted_cert_number ?? 'Tidak tersedia' }}</td>
                </tr>
                <tr>
                    <th>Participant Name</th>
                    <td>{{ $decrypted_name ?? 'Tidak tersedia' }}</td>
                </tr>
                <tr>
                    <th>Name Of Activity</th>
                    <td>TOEFL Prediction Test</td>
                </tr>
                <tr>
                    <th>Date of Test</th>
                    <td>
                        @if (!empty($certificate->test_date))
                            {{ \Carbon\Carbon::parse($certificate->test_date)->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Valid Until</th>
                    <td>
                        @if (!empty($certificate->validity))
                            {{ \Carbon\Carbon::parse($certificate->validity)->translatedFormat('d F Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Tombol Download PDF --}}
            @if ($isValid)
                <a href="{{ route('certificate.download.pdf', $certificate->uuid) }}" class="btn btn-primary mt-3">
                    Download PDF
                </a>
            @else
                <div class="alert alert-danger mt-3">
                    Sertifikat ini tidak valid atau telah diubah.
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Preview Sertifikat --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Preview Sertifikat</h5>
                    @if (!empty($certificate->file_name))
                        <img src="{{ asset('generated_certificates/' . $certificate->file_name) }}" alt="Gambar Sertifikat" class="img-fluid rounded">
                    @else
                        <p class="text-muted">Belum ada gambar sertifikat</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
