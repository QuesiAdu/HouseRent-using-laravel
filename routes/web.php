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

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HouseController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HomeController;
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
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('area', 'AreaController');
        Route::resource('house', 'HouseController');
        Route::get('manage-landlord', [HouseController::class, 'manageLandlord'])->name('manage.landlord');
        Route::delete('manage-landlord/destroy/{id}', [HouseController::class, 'removeLandlord'])->name('remove.landlord');

        Route::get('manage-renter', [HouseController::class,'manageRenter'])->name('manage.renter');
        Route::delete('manage-renter/destroy/{id}', [HouseController::class,'removeRenter'])->name('remove.renter');

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
        Route::resource('area', 'AreaController');
        Route::resource('house', 'HouseController');
        Route::get('house/switch-status/{id}', [HouseController::class,'switch'])->name('house.status');

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

        Route::get('booking/history', [DashboardController::class,'bookingHistory'])->name('booking.history');
        Route::get('pending/booking', [DashboardController::class,'bookingPending'])->name('booking.pending');
        Route::post('pending/booking/cancel/{id}', [DashboardController::class,'cancelBookingRequest'])->name('cancel.booking.request');

        Route::post('review', [DashboardController::class,'review'])->name('review');
        Route::get('review-edit/{id}', [DashboardController::class,'reviewEdit'])->name('review.edit');
        Route::post('review-update/{id}', [DashboardController::class,'reviewUpdate'])->name('review.update');
    });
