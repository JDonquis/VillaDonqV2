<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\BitacoraController;
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

Route::get('/', [AppController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth')->name('logout');         

Route::middleware(['auth'])->group(function () 
{
    Route::get('/dashboard', [AppController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/dashboard/matricula', [StudentController::class, 'index']);    
    Route::post('/dashboard/matricula', [StudentController::class, 'store']);    

    Route::post('/dashboard/secciones', [SectionController::class, 'store']);    
    Route::delete('/dashboard/secciones/{course_id}/{section_id}', [SectionController::class, 'destroy']);    

    Route::get('/dashboard/pagos', [PaymentController::class, 'index']);    
    
    
});

