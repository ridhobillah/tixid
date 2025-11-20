<?php


use App\Http\Controllers\UserController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

Route::get('/', [MovieController::class, 'home'])->name('home');

Route::get('/home/movies', [MovieController::class,'homeAllMovies'])->name('home.movies');

Route::get('/schedules/{movie_id}', [MovieController::class,'movieSchedules'])->name('schedules.detail');


Route::middleware('isUser')->group(function(){

    Route::get('/schedules/{scheduleId}/hours/{hourId}/show-seats', [TicketController::class, 'showSeats'])->name('schedules.seats');
    Route::prefix('/tickets')->name('tickets.')->group(function() {
        Route::get('/', [TicketController::class, 'index'])->name('index');


        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticketId}/order', [TicketController::class, 'ticketOrder'])->name('order');
        Route::post('/payment', [TicketController::class, 'ticketPayment'])->name('payment');
        Route::get('/{ticketId}/payment', [TicketController::class, 'ticketPaymentPage'])->name('payment.page');
        Route::Patch('{ticketId}/payment/proof', [TicketController::class, 'paymentProof'])->name('payment.proof');
        Route::get('/{ticketId}/receipt', [TicketController::class, 'ticketReceipt'])->name('receipt');
        Route::get('/{ticketId}/pdf', [TicketController::class, 'exportPdf'])->name('export_pdf'); //route export pdf
    });
});

Route::get('/cinemas/list', [CinemaController::class, 'cinemaList'])->name('cinemas.list');
Route::get('/cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');

Route::middleware('isGuest')->group(function() {

    Route::get('/login', function () {
        return view('login');
    })->name('login');
    Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');

    Route::get('/sign-up', function () {
        return view('signup');
    })->name('sign_up');
    Route::post('/sign-up', [UserController::class, 'signUp'])->name('sign_up.add');

});




Route::get('/logout', [UserController::class, 'logout'])
->name('logout');



// middleware : authorization, pengaturan hak akses pengguna
// prefix awalan path
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function() {
    Route::get('/tickets/chart', [TicketController::class, 'chartData'])->name('tickets.chart');
    Route::get('/dashboard', function() {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::prefix('/cinemas')->name('cinemas.')->group(function() {
        Route::get('/', [Cinemacontroller::class,'index'])->name('index');
        Route::get('create', function() {
            return view('admin.cinema.create');
        })->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('/export', [CinemaController::class, 'export'])->name('export');
        Route::get('/datatables', [CinemaController::class,'datatables'])->name('datatables');




        Route::get('/trash', [CinemaController::class,'trash'])->name('trash');
        Route::patch('/restore/{id}', [CinemaController::class,'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
    });
    //pengguna
    Route::prefix('/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', function () {
            return view('admin.users.create');
        })->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [UserController::class, 'destroy'])->name('delete');
         Route::get('/export', [UserController::class, 'export'])->name('export');

        Route::get('/datatables', [UserController::class,'datatables'])->name('datatables');


        Route::get('/trash', [UserController::class,'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class,'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');

    });

    Route::prefix('/movies')->name('movies.')->group(function() {
         Route::get('/chart', [MovieController::class, 'chartData'])->name('chart');
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::put('/nonaktif/{id}', [MovieController::class, 'nonAktif'])->name('nonaktif');
        Route::get('/export', [MovieController::class, 'export'])->name('export');


        Route::get('/datatables', [MovieController::class,'datatables'])->name('datatables');


        Route::get('/trash', [MovieController::class,'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class,'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');


    });
});

//petugas


Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function () {

    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    //promo
    Route::prefix('/promos')->name('promos.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::put('/nonactived/{id}', [PromoController::class, 'nonactived'])->name('nonactived');
        Route::delete('/delete/{id}', [PromoController::class,'destroy'])->name('delete');
        Route::get('/export', [PromoController::class, 'export'])->name('export');

        Route::get('/datatables', [PromoController::class,'datatables'])->name('datatables');


        Route::get('/trash', [PromoController::class,'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class,'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
    });

    Route::prefix('/schedules')->name('schedules.')->group(function (){
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');


         Route::get('/datatables', [ScheduleController::class,'datatables'])->name('datatables');


        Route::get('/trash', [ScheduleController::class,'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');

    });
});











