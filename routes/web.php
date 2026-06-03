<?php

use App\Http\Controllers\AccountInfo;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OJTController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EvaluationController;
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

// ─── PUBLIC ROUTES (no auth required) ────────────────────────────────

Route::get('/', function () {
    return view('auth.login');
});



Route::get('/login', [AuthController::class,'login'])->name('login');
Route::get('/registration', [AuthController::class,'registration']);
Route::post('/register-user', [AuthController::class,'registerUser'])->name('register-user');
Route::get('/fetch-professors/{semester}/{startYear}/{endYear}', [ProfessorController::class,'fetchProfessors'])->name('fetch.professors');
Route::post('/login-user',[AuthController::class,'loginUser'])->name('login-user');
Route::get('/reset', [ForgotPassController::class,'resetP']);
Route::get('/forgot', [ForgotPassController::class,'forgotP']);
Route::post('/forgotPass', [ForgotPassController::class,'forgotPass'])->name('forgotPass');
Route::post('/reset-password', [ForgotPassController::class, 'resetPass'])->name('password.reset');

Route::get('/terms', function () {
    return view('students.terms');
});

Route::get('/privacy', function () {
    return view('students.privacy');
});

Route::get('/evaluation/form/{token}', [EvaluationController::class, 'showSupervisorForm'])->name('evaluation.form.show');
Route::post('/evaluation/form/{token}/review', [EvaluationController::class, 'reviewSupervisorForm'])->name('evaluation.form.review');
Route::post('/evaluation/form/{token}', [EvaluationController::class, 'submitSupervisorForm'])->name('evaluation.form.submit');
Route::get('/evaluation/submitted', [EvaluationController::class, 'thankYou'])->name('evaluation.form.thankyou');

// ─── AUTHENTICATED: ANY LOGGED-IN USER ──────────────────────────────

Route::middleware(['auth.session.custom'])->group(function () {
    Route::match(['get', 'post'], '/logout',[AuthController::class, 'logout']);
});

// ─── OJT COORDINATOR (role 1) ───────────────────────────────────────

Route::middleware(['auth.session.custom', 'role:1'])->group(function () {
    Route::get('/dashboard',[AuthController::class,'dashboard']);
    Route::get('/analytics',[AuthController::class,'analytics'])->name('analytics');
    Route::get('/analytics/data',[AuthController::class,'coordinatorAnalyticsData'])->name('coordinator.analytics.data');
    Route::get('/analytics/drilldown',[AuthController::class,'coordinatorAnalyticsDrilldown'])->name('coordinator.analytics.drilldown');
    Route::get('/analytics/export/csv',[AuthController::class,'coordinatorAnalyticsExportCsv'])->name('coordinator.analytics.export.csv');
    Route::get('/analytics/export/pdf',[AuthController::class,'coordinatorAnalyticsExportPdf'])->name('coordinator.analytics.export.pdf');
    Route::get('/professorTab', [AuthController::class,'professorTab']);
    Route::post('/professorCreate', [AuthController::class,'professorCreate'])->name('professorCreate');
    Route::put('/updateProfessor', [ProfessorController::class, 'update'])->name('updateProfessor');
    Route::get('/maintenance',[MaintenanceController::class,'maintenance']);
    Route::post('/remove/course/{id}', [MaintenanceController::class,'remove']);
    Route::post('/courses', [MaintenanceController::class,'courses'])->name('courses');
    Route::get('/auditlog', [MaintenanceController::class, 'auditTrail'])->name('auditlog');
    Route::get('/reports', [ReportsController::class, 'reports'])->name('reports');
    Route::match(['get', 'post'], '/OJTReports', [ReportsController::class, 'generateReport'])->name('studentojt.report.generate');
    Route::post('/reports/send-email', [ReportsController::class, 'sendEmail'])->name('reports.send.email');
    Route::get('/reportsExpired', [ReportsController::class, 'reportsExpired'])->name('reportsExpired');
    Route::match(['get', 'post'], '/ExpiredMOAReports', [ReportsController::class, 'generateMOAReport'])->name('reports.generate');

    Route::get('/MOA', [CompanyController::class,'companies']);
    Route::post('/uploadMOA', [MOAUploadController::class,'uploadfile']);
    Route::post('/moa/remove/{id}', [MOAUploadController::class,'remove']);
    Route::get('/moa/view/{companyId}', [MOAUploadController::class, 'view'])->name('moa.view');
    Route::post('/sendFile', [MOAUploadController::class,'sendFiles']);
    Route::get('/send/download/{file}', [MOAUploadController::class, 'downloadFile'])->name('download.file');
    Route::post('/status/{studentNum}', [StudentController::class,'update']);
    Route::get('/studentLists', [StudentController::class,'StuList']);
    Route::get('/ojt-report', [OJTController::class, 'showForm'])->name('ojt.report.form');
    Route::post('/ojt-report', [OJTController::class, 'generateReport'])->name('ojt.report.generate');
    Route::get('/accountinfo', [AccountInfo::class,'accountinfo']);
    Route::put('/edit/{email}', [AccountInfo::class,'editojt']);
    Route::get('/pending',[AuthController::class,'pending']);
    Route::post('/removeProfessor/{id}', [ProfessorController::class, 'removeProfessor'])->name('removeProfessor');
});

// ─── STUDENT (role 0) ───────────────────────────────────────────────

Route::middleware([
    \App\Http\Middleware\AuthMiddleware::class,
    \App\Http\Middleware\RoleMiddleware::class . ':0',
])->group(function () {
    Route::get('/student/home', [StudentController::class, 'home'])->name('student_home');
    Route::match(['get', 'post'], 'student/login',[AuthController::class, 'logout']);
    Route::get('/student/accountinfo', [StudentController::class,'student_acc']);
    Route::put('/student/edit/{email}', [StudentController::class,'edit']);
    Route::get('/student/class', [StudentController::class,'class']);
    Route::post('/student/join/{email}/{classId}', [StudentController::class,'join']);
    Route::post('/student/leave', [StudentController::class,'leave']);
    Route::get('/student/files', [StudentController::class,'fileSee']);
    Route::get('/student/ojtinfo', [StudentController::class,'ojtInformation']);
    Route::put('/student/ojtEdit/{studentNum}', [StudentController::class,'ojt_edit']);
    Route::post('/student/accept-terms', [StudentController::class, 'acceptTerms'])->name('student.acceptTerms');
    Route::get('/student/MOA', [CompanyController::class,'companiesup']);
    Route::post('/student/moa/remove/{id}', [MOAUploadController::class,'studentRemove'])->name('student.moa.remove');
    Route::get('/student/pending', [CompanyController::class,'pending']);
    Route::get('/student/requirements', [PassDocuController::class,'fileReq']);
    Route::post('/uploadReq', [PassDocuController::class,'fileReqCreate']);
    Route::get('/student/requirements/view/{id}', [PassDocuController::class,'viewFile']);
    Route::post('/remove/filesReq/{id}', [PassDocuController::class,'removeFile']);
    Route::get('/student/evaluation', [EvaluationController::class, 'studentIndex'])->name('student.evaluation');
    Route::post('/student/evaluation/send', [EvaluationController::class, 'sendEvaluationForm'])->name('student.evaluation.send');
    Route::post('/student/evaluation/{requestId}/resend', [EvaluationController::class, 'resendEvaluationForm'])->name('student.evaluation.resend');
    Route::post('/student/evaluation/{requestId}/cancel', [EvaluationController::class, 'cancelEvaluationForm'])->name('student.evaluation.cancel');
    Route::get('/student/evaluation/{requestId}', [EvaluationController::class, 'studentShowEvaluation'])->name('student.evaluation.show');
});

// ─── PROFESSOR (role 2) ─────────────────────────────────────────────

Route::middleware(['auth.session.custom', 'role:2'])->group(function () {
    Route::get('/professor/home',[AuthController::class,'professor_home'])->name('professor_home');
    Route::get('/professor/analytics',[AuthController::class,'professorAnalytics'])->name('professor.analytics');
    Route::get('/professor/analytics/data',[AuthController::class,'professorAnalyticsData'])->name('professor.analytics.data');
    Route::get('/professor/analytics/drilldown',[AuthController::class,'professorAnalyticsDrilldown'])->name('professor.analytics.drilldown');
    Route::get('/professor/analytics/export/csv',[AuthController::class,'professorAnalyticsExportCsv'])->name('professor.analytics.export.csv');
    Route::get('/professor/analytics/export/pdf',[AuthController::class,'professorAnalyticsExportPdf'])->name('professor.analytics.export.pdf');
    Route::match(['get', 'post'], '/professor/login',[AuthController::class, 'logout']);
    Route::get('/professor/accountinfo', [AccountInfo::class,'profAcc']);
    Route::put('/professor/edit/{id}', [AccountInfo::class,'edit']);
    Route::put('/professor/change_password/{id}', [AccountInfo::class,'change_password']);
    Route::get('/professor/class', [ProfessorController::class,'class']);
    Route::get('/professor/listStudents/{roomId}', [ProfessorController::class,'show']);
    Route::get('/professor/classList/{roomId}', [ProfessorController::class,'show_list']);
    Route::post('/professor/approve/{email}', [ProfessorController::class,'approve']);
    Route::post('/professor/deny/{email}', [ProfessorController::class,'deny']);
    Route::get('/professor/upload', function () {
        return redirect('/professor/class');
    });
    Route::get('/allStudents', [ProfessorController::class,'allStudents']);
    Route::post('/roomCreate', [ProfessorController::class,'roomCreate'])->name('roomCreate');
    Route::put('/roomUpdate/{id}', [ProfessorController::class,'roomUpdate'])->name('roomUpdate');
    Route::post('/roomDelete/{id}', [ProfessorController::class,'roomDelete'])->name('roomDelete');
    Route::get('/professor/maintain',[PassDocuController::class,'maintainFileCategory']);
    Route::post('/fileCategory', [PassDocuController::class,'fileCategory']);
    Route::post('/remove/files/{id}', [PassDocuController::class,'removeCategory']);
    Route::get('/studentrequire',[PassDocuController::class,'studentRequirements']);
    Route::post('/update/approve/status/{id}', [PassDocuController::class, 'updateApproveStatus']);
    Route::post('/update/denied/status/{id}', [PassDocuController::class, 'updateDeniedStatus']);
    Route::get('/requireview',[PassDocuController::class,'requirementsView']);
    Route::get('/reportsExpiredProf', [ReportsController::class, 'reportsExpiredProf'])->name('reportsExpiredProf');
    Route::match(['get', 'post'], '/ExpiredMOAReportsProf', [ReportsController::class, 'generateMOAReportProf'])->name('reports.generate.prof');
    Route::post('/professor/template/remove/{id}', [FileController::class, 'removeProfessorTemplate']);
    Route::get('/professor/evaluation', [EvaluationController::class, 'professorIndex'])->name('professor.evaluation');
    Route::get('/professor/evaluation/class/{classId}', [EvaluationController::class, 'professorClassList'])->name('professor.evaluation.class');
    Route::get('/professor/evaluation/export', [EvaluationController::class, 'exportProfessorEvaluation'])->name('professor.evaluation.export');
    Route::get('/professor/evaluation/print', [EvaluationController::class, 'printProfessorEvaluation'])->name('professor.evaluation.print');
    Route::get('/professor/evaluation/history/{studentId}', [EvaluationController::class, 'professorStudentHistory'])->name('professor.evaluation.history');
    Route::put('/professor/evaluation/template/{templateId}', [EvaluationController::class, 'updateTemplate'])->name('professor.evaluation.template.update');
    Route::get('/professor/evaluation/{requestId}', [EvaluationController::class, 'professorShowEvaluation'])->name('professor.evaluation.show');
});

// ─── SHARED: ALL AUTHENTICATED USERS (roles 0, 1 & 2) ──────────────

Route::middleware(['auth.session.custom', 'role:0,1,2'])->group(function () {
    Route::get('/download/{file}', [FileController::class,'download']);
    Route::put('/change_password/{id}', [AccountInfo::class,'change_password']);
    Route::post('/announcements', [AnnouncementController::class,'announcement']);
    Route::post('/companyCreate', [CompanyController::class,'companyCreate'])->name('companyCreate');
    Route::get('/print-data/{company}', [MOAUploadController::class, 'printData'])->name('print-data');
    Route::get('/voucher/{company}', [CompanyController::class,'voucher'])->name('voucher');
    Route::post('/notify/{studentNum}', [StudentController::class, 'notify']);
    Route::get('/moa/download/{file}', [MOAUploadController::class,'download']);
});

// ─── SHARED: COORDINATOR + PROFESSOR (roles 1 & 2) ── Reports ───────

Route::middleware(['auth.session.custom', 'role:1,2'])->group(function () {
    Route::post('/reportsExpired/send-email', [ReportsController::class, 'sendEmailExpired'])->name('reportsExpired.send.email');
});

// ─── SHARED: COORDINATOR + PROFESSOR (roles 1 & 2) ─────────────────

Route::middleware(['auth.session.custom', 'role:1,2'])->group(function () {
    Route::post('/uploadfile', [FileController::class,'uploadfile']);
    Route::get('/download/req/{file}', [PassDocuController::class,'download']);
});

Route::middleware(['auth.session.custom', 'role:1'])->group(function () {
    Route::get('/uploadpage', [FileController::class, 'show'])->name('uploadpage');
    Route::get('/view/{is}', [FileController::class,'view']);
    Route::post('/remove/{id}', [FileController::class,'remove']);
    Route::post('/remove/file/{id}', [FileController::class,'remove']);
    Route::get('/search', [FileController::class,'search']);
});