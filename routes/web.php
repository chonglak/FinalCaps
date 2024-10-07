<?php
use App\Http\Livewire\Users;
use App\Http\Livewire\Chat\Index;
use App\Http\Livewire\Chat\Chat;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TherapistController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/select-register', [AdminController::class, 'selectRegister'])->name('view.select-register');

Route::get('/register/patient', [PatientController::class, 'showRegistrationForm'])->name('patient.register');
Route::get('/register/therapist', [TherapistController::class, 'showRegistrationForm'])->name('therapist.register');

Route::post('/register/patient', [RegisteredUserController::class, 'storePatient'])->name('patient.store');
Route::post('/register/therapist', [RegisteredUserController::class, 'storeTherapist'])->name('therapist.store');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'therapist') {
        return redirect()->route('therapist.dashboard');
    } elseif ($user->role === 'patient') {
        return redirect()->route('patients.dashboard');
    } elseif ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
     else {
        abort(403, 'Unauthorized');
    }
})->middleware(['auth', 'verified'])->name('dashboard');
// Admin dashboard route
Route::middleware(['auth', 'role:admin'])->get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

// Therapist dashboard route
Route::middleware(['auth', 'role:therapist'])->get('/therapist/dashboard', [TherapistController::class, 'index'])->name('therapist.dashboard');

Route::get('/patient/subscriptions', [SubscriptionController::class, 'subPlan'])->name('subscriptions.plan'); // View subscriptions
Route::get('/patient/my-subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index'); // View subscriptions
Route::get('/patient/subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');  // Form to subscribe
Route::post('/patient/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');     // Store subscription
Route::get('/patient/subscriptions/{id}/edit', [SubscriptionController::class, 'edit']);  // Edit subscription
Route::put('/patient/subscriptions/{id}', [SubscriptionController::class, 'update']);    // Update subscription
Route::delete('/patient/subscriptions/{id}', [SubscriptionController::class, 'destroy']); // Cancel subscription
Route::get('/patient/subscriptions/payment', [SubscriptionController::class, 'payment'])->name('subscriptions.payment');
Route::post('/patient/subscriptions/payments/store', [PaymentController::class, 'store'])->name('payments.store');


// Patient dashboard route
Route::middleware(['auth', 'role:patient'])->get('/patient/dashboard', [PatientController::class, 'index'])->name('patients.dashboard');
// Patient view appointment
Route::middleware(['auth', 'role:patient'])->get('/patient/appointment', [PatientController::class, 'viewApp'])->name('patients.appointment');
// Patient cancel appointment
Route::middleware(['auth', 'role:patient'])->post('/patient/appointment{appointmentID}', [AppointmentController::class, 'cancelApp'])->name('patients.cancelApp');
// Patient bookappointment route
Route::middleware(['auth', 'role:patient'])->get('/patient/bookappointment', [PatientController::class, 'appIndex'])->name('patients.bookappointments');
// Patient appointment details
Route::middleware(['auth', 'role:patient'])->get('/patient/bookappointment/{id}', [PatientController::class, 'appDetails'])->name('patients.therapist-details');
// Patient store appointment
Route::post('patients/bookappointment/store', [AppointmentController::class, 'store'])->name('appointments.store');

// Therapist appointment
Route::middleware(['auth', 'role:therapist'])->get('/therapist/appointment', [TherapistController::class, 'appIndex'])->name('therapist.appointment');


Route::post('/login', [LoginController::class, 'login'])->name('login');
    
Route::middleware('auth')->group(function (){
        Route::get('/chat',Index::class)->name('chat.index');
        Route::get('/chat/{query}',Chat::class)->name('chat');
        Route::get('/users',Users::class)->name('users');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Additional routes
require __DIR__.'/auth.php';
