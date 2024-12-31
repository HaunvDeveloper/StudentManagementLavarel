<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LecturerController;
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



// LECTURER
Route::get('/lecturer', [LecturerController::class, 'dashboard'])->name('lecturer.dashboard')->middleware('auth');

Route::get('/lecturer/schedules', [LecturerController::class, 'schedules'])->name('lecturer.schedules')->middleware('auth');

Route::get('/lecturer/getWeekBySemester', [LecturerController::class, 'getWeekBySemester'])->name('lecturer.getWeekBySemester')->middleware('auth');

Route::get('/lecturer/getSchedules', [LecturerController::class, 'getSchedules'])->name('lecturer.getSchedules')->middleware('auth');


Route::get('/lecturer/index', [LecturerController::class, 'index'])->name('lecturer.index')->middleware('auth');

Route::get('/lecturer/getListClass', [LecturerController::class, 'getListClass'])->name('lecturer.getListClass')->middleware('auth');

Route::get('/lecturer/viewStudentList/{id}', [LecturerController::class, 'viewStudentList'])->name('lecturer.viewStudentList')->middleware('auth');


Route::get('/lecturer/attendance', [LecturerController::class, 'attendance_index'])->name('lecturer.attendance')->middleware('auth');
Route::get('/lecturer/_attendance', [LecturerController::class, 'attendance_getListStudent'])->name('partial.lecturer.getListStudent')->middleware('auth');

Route::get('/api/lecturer/activate', [LecturerController::class, 'attendance_activate'])->name('api.lecturer.activate')->middleware('auth');
Route::get('/api/lecturer/getLessons', [LecturerController::class, 'attendance_getLessons'])->name('api.lecturer.getLessons')->middleware('auth');
Route::get('/api/lecturer/getClasses', [LecturerController::class, 'attendance_getClasses'])->name('api.lecturer.getClasses')->middleware('auth');

Route::post('/lecturer/attendance/save', [LecturerController::class, 'attendance_save'])->name('lecturer.attendance.save')->middleware('auth');



Route::get('/lecturer/list-time', [LecturerController::class, 'listTime'])->name('lecturer.listTime');
Route::get('/lecturer/get-list-time', [LecturerController::class, '_getListTime'])->name('lecturer._getListTime');
Route::get('/lecturer/get-list-lesson', [LecturerController::class, 'getListLesson'])->name('lecturer.getListLesson');











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
Route::get('/admin/curriculum/getList', [AdminController::class, 'curriculum_getList'])->name('admin.curriculum.getList')->middleware('auth');



Route::get('/admin/curriculum/create', [AdminController::class, 'curriculum_create'])->name('admin.curriculum.create')->middleware('auth');
Route::post('/admin/curriculum/create', [AdminController::class, 'curriculum_store'])->name('admin.curriculum.store')->middleware('auth');


Route::get('/admin/curriculum/{id}/edit', [AdminController::class, 'curriculum_editCourses'])->name('admin.curriculum.editCourses')->middleware('auth');
Route::put('/admin/curriculum/update/{id}', [AdminController::class, 'curriculum_updateCourses'])->name('admin.curriculum.update')->middleware('auth');

Route::get('/admin/curriculum/details/{id}', [AdminController::class, 'curriculum_details'])->name('admin.curriculum.details')->middleware('auth');
Route::get('/admin/getsubject/{id}', [AdminController::class, 'getSubject'])->name('admin.getsubject')->middleware('auth');


Route::get('/admin/curriculum/{id}/delete/', [AdminController::class, 'curriculum_delete'])->name('admin.curriculum.delete')->middleware('auth');
Route::delete('/admin/curriculum/destroy/{id}', [AdminController::class, 'curriculum_destroy'])->name('admin.curriculum.destroy');


//ADMIN COURSE CLASS

Route::get('/admin/courseclass/index', [AdminController::class, 'courseclass_index'])->name('admin.courseclass.index')->middleware('auth');
Route::get('/admin/courseclass/getList', [AdminController::class, 'getListClass'])->name('admin.courseclass.getListClass')->middleware('auth');


Route::get('/admin/courseclass/create', [AdminController::class, 'courseclass_create'])->name('admin.courseclass.create')->middleware('auth');
Route::post('/admin/courseclass/store', [AdminController::class, 'courseclass_store'])->name('admin.courseclass.store')->middleware('auth');


Route::get('/admin/courseclass/createWithList', [AdminController::class, 'courseclass_createWithList'])->name('admin.courseclass.createWithList')->middleware('auth');
Route::post('/admin/courseclass/storeWithList', [AdminController::class, 'courseclass_storeWithList'])->name('admin.courseclass.storeWithList')->middleware('auth');
Route::get('/admin/courseclass/download-excel-template', [AdminController::class, 'courseclass_downloadExcelFile'])->name('admin.courseclass.downloadExcelTemplate');

Route::get('/admin/courseclass/viewStudentList/{id}', [AdminController::class, 'viewStudentList'])->name('admin.courseclass.viewStudentList')->middleware('auth');

Route::get('/admin/courseclass/{id}/edit', [AdminController::class, 'courseclass_edit'])->name('admin.courseclass.edit')->middleware('auth');
Route::put('/admin/courseclass/update/{id}', [AdminController::class, 'courseclass_update'])->name('admin.courseclass.update')->middleware('auth');

Route::delete('/admin/courseclass/destroy/{id}', [AdminController::class, 'courseclass_destroy'])->name('admin.courseclass.destroy');

Route::get('/admin/courseclass/downloadImportStudentList', [AdminController::class, 'downloadImportStudentList'])->name('admin.courseclass.downloadImportStudentList')->middleware('auth');
Route::get('/admin/courseclass/importStudentList/{id}', [AdminController::class, 'importStudentList'])->name('admin.courseclass.importStudentList')->middleware('auth');
Route::post('/admin/courseclass/storeStudentList', [AdminController::class, 'storeImportedStudentList'])->name('admin.courseclass.storeImportedStudentList')->middleware('auth');
Route::get('/admin/courseclass/exportStudentList/{id}', [AdminController::class, 'exportStudentList'])->name('admin.courseclass.exportStudentList')->middleware('auth');
Route::delete('/admin/courseclass/removeStudent/{id}', [AdminController::class, 'removeStudent'])->name('admin.courseclass.removeStudent')->middleware('auth');

Route::get('/api/get-student-by-id', [AdminController::class, 'getStudentById'])->name('admin.getStudentById')->middleware('auth');
Route::post('/admin/courseclass/addstudent', [AdminController::class, 'addStudentToClass'])->name('admin.courseclass.addStudent')->middleware('auth');



Route::get('/admin/getsemesterbyyearid/{id}', [AdminController::class, 'GetSemesterByYearDetail'])->name('admin.getsemesterbyyearid')->middleware('auth');

Route::get('/api/GetNewCodeCourseClass', [AdminController::class, 'getNewCodeCourseClass']);


// ADMIN STUDENT

Route::get('/admin/student/index', [AdminController::class, 'student_index'])->name('admin.student.index')->middleware('auth');

Route::get('/admin/student/create', [AdminController::class, 'student_create'])->name('admin.student.create')->middleware('auth');
Route::get('/admin/student/createWithList', [AdminController::class, 'student_createWithList'])->name('admin.student.createWithList')->middleware('auth');
Route::get('/admin/student/downloadList', [AdminController::class, 'student_downloadList'])->name('admin.student.downloadList')->middleware('auth');
Route::get('/admin/student/downloadExcelFile', [AdminController::class, 'student_downloadExcelFile'])->name('admin.student.downloadExcelFile')->middleware('auth');
Route::post('/admin/student/getList', [AdminController::class, 'student_getList'])->name('admin.student.getList')->middleware('auth');
Route::get('/api/specializations/byDept', [AdminController::class, 'getSpecializationByDept'])->name('api.specializations.byDept')->middleware('auth');


Route::post('/admin/student/storeWithList', [AdminController::class, 'student_storeWithList'])->name('admin.student.storeWithList')->middleware('auth');


Route::get('/admin/student/{id}/edit', [AdminController::class, 'student_edit'])->name('admin.student.edit')->middleware('auth');
Route::put('/admin/student/update/{id}', [AdminController::class, 'student_update'])->name('admin.student.update')->middleware('auth');


Route::delete('/admin/student/destroy/{id}', [AdminController::class, 'student_destroy'])->name('admin.student.destroy')->middleware('auth');


Route::get('/profile', [UserController::class, 'profile'])->name('profile');
