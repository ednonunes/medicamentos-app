<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota dedicada para a linha do tempo do dia
    Route::get('/medications/agenda', [App\Http\Controllers\MedicationController::class, 'agenda'])->name('medications.agenda');

    // Salvar a medicação tomada:
    Route::post('/medications/take', [MedicationController::class, 'takeDose'])->name('medications.take');

    // Desfazer a medicação tomada:
    Route::post('/medications/undo', [MedicationController::class, 'undo'])->name('medications.undo');

    // Esta linha cria automaticamente as rotas: index, create, store, edit, update, destroy
    Route::resource('medications', App\Http\Controllers\MedicationController::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/usuarios', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
});

require __DIR__.'/auth.php';
