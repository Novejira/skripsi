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
    // Validasi form awal
    $request->validate([
        'participant_name' => 'required|string|min:3|max:255',
        'student_id' => 'required|numeric',
    ]);

    $request->session()->put('form_data', $request->only([
        'participant_name', 'birth_place', 'birth_date', 'student_id', 'institution'
    ]));

    return redirect()->route('certificate.admin.form');
})->name('certificate.to-admin');

Route::get('/certificate/admin-form', [CertificateController::class, 'showAdminForm'])->name('certificate.admin.form');
Route::post('/certificate/final-generate', [CertificateController::class, 'finalGenerate'])->name('certificate.final.generate');
