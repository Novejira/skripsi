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
                    <td>{{ $certificate->certificate_number }}</td>
                </tr>
                <tr>
                    <th>Participant Name</th>
                    <td>
                        @if(isset($valid) && $valid)
                            {{ $decrypted_name }}
                        @else
                            Tidak tersedia
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Name Of Activity</th>
                    <td>TOEFL Prediction Test</td>
                </tr>
                <tr>
                    <th>Date of Test</th>
                    <td>{{ \Carbon\Carbon::parse($certificate->test_date)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Valid Until</th>
                    <td>{{ \Carbon\Carbon::parse($certificate->validity)->format('d F Y') }}</td>
                </tr>
            </table>

            {{-- Tombol Download PDF --}}
            <a href="{{ route('certificate.download.pdf', $certificate->uuid) }}" class="btn btn-primary mt-3">
                Download PDF
            </a>
        </div>

        {{-- Kolom Kanan: Preview Sertifikat --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">Preview Sertifikat</h5>
                    <img src="{{ asset('generated_certificates/' . $certificate->file_name) }}" alt="Gambar Sertifikat" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
