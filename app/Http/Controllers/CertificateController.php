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



class CertificateController extends Controller
{
    public function showForm()
    {
        return view('certificate.form');
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

    public function showScoreForm($id)
    {
        $participant = CreateCertificate::findOrFail($id);
        return view('certificate.score_form', compact('participant'));
    }

    public function storeScoreAndGenerate(Request $request, $id, Generator $qr)
    {
        $request->validate([
            'listening' => 'required|numeric|min:0|max:100',
            'structure' => 'required|numeric|min:0|max:100',
            'reading' => 'required|numeric|min:0|max:100',
        ]);

        $totalScore = $request->listening + $request->structure + $request->reading;

        $participant = CreateCertificate::findOrFail($id);

        $imageManager = new ImageManager(new GdDriver());
        $templatePath = public_path('certificates/certificate_template.png');
        if (!file_exists($templatePath)) {
            return back()->withErrors(['Template tidak ditemukan']);
        }

        $img = $imageManager->read($templatePath);
        $fontPath = public_path('fonts/OpenSans-Regular.ttf');
        if (!file_exists($fontPath)) $fontPath = null;

        $img->text(strtoupper($participant->name), 122, 655, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->birth_place), 122, 750, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->birth_date), 122, 850, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->student_id), 122, 945, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->institution), 720, 655, fn($f) => $this->applyFont($f, $fontPath));
        $img->text($participant->certificate_number, 720, 940, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($participant->test_date)->format('d/m/Y'), 720, 745, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($participant->validity), 720, 845, fn($f) => $this->applyFont($f, $fontPath));

        $img->text($request->listening, 255, 1130, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($request->structure, 655, 1130, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($request->reading, 1055, 1130, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($totalScore, 655, 1390, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));

        $fileName = 'certificate_' . uniqid() . '.png';
        $outputPath = public_path('generated_certificates/' . $fileName);
        if (!file_exists(dirname($outputPath))) mkdir(dirname($outputPath), 0777, true);

        $url = route('certificate.view', ['id' => $participant->id]);
        $qrPath = public_path('generated_certificates/qr_' . $participant->id . '.png');
        file_put_contents($qrPath, $qr->format('png')->size(250)->generate($url));
        $qrImage = $imageManager->read($qrPath);
        $img->place($qrImage, 'bottom-left', 250, 200);
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
            $request->structure,
            $request->reading,
            $totalScore,
        ]);


        $participant->update([
            'listening' => $request->listening,
            'structure' => $request->structure,
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

        public function view($id)
    {
        $certificate = CreateCertificate::findOrFail($id);

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
            $certificate->structure,
            $certificate->reading,
            $certificate->score,
        ]);


        $isValid = $recalculatedHash === $certificate->data_hash;

        // 📝 Simpan log scan
        QrScanLog::create([
            'certificate_id' => $certificate->id,
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

    public function downloadPdf($id)
    {
        $certificate = CreateCertificate::findOrFail($id);
        $imagePath = public_path('generated_certificates/' . $certificate->file_name);
        if (!file_exists($imagePath)) abort(404, 'File sertifikat tidak ditemukan');

        $pdf = Pdf::loadView('certificate.pdf_view', [
            'certificate' => $certificate,
            'imagePath' => $imagePath
        ])->setPaper('a4');

        return $pdf->download('sertifikat_' . Str::slug($certificate->name) . '.pdf');
    }

    public function deleteParticipant($id)
    {
        $participant = CreateCertificate::findOrFail($id);
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

        public function store(Request $request)
    {
        // hitung jumlah pendaftar sebelumnya
        $order = Certificate::count() + 1;

        // buat format nomor sertifikat
        $formattedOrder = str_pad($order, 3, '0', STR_PAD_LEFT); // jadi 001, 002, dst
        $certNumber = "$formattedOrder/Sert/TOEFL/03/CEdEC/2025";

        Certificate::create([
            'user_id' => auth()->id(),
            'order_number' => $order,
            'certificate_number' => $certNumber,
            // data lain
        ]);

        return redirect()->route('admin.form'); // atau step berikutnya
    }

        public function scanLogs()
    {
        $logs = QrScanLog::with('certificate')->latest()->paginate(10);
        return view('admin.qr_scan_logs', compact('logs'));
    }

}
