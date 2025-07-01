<!DOCTYPE html>
<html lang="id">
<head>
    <style>
        .table-thick-border td,
        .table-thick-border th,
        .table-thick-border {
            border-width: 1px !important; /* ← Atur ketebalan garis */
            border-color: #000 !important; /* ← (opsional) warna garis */
        }
    </style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Sertifikat</title>

    <!-- ✅ Bootstrap CSS (cukup 1x) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        @yield('content')
    </div>

    <!-- ✅ Bootstrap JS (cukup 1x & ditaruh sebelum </body>) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
