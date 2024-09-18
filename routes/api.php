<?php

use App\Http\Controllers\StressPoleController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\WindyAreaController;
use App\Http\Controllers\Pole\PoleController;
use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\Device\VendorController;
use App\Http\Controllers\Address\AddressController;
use App\Http\Controllers\Address\CommuneController;
use App\Http\Controllers\Address\CountryController;
use App\Http\Controllers\Address\DistrictController;
use App\Http\Controllers\Address\ProvinceController;
use App\Http\Controllers\Pole\PoleCategoryController;
use App\Http\Controllers\Device\DeviceCategoryController;

// Route Authentication vs Author
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
});

/*Route::group(['middleware' => 'api'], function () {*/
    // Station Manager
    Route::group(['prefix' => 'stations'], function (){
        Route::get('/', [StationController::class, 'index'])->name('station.index');
        Route::get('/codes', [StationController::class, 'listCode'])->name('station.list.code');
        Route::post('/', [StationController::class, 'store'])->name('station.store');
        Route::get('/{id}', [StationController::class, 'detail'])->name('station.detail');
        Route::patch('/{id}', [StationController::class, 'update'])->name('station.update');
        Route::delete('/{id}', [StationController::class, 'destroy'])->name('station.destroy');
        Route::post('/{id}/poles', [StationController::class, 'addPole'])->name('station.pole.addPole');
        Route::delete('/{id}/poles/{pole_id}', [StationController::class, 'removePole'])->name('station.pole.removePole');
        //Route::post('/excel/import', [PoleController::class, 'importExcel'])->name('device.category.import-excel');
        //Route::get('/excel/export', [PoleController::class, 'exportExcel'])->name('device.category.export-excel');
    });

    // Location Manager
    Route::group(['prefix' => 'address'], function (){
        // Chung
        Route::post('/excel/import', [AddressController::class, 'importExcel'])->name('address.excel.import');
        Route::get('/excel/export', [AddressController::class, 'exportExcel'])->name('address.excel.export');

        // Quốc gia
        Route::group(['prefix' => 'countries'], function (){
            Route::get('/', [CountryController::class, 'index'])->name('address.country.index');
            Route::post('/', [CountryController::class, 'store'])->name('address.country.store');
            Route::patch('/{id}', [CountryController::class, 'update'])->name('address.country.update');
            Route::delete('/{id}', [CountryController::class, 'destroy'])->name('address.country.destroy');
        });

        // Tỉnh thành phố
        Route::group(['prefix' => 'provinces'], function (){
            Route::get('/', [ProvinceController::class, 'index'])->name('address.province.index');
            Route::post('/', [ProvinceController::class, 'store'])->name('address.province.store');
            Route::patch('/{id}', [ProvinceController::class, 'update'])->name('address.province.update');
            Route::delete('/{id}', [ProvinceController::class, 'destroy'])->name('address.province.destroy');
        });
        // Quận huyện
        Route::group(['prefix' => 'districts'], function (){
            Route::get('/', [DistrictController::class, 'index'])->name('address.district.index');
            Route::post('/', [DistrictController::class, 'store'])->name('address.district.store');
            Route::patch('/{id}', [DistrictController::class, 'update'])->name('address.district.update');
            Route::delete('/{id}', [DistrictController::class, 'destroy'])->name('address.district.destroy');
        });
        // Phường xã
        Route::group(['prefix' => 'communes'], function (){
            Route::get('/', [CommuneController::class, 'index'])->name('address.commune.index');
            Route::post('/', [CommuneController::class, 'store'])->name('address.commune.store');
            Route::patch('/{id}', [CommuneController::class, 'update'])->name('address.commune.update');
            Route::delete('/{id}', [CommuneController::class, 'destroy'])->name('address.commune.destroy');
        });

    })->name('address.manager');

    // Windy Area Manager
    Route::group(['prefix' => 'windy-areas'], function (){
        Route::get('/', [WindyAreaController::class, 'index'])->name('windy-area.index');
        Route::post('/', [WindyAreaController::class, 'store'])->name('windy-area.store');
        Route::patch('/{id}', [WindyAreaController::class, 'update'])->name('windy-area.update');
        Route::delete('/{id}', [WindyAreaController::class, 'destroy'])->name('windy-area.destroy');
        Route::post('/excel/import', [WindyAreaController::class, 'importExcel'])->name('windy-area.import-excel');
        Route::get('/excel/export', [WindyAreaController::class, 'exportExcel'])->name('windy-area.export-excel');
    });

    // Vendor Manager
    Route::group(['prefix' => 'vendors'], function (){
        Route::get('/', [VendorController::class, 'index'])->name('device.vendor.index');
        Route::post('/', [VendorController::class, 'store'])->name('device.vendor.store');
        Route::patch('/{id}', [VendorController::class, 'update'])->name('device.vendor.update');
        Route::delete('/{id}', [VendorController::class, 'destroy'])->name('device.vendor.destroy');
        Route::post('/excel/import', [VendorController::class, 'importExcel'])->name('device.vendor.import-excel');
        Route::get('/excel/export', [VendorController::class, 'exportExcel'])->name('device.vendor.export-excel');
    });

    // Device Category Manager
    Route::group(['prefix' => 'device-categories'], function (){
        Route::get('/', [DeviceCategoryController::class, 'index'])->name('device.category.index');
        Route::post('/', [DeviceCategoryController::class, 'store'])->name('device.category.store');
        Route::patch('/{id}', [DeviceCategoryController::class, 'update'])->name('device.category.update');
        Route::delete('/{id}', [DeviceCategoryController::class, 'destroy'])->name('device.category.destroy');
        //Route::post('/excel/import', [DeviceCategoryController::class, 'importExcel'])->name('device.category.import-excel');
        //Route::get('/excel/export', [DeviceCategoryController::class, 'exportExcel'])->name('device.category.export-excel');
    });


    // Device Manager
    Route::group(['prefix' => 'devices'], function (){
        Route::get('/', [DeviceController::class, 'index'])->name('device.index');
        Route::get('/codes', [DeviceController::class, 'listCode'])->name('device.list.code');
        Route::post('/', [DeviceController::class, 'store'])->name('device.store');
        Route::patch('/{id}', [DeviceController::class, 'update'])->name('device.update');
        Route::delete('/{id}', [DeviceController::class, 'destroy'])->name('device.destroy');
        Route::post('/excel/import', [DeviceController::class, 'importExcel'])->name('device.import-excel');
        Route::get('/excel/export', [DeviceController::class, 'exportExcel'])->name('device.export-excel');
    });


    // Pole Category Manager
    Route::group(['prefix' => 'pole-categories'], function (){
        Route::get('/', [PoleCategoryController::class, 'index'])->name('pole.category.index');
        Route::post('/', [PoleCategoryController::class, 'store'])->name('pole.category.store');
        Route::patch('/{id}', [PoleCategoryController::class, 'update'])->name('pole.category.update');
        Route::delete('/{id}', [PoleCategoryController::class, 'destroy'])->name('pole.category.destroy');
        //Route::post('/excel/import', [PoleCategoryController::class, 'importExcel'])->name('device.category.import-excel');
        //Route::get('/excel/export', [PoleCategoryController::class, 'exportExcel'])->name('device.category.export-excel');
    });

    // Pole Manager
    Route::group(['prefix' => 'poles'], function (){
        Route::get('/', [PoleController::class, 'index'])->name('pole.index');
        Route::post('/', [PoleController::class, 'store'])->name('pole.store');
        Route::patch('/{id}', [PoleController::class, 'update'])->name('pole.update');
        Route::delete('/{id}', [PoleController::class, 'destroy'])->name('pole.destroy');

        Route::post('/add-device', [PoleController::class, 'addDevice'])->name('pole.device.add');
        Route::patch('/{id}/edit-device', [PoleController::class, 'updateDevice'])->name('pole.device.update');
        Route::delete('/{id}/delete-device', [PoleController::class, 'removeDevice'])->name('pole.device.remove');



    //Route::post('/excel/import', [PoleController::class, 'importExcel'])->name('device.category.import-excel');
    //Route::get('/excel/export', [PoleController::class, 'exportExcel'])->name('device.category.export-excel');

});
Route::get('/test-command', [\App\Http\Controllers\CommandController::class, 'index']);

// Upload Manager
Route::group(['prefix' => 'upload'], function (){
    Route::post('/{type}', [UploadController::class, 'uploadFile'])->name('upload.file');
});

/*});*/


// Calculate Pole street
Route::get('/calculate-pole-stress', [StressPoleController::class, 'poleStress']);


Route::get('/', function(){
    return response()->json(['message' => 'Hello World!']);
});

