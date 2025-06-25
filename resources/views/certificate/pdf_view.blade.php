<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat PDF</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
        }
        img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* untuk isi penuh tanpa sisa putih */
        }
    </style>
</head>
<body>
    @php
        $imageData = base64_encode(file_get_contents($imagePath));
        $src = 'data:image/png;base64,' . $imageData;
    @endphp

    <img src="{{ $src }}" alt="Sertifikat">
</body>
</html>
