<?php

use Illuminate\Support\Facades\Route;
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
Route::get("/daily","App\Http\Controllers\UIController@daily")->name("ui.daily");
Route::get("/weekly","App\Http\Controllers\UIController@weekly")->name("ui.weekly");
Route::get("/monthly","App\Http\Controllers\UIController@monthly")->name("ui.monthly");
Route::get("/percentages","App\Http\Controllers\UIController@percentages")->name("ui.percentages");