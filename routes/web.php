 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [CertificateController::class, 'showForm'])->name('certificate.form');
Route::post('/certificate/generate', [CertificateController::class, 'generate'])->name('certificate.generate');
Route::get('/certificate/view/{id}', [CertificateController::class, 'view'])->name('certificate.view');
Route::get('/certificate/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.download.pdf');


Route::post('/certificate/to-admin', function(Request $request) {
    // Validasi form awal + bukti pembayaran
    $request->validate([
        'participant_name' => 'required|string|min:3|max:255',
        'student_id' => 'required|numeric',
        'birth_place' => 'required|string',
        'birth_date' => 'required|date',
        'institution' => 'required|string',
        'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Simpan gambar ke storage
    $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

    // Simpan semua ke session (termasuk path bukti pembayaran)
    $request->session()->put('form_data', [
        'participant_name' => $request->input('participant_name'),
        'birth_place' => $request->input('birth_place'),
        'birth_date' => $request->input('birth_date'),
        'student_id' => $request->input('student_id'),
        'institution' => $request->input('institution'),
        'payment_proof' => $paymentProofPath, // <== ini penting
    ]);

    return redirect()->route('certificate.admin.form');
})->name('certificate.to-admin');


Route::get('/certificate/admin-form', [CertificateController::class, 'showAdminForm'])->name('certificate.admin.form');
//Route::post('/certificate/final-generate', [CertificateController::class, 'finalGenerate'])->name('certificate.final.generate');

Route::post('/certificate/admin-to-list', [CertificateController::class, 'storeAdminAndRedirect'])->name('certificate.admin.to-participant-list');

Route::delete('/certificate/participants/{id}', [CertificateController::class, 'deleteParticipant'])->name('certificate.delete');

Route::get('/certificate/participants', [CertificateController::class, 'listParticipants'])->name('certificate.participants');
Route::get('/certificate/participants/{id}/score', [CertificateController::class, 'showScoreForm'])->name('certificate.score.form');

Route::post('/certificate/participants/{id}/score', [CertificateController::class, 'storeScoreAndGenerate'])->name('certificate.score.store');

Route::get('/admin/qr-scan-logs', [CertificateController::class, 'scanLogs'])->name('admin.qr_scan_logs');
