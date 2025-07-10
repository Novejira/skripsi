<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Informasi Lanjut</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex flex-column justify-content-center align-items-center min-vh-100 bg-light">
    <div class="text-center">
        <h2>Terima kasih telah mendaftar!</h2>
        <p>Untuk informasi lebih lanjut, silakan gabung ke grup WhatsApp kami:</p>

        <!-- Tombol sejajar -->
        <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
            <!-- Tombol Gabung WhatsApp -->
            <a href="https://chat.whatsapp.com/LHqL3kC0VLt8yK5fT2b3w5?mode=ac_t" class="btn btn-success btn-lg" target="_blank">
                Gabung Grup WhatsApp
            </a>

            <!-- Tombol Kembali ke Halaman Utama -->
            <a href="{{ url('/') }}" class="btn btn-secondary btn-lg">
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>
</body>
</html>
