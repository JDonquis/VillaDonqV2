<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\MainConfigController;
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
    Route::put('/dashboard/matricula/{id}', [StudentController::class, 'update']);    
    Route::delete('/dashboard/matricula/{studentId}', [StudentController::class, 'destroy']);    

    Route::get('/dashboard/matricula/search-representative/{ci}', [StudentController::class, 'searchRepresentativeByCI']);
    Route::get('/dashboard/matricula/search-second_representative/{ci}', [StudentController::class, 'searchSecondRepresentativeByCI']);
    
    Route::post('/dashboard/secciones', [SectionController::class, 'store']);    
    Route::delete('/dashboard/secciones/{course_id}/{section_id}', [SectionController::class, 'destroy']);    
    
    Route::get('/dashboard/pagos', [PaymentController::class, 'index']);
    Route::get('/dashboard/pagos/search-representative/{search}', [StudentController::class, 'searchRepresentative']);
    Route::get('/dashboard/registrar-pago', [PaymentController::class, 'showCreatePayment']);



    Route::get('/dashboard/configuracion', [MainConfigController::class, 'index']);
    Route::get('/dashboard/configuracion/editar-cuenta/{id}', [MainConfigController::class, 'showEditAccount']);
    Route::get('/dashboard/configuracion/crear-cuenta/{methodID}', [MainConfigController::class, 'showCreateAccount']);
    Route::post('/dashboard/configuracion/crear-cuenta', [MainConfigController::class, 'createAccount']);
    Route::put('/dashboard/configuracion/editar-cuenta/{id}', [MainConfigController::class, 'editAccount']);
    Route::delete('/dashboard/configuracion/eliminar-cuenta/{id}', [MainConfigController::class, 'deleteAccount']);

    Route::put('/dashboard/configuracion/pagos', [MainConfigController::class, 'updatePaymentConfig']);

    
});

