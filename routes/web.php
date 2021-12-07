<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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

Route::name('home')->get('/', [
    HomeController::class,
    'index'
]);

Route::name('dashboard')->get('/dashboard', [
    DashboardController::class,
    'index'
]);

// Registration or Setup Routes
Route::name('auth.azure')->get('/auth/azure/redirect', [
    App\Http\Controllers\Auth\RegisterController::class,
    'azureOauthRedirect'
]);
Route::get('/auth/azure/callback', [
    App\Http\Controllers\Auth\RegisterController::class,
    'azureOauthCallback'
]);
Route::name('setup')->get('/setup', [
    App\Http\Controllers\Auth\RegisterController::class,
    'showRegistrationForm'
]);
Route::name('login')->get('/login', [
    App\Http\Controllers\Auth\LoginController::class,
    'showLoginForm'
]);

// Logout Routes
Route::name('logout')->post('/logout', [
    App\Http\Controllers\Auth\LoginController::class,
    'logout'
]);
Route::name('reset')->post('/reset', [
    App\Http\Controllers\Auth\LoginController::class,
    'logout'
]);

// Dispatch Jobs Synchronously
Route::get('/refreshAzureToken', [
    App\Http\Controllers\JobsController::class,
    'refreshAzureToken'
]);
Route::get('/retrieveDevices', [
    App\Http\Controllers\JobsController::class,
    'retrieveDevices'
]);
Route::get('/retrieveMeetings', [
    App\Http\Controllers\JobsController::class,
    'retrieveMeetings'
]);
Route::get('/performBookingsPut', [
    App\Http\Controllers\JobsController::class,
    'performBookingsPut'
]);

// Get/Post JSON
Route::name('devices')->get('/devices', [
    DashboardController::class,
    'getDevices'
]);
Route::name('unmappedDevices')->get('/unmappedDevices', [
    DashboardController::class,
    'getUnmappedDevices'
]);
Route::name('addMapping')->post('/addMapping', [
    DashboardController::class,
    'postAddMapping'
]);
Route::name('removeMapping')->post('/removeMapping', [
    DashboardController::class,
    'postRemoveMapping'
]);
