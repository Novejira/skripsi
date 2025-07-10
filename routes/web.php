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
Route::post('/certificate/to-admin', [CertificateController::class, 'storePendaftaran'])->name('certificate.to-admin');
Route::get('/certificate/admin-form/{uuid}', [CertificateController::class, 'showAdminForm'])->name('certificate.admin.form');
Route::post('/certificate/participants/settings', [CertificateController::class, 'storeGlobalSettings'])->name('certificate.participants.settings');
Route::delete('/certificate/participants/{uuid}', [CertificateController::class, 'deleteParticipant'])->name('certificate.delete');
Route::get('/certificate/participants', [CertificateController::class, 'listParticipants'])->name('certificate.participants');
Route::get('/certificate/participants/{uuid}/score', [CertificateController::class, 'showScoreForm'])->name('certificate.score.form');
Route::post('/certificate/participants/{uuid}/score', [CertificateController::class, 'storeScoreAndGenerate'])->name('certificate.score.store');
Route::get('/certificate/view/{uuid}', [CertificateController::class, 'view'])->name('certificate.view');
Route::get('/certificate/download-pdf/{uuid}', [CertificateController::class, 'downloadPdf'])->name('certificate.download.pdf');
Route::get('/admin/qr-scan-logs', [CertificateController::class, 'scanLogs'])->name('admin.qr_scan_logs');
Route::get('/whatsapp-info', function () {
    return view('whatsapp-info');
})->name('whatsapp.info');
