<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Mail;
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
use App\Mail\CertificateSent;



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
            'email' => 'required|email',
            'batch' => 'required',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:1000',
        ]);

        // Simpan bukti pembayaran ke storage
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        // 🔐 Enkripsi data dari form
        $encryptedName = SecurityHelper::encryptAES($request->participant_name);
        $encryptedNIM = SecurityHelper::encryptAES($request->student_id);
        $encryptedBirthPlace = SecurityHelper::encryptAES($request->birth_place);
        $encryptedBirthDate = SecurityHelper::encryptAES($request->birth_date);
        $encryptedInstitution = SecurityHelper::encryptAES($request->institution);
        $encryptedEmail = SecurityHelper::encryptAES($request->email);

        // Simpan ke database
        CreateCertificate::create([
            'uuid' => Str::uuid(),
            'encrypted_name' => $encryptedName,
            'encrypted_student_id' => $encryptedNIM,
            'encrypted_birth_place' => $encryptedBirthPlace,
            'encrypted_birth_date' => $encryptedBirthDate,
            'encrypted_institution' => $encryptedInstitution,
            'encrypted_email' => $encryptedEmail,
            'batch' => $request->batch,
            'payment_proof' => $paymentProofPath,
            'file_name' => 'pending.png',
        ]);

        return redirect()->route('whatsapp.info')->with('success', 'Pendaftaran berhasil!');
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
        $certNumber = "$formattedOrder/Sert/TOEFL/" . str_pad($participant['batch'], 2, '0', STR_PAD_LEFT) . "/CEdEC/2025";

        $data = array_merge($participant, $request->only(['test_date', 'validity']));
        $data['certificate_number'] = $certNumber; // generate otomatis

        $data['name'] = $data['participant_name'];
        unset($data['participant_name']);
        $data['file_name'] = 'pending.png';

        CreateCertificate::create($data);

        return redirect()->route('certificate.participants')->with('success', 'Data peserta berhasil disimpan. Silakan input skor.');
    }


        public function listParticipants(Request $request)
    {


        $query = CreateCertificate::query();

        // Filter batch
        if ($request->filled('batch')) {
            $query->where('batch', $request->batch);
        }

        // Filter status skor (sudah atau belum isi skor)
        if ($request->filled('has_score')) {
            if ($request->has_score === 'yes') {
                $query->whereNotNull('score');
            } elseif ($request->has_score === 'no') {
                $query->whereNull('score');
            }
        }

        // ✅ Filter pencarian nama atau institusi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('participant_name', 'like', "%{$search}%")
                ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $participants = $query->orderBy('created_at', 'asc')->get();

        $participants = CreateCertificate::orderBy('created_at')->get()->map(function ($item) {
            return (object) [
                'uuid' => $item->uuid,
                'name' => SecurityHelper::decryptAES($item->encrypted_name),
                'student_id' => SecurityHelper::decryptAES($item->encrypted_student_id),
                'institution' => SecurityHelper::decryptAES($item->encrypted_institution),
                'batch' => $item->batch,
                'payment_proof' => $item->payment_proof,
                'score' => $item->enc_score,
            ];
        });

        return view('certificate.participant_list', [
            'participants' => $participants,
            'selectedBatch' => $request->batch,
            'selectedHasScore' => $request->has_score,
        ]);
    }



    public function showScoreForm($uuid)
    {
        $participant = CreateCertificate::findOrFail($uuid);
        return view('certificate.score_form', compact('participant'));
    }

        public function storeScoreAndGenerate(Request $request, $uuid, Generator $qr)
    {
        // 1. Validasi input skor
        $request->validate([
            'listening' => 'required|numeric|min:0|max:500',
            'reading' => 'required|numeric|min:0|max:500',
            'toefl' => 'required|numeric|min:0|max:500',
            'toeic' => 'required|numeric|min:0|max:500',
        ]);

        $totalScore = $request->listening + $request->reading;
        $participant = CreateCertificate::findOrFail($uuid);

        // 2. Load template dan font
        $imageManager = new ImageManager(new GdDriver());
        $templatePath = public_path('certificates/certificate_template.png');
        $fontPath = public_path('fonts/OpenSans-Regular.ttf');

        if (!file_exists($templatePath)) {
            return back()->withErrors(['Template tidak ditemukan']);
        }
        if (!file_exists($fontPath)) $fontPath = null;

        $img = $imageManager->read($templatePath);

        // 3. Hitung dan set nomor sertifikat
        if (!$participant->certificate_number) {
            $order = CreateCertificate::where('created_at', '<=', $participant->created_at)->count();
            $formattedOrder = str_pad($order, 3, '0', STR_PAD_LEFT);
            $certificateNumber = "$formattedOrder/Sert/TOEFL/" . str_pad($participant->batch, 2, '0', STR_PAD_LEFT) . "/CEdEC/2025";
            $participant->encrypted_certificate_number = SecurityHelper::encryptAES($certificateNumber);
        }

        // 4. Dekripsi data untuk ditampilkan
        $decryptedData = [
            'name'         => SecurityHelper::decryptAES($participant->encrypted_name),
            'nim'          => SecurityHelper::decryptAES($participant->encrypted_student_id),
            'birth_place'  => SecurityHelper::decryptAES($participant->encrypted_birth_place),
            'birth_date'   => SecurityHelper::decryptAES($participant->encrypted_birth_date),
            'institution'  => SecurityHelper::decryptAES($participant->encrypted_institution),
            'cert_number'  => SecurityHelper::decryptAES($participant->encrypted_certificate_number),
        ];

        // 5. Tulis teks ke gambar
        $img->text(strtoupper($decryptedData['name']), 122, 580, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($decryptedData['birth_place']), 122, 675, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($decryptedData['birth_date'])->format('d/m/Y'), 122, 775, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($decryptedData['nim']), 122, 870, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(strtoupper($decryptedData['institution']), 750, 580, fn($f) => $this->applyFont($f, $fontPath));
        $img->text($decryptedData['cert_number'], 750, 865, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($participant->test_date)->format('d/m/Y'), 750, 675, fn($f) => $this->applyFont($f, $fontPath));
        $img->text(Carbon::parse($participant->validity)->format('d/m/Y'), 750, 770, fn($f) => $this->applyFont($f, $fontPath));

        // 6. Tulis skor ke gambar
        $img->text($request->listening, 255, 1065, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($request->reading, 700, 1065, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($totalScore, 1135, 1065, fn($f) => $this->applyFont($f, $fontPath, 80, 'center'));
        $img->text($request->toefl, 260, 1290, fn($f) => $this->applyFont($f, $fontPath, 60, 'center'));
        $img->text($request->toeic, 550, 1290, fn($f) => $this->applyFont($f, $fontPath, 60, 'center'));

        // 7. Simpan gambar dan QR
        $fileName = 'cert_' . Str::uuid() . '.png';
        $outputPath = public_path('generated_certificates/' . $fileName);
        if (!file_exists(dirname($outputPath))) mkdir(dirname($outputPath), 0777, true);

        $url = URL::signedRoute('certificate.view', ['uuid' => $participant->uuid]);
        $qrPath = public_path('generated_certificates/qr_' . $participant->uuid . '.png');
        file_put_contents($qrPath, $qr->format('png')->size(200)->generate($url));

        $qrImage = $imageManager->read($qrPath);
        $img->place($qrImage, 'bottom-left', 740, 600);
        $img->save($outputPath);

        $pdfPath = public_path('generated_certificates/pdf_' . Str::uuid() . '.pdf');
        $pdf = Pdf::loadView('certificate.pdf_view', [
            'certificate' => $participant,
            'imagePath' => $outputPath
        ])->setPaper('a4');
        $pdf->save($pdfPath);

        $email = SecurityHelper::decryptAES($participant->encrypted_email);
        if ($email) {
            Mail::to($email)->send(new CertificateSent((object)[
                'name' => $decryptedData['name'],
                'batch' => $participant->batch

            ], $pdfPath));
        }


        // 8. Enkripsi data skor
        $encryptedFields = [
            'encrypted_name'               => SecurityHelper::encryptAES($decryptedData['name']),
            'encrypted_student_id'        => SecurityHelper::encryptAES($decryptedData['nim']),
            'encrypted_birth_place'       => SecurityHelper::encryptAES($decryptedData['birth_place']),
            'encrypted_birth_date'        => SecurityHelper::encryptAES($decryptedData['birth_date']),
            'encrypted_institution'       => SecurityHelper::encryptAES($decryptedData['institution']),
            'encrypted_certificate_number'=> SecurityHelper::encryptAES($decryptedData['cert_number']),
            'enc_listening'               => SecurityHelper::encryptAES($request->listening),
            'enc_reading'                 => SecurityHelper::encryptAES($request->reading),
            'enc_score'                   => SecurityHelper::encryptAES($totalScore),
            'enc_toefl'                   => SecurityHelper::encryptAES($request->toefl),
            'enc_toeic'                   => SecurityHelper::encryptAES($request->toeic),
        ];

        // 9. Hash integritas data
        $dataHash = SecurityHelper::createSHA256Hash([
            $encryptedFields['encrypted_name'],
            $encryptedFields['encrypted_student_id'],
            $encryptedFields['encrypted_birth_place'],
            $encryptedFields['encrypted_birth_date'],
            $encryptedFields['encrypted_institution'],
            $encryptedFields['encrypted_certificate_number'],
            $encryptedFields['enc_listening'],
            $encryptedFields['enc_reading'],
            $encryptedFields['enc_score'],
            $encryptedFields['enc_toefl'],
            $encryptedFields['enc_toeic'],
            $request->test_date,
            $request->validity,
        ]);

        // 10. Simpan ke database
        $participant->update(array_merge($encryptedFields, [
            'test_date'   => $request->test_date ?? $participant->test_date,
            'validity'    => $request->validity ?? $participant->validity,
            'file_name'   => $fileName,
            'data_hash'   => $dataHash,
        ]));

        return redirect()->route('certificate.participants')
            ->with('success', 'Sertifikat berhasil digenerate dan telah dikirim ke email peserta.');

        // 11. Tampilkan hasil
        // return view('certificate.display_image', [
        //     'certificateData' => $participant,
        //     'fileName' => $fileName,
        // ]);
    }


        public function view($uuid, Request $request)
    {
         if (!$request->hasValidSignature()) {
            abort(403, 'Link tidak valid atau sudah kedaluwarsa.');
        }

        $certificate = CreateCertificate::where('uuid', $uuid)->firstOrFail();

        // ✅ Rekalkulasi ulang hash dengan urutan dan data terenkripsi yang identik
        $recalculatedHash = SecurityHelper::createSHA256Hash([
            $certificate->encrypted_name,
            $certificate->encrypted_student_id,
            $certificate->encrypted_birth_place,
            $certificate->encrypted_birth_date,
            $certificate->encrypted_institution,
            $certificate->encrypted_certificate_number,
            $certificate->enc_listening,
            $certificate->enc_reading,
            $certificate->enc_score,
            $certificate->enc_toefl,
            $certificate->enc_toeic,
            $certificate->test_date,
            $certificate->validity,
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
            $decryptedData = [
                'name' => SecurityHelper::decryptData($certificate->encrypted_name),
                'nim' => SecurityHelper::decryptData($certificate->encrypted_nim),
                'birth_place' => SecurityHelper::decryptData($certificate->encrypted_birth_place),
                'address' => SecurityHelper::decryptData($certificate->encrypted_address),
                'certificate_number' => SecurityHelper::decryptData($certificate->encrypted_certificate_number),
                'listening_score' => SecurityHelper::decryptData($certificate->encrypted_listening_score),
                'reading_score' => SecurityHelper::decryptData($certificate->encrypted_reading_score),
                'total_score' => SecurityHelper::decryptData($certificate->encrypted_total_score),
                'toefl_score' => SecurityHelper::decryptData($certificate->encrypted_toefl_score),
                'toeic_score' => SecurityHelper::decryptData($certificate->encrypted_toeic_score),
            ];
        // if ($isValid) {
        //     $decryptedName = SecurityHelper::decryptAES($certificate->encrypted_name);
        //     $decryptedCertNumber = SecurityHelper::decryptAES($certificate->encrypted_certificate_number);

            return view('certificate.view', [
                'certificate' => $certificate,
                'isValid' => true,
                'decryptedData' => $decryptedData
                // 'decrypted_name' => $decryptedName,
                // 'decrypted_cert_number' => $decryptedCertNumber,
                // 'valid' => true,
            ]);
        } else {
            return view('certificate.view', [
                'certificate' => $certificate,
                // 'decrypted_name' => null,
                // 'decrypted_cert_number' => null,
                // 'valid' => false,
                'certificate' => $certificate,
                'isValid' => false,
                'decryptedData' => null


            ]);
        }

        // $certificate = CreateCertificate::where('uuid', $uuid)->firstOrFail();

        // $decryptedData = [
        //     'name' => SecurityHelper::decryptAES($certificate->encrypted_name),
        //     'student_id' => SecurityHelper::decryptAES($certificate->encrypted_student_id),
        //     'birth_place' => SecurityHelper::decryptAES($certificate->encrypted_birth_place),
        //     'birth_date' => SecurityHelper::decryptAES($certificate->encrypted_birth_date),
        //     'institution' => SecurityHelper::decryptAES($certificate->encrypted_institution),
        //     'certificate_number' => SecurityHelper::decryptAES($certificate->encrypted_certificate_number),
        //     'listening' => SecurityHelper::decryptAES($certificate->enc_listening),
        //     'reading' => SecurityHelper::decryptAES($certificate->enc_reading),
        //     'score' => SecurityHelper::decryptAES($certificate->enc_score),
        //     'toefl' => SecurityHelper::decryptAES($certificate->enc_toefl),
        //     'toeic' => SecurityHelper::decryptAES($certificate->enc_toeic),
        // ];

        // return view('certificate.view', [
        //     'certificate' => $certificate,
        //     'decrypted' => $decryptedData,
        //     'valid' => $isValid,
        // ]);

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

        // Hapus file sertifikat jika ada
        if (!empty($participant->file_name)) {
            $filePath = public_path('generated_certificates/' . $participant->file_name);
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus file QR code jika ada
        $qrPath = public_path('generated_certificates/qr_' . $participant->id . '.png');
        if (file_exists($qrPath) && is_file($qrPath)) {
            unlink($qrPath);
        }

        // Hapus data di database
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
