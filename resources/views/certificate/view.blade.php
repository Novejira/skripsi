<!DOCTYPE html>
<html>
<head>
    <title>Certificate Details</title>
    <h1>CERTIFICATE DETAILS</h1>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        table {
            width: 100%;
        }
        td {
            padding: 4px 8px;
            vertical-align: top;
        }
        td:first-child {
            white-space: nowrap;
            width: 220px;
        }
    </style>
</head>
<body class="p-5">

    {{-- ✅ Validity message --}}
    @if (isset($valid) && $valid)
        <div class="alert alert-success">
            This certificate is <strong>valid</strong> ✅
        </div>
    @else
        <div class="alert alert-danger">
            This certificate is <strong>not valid</strong> ❌ — data may have been tampered or corrupted.
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <table>
                <tr>
                    <td><strong>Certificate Number</strong></td>
                    <td>: {{ $certificate->certificate_number }}</td>
                </tr>
                <tr>
                    <td><strong>Participant Name</strong></td>
                    <td>: {{ isset($valid) && $valid ? $decrypted_name : 'Unavailable' }}</td>
                </tr>
                <tr>
                    <td><strong>Activity Name</strong></td>
                    <td>: TOEFL Prediction Test</td>
                </tr>
                <tr>
                    <td><strong>Test Date</strong></td>
                    <td>: {{ \Carbon\Carbon::parse($certificate->test_date)->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Certificate Valid Until</strong></td>
                    <td>: {{ \Carbon\Carbon::parse($certificate->validity)->format('d F Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if (isset($valid) && $valid)
        <hr>
        <h3>DETAILED SCORES</h3>
        <div class="row">
            <div class="col-md-6">
                <table>
                    <tr>
                        <td><strong>Listening Comprehension</strong></td>
                        <td>: {{ $certificate->listening }}</td>
                    </tr>
                    <tr>
                        <td><strong>Reading Comprehension</strong></td>
                        <td>: {{ $certificate->reading }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Score</strong></td>
                        <td>: {{ $certificate->score }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOEFL Score</strong></td>
                        <td>: {{ $certificate->toefl }}</td>
                    </tr>
                    <tr>
                        <td><strong>TOEIC Score</strong></td>
                        <td>: {{ $certificate->toeic }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-4 p-4 bg-success bg-opacity-10 border border-success rounded shadow-sm text-center">
        <h2 class="text-success fw-bold mb-1">CEdEC Verified</h2>
        <p class="text-muted mb-0">
            This certificate has been verified and declared valid by the CEdEC system.
        </p>
    </div>

</body>
</html>
