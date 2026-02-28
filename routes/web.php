<?php

use App\Http\Controllers\AccountInfo;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OJTController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PassDocuController;
use App\Http\Controllers\MOAUploadController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\ForgotPassController;
use App\Http\Controllers\CoursePerSYController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AnnouncementController;

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

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/login', [AuthController::class,'login']);
Route::get('/registration', [AuthController::class,'registration']);

Route::post('/register-user', [AuthController::class,'registerUser'])->name('register-user');
Route::get('/fetch-professors/{semester}/{startYear}/{endYear}', [ProfessorController::class,'fetchProfessors'])->name('fetch.professors');
Route::post('/login-user',[AuthController::class,'loginUser'])->name('login-user');

Route::get('/reset', [ForgotPassController::class,'resetP']);
Route::get('/forgot', [ForgotPassController::class,'forgotP']);
Route::post('/forgotPass', [ForgotPassController::class,'forgotPass'])->name('forgotPass');
Route::post('/reset-password', [ForgotPassController::class, 'resetPass'])->name('password.reset');



Route::get('/dashboard',[AuthController::class,'dashboard']); 

Route::get('/logout',[AuthController::class, 'logout']);
 
Route::get('/professorTab', [AuthController::class,'professorTab']);
Route::post('/professorCreate', [AuthController::class,'professorCreate'])->name('professorCreate');
Route::put('/updateProfessor', [ProfessorController::class, 'update'])->name('updateProfessor');
Route::get('/maintenance',[MaintenanceController::class,'maintenance']);
Route::post('/remove/course/{id}', [MaintenanceController::class,'remove']);
Route::post('/courses', [MaintenanceController::class,'courses'])->name('courses');

Route::get('/uploadpage', [FileController::class, 'show'])->name('uploadpage');
Route::get('/reports', [ReportsController::class, 'reports'])->name('reports');
Route::match(['get', 'post'], '/OJTReports', [ReportsController::class, 'generateReport'])->name('studentojt.report.generate');
Route::post('/reports/send-email', [ReportsController::class, 'sendEmail'])->name('reports.send.email');
Route::get('/reportsExpired', [ReportsController::class, 'reportsExpired'])->name('reportsExpired');
Route::match(['get', 'post'], '/ExpiredMOAReports', [ReportsController::class, 'generateMOAReport'])->name('reports.generate');
Route::post('/reportsExpired/send-email', [ReportsController::class, 'sendEmailExpired'])->name('reportsExpired.send.email');
Route::get('/reportsExpiredProf', [ReportsController::class, 'reportsExpiredProf'])->name('reportsExpiredProf');
Route::match(['get', 'post'], '/ExpiredMOAReportsProf', [ReportsController::class, 'generateMOAReportProf'])->name('reports.generate.prof');
Route::post('/uploadfile', [FileController::class,'uploadfile']);



Route::get('/download/{file}', [FileController::class,'download']);
Route::get('/view/{is}', [FileController::class,'view']);
Route::post('/remove/{id}', [FileController::class,'remove']);
Route::post('/remove/file/{id}', [FileController::class,'remove']);
Route::get('/search', [FileController::class,'search']);
Route::get('/MOA', [CompanyController::class,'companies']);
Route::get('/student/MOA', [CompanyController::class,'companiesup']);
Route::get('/student/pending', [CompanyController::class,'pending']);


Route::post('/companyCreate', [CompanyController::class,'companyCreate'])->name('companyCreate');
Route::post('/uploadMOA', [MOAUploadController::class,'uploadfile']);
Route::get('/moa/download/{file}', [MOAUploadController::class,'download']);
Route::post('/moa/remove/{id}', [MOAUploadController::class,'remove']);
Route::get('/moa/view/{companyId}', [MOAUploadController::class, 'view'])->name('moa.view');
Route::post('/sendFile', [MOAUploadController::class,'sendFiles']);
Route::get('/send/download/{file}', [MOAUploadController::class, 'downloadFile'])->name('download.file');
Route::get('/print-data/{company}', [MOAUploadController::class, 'printData'])->name('print-data');
Route::post('/status/{studentNum}', [StudentController::class,'update']);
Route::post('/notify/{studentNum}', [StudentController::class, 'notify']);
Route::get('/voucher/{company}', [CompanyController::class,'voucher'])->name('voucher');

 
Route::get('/studentLists', [StudentController::class,'StuList']);
Route::get('/ojt-report', [OJTController::class, 'showForm'])->name('ojt.report.form');
Route::post('/ojt-report', [OJTController::class, 'generateReport'])->name('ojt.report.generate');
Route::post('/announcements', [AnnouncementController::class,'announcement']);
 
Route::get('/accountinfo', [AccountInfo::class,'accountinfo']);
Route::put('/edit/{email}', [AccountInfo::class,'editojt']);
Route::put('/change_password/{id}', [AccountInfo::class,'change_password']);

Route::get('/student/home',[AuthController::class,'student_home'])->name('student_home');
Route::get('student/login',[AuthController::class, 'logout']);
Route::get('/student/accountinfo', [StudentController::class,'student_acc']);
Route::put('/student/edit/{email}', [StudentController::class,'edit']);
Route::get('/student/class', [StudentController::class,'class']);
Route::post('/student/join/{email}', [StudentController::class,'join']);
Route::post('/student/leave', [StudentController::class,'leave']);
Route::get('/student/files', [StudentController::class,'fileSee']);
Route::get('/student/ojtinfo', [StudentController::class,'ojtInformation']);
Route::put('/student/ojtEdit/{studentNum}', [StudentController::class,'ojt_edit']); 
Route::post('/student/accept-terms', [StudentController::class, 'acceptTerms'])->name('student.acceptTerms');

Route::get('/professor/home',[AuthController::class,'professor_home'])->name('professor_home');
Route::get('/professor/login',[AuthController::class, 'logout']);
Route::get('/professor/accountinfo', [AccountInfo::class,'profAcc']);
Route::put('/professor/edit/{id}', [AccountInfo::class,'edit']);
Route::put('/professor/change_password/{id}', [AccountInfo::class,'change_password']);
Route::get('/professor/class', [ProfessorController::class,'class']);
Route::get('/professor/listStudents/{course}', [ProfessorController::class,'show']);
Route::get('/supTab', [AuthController::class,'supTab']);
Route::post('/supCreate', [AuthController::class,'supCreate'])->name('supCreate');
Route::post('/roomCreate', [ProfessorController::class,'roomCreate'])->name('roomCreate');
Route::post('/roomDelete/{id}', [ProfessorController::class,'roomDelete'])->name('roomDelete');
Route::get('/professor/classList/{course}', [ProfessorController::class,'show_list']);
Route::post('/professor/approve/{email}', [ProfessorController::class,'approve']);
Route::post('/professor/deny/{email}', [ProfessorController::class,'deny']);
Route::get('/professor/upload', [ProfessorController::class,'uploadP']);
Route::get('/allStudents', [ProfessorController::class,'allStudents']);
Route::post('/removeProfessor/{id}', [ProfessorController::class, 'removeProfessor'])->name('removeProfessor');
 
Route::get('/pending',[AuthController::class,'pending']);



//Passing files
Route::get('/professor/maintain',[PassDocuController::class,'maintainFileCategory']);
Route::post('/fileCategory', [PassDocuController::class,'fileCategory']);
Route::post('/remove/files/{id}', [PassDocuController::class,'removeCategory']);
Route::get('/student/requirements', [PassDocuController::class,'fileReq']);
Route::post('/uploadReq', [PassDocuController::class,'fileReqCreate']);
Route::post('/remove/filesReq/{id}', [PassDocuController::class,'removeFile']);
Route::get('/studentrequire',[PassDocuController::class,'studentRequirements']);
Route::post('/update/approve/status/{id}', [PassDocuController::class, 'updateApproveStatus']);
Route::post('/update/denied/status/{id}', [PassDocuController::class, 'updateDeniedStatus']);
Route::get('/requireview',[PassDocuController::class,'requirementsView']);
Route::get('/download/req/{file}', [PassDocuController::class,'download']);

Route::get('/terms', function () {
    return view('legal.terms'); 
});

Route::get('/privacy', function () {
    return view('legal.privacy'); 
});
