 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;

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
