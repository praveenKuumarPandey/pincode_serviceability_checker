<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\pincodeChecker;

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

Route::get('/', [pincodeChecker::class, 'index']);


Route::get('/pincode_code_checker', [pincodeChecker::class, 'index'])->name('pincode.view');
Route::get('/internalGetDeliveryTatNStatus', [pincodeChecker::class, 'internalGetTatNDeliveryStatus'])->name('pincode.statusInternal');
Route::get('/getDeliveryTatNStatus', [pincodeChecker::class, 'getTatNDeliveryStatus'])->name('pincode.status');
Route::get('/getDeliveryTatNStatusProxyV', [pincodeChecker::class, 'getTatNDeliveryStatus'])->middleware(['auth.proxy'])->name('pincode.statusProxyV');
Route::get('/tatCheckByPincodeApi', [pincodeChecker::class, 'soapClientTest'])->name('pincode.getTat');
// Route::get('/checkApiBoth/{pincode}', [pincodeChecker::class, 'getTatNDeliveryStatus'])->name('pincode.checkSoap');
Route::get('/soapApiCheck/', [pincodeChecker::class, 'getTatNDeliveryStatus'])->name('pincode.checkSoap');
// Route::get('/soapApiCheck/{pincode}', [pincodeChecker::class, 'getExpectedDateofDelivery_BlueDart'])->name('pincode.checkSoap');
Route::get('/soapApiCheckall', [pincodeChecker::class, 'getAllDelhiveryPincode'])->name('pincode.delhiveryAllPincode');
Route::get('/getOrderTrackingApi', [pincodeChecker::class, 'getOrderTrackingDetails'])->name('pincode.getOrderTrackingApi');
