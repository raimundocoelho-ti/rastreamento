<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // CRUD de Users
    Volt::route('users', 'user.index-user')->name('users.index');
    Volt::route('/create', 'user.create-user')->name('users.create');
    Volt::route('/{user}/edit', 'user.edit-user')->name('users.edit');

    // CRUD de Departamentos
    Volt::route('/departments', 'department.index-department')->name('departments.index');
    Volt::route('/departments/create', 'department.create-department')->name('departments.create');
    Volt::route('/departments/{department}/edit', 'department.edit-department')->name('departments.edit');

    // CRUD de Tipos de Veículos
    Volt::route('/vehicle-types', 'vehicle-type.index-vehicle-type')->name('vehicle-types.index');
    Volt::route('/vehicle-types/create', 'vehicle-type.create-vehicle-type')->name('vehicle-types.create');
    Volt::route('/vehicle-types/{vehicleType}/edit', 'vehicle-type.edit-vehicle-type')->name('vehicle-types.edit');

    // CRUD de Marcas de Veículos (Brands)
    Volt::route('/brands', 'vehicle-brand.index-vehicle-brand')->name('brands.index');
    Volt::route('/brands/create', 'vehicle-brand.create-vehicle-brand')->name('brands.create');
    Volt::route('/brands/{brand}/edit', 'vehicle-brand.edit-vehicle-brand')->name('brands.edit');

    // CRUD de Modelos de Veículos (Vehicle Models)
    Volt::route('/vehicle-models', 'vehicle-model.index-vehicle-model')->name('vehicle-models.index');
    Volt::route('/vehicle-models/create', 'vehicle-model.create-vehicle-model')->name('vehicle-models.create');
    Volt::route('/vehicle-models/{vehicleModel}/edit', 'vehicle-model.edit-vehicle-model')->name('vehicle-models.edit');

    // CRUD de Veículos
    Volt::route('/vehicles', 'vehicle.index-vehicle')->name('vehicles.index');
    Volt::route('/vehicles/create', 'vehicle.create-vehicle')->name('vehicles.create');
    Volt::route('/vehicles/{vehicle}/edit', 'vehicle.edit-vehicle')->name('vehicles.edit');


    Volt::route('/locations', 'vehicle-location.index-vehicle-locations')->name('locations.index');

// Rota para o rastreamento de um veículo específico (com mapa)
    Volt::route('/track-vehicle/{vehicleId}', 'vehicle-tracking.show-vehicle-tracking')->name('vehicle.track');
    Volt::route('/track-all-vehicles', 'all-vehicles-map.index-all-vehicles-map')->name('vehicles.track.all');
});
