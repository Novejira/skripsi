<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use App\Models\CreateCertificate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Generator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;


class CertificateController extends Controller
{
    /**
     * Menampilkan form untuk input nama.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('certificate.form');
    }

    /**
     * Menggenerate sertifikat dari gambar template berdasarkan nama yang diinput.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Intervention\Image\ImageManager  $imageManager
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function generate(Request $request, Generator $qr)
    {
        $imageManager = new ImageManager(new GdDriver());

        // Validasi input
        $request->validate([
            'participant_name' => 'required|string|min:3|max:255',
        ], [
            'participant_name.required' => 'Nama peserta wajib diisi.',
            'participant_name.min' => 'Nama peserta minimal 3 karakter.',
            'participant_name.max' => 'Nama peserta maksimal 255 karakter.',
        ]);

        $name = $request->input('participant_name');

        // Buat instance dari model Certificate
        $certificateData = new Certificate($name);

        // Path gambar template
        $templatePath = public_path('certificates/certificate_template.png');

        if (!file_exists($templatePath)) {
            return back()->withErrors(['template_error' => 'File template sertifikat tidak ditemukan. Harap pastikan ada di public/certificates/certificate_template.png']);
        }

        // Muat gambar menggunakan ImageManager v3
        $img = $imageManager->read($templatePath);

        // Path font
        $fontPath = public_path('fonts/OpenSans-Regular.ttf');

        if (!file_exists($fontPath)) {
            $fontPath = null;
        }

        // Tambahkan teks ke gambar
        $img->text(strtoupper($name), 1000, 500, function ($font) use ($fontPath) {
            if ($fontPath) {
                $font->filename($fontPath);
            }
            $font->size(100);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });

        // Simpan gambar
        $fileName = 'certificate_' . uniqid() . '.png';
        $outputDir = public_path('generated_certificates');

        $certificate = CreateCertificate::create([
        'name' => $name,
        'file_name' => $fileName,
        ]);

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $outputPath = $outputDir . '/' . $fileName;

         // Generate URL untuk QR Code
        $url = route('certificate.view', ['id' => $certificate->id]);
        $qrPng = $qr->format('png')->size(250)->generate($url);
        $qrPath = public_path('generated_certificates/qr_' . $certificate->id . '.png');
        file_put_contents($qrPath, $qrPng);

        $qrImage = $imageManager->read($qrPath);
        $img->place($qrImage, 'bottom-left', 260, 70);

        $img->save($outputPath);

        return view('certificate.display_image', [
        'certificateData' => $certificate,
        'fileName' => $fileName,
    ]);
}


    public function view($id){
    // Cari data sertifikat berdasarkan ID
    $certificate = CreateCertificate::findOrFail($id);

    // Tampilkan view detail dan kirim data sertifikat
    return view('certificate.view', compact('certificate'));
}

    public function downloadPdf($id)
    {
        $certificate = CreateCertificate::findOrFail($id);
        $imagePath = public_path('generated_certificates/' . $certificate->file_name);

        if (!file_exists($imagePath)) {
            abort(404, 'File sertifikat tidak ditemukan');
        }

        // Mendapatkan dimensi asli dari gambar sertifikat (PNG)
        // Fungsi getimagesize() mengembalikan array: [width, height, type, attribute]
        list($widthPx, $heightPx) = getimagesize($imagePath);

        // Mengkonversi piksel ke poin (1 inci = 72 poin, dan asumsi 96 DPI untuk gambar web)
        // Ini akan membuat halaman PDF memiliki ukuran yang sama persis dengan gambar Anda dalam satuan poin.
        $widthPt = $widthPx * 72 / 96; // Konversi dari px ke pt
        $heightPt = $heightPx * 72 / 96; // Konversi dari px ke pt

        // Menentukan orientasi berdasarkan dimensi gambar
        $orientation = ($widthPx > $heightPx) ? 'landscape' : 'portrait';

       $pdf = Pdf::loadView('certificate.pdf_view', [
                'certificate' => $certificate,
                'imagePath' => $imagePath
        ])->setPaper('a4', 'landscape'); // tambahkan ini

        return $pdf->download('sertifikat_' . Str::slug($certificate->name) . '.pdf');
    }


}
