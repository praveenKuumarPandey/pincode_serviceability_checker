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

Route::get('/', [pincodeChecker::class, 'home']);


Route::get('/pincode_code_checker', [pincodeChecker::class, 'index'])->name('pincode.view');


Route::get('/pincode_serviceability_checker', [pincodeChecker::class, 'home'])->name('pincode.default_view');


Route::get('/upload_serviceability_data', [pincodeChecker::class, 'uploadServiceabilityDataView'])->name('pincode.uploadServiceabilityData');
Route::get('/pincodeServiceabilityUploadSampleDownload', [pincodeChecker::class, 'downloadPincodeServiceabilityDataUploadTemplate'])->name('pincode.serviceabilityUploadSampleDownload');
Route::post('/uploadPincodeServiceabilityDetailsSheet', [pincodeChecker::class, 'uploadPincodeServiceabilityDetailsSheet'])->name('pincode.uploadPincodeServiceabilityDetailsSheet');



//required
Route::get('/getDeliveryTatNStatus', [pincodeChecker::class, 'getTatNDeliveryStatus'])->name('pincode.status');

//remove - not used
Route::get('/getDeliveryTatNStatusProxyV', [pincodeChecker::class, 'getTatNDeliveryStatus'])->middleware(['auth.proxy'])->name('pincode.statusProxyV');





//remove - not used in web 
Route::get('/soapApiCheck/{pincode}', [pincodeChecker::class, 'getExpectedDateofDelivery_BlueDart'])->name('pincode.checkSoap1');

//remove - not used in web
Route::get('/delhiveryApiTest/{pincode}', [pincodeChecker::class, 'getExpectedDateofDelivery_Delhivery'])->name('pincode.checkDelhivery');

//remove - not used
Route::get('/soapApiCheckall', [pincodeChecker::class, 'getAllDelhiveryPincode'])->name('pincode.delhiveryAllPincode');

//remove - not used
Route::get('/getOrderTrackingApi', [pincodeChecker::class, 'getOrderTrackingDetails'])->name('pincode.getOrderTrackingApi');
