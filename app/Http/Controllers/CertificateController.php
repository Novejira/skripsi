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
use Carbon\Carbon;
use App\Helpers\SecurityHelper;
use App\Models\QrScanLog;
use Illuminate\Support\Facades\URL;



class CertificateController extends Controller
{
    public function showForm()
    {
        return view('certificate.form');
    }

        public function storePendaftaran(Request $request)
    {
        $request->validate([
            'participant_name' => 'required|string|min:3|max:255',
            'student_id' => 'required|numeric',
            'birth_place' => 'required|string',
            'birth_date' => 'required|date',
            'institution' => 'required|string',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:1000',
        ]);

        // Simpan bukti pembayaran ke storage
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Simpan ke database
        $certificate = CreateCertificate::create([
//          'participant_name' => $request->participant_name,
            'student_id' => $request->student_id,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'institution' => $request->institution,
            'payment_proof' => $paymentProofPath,
        ]);

        // Redirect ke form admin dengan membawa UUID peserta tersebut
        return redirect()->route('certificate.participants')->with('success', 'Pendaftaran berhasil!');
    }

        public function storeGlobalSettings(Request $request)
    {
        $request->validate([
            'test_date' => 'required|date',
            'validity' => 'required|date',
        ]);

        // Update semua peserta yang belum memiliki tanggal tes / validitas
        CreateCertificate::whereNull('test_date')
            ->orWhereNull('validity')
            ->update([
                'test_date' => $request->test_date,
                'validity' => $request->validity,
            ]);

        return redirect()->route('certificate.participants')->with('success', 'Tanggal tes & validitas berhasil diterapkan.');
    }

    public function showAdminForm(Request $request)
    {
        $data = $request->session()->get('form_data');
        if (!$data) return redirect()->route('certificate.form')->withErrors(['Silakan isi data peserta terlebih dahulu.']);

        return view('certificate.admin_form', ['participant' => $data]);
    }

        public function storeAdminAndRedirect(Request $request)
    {
        $request->validate([
            'test_date' => 'required|date',
            'validity' => 'required|string|max:255',
            // 'certificate_number' dihapus karena akan digenerate otomatis
        ]);

        $participant = $request->session()->get('form_data');
        if (!$participant) {
            return redirect()->route('certificate.form')->withErrors(['Data peserta tidak ditemukan.']);
        }

        // Hitung urutan berdasarkan CreateCertificate
        $order = CreateCertificate::count() + 1;
        $formattedOrder = str_pad($order, 3, '0', STR_PAD_LEFT);
        $certNumber = "$formattedOrder/Sert/TOEFL/03/CEdEC/2025";

        $data = array_merge($participant, $request->only(['test_date', 'validity']));
        $data['certificate_number'] = $certNumber; // generate otomatis

        $data['name'] = $data['participant_name'];
        unset($data['participant_name']);
        $data['file_name'] = 'pending.png';

        CreateCertificate::create($data);

        return redirect()->route('certificate.participants')->with('success', 'Data peserta berhasil disimpan. Silakan input skor.');
    }


    public function listParticipants()
    {
        $participants = CreateCertificate::orderBy('created_at', 'asc')->get();
        return view('certificate.participant_list', compact('participants'));
    }

    public function showScoreForm($uuid)
    {
        $participant = CreateCertificate::findOrFail($uuid);
        return view('certificate.score_form', compact('participant'));
    }

    public function storeScoreAndGenerate(Request $request, $uuid, Generator $qr)
    {
        $request->validate([
            'listening' => 'required|numeric|min:0|max:500',
            'reading' => 'required|numeric|min:0|max:500',
            'toefl' => 'required|numeric|min:0|max:500',
            'toeic' => 'required|numeric|min:0|max:500',
        ]);

        $totalScore = $request->listening + $request->reading;

        $participant = CreateCertificate::findOrFail($uuid);

        $imageManager = new ImageManager(new GdDriver());
        $templatePath = public_path('certificates/certificate_template.png');
        if (!file_exists($templatePath)) {
            return back()->withErrors(['Template tidak ditemukan']);
        }

        $img = $imageManager->read($templatePath);
        $fontPath = public_path('fonts/OpenSans-Regular.ttf');
        if (!file_exists($fontPath)) $fontPath = null;

        $order = CreateCertificate::where('created_at', '<=', $participant->created_at)->count();
        $formattedOrder = str_pad($order, 3, '0', STR_PAD_LEFT);
        $certificateNumber = "$formattedOrder/Sert/TOEFL/03/CEdEC/2025";

        if (!$participant->certificate_number) {
            $participant->certificate_number = $certificateNumber;
        }

        $img->text(strtoupper($participant->name), 122, 580, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->birth_place), 122, 675, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($participant->birth_date)->format('d/m/Y'), 122, 775, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->student_id), 122, 870, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->institution), 750, 580, fn($f) => $this->applyFont($f, $fontPath));
        $img->text($participant->certificate_number, 750, 865, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($participant->test_date)->format('d/m/Y'), 750, 675, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($participant->validity)->format('d/m/Y'),750, 770, fn($f) => $this->applyFont($f, $fontPath));

        $img->text($request->listening, 255, 1065, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($request->reading, 700, 1065, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($request->toefl, 260, 1290, fn($f) => $this->applyFont($f, $fontPath, 60, 'center'));
        $img->text($request->toeic, 550, 1290, fn($f) => $this->applyFont($f, $fontPath, 60, 'center'));
        $img->text($totalScore, 1135, 1065, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));

        $fileName = 'certificate_' . uniqid() . '.png';
        $outputPath = public_path('generated_certificates/' . $fileName);
        if (!file_exists(dirname($outputPath))) mkdir(dirname($outputPath), 0777, true);

        $url = URL::signedRoute('certificate.view', ['uuid' => $participant->uuid]);

        $qrPath = public_path('generated_certificates/qr_' . $participant->uuid . '.png');
        file_put_contents($qrPath, $qr->format('png')->size(200)->generate($url));
        $qrImage = $imageManager->read($qrPath);
        $img->place($qrImage, 'bottom-left', 740, 600);
        $img->save($outputPath);

                // 🔐 Enkripsi nama peserta
        $encryptedName = SecurityHelper::encryptAES($participant->name);

        // 🔐 Buat hash dari data penting untuk verifikasi integritas
        $dataHash = SecurityHelper::createSHA256Hash([
            $encryptedName,
            $participant->birth_place,
            $participant->birth_date,
            $participant->student_id,
            $participant->institution,
            $participant->certificate_number,
            $participant->test_date,
            $participant->validity,
            $request->listening,
            $request->toefl,
            $request->toeic,
            $request->reading,
            $totalScore,
        ]);


        $participant->update([
            'certificate_number' => $participant->certificate_number,
            'listening' => $request->listening,
            'toefl' => $request->toefl,
            'toeic' => $request->toeic,
            'reading' => $request->reading,
            'score' => $totalScore,
            'file_name' => $fileName,
            'encrypted_name' => $encryptedName,
            'data_hash' => $dataHash,

        ]);

        return view('certificate.display_image', [
            'certificateData' => $participant,
            'fileName' => $fileName,
        ]);
    }

        public function view($uuid, Request $request)
    {
         if (!$request->hasValidSignature()) {
        abort(403, 'Link tidak valid atau sudah kedaluwarsa.');
    }
        $certificate = CreateCertificate::where('uuid', $uuid)->firstOrFail();

        // ✅ Rekalkulasi ulang hash
        $recalculatedHash = SecurityHelper::createSHA256Hash([
            $certificate->encrypted_name,
            $certificate->birth_place,
            $certificate->birth_date,
            $certificate->student_id,
            $certificate->institution,
            $certificate->certificate_number,
            $certificate->test_date,
            $certificate->validity,
            $certificate->listening,
            $certificate->toefl,
            $certificate->toeic,
            $certificate->reading,
            $certificate->score,
        ]);


        $isValid = $recalculatedHash === $certificate->data_hash;

        // 📝 Simpan log scan
        QrScanLog::create([
            'certificate_id' => $certificate->uuid,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'is_valid' => $isValid,
        ]);

        if ($isValid) {
            $decryptedName = SecurityHelper::decryptAES($certificate->encrypted_name);

            return view('certificate.view', [
                'certificate' => $certificate,
                'decrypted_name' => $decryptedName,
                'valid' => true,
            ]);
        } else {
            return view('certificate.view', [
                'certificate' => $certificate,
                'valid' => false,
            ]);
        }
    }

    public function downloadPdf($uuid)
    {
        $certificate = CreateCertificate::findOrFail($uuid);
        $imagePath = public_path('generated_certificates/' . $certificate->file_name);
        if (!file_exists($imagePath)) abort(404, 'File sertifikat tidak ditemukan');

        $pdf = Pdf::loadView('certificate.pdf_view', [
            'certificate' => $certificate,
            'imagePath' => $imagePath
        ])->setPaper('a4');

        return $pdf->download('sertifikat_' . Str::slug($certificate->name) . '.pdf');
    }

    public function deleteParticipant($uuid)
    {
        $participant = CreateCertificate::findOrFail($uuid);
        $filePath = public_path('generated_certificates/' . $participant->file_name);
        if (file_exists($filePath)) unlink($filePath);

        $qrPath = public_path('generated_certificates/qr_' . $participant->id . '.png');
        if (file_exists($qrPath)) unlink($qrPath);

        $participant->delete();

        return redirect()->route('certificate.participants')->with('success', 'Data berhasil dihapus.');
    }

    private function applyFont($font, $fontPath, $size = 30, $align = 'left')
    {
        if ($fontPath) $font->filename($fontPath);
        $font->size($size);
        $font->color('#000000');
        $font->align($align);
        $font->valign('top');
    }

        public function scanLogs()
    {
        $logs = QrScanLog::with('certificate')->latest()->paginate(10);
        return view('admin.qr_scan_logs', compact('logs'));
    }

}
