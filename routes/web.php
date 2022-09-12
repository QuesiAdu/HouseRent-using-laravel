<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HouseController as AdminHouseController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Landlord\AreaController as LandlordAreaController;
use App\Http\Controllers\Landlord\BookingController as LandlordBookingController;
use App\Http\Controllers\Landlord\DashboardController as LandlordDashboardController;
use App\Http\Controllers\Landlord\SettingsController as LandlordSettingsController;
use App\Http\Controllers\Renter\DashboardController as RenterDashboardController;
use App\Http\Controllers\Renter\SettingsController as RenterSettingsController;

Route::get('/', [HomeController::class, 'index'])->name('welcome');

Route::get('/descending-order-houses-price', [HomeController::class, 'highToLow'])->name('highToLow');
Route::get('/ascending-order-houses-price', [HomeController::class, 'lowToHigh'])->name('lowToHigh');

Route::get('/search-result', [HomeController::class, 'search'])->name('search');
Route::get('/search-result-by-range', [HomeController::class, 'searchByRange'])->name('searchByRange');

Route::get('/houses/details/{id}', [HomeController::class, 'details'])->name('house.details');
Route::get('/all-available/houses',  [HomeController::class,'allHouses'])->name('house.all');
Route::get('/available-houses/area/{id}', [HomeController::class,'areaWiseShow'])->name('available.area.house');

Route::post('/house-booking/id/{id}', [HomeController::class,'booking'])->name('booking');

Auth::routes(['verify' => true]);

Route::get('/home', [HomeController::class,'index'])->name('home'); 
Route::get('auth/google', [GoogleController::class,'redirectToGoogle']);

Route::get('auth/google/callback', [GoogleController::class,'handleGoogleCallback']);

//admin

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin', 'verified']],
    function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('area', AreaController::class);
        Route::resource('area', AreaController::class);
        Route::resource('house', HouseController::class);
        Route::get('manage-landlord', [AdminHouseController::class, 'manageLandlord'])->name('manage.landlord');
        Route::delete('manage-landlord/destroy/{id}', [AdminHouseController::class, 'removeLandlord'])->name('remove.landlord');

        Route::get('manage-renter', [AdminHouseController::class,'manageRenter'])->name('manage.renter');
        Route::delete('manage-renter/destroy/{id}', [AdminHouseController::class,'removeRenter'])->name('remove.renter');

        Route::get('profile-info', [SettingsController::class,'showProfile'])->name('profile.show');
        Route::get('profile-info/edit/{id}', [SettingsController::class,'editProfile'])->name('profile.edit');
        Route::post('profile-info/update/', [SettingsController::class,'updateProfile'])->name('profile.update');

        Route::get('booked-houses-list', [BookingController::class,'bookedList'])->name('booked.list');
        Route::get('booked-houses-history', [BookingController::class,'historyList'])->name('history.list');

    });

//landlord

Route::group(['as' => 'landlord.', 'prefix' => 'landlord', 'namespace' => 'Landlord', 'middleware' => ['auth', 'landlord', 'verified']],
    function () {
        Route::get('dashboard', [LandlordDashboardController::class,'index'])->name('dashboard');
        Route::resource('area', LandlordAreaController::class);
        Route::resource('house', AdminHouseController::class);
        Route::get('house/switch-status/{id}', [AdminHouseController::class,'switch'])->name('house.status');

        Route::get('booking-request-list', [LandlordBookingController::class,'bookingRequestListForLandlord'])->name('bookingRequestList');
        Route::post('booking-request/accept/{id}', [LandlordBookingController::class,'bookingRequestAccept'])->name('request.accept');
        Route::post('booking-request/reject/{id}', [LandlordBookingController::class,'bookingRequestReject'])->name('request.reject');
        Route::get('booking/history', [LandlordBookingController::class,'bookingHistory'])->name('history');
        Route::get('booked/currently/renter', [LandlordBookingController::class,'currentlyStaying'])->name('currently.staying');
        Route::post('renter/leave/{id}', [LandlordBookingController::class,'leaveRenter'])->name('leave.renter');

        Route::get('profile-info', [LandlordSettingsController::class,'showProfile'])->name('profile.show');
        Route::get('profile-info/edit/{id}', [LandlordSettingsController::class,'editProfile'])->name('profile.edit');
        Route::post('profile-info/update/', [LandlordSettingsController::class,'updateProfile'])->name('profile.update');
    });

//renter

Route::group(['as' => 'renter.', 'prefix' => 'renter', 'namespace' => 'renter', 'middleware' => ['auth', 'renter', 'verified']],
    function () {
        Route::get('dashboard', [RenterDashboardController::class,'index'])->name('dashboard');

        Route::get('areas', [RenterDashboardController::class,'areas'])->name('areas');

        Route::get('houses', [RenterDashboardController::class,'allHouses'])->name('allHouses');
        Route::get('house/details/{id}', [RenterDashboardController::class,'housesDetails'])->name('houses.details');

        Route::get('profile-info', [RenterSettingsController::class,'showProfile'])->name('profile.show');
        Route::get('profile-info/edit/{id}', [RenterSettingsController::class,'editProfile'])->name('profile.edit');
        Route::post('profile-info/update/', [RenterSettingsController::class,'updateProfile'])->name('profile.update');

        Route::get('booking/history', [RenterDashboardController::class,'bookingHistory'])->name('booking.history');
        Route::get('pending/booking', [RenterDashboardController::class,'bookingPending'])->name('booking.pending');
        Route::post('pending/booking/cancel/{id}', [RenterDashboardController::class,'cancelBookingRequest'])->name('cancel.booking.request');

        Route::post('review', [RenterDashboardController::class,'review'])->name('review');
        Route::get('review-edit/{id}', [RenterDashboardController::class,'reviewEdit'])->name('review.edit');
        Route::post('review-update/{id}', [RenterDashboardController::class,'reviewUpdate'])->name('review.update');
    });
