<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home'); // Thay 'welcome' bằng tên view của bạn
})->name('home');



Route::group(['middleware' => 'web'], function () {
    Route::get('bar', function () {
        return csrf_token(); // works
    });
    // Trang chủ
    

    Route::get('/login', [UserController::class, 'login'])->name('login');
    Route::post('/login', [UserController::class, 'handleLogin'])->name('login.post');
});

// Đăng xuất
Route::get('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth');


// ADMIN KHOA
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth');

Route::get('/admin/department/index', [AdminController::class, 'department_index'])->name('admin.department.index')->middleware('auth');

Route::get('/admin/department/create', [AdminController::class, 'department_create'])->name('admin.department.create')->middleware('auth');
Route::post('/admin/department/create', [AdminController::class, 'department_createpost'])->name('admin.department.create.post')->middleware('auth');


Route::get('/admin/department/{id}/edit', [AdminController::class, 'department_edit'])->name('admin.department.edit')->middleware('auth');
Route::put('/admin/department/update/{id}', [AdminController::class, 'department_update'])->name('admin.department.update')->middleware('auth');


Route::get('/admin/department/{id}/delete/', [AdminController::class, 'department_delete'])->name('admin.department.delete')->middleware('auth');
Route::delete('/admin/department/destroy/{id}', [AdminController::class, 'department_destroy'])->name('admin.department.destroy');


// ADMIN GIANG VIEN
Route::get('/admin/lecturer/index', [AdminController::class, 'lecturer_index'])->name('admin.lecturer.index')->middleware('auth');

Route::get('/admin/lecturer/create', [AdminController::class, 'lecturer_create'])->name('admin.lecturer.create')->middleware('auth');
Route::post('/admin/lecturer/create', [AdminController::class, 'lecturer_createpost'])->name('admin.lecturer.create.post')->middleware('auth');


Route::get('/admin/lecturer/{id}/edit', [AdminController::class, 'lecturer_edit'])->name('admin.lecturer.edit')->middleware('auth');
Route::put('/admin/lecturer/update/{id}', [AdminController::class, 'lecturer_update'])->name('admin.lecturer.update')->middleware('auth');


Route::get('/admin/lecturer/{id}/delete/', [AdminController::class, 'lecturer_delete'])->name('admin.lecturer.delete')->middleware('auth');
Route::delete('/admin/lecturer/destroy/{id}', [AdminController::class, 'lecturer_destroy'])->name('admin.lecturer.destroy');


Route::get('/admin/lecturer/export/', [AdminController::class, 'downloadListLecturer'])->name('admin.lecturer.export')->middleware('auth');



// ADMIN MAJOR
Route::get('/admin/major/index', [AdminController::class, 'major_index'])->name('admin.major.index')->middleware('auth');

Route::get('/admin/major/create', [AdminController::class, 'major_create'])->name('admin.major.create')->middleware('auth');
Route::post('/admin/major/create', [AdminController::class, 'major_createpost'])->name('admin.major.create.post')->middleware('auth');


Route::get('/admin/major/{id}/edit', [AdminController::class, 'major_edit'])->name('admin.major.edit')->middleware('auth');
Route::put('/admin/major/update/{id}', [AdminController::class, 'major_update'])->name('admin.major.update')->middleware('auth');


Route::get('/admin/major/{id}/delete/', [AdminController::class, 'major_delete'])->name('admin.major.delete')->middleware('auth');
Route::delete('/admin/major/destroy/{id}', [AdminController::class, 'major_destroy'])->name('admin.major.destroy');


// ADMIN SUBJECT
Route::get('/admin/subject/index', [AdminController::class, 'subject_index'])->name('admin.subject.index')->middleware('auth');

Route::get('/admin/subject/create', [AdminController::class, 'subject_create'])->name('admin.subject.create')->middleware('auth');
Route::post('/admin/subject/create', [AdminController::class, 'subject_createpost'])->name('admin.subject.create.post')->middleware('auth');


Route::get('/admin/subject/{id}/edit', [AdminController::class, 'subject_edit'])->name('admin.subject.edit')->middleware('auth');
Route::put('/admin/subject/update/{id}', [AdminController::class, 'subject_update'])->name('admin.subject.update')->middleware('auth');


Route::get('/admin/subject/{id}/delete/', [AdminController::class, 'subject_delete'])->name('admin.subject.delete')->middleware('auth');
Route::delete('/admin/subject/destroy/{id}', [AdminController::class, 'subject_destroy'])->name('admin.subject.destroy');


//ADMIN CURRICULUM
Route::get('/admin/curriculum/index', [AdminController::class, 'curriculum_index'])->name('admin.curriculum.index')->middleware('auth');

Route::get('/admin/curriculum/create', [AdminController::class, 'curriculum_create'])->name('admin.curriculum.create')->middleware('auth');
Route::post('/admin/curriculum/create', [AdminController::class, 'curriculum_createpost'])->name('admin.curriculum.create.post')->middleware('auth');


Route::get('/admin/curriculum/{id}/edit', [AdminController::class, 'curriculum_edit'])->name('admin.curriculum.edit')->middleware('auth');
Route::put('/admin/curriculum/update/{id}', [AdminController::class, 'curriculum_update'])->name('admin.curriculum.update')->middleware('auth');


Route::get('/admin/curriculum/{id}/delete/', [AdminController::class, 'curriculum_delete'])->name('admin.curriculum.delete')->middleware('auth');
Route::delete('/admin/curriculum/destroy/{id}', [AdminController::class, 'curriculum_destroy'])->name('admin.curriculum.destroy');





Route::get('/profile', [UserController::class, 'profile'])->name('profile');
