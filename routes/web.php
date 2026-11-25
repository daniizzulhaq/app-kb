<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController,DashboardController,ParticipantController,ServiceController,AppointmentController,MasterController,TargetController,UserController,ReportController};
use App\Models\AuditLog;

Route::middleware('guest')->group(function(){ Route::get('/login',[AuthController::class,'login'])->name('login'); Route::post('/login',[AuthController::class,'authenticate'])->name('login.submit')->middleware('throttle:20,1'); });
Route::middleware(['auth','role:admin,petugas,pimpinan'])->group(function(){
    Route::get('/',DashboardController::class)->name('dashboard');
    Route::post('/logout',[AuthController::class,'logout'])->name('logout');
    Route::get('/laporan',[ReportController::class,'index'])->name('reports.index');
    Route::get('/laporan/export/{format}',[ReportController::class,'export'])->name('reports.export');
    Route::middleware('role:admin,petugas')->group(function(){
        Route::get('/cari-peserta',[ServiceController::class,'lookup'])->name('participants.lookup');
        Route::resource('peserta',ParticipantController::class)->parameters(['peserta'=>'participant'])->names('participants')->except('destroy');
        Route::resource('pelayanan',ServiceController::class)->parameters(['pelayanan'=>'service'])->names('services')->except(['show','destroy']);
        Route::get('/jadwal-kontrol',[AppointmentController::class,'index'])->name('appointments.index');
        Route::patch('/jadwal-kontrol/{service}',[AppointmentController::class,'update'])->name('appointments.update');
    });
    Route::middleware('role:admin')->group(function(){
        Route::delete('/peserta/{participant}',[ParticipantController::class,'destroy'])->name('participants.destroy');
        Route::delete('/pelayanan/{service}',[ServiceController::class,'destroy'])->name('services.destroy');
        Route::get('/master/{kind}',[MasterController::class,'index'])->name('master.index');
        Route::get('/master/{kind}/create',[MasterController::class,'create'])->name('master.create');
        Route::post('/master/{kind}',[MasterController::class,'store'])->name('master.store');
        Route::get('/master/{kind}/{id}/edit',[MasterController::class,'edit'])->name('master.edit');
        Route::put('/master/{kind}/{id}',[MasterController::class,'update'])->name('master.update');
        Route::delete('/master/{kind}/{id}',[MasterController::class,'destroy'])->name('master.destroy');
        Route::resource('pengguna',UserController::class)->parameters(['pengguna'=>'user'])->names('users')->except('show');
        Route::get('/target',[TargetController::class,'index'])->name('targets.index');
        Route::post('/target',[TargetController::class,'store'])->name('targets.store');
        Route::get('/audit-log',function(){return view('audit',['logs'=>AuditLog::with('user')->latest('id')->paginate(20)]);})->name('audit.index');
    });
});
