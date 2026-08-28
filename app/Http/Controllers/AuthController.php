<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use App\Models\FileRequirement;
use App\Models\FileCategory;
use App\Models\OjtEvaluationRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\Announcements;
use App\Models\OJTInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Services\GuidanceApiService;
use App\Services\SyncFacultyFromFlss;
use App\Services\SyncUsersFromIdp;
use App\Services\SyncStudentsUnified;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Helpers\AuditLogger;
use App\Services\ReportAiInsightService;
use App\Services\IdpService;
use App\Services\FacultyApiService;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected IdpService $idpService;

    public function __construct(IdpService $idpService)
    {
        $this->idpService = $idpService;
    }

    public function loginGateway(Request $request)
    {
        $portal = $request->query('portal');
        if ($portal && in_array($portal, ['student', 'faculty'], true)) {
            Session::put('target_login_portal', $portal);
        }

        if (config('services.idp.enabled')) {
            return view('auth.login-gateway');
        }

        return view('auth.login');
    }

    public function showIdpTransition(Request $request)
    {
        $portal = $request->query('portal', Session::get('target_login_portal', 'student'));
        if (in_array($portal, ['student', 'faculty'], true)) {
            Session::put('target_login_portal', $portal);
        }

        if (!$this->idpService->isReachable()) {
            return view('auth.idp-transition', [
                'error' => 'Unable to connect to Identity Provider. The service may be offline or unreachable.'
            ]);
        }

        return view('auth.idp-transition');
    }

    public function redirectToIdp(Request $request)
    {
        $portal = $request->query('portal', Session::get('target_login_portal', 'student'));
        if (in_array($portal, ['student', 'faculty'], true)) {
            Session::put('target_login_portal', $portal);
        }

        return redirect()->away($this->idpService->getAuthorizeUrl());
    }

    public function handleIdpCallback(Request $request, FacultyApiService $flssApi)
    {
        $code = $request->query('code');

        if (!$code) {
            return view('auth.idp-transition', [
                'error' => 'Authorization code was not provided by Identity Provider.'
            ]);
        }

        try {
            // 1. Exchange code for tokens
            $tokens = $this->idpService->exchangeCode($code);
            $accessToken = $tokens['access_token'] ?? null;

            if (!$accessToken) {
                throw new \RuntimeException('No access token received from Identity Provider.');
            }

            // 2. Validate token structure & signature via JWKS
            $this->idpService->validateToken($accessToken);

            // 3. Get User Profile from IdP
            $idpUser = $this->idpService->getUserInfo($accessToken);
            $idpUserId = $idpUser['id'] ?? null;
            $email = $idpUser['email'] ?? null;

            if (!$idpUserId || !$email) {
                throw new \RuntimeException('Incomplete user profile received from Identity Provider.');
            }

            // Store IdP access token in session and cache for admin sync requests
            Session::put('idp_access_token', $accessToken);
            Cache::put('idp_admin_access_token', $accessToken, now()->addMinutes(55));

            // 4. Find local user by idp_user_id or email
            $user = User::findByIdpId($idpUserId);

            if (!$user) {
                $user = User::where('email', $email)->first();
            }

            // Fallback: match by multi-tier name/initial matching if student updated email to official PUP webmail
            if (!$user && !empty($idpUser['first_name']) && !empty($idpUser['last_name'])) {
                $idpFirst = strtolower(trim($idpUser['first_name']));
                $idpLast  = strtolower(trim($idpUser['last_name']));
                $idpNormFull = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $idpFirst . $idpLast));
                $idpNormLast = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $idpLast));
                $idpFirstTokens = preg_split('/\s+/', $idpFirst);
                $idpInitials = implode('', array_map(fn($t) => substr($t, 0, 1), $idpFirstTokens));

                $candidates = [];
                $candidateStudents = User::where('role', 0)->get();

                foreach ($candidateStudents as $cand) {
                    $cFirst = strtolower(trim($cand->first_name ?? ''));
                    $cLast  = strtolower(trim($cand->last_name ?? ''));
                    if (empty($cLast)) continue;

                    $cNormFull = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cFirst . $cLast));
                    $cNormLast = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cLast));

                    // Exact full name match
                    if ($cNormFull === $idpNormFull) {
                        $user = $cand;
                        break;
                    }

                    // Must have matching last name (allowing at most 1 typo)
                    if ($cNormLast !== $idpNormLast && levenshtein($cNormLast, $idpNormLast) > 1) {
                        continue;
                    }

                    $cFirstTokens = preg_split('/\s+/', $cFirst);
                    $cInitials = implode('', array_map(fn($t) => substr($t, 0, 1), $cFirstTokens));

                    // Primary First Name match ("John Cris" vs "John")
                    if (!empty($idpFirstTokens[0]) && !empty($cFirstTokens[0]) && $idpFirstTokens[0] === $cFirstTokens[0]) {
                        $candidates[] = ['user' => $cand, 'score' => 95];
                        continue;
                    }

                    // Initials match ("JC" vs "John Cris")
                    if ((!empty($cInitials) && $idpFirst === $cInitials) || (!empty($idpInitials) && $cFirst === $idpInitials) ||
                        (!empty($cInitials) && strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $idpFirst)) === $cInitials) ||
                        (!empty($idpInitials) && strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cFirst)) === $idpInitials)) {
                        $candidates[] = ['user' => $cand, 'score' => 90];
                        continue;
                    }

                    // Prefix / Substring match
                    if (str_starts_with($idpFirst, $cFirst) || str_starts_with($cFirst, $idpFirst)) {
                        $candidates[] = ['user' => $cand, 'score' => 85];
                        continue;
                    }
                }

                if (!$user && !empty($candidates)) {
                    usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
                    $user = $candidates[0]['user'];
                }
            }

            if ($user) {
                // Link idp_user_id if not linked yet
                if (!$user->idp_user_id) {
                    $user->idp_user_id = $idpUserId;
                }
                if ($user->email !== $email) {
                    $user->email = $email;
                }
                $user->save();

                // Log in existing user
                return $this->loginLocalUser($user, $request);
            }

            // --- NEW USER HANDLING BASED ON TARGET PORTAL ---
            $targetPortal = Session::get('target_login_portal', 'student');

            if ($targetPortal === 'faculty') {
                // Real-time verification against FLSS API
                $flssData = $flssApi->getFacultyList();
                $flssMatch = null;

                if ($flssData && !empty($flssData['faculties'])) {
                    foreach ($flssData['faculties'] as $fac) {
                        if (!empty($fac['email']) && strtolower($fac['email']) === strtolower($email)) {
                            $flssMatch = $fac;
                            break;
                        }
                    }
                }

                if ($flssMatch) {
                    // Auto-create new Faculty user (role=2)
                    $firstName  = $flssMatch['first_name'] ?? ($idpUser['first_name'] ?? 'Faculty');
                    $middleName = $flssMatch['middle_name'] ?? ($idpUser['middle_name'] ?? '');
                    $lastName   = $flssMatch['last_name'] ?? ($idpUser['last_name'] ?? '');
                    $suffix     = $flssMatch['suffix_name'] ?? '';
                    $fullName   = trim($firstName . ' ' . $lastName);

                    $user = new User();
                    $user->first_name = $firstName;
                    $user->middle_name = $middleName;
                    $user->last_name = $lastName;
                    $user->suffix = $suffix;
                    $user->full_name = $fullName;
                    $user->email = $email;
                    $user->password = Hash::make('Password123!');
                    $user->has_local_password = true;
                    $user->role = 2; // Professor
                    $user->idp_user_id = $idpUserId;
                    $user->status = 'Active';
                    $user->save();

                    $professor = new Professor();
                    $professor->user_id = $user->id;
                    $professor->full_name = $fullName;
                    $professor->email = $email;
                    $professor->save();

                    AuditLogger::log(
                        'Faculty SSO Registration',
                        'create',
                        'Auto-registered faculty via IdP SSO & FLSS verification for: ' . $fullName,
                        $user->id
                    );

                    return $this->loginLocalUser($user, $request);
                }

                // If not found in FLSS API
                return view('auth.idp-transition', [
                    'error' => "Your email ($email) was not found in the Faculty Loading & Scheduling System (FLSS). Please contact the OJT Coordinator to register your faculty account."
                ]);
            }

            // Default Student Onboarding: Store IdP details in session and redirect to onboarding
            Session::put('idp_onboarding_data', [
                'idp_user_id' => $idpUserId,
                'email'       => $email,
                'first_name'  => $idpUser['first_name'] ?? '',
                'middle_name' => $idpUser['middle_name'] ?? '',
                'last_name'   => $idpUser['last_name'] ?? '',
            ]);

            return redirect()->route('onboarding.show');

        } catch (\Exception $e) {
            Log::error('IdP Callback Error', ['error' => $e->getMessage()]);
            return view('auth.idp-transition', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function loginLocalUser(User $user, Request $request)
    {
        $request->session()->put('loginId', $user->id);
        $request->session()->put('show_terms', true);

        // Prompt for local password setup if they don't have one set up yet
        if (!$user->has_local_password) {
            $request->session()->put('show_password_setup', true);
        }

        Cache::put(
            'active_session_id:' . $user->id,
            $request->session()->getId(),
            now()->addMinutes((int) config('session.lifetime', 120))
        );

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login_at')) {
                $user->last_login_at = now();
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_activity_at')) {
                $user->last_activity_at = now();
            }
            $user->saveQuietly();
        } catch (\Throwable $e) {
            // Safe fallback if staging DB has not migrated new columns yet
        }

        AuditLogger::log(
            'Authentication',
            'login',
            'User logged in via Identity Provider: ' . $user->email,
            $user->id
        );

        if ($user->role == 0) {
            return redirect()->route('student_home');
        } else if ($user->role == 2) {
            return redirect()->route('professor_home');
        } else if ($user->role == 1) {
            return redirect('dashboard');
        }

        return redirect('/login');
    }

    public function showOnboarding(GuidanceApiService $guidanceApi)
    {
        $idp = Session::get('idp_onboarding_data');

        if (!$idp) {
            return redirect('/')->with('fail', 'No active onboarding session found.');
        }

        // Auto-search student in GuiSIS by UUID, email, or name
        $guisisData = null;
        try {
            $guisisData = $guidanceApi->findStudentProfile(
                $idp['idp_user_id'] ?? null,
                $idp['email'] ?? null,
                ($idp['first_name'] ?? '') . ' ' . ($idp['last_name'] ?? '')
            );
        } catch (\Throwable $e) {
            Log::warning('Onboarding GuiSIS lookup error: ' . $e->getMessage());
        }

        $professors = Professor::all();
        $courses = Courses::all();
        $schedules = Schedule::with('subject')->get();

        return view('onboarding', compact('idp', 'guisisData', 'professors', 'courses', 'schedules'));
    }

    public function storeOnboarding(Request $request, string $email, GuidanceApiService $guidanceApi)
    {
        $idp = Session::get('idp_onboarding_data');

        $request->validate([
            'studentNum' => ['required', 'regex:' . $this->studentNumberValidationPattern()],
            'course' => ['required', 'string', 'max:255'],
            'adviser_name' => ['required', 'string', 'max:255'],
            'academic_year_start' => ['required', 'integer'],
            'academic_year_end' => ['required', 'integer', 'gt:academic_year_start'],
            'year_and_section' => ['required', 'regex:' . $this->yearAndSectionValidationPattern()],
        ]);

        $firstName = $idp['first_name'] ?? $request->input('first_name', '');
        $middleName = $idp['middle_name'] ?? $request->input('middle_name', '');
        $lastName = $idp['last_name'] ?? $request->input('last_name', '');
        $idpUserId = $idp['idp_user_id'] ?? null;

        $user = new User();
        $user->first_name = $firstName;
        $user->middle_name = $middleName;
        $user->last_name = $lastName;
        $user->email = $email;
        $user->password = Hash::make(Str::random(32)); // Placeholder hash until local password is set
        $user->has_local_password = false;
        $user->idp_user_id = $idpUserId;
        $user->full_name = trim($firstName . ' ' . $lastName);
        $user->role = 0; // Default role: Student
        $user->save();

        $student = new OJTInformation();
        $studentE = new Student();

        $student->studentNum = $request->studentNum;
        $studentE->studentNum = $request->studentNum;
        $studentE->course = $request->course;
        $studentE->year_and_section = $request->year_and_section;
        $studentE->school_year_start = $request->academic_year_start;
        $studentE->school_year_end = $request->academic_year_end;
        $studentE->adviser_name = $request->adviser_name;
        $studentE->user_id = $user->id;

        // Auto-fetch additional demographic details from GuiSIS
        try {
            $studentNum = $request->studentNum;
            $personalInfo = $guidanceApi->getPersonalInfo($studentNum);
            if ($personalInfo && !empty($personalInfo['dateOfBirth'])) {
                try {
                    $studentE->date_of_birth = \Carbon\Carbon::parse($personalInfo['dateOfBirth'])->format('Y-m-d');
                } catch (\Throwable $e) {
                    $studentE->date_of_birth = substr($personalInfo['dateOfBirth'], 0, 10);
                }
            }

            $guisisProfile = $guidanceApi->getStudentByNumber($studentNum);
            if ($guisisProfile) {
                $mobile = $guisisProfile['mobileNumber'] ?? $guisisProfile['contactNumber'] ?? null;
                if ($mobile) {
                    $studentE->contact_number = $mobile;
                }
                $suffix = $guisisProfile['suffixName'] ?? null;
                if ($suffix && empty($user->suffix)) {
                    $user->suffix = $suffix;
                    $user->save();
                }
            }

            $addresses = $guidanceApi->getAddresses($studentNum);
            if (!empty($addresses) && is_array($addresses)) {
                $firstAddr = reset($addresses);
                if (is_array($firstAddr)) {
                    $street = $firstAddr['streetDetail'] ?? $firstAddr['street'] ?? '';
                    $brgy = is_array($firstAddr['barangay'] ?? null) ? ($firstAddr['barangay']['name'] ?? '') : ($firstAddr['barangay'] ?? '');
                    $city = is_array($firstAddr['city'] ?? null) ? ($firstAddr['city']['name'] ?? '') : ($firstAddr['city'] ?? '');
                    $prov = is_array($firstAddr['province'] ?? null) ? ($firstAddr['province']['name'] ?? '') : ($firstAddr['province'] ?? '');
                    $reg = is_array($firstAddr['region'] ?? null) ? ($firstAddr['region']['name'] ?? '') : ($firstAddr['region'] ?? '');

                    $fullAddr = implode(', ', array_filter([$street, $brgy, $city, $prov ?: $reg]));
                    if (!empty($fullAddr)) {
                        $studentE->address = $fullAddr;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Onboarding background demographic fetch error: ' . $e->getMessage());
        }

        $student->save();
        $studentE->save();

        $this->autoAssignStudentToMatchingClass($user, $studentE);

        Session::forget('idp_onboarding_data');

        AuditLogger::log(
            'Student Account',
            'create',
            'Completed IdP Onboarding for: ' . $user->full_name,
            $user->id
        );

        return $this->loginLocalUser($user, $request);
    }

    public function setLocalPassword(Request $request)
    {
        $request->validate([
            'new_password' => $this->passwordRules(),
            'new_password_confirmation' => 'required|same:new_password',
        ], $this->passwordValidationMessages('new_password', 'new_password_confirmation'));

        $user = User::find(Session::get('loginId'));

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User session expired. Please log in again.'
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->has_local_password = true;
        $user->save();

        Session::forget('show_password_setup');

        AuditLogger::log(
            'Account Security',
            'update',
            'Set local fallback password for IdP user: ' . $user->email,
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Local fallback password created successfully!'
        ]);
    }
    public function login(){
        return view("auth.login");
    }

    public function registration(){
        $data=Professor::all();
        $course=Courses::all();
        $schedules = Schedule::with('subject')->get();
        return view('auth.registration', compact('data','course','schedules'));
    }

    public function checkEmailAvailability(Request $request)
    {
        $email = trim((string) $request->query('email', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if ($email === '') {
            return response()->json([
                'available' => false,
                'message' => 'Email is required.',
            ], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'available' => false,
                'message' => 'Please enter a valid email address.',
            ], 422);
        }

        $exists = User::where('email', $email)
            ->when($ignoreId > 0, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This email is already in use.' : 'Email is available.',
        ]);
    }

    public function registerUser(Request $request){
        $request->validate([
            'first_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'middle_name' => ['nullable', 'regex:' . $this->nameValidationPattern()],
            'last_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'email'=>'required|email|unique:users,email',
            'studentNum' => ['required', 'regex:' . $this->studentNumberValidationPattern()],
            'course' => ['required', 'string', 'max:255'],
            'adviser_name' => ['required', 'string', 'max:255'],
            'academic_year_start' => ['required', 'integer'],
            'academic_year_end' => ['required', 'integer', 'gt:academic_year_start'],
            'year_and_section' => ['required', 'regex:' . $this->yearAndSectionValidationPattern()],
            'password' => $this->passwordRules(),
            'confirm_password' => 'required|same:password',
        ], array_merge(
            $this->nameValidationMessages(),
            $this->studentNumberValidationMessages(),
            $this->yearAndSectionValidationMessages(),
            [
                'course.required' => 'Course is required.',
                'adviser_name.required' => 'Please select a professor or select "Not Yet Listed".',
                'academic_year_start.required' => 'Start year is required.',
                'academic_year_start.integer' => 'Start year must be a valid year.',
                'academic_year_end.required' => 'End year is required.',
                'academic_year_end.integer' => 'End year must be a valid year.',
                'academic_year_end.gt' => 'End year must be later than the start year.',
            ],
            $this->passwordValidationMessages('password', 'confirm_password')
        ));
        $student = new OJTInformation();
        $user =new User();
        $studentE =new Student();
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $student->studentNum =  $request->studentNum;
        $studentE->studentNum =$request->studentNum;
        $studentE->course = $request->course;
        $studentE->year_and_section =$request->year_and_section;
        $studentE->school_year_start = $request->academic_year_start;
        $studentE->school_year_end   = $request->academic_year_end;
        $studentE->adviser_name =$request->adviser_name;

        $res = $user->save();
        $studentE->user_id = $user->id;
        $student->save();
        $studentE->save();

        if ($res) {
            $this->autoAssignStudentToMatchingClass($user, $studentE);
        }

        if($res){
            AuditLogger::log(
                'Student Account',
                'create',
                'Registered new student: ' . $user->full_name,
                $user->id
            );
            return back()->with('success','You have registered successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }

    protected function autoAssignStudentToMatchingClass(User $user, Student $studentProfile): void
    {
        if (!Schema::hasColumn('students', 'class_id') || empty($studentProfile->adviser_name) || $studentProfile->adviser_name === 'Not Yet Listed') {
            return;
        }

        $baseQuery = Classes::query()
            ->where('adviser_name', $studentProfile->adviser_name)
            ->where('course', $studentProfile->course);

        $matchingClasses = collect();

        if (
            Schema::hasColumn('classes', 'school_year_start')
            && Schema::hasColumn('classes', 'school_year_end')
            && !empty($studentProfile->school_year_start)
            && !empty($studentProfile->school_year_end)
        ) {
            $matchingClasses = (clone $baseQuery)
                ->where('school_year_start', $studentProfile->school_year_start)
                ->where('school_year_end', $studentProfile->school_year_end);
            $matchingClasses = $matchingClasses->get();
        }

        if ($matchingClasses->count() !== 1) {
            $fallbackQuery = clone $baseQuery;

            if (Schema::hasColumn('classes', 'school_year_start')) {
                $fallbackQuery->orderByDesc('school_year_start');
            }
            if (Schema::hasColumn('classes', 'school_year_end')) {
                $fallbackQuery->orderByDesc('school_year_end');
            }

            $matchingClasses = $fallbackQuery
                ->latest('created_at')
                ->latest('id')
                ->get();
        }

        if ($matchingClasses->count() !== 1) {
            return;
        }

        $matchedClass = $matchingClasses->first();
        $studentProfile->class_id = $matchedClass->id;
        $studentProfile->save();

        $user->status = 3;
        $user->save();

        AuditLogger::log(
            'Student Account',
            'join',
            'Student auto-joined class after registration: ' . $matchedClass->room . ' (' . $matchedClass->course . ')',
            $user->id
        );
    }

    public function loginUser(Request $request){
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        $user = User::where('email','=',$request->email)->first();

        if ($user) {
            if(Hash::check($request->password, $user->password)){
                $targetPortal = $request->input('portal', Session::get('target_login_portal', 'student'));

                // Enforce portal isolation
                if ($targetPortal === 'student' && (int) $user->role !== 0) {
                    return back()->with('fail', 'Faculty & Staff accounts must log in under the Faculty & Staff Portal.');
                }
                if ($targetPortal === 'faculty' && (int) $user->role === 0) {
                    return back()->with('fail', 'Student accounts must log in under the Student Portal.');
                }

                $request->session()->put('loginId',$user->id);
                $request->session()->put('show_terms', true);
                Cache::put(
                    'active_session_id:' . $user->id,
                    $request->session()->getId(),
                    now()->addMinutes((int) config('session.lifetime', 120))
                );

                try {
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_login_at')) {
                        $user->last_login_at = now();
                    }
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'last_activity_at')) {
                        $user->last_activity_at = now();
                    }
                    $user->saveQuietly();
                } catch (\Throwable $e) {
                    // Safe fallback if staging DB has not migrated new columns yet
                }

                if ($user->role == 0) {
                    return redirect()->route('student_home');
                } 
                else if ($user->role == 2) {
                    return redirect()->route('professor_home');
                }
                else if($user->role == 1) {
                    return redirect('dashboard');
                }
                else {
                    return redirect('/login');
                }
            }
            else{
                return back()->with('fail','Password does not match.');
            }
        }
        else{
            return back()->with('fail','Email is not registered.');
        }
    }

    public function dashboard(){
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $roleCount = User::where('role', 0)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->count();
        $roleCountP = User::where('role', 2)->count();

        $data=array();
        if(Session::has('loginId')){
            $data=User::where('id','=', Session::get('loginId'))->first();
        }

        $userName = $data->full_name ?? '';
        $fileCount = UploadedFile::query()
            ->when(
                $data && Schema::hasColumn('uploaded_files', 'uploader_user_id'),
                fn ($query) => $query->where('uploader_user_id', $data->id),
                fn ($query) => $query->where('uploader_name', $userName)
            )
            ->count();
        $announcements = Announcements::query()
            ->when(
                $data && Schema::hasColumn('announcements', 'announcer_user_id'),
                fn ($query) => $query->where('announcer_user_id', $data->id),
                fn ($query) => $query->where('announcer', $userName)
            )
            ->latest()
            ->get();

        $pendingStudents = User::where('role', 0)->where('status', 0)->count();
        $approvedStudents = User::where('role', 0)->where('status', 1)->count();
        $pendingRequirements = FileRequirement::where('status', 0)->count();
        $partnerCompanies = Company::count();
        $placedStudents = Student::whereHas('companies')->count();
        $unplacedStudents = max(0, $roleCount - $placedStudents);
        $expiredMoaCount = $this->countExpiredMoaRecords();
        $dashboardInsights = $this->buildCoordinatorDashboardInsights(
            $roleCount,
            $roleCountP,
            $fileCount,
            $announcements->count(),
            $pendingStudents,
            $approvedStudents,
            $pendingRequirements,
            $partnerCompanies,
            $placedStudents,
            $unplacedStudents,
            $expiredMoaCount
        );

        return view('ojtCoordinator.dashboard', compact(
            'data',
            'roleCount',
            'roleCountP',
            'fileCount',
            'announcements',
            'dashboardInsights',
            'pendingStudents',
            'approvedStudents',
            'pendingRequirements',
            'partnerCompanies',
            'placedStudents',
            'unplacedStudents',
            'expiredMoaCount'
        ));
    }

    public function analytics()
    {
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
        }

        // Optimized: Use grouped query instead of repeated counts
        $studentStats = User::where('role', 0)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalStudents = array_sum($studentStats);
        $approvedStudents = $studentStats[1] ?? 0;
        $pendingStudents = $studentStats[0] ?? 0;
        $deniedStudents = $studentStats[2] ?? 0;
        $inClassStudents = $studentStats[3] ?? 0;

        $studentStatusAnalytics = [
            [
                'label' => 'Approved students',
                'count' => $approvedStudents,
                'percentage' => $totalStudents > 0 ? round(($approvedStudents / $totalStudents) * 100) : 0,
                'class' => 'green',
            ],
            [
                'label' => 'Pending students',
                'count' => $pendingStudents,
                'percentage' => $totalStudents > 0 ? round(($pendingStudents / $totalStudents) * 100) : 0,
                'class' => 'amber',
            ],
            [
                'label' => 'Denied students',
                'count' => $deniedStudents,
                'percentage' => $totalStudents > 0 ? round(($deniedStudents / $totalStudents) * 100) : 0,
                'class' => 'red',
            ],
            [
                'label' => 'Joined rooms',
                'count' => $inClassStudents,
                'percentage' => $totalStudents > 0 ? round(($inClassStudents / $totalStudents) * 100) : 0,
                'class' => 'blue',
            ],
        ];

        // Optimized: Single grouped query for file stats
        $fileStats = FileRequirement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $totalRequirements = array_sum($fileStats);
        $approvedRequirements = $fileStats[1] ?? 0;
        $pendingRequirements = $fileStats[0] ?? 0;
        $deniedRequirements = $fileStats[2] ?? 0;

        $fileStatusAnalytics = [
            [
                'label' => 'Approved files',
                'count' => $approvedRequirements,
                'percentage' => $totalRequirements > 0 ? round(($approvedRequirements / $totalRequirements) * 100) : 0,
                'class' => 'green',
            ],
            [
                'label' => 'Pending files',
                'count' => $pendingRequirements,
                'percentage' => $totalRequirements > 0 ? round(($pendingRequirements / $totalRequirements) * 100) : 0,
                'class' => 'amber',
            ],
            [
                'label' => 'Denied files',
                'count' => $deniedRequirements,
                'percentage' => $totalRequirements > 0 ? round(($deniedRequirements / $totalRequirements) * 100) : 0,
                'class' => 'red',
            ],
        ];

        $partnerCompanies = Company::count();
        $placedStudents = Student::whereHas('companies')->count();
        $unplacedStudents = max(0, $totalStudents - $placedStudents);

        $companies = Company::withCount('students')->get();
        $activeMoaCount = 0;
        $expiredMoaCount = 0;
        $unassignedMoaCount = 0;

        foreach ($companies as $company) {
            $parts = explode('-', str_replace(' ', '', (string) ($company->school_year ?? '0-0')));
            $startYear = (int) ($parts[0] ?? 0);
            $isExpired = $startYear > 0 ? ((now()->year - $startYear) > 3) : false;

            if ($isExpired) {
                $expiredMoaCount++;
            } else {
                $activeMoaCount++;
            }

            if ((int) $company->students_count === 0) {
                $unassignedMoaCount++;
            }
        }

        $courseAnalytics = Student::select('course', DB::raw('COUNT(*) as total'))
            ->groupBy('course')
            ->orderByDesc('total')
            ->get();

        $courseMax = max(1, (int) $courseAnalytics->max('total'));
        $courseAnalytics = $courseAnalytics->map(function ($course) use ($courseMax) {
            return [
                'label' => $course->course ?: 'Unassigned',
                'count' => (int) $course->total,
                'percentage' => round(((int) $course->total / $courseMax) * 100),
            ];
        })->values();

        $topCompanies = Company::withCount('students')
            ->orderByDesc('students_count')
            ->limit(5)
            ->get();

        $topCompanyMax = max(1, (int) $topCompanies->max('students_count'));
        $topCompanies = $topCompanies->map(function ($company) use ($topCompanyMax) {
            return [
                'label' => $company->company_name,
                'count' => (int) $company->students_count,
                'percentage' => round(((int) $company->students_count / $topCompanyMax) * 100),
            ];
        })->values();

        $placementAnalytics = [
            [
                'label' => 'Placed students',
                'count' => $placedStudents,
                'percentage' => $totalStudents > 0 ? round(($placedStudents / $totalStudents) * 100) : 0,
                'class' => 'green',
            ],
            [
                'label' => 'Not yet placed',
                'count' => $unplacedStudents,
                'percentage' => $totalStudents > 0 ? round(($unplacedStudents / $totalStudents) * 100) : 0,
                'class' => 'amber',
            ],
            [
                'label' => 'Joined rooms',
                'count' => $inClassStudents,
                'percentage' => $totalStudents > 0 ? round(($inClassStudents / $totalStudents) * 100) : 0,
                'class' => 'blue',
            ],
        ];

        $moaStatusAnalytics = [
            [
                'label' => 'Active MOAs',
                'count' => $activeMoaCount,
                'percentage' => $partnerCompanies > 0 ? round(($activeMoaCount / $partnerCompanies) * 100) : 0,
                'class' => 'green',
            ],
            [
                'label' => 'Expired MOAs',
                'count' => $expiredMoaCount,
                'percentage' => $partnerCompanies > 0 ? round(($expiredMoaCount / $partnerCompanies) * 100) : 0,
                'class' => 'red',
            ],
            [
                'label' => 'MOAs without students',
                'count' => $unassignedMoaCount,
                'percentage' => $partnerCompanies > 0 ? round(($unassignedMoaCount / $partnerCompanies) * 100) : 0,
                'class' => 'amber',
            ],
        ];

        $analyticsInsights = $this->buildCoordinatorAnalyticsInsights(
            $studentStatusAnalytics,
            $fileStatusAnalytics,
            $courseAnalytics,
            $topCompanies,
            $totalStudents,
            $partnerCompanies,
            $placedStudents
        );

        return view('ojtCoordinator.analytics', compact(
            'data',
            'totalStudents',
            'approvedStudents',
            'pendingStudents',
            'deniedStudents',
            'inClassStudents',
            'studentStatusAnalytics',
            'totalRequirements',
            'approvedRequirements',
            'pendingRequirements',
            'deniedRequirements',
            'fileStatusAnalytics',
            'partnerCompanies',
            'placedStudents',
            'unplacedStudents',
            'courseAnalytics',
            'topCompanies',
            'placementAnalytics',
            'moaStatusAnalytics',
            'analyticsInsights'
        ));
    }

    public function coordinatorAnalyticsData(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $cacheKey = 'coord_analytics_' . md5($start . '|' . $end);

        return response()->json(Cache::remember($cacheKey, 60, function () use ($start, $end) {
            $filesQuery = FileRequirement::query();
        $studentsQuery = Student::query();

        if ($start) {
            $filesQuery->where('created_at', '>=', $start);
            $studentsQuery->where('created_at', '>=', $start);
        }
        if ($end) {
            $filesQuery->where('created_at', '<=', $end);
            $studentsQuery->where('created_at', '<=', $end);
        }

        // Group by YYYY-MM
        $filesData = $filesQuery
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $studentsData = $studentsQuery
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        // Generate all months in range for complete timeline
        $allMonths = [];
        $startDate = $start ? Carbon::createFromFormat('Y-m-d', $start) : Carbon::now()->subMonths(5);
        $endDate = $end ? Carbon::createFromFormat('Y-m-d', $end) : Carbon::now();

        for ($date = $startDate->copy()->startOfMonth(); $date <= $endDate; $date->addMonth()) {
            $monthKey = $date->format('Y-m');
            $allMonths[$monthKey] = [
                'label' => $date->format('M Y'),
                'files' => (int) ($filesData->get($monthKey) ?? 0),
                'students' => (int) ($studentsData->get($monthKey) ?? 0),
            ];
        }

        // Build response with labels and datasets
        $labels = [];
        $filesArray = [];
        $studentsArray = [];

        foreach ($allMonths as $month) {
            $labels[] = $month['label'];
            $filesArray[] = $month['files'];
            $studentsArray[] = $month['students'];
        }

        return [
            'labels' => $labels,
            'files' => $filesArray,
            'students' => $studentsArray,
        ];
        }));
    }

    public function coordinatorAnalyticsDrilldown(Request $request)
    {
        $year = $request->query('year');
        $month = $request->query('month');
        $type = $request->query('type', 'files');
        $status = $request->query('status');
        $q = trim((string) $request->query('q', ''));
        $page = $request->query('page', 1);
        $perPage = 20;

        if (!$year || !$month) {
            return response()->json(['error' => 'Year and month required'], 400);
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        if ($type === 'files') {
            $items = FileRequirement::whereBetween('created_at', [$start, $end])
                ->select('id', 'file_name', 'status', 'created_at', 'adviser')
                ->when($status !== null && $status !== '', function ($query) use ($status) {
                    $query->where('status', (int) $status);
                })
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('file_name', 'like', '%' . $q . '%')
                            ->orWhere('adviser', 'like', '%' . $q . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $items = Student::whereBetween('created_at', [$start, $end])
                ->select('id', 'first_name', 'last_name', 'course', 'created_at')
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($inner) use ($q) {
                        $inner->where('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%')
                            ->orWhere('course', 'like', '%' . $q . '%');
                    });
                })
                ->orderByDesc('created_at')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }

    public function coordinatorAnalyticsExportCsv(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $filename = 'coordinator-analytics-' . now()->format('Ymd-His') . '.csv';

        $studentStats = User::where('role', 0)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildCoordinatorMonthlySeries($start, $end);

        return response()->streamDownload(function () use ($studentStats, $fileStats, $months) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Section', 'Label', 'Count']);
            fputcsv($handle, ['Students', 'Approved', $studentStats[1] ?? 0]);
            fputcsv($handle, ['Students', 'Pending', $studentStats[0] ?? 0]);
            fputcsv($handle, ['Students', 'Denied', $studentStats[2] ?? 0]);
            fputcsv($handle, ['Students', 'Joined rooms', $studentStats[3] ?? 0]);
            fputcsv($handle, ['Files', 'Approved', $fileStats[1] ?? 0]);
            fputcsv($handle, ['Files', 'Pending', $fileStats[0] ?? 0]);
            fputcsv($handle, ['Files', 'Denied', $fileStats[2] ?? 0]);
            foreach ($months as $month) {
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Files', $month['files']]);
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Students', $month['students']]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function coordinatorAnalyticsExportPdf(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $studentStats = User::where('role', 0)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildCoordinatorMonthlySeries($start, $end);

        $html = view('reports.analytics_export', [
            'title' => 'Coordinator Analytics Report',
            'subtitle' => 'OJT Coordinator overview',
            'summaryRows' => [
                ['label' => 'Approved students', 'value' => $studentStats[1] ?? 0],
                ['label' => 'Pending students', 'value' => $studentStats[0] ?? 0],
                ['label' => 'Denied students', 'value' => $studentStats[2] ?? 0],
                ['label' => 'Joined rooms', 'value' => $studentStats[3] ?? 0],
                ['label' => 'Approved files', 'value' => $fileStats[1] ?? 0],
                ['label' => 'Pending files', 'value' => $fileStats[0] ?? 0],
                ['label' => 'Denied files', 'value' => $fileStats[2] ?? 0],
            ],
            'monthlyRows' => $months,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'coordinator-analytics-' . now()->format('Ymd-His') . '.pdf', ['Content-Type' => 'application/pdf']);
    }

    public function professorAnalyticsExportCsv(Request $request)
    {
        $data = Session::has('loginId') ? User::where('id', Session::get('loginId'))->first() : null;
        if (!$data) {
            return redirect('/login');
        }

        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();
        $filterClass = $request->query('class_id');
        if ($filterClass) {
            $classIds = array_intersect($classIds, [(int) $filterClass]);
        }

        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfMonth() : Carbon::now()->subMonths(5)->startOfMonth();
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfMonth() : Carbon::now()->endOfMonth();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildProfessorMonthlySeries($classIds, $start, $end);

        $filename = 'professor-analytics-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () use ($requestStats, $fileStats, $months) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Section', 'Label', 'Count']);
            fputcsv($handle, ['Requests', 'Sent', $requestStats['sent'] ?? 0]);
            fputcsv($handle, ['Requests', 'Opened', $requestStats['opened'] ?? 0]);
            fputcsv($handle, ['Requests', 'Submitted', $requestStats['submitted'] ?? 0]);
            fputcsv($handle, ['Requests', 'Expired', $requestStats['expired'] ?? 0]);
            fputcsv($handle, ['Requests', 'Cancelled', $requestStats['cancelled'] ?? 0]);
            fputcsv($handle, ['Files', 'Approved', $fileStats[1] ?? 0]);
            fputcsv($handle, ['Files', 'Pending', $fileStats[0] ?? 0]);
            fputcsv($handle, ['Files', 'Denied', $fileStats[2] ?? 0]);
            foreach ($months as $month) {
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Sent', $month['sent']]);
                fputcsv($handle, ['Monthly Activity', $month['label'] . ' - Submitted', $month['submitted']]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function professorAnalyticsExportPdf(Request $request)
    {
        $data = Session::has('loginId') ? User::where('id', Session::get('loginId'))->first() : null;
        if (!$data) {
            return redirect('/login');
        }

        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();
        $filterClass = $request->query('class_id');
        if ($filterClass) {
            $classIds = array_intersect($classIds, [(int) $filterClass]);
        }

        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfMonth() : Carbon::now()->subMonths(5)->startOfMonth();
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfMonth() : Carbon::now()->endOfMonth();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $fileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $months = $this->buildProfessorMonthlySeries($classIds, $start, $end);

        $html = view('reports.analytics_export', [
            'title' => 'Professor Analytics Report',
            'subtitle' => $data->full_name,
            'summaryRows' => [
                ['label' => 'Sent requests', 'value' => $requestStats['sent'] ?? 0],
                ['label' => 'Opened requests', 'value' => $requestStats['opened'] ?? 0],
                ['label' => 'Submitted requests', 'value' => $requestStats['submitted'] ?? 0],
                ['label' => 'Expired requests', 'value' => $requestStats['expired'] ?? 0],
                ['label' => 'Cancelled requests', 'value' => $requestStats['cancelled'] ?? 0],
                ['label' => 'Approved files', 'value' => $fileStats[1] ?? 0],
                ['label' => 'Pending files', 'value' => $fileStats[0] ?? 0],
                ['label' => 'Denied files', 'value' => $fileStats[2] ?? 0],
            ],
            'monthlyRows' => $months,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, 'professor-analytics-' . now()->format('Ymd-His') . '.pdf', ['Content-Type' => 'application/pdf']);
    }

    protected function buildCoordinatorMonthlySeries(?string $start, ?string $end): array
    {
        $startDate = $start ? Carbon::createFromFormat('Y-m-d', $start) : Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = $end ? Carbon::createFromFormat('Y-m-d', $end) : Carbon::now()->endOfMonth();

        $fileTotals = FileRequirement::query()
            ->when($start, fn ($query) => $query->where('created_at', '>=', $start))
            ->when($end, fn ($query) => $query->where('created_at', '<=', $end))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $studentTotals = Student::query()
            ->when($start, fn ($query) => $query->where('created_at', '>=', $start))
            ->when($end, fn ($query) => $query->where('created_at', '<=', $end))
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $months = [];
        for ($date = $startDate->copy()->startOfMonth(); $date <= $endDate; $date->addMonth()) {
            $key = $date->format('Y-m');
            $months[] = [
                'label' => $date->format('M Y'),
                'files' => (int) ($fileTotals[$key] ?? 0),
                'students' => (int) ($studentTotals[$key] ?? 0),
            ];
        }

        return $months;
    }

    protected function buildProfessorMonthlySeries(array $classIds, Carbon $start, Carbon $end): array
    {
        $sentTotals = OjtEvaluationRequest::query()
            ->whereIn('class_id', $classIds)
            ->whereNotNull('emailed_at')
            ->whereBetween('emailed_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(emailed_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(emailed_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $submittedTotals = OjtEvaluationRequest::query()
            ->whereIn('class_id', $classIds)
            ->whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$start, $end])
            ->selectRaw("DATE_FORMAT(submitted_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy(DB::raw("DATE_FORMAT(submitted_at, '%Y-%m')"))
            ->pluck('total', 'month');

        $months = [];
        for ($date = $start->copy()->startOfMonth(); $date <= $end->copy()->endOfMonth(); $date->addMonth()) {
            $key = $date->format('Y-m');
            $months[] = [
                'label' => $date->format('M Y'),
                'sent' => (int) ($sentTotals[$key] ?? 0),
                'submitted' => (int) ($submittedTotals[$key] ?? 0),
            ];
        }

        return $months;
    }

    public function logout(){
        if(Session::has('loginId')){
            $id = Session::get('loginId');
            
            // Perform IdP logout if token is present
            if (Session::has('idp_access_token')) {
                $token = Session::get('idp_access_token');
                $this->idpService->logout($token);
                Session::forget('idp_access_token');
            }

            Session::pull('loginId');
            Session::forget('termsAccepted');
            Session::forget('show_password_setup');
            Cache::forget('active_session_id:' . $id);
            return redirect('/');
        }
        return redirect('/');
    }

    public function professorTab()
    {
        $user = [];
        if (Session::has('loginId')) {
            $user = User::where('id', Session::get('loginId'))->first();
        }
        $course= Courses::all();
        $data = Professor::with('subjects')->get();
        $usersP = User::whereIn('email', $data->pluck('email'))->get();

        // Fetch FLSS API emails & names for source detection
        $flssApi = app(\App\Services\FacultyApiService::class);
        $flssRes = $flssApi->getFacultyList();
        $flssEmails = [];
        $flssNames = [];
        if ($flssRes && !empty($flssRes['faculties'])) {
            foreach ($flssRes['faculties'] as $f) {
                if (!empty($f['email'])) $flssEmails[] = strtolower(trim($f['email']));
                $fn = ($f['first_name'] ?? '') . ' ' . ($f['last_name'] ?? '');
                if (trim($fn)) $flssNames[] = strtolower(trim($fn));
            }
        }

        // Transform the subjects data
        $subjectData = $data->flatMap(function ($professor) {
            return $professor->subjects->map(function ($subject) {
                return [
                    'subject_code' => $subject->code,
                    'subject_description' => $subject->description,
                ];
            });
        })->toArray();

        return view('ojtCoordinator.professorTab', compact('data', 'user', 'subjectData','usersP','course', 'flssEmails', 'flssNames'));
    }

    public function syncFacultyFromFlss(SyncFacultyFromFlss $syncer)
    {
        $summary = $syncer->execute();

        if (!empty($summary['errors']) && $summary['created'] === 0 && $summary['updated'] === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . implode(' ', $summary['errors']),
                'summary' => $summary,
            ], 500);
        }

        $missingAccounts = $summary['missing_accounts'] ?? [];
        $hasMissing = !empty($missingAccounts);

        $message = "Faculty sync completed! Created: {$summary['created']} new accounts, Updated: {$summary['updated']} existing accounts, Skipped: {$summary['skipped']}.";

        if ($hasMissing) {
            $message .= " Note: " . count($missingAccounts) . " local faculty account(s) were not found in FLSS.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'summary' => $summary,
            'has_missing' => $hasMissing,
            'missing_accounts' => $missingAccounts,
        ]);
    }

    /**
     * Bulk sync all existing students from IDP to link their UUIDs.
     * Accessible from the coordinator Students tab.
     */
    public function syncUsersFromIdp(SyncStudentsUnified $syncer)
    {
        @set_time_limit(300);
        $summary = $syncer->syncIdpUuids();

        $message = "IDP Sync completed! Newly Linked: {$summary['idp_linked']}, Already Linked: {$summary['already_linked']}.";

        if (!empty($summary['errors'])) {
            $message .= ' (Notes: ' . count($summary['errors']) . ')';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'summary' => $summary,
        ]);
    }

    /**
     * Bulk sync existing students' profiles from GuiSIS.
     */
    public function syncUsersFromGuisis(SyncStudentsUnified $syncer)
    {
        @set_time_limit(300);
        $summary = $syncer->syncGuisisOnly();

        $pool = $summary['guisis_total_pool'] ?? 0;
        return response()->json([
            'success' => true,
            'message' => "GuiSIS Demographic Sync completed! Profiles updated: {$summary['guisis_synced']}" . ($pool > 0 ? " from {$pool} GuiSIS records." : "."),
            'summary' => $summary,
        ]);
    }

    /**
     * Remove selected faculty accounts that are no longer in FLSS.
     */
    public function pruneMissingFaculty(Request $request)
    {
        $currentUserId = Session::get('loginId') ?? Auth::id();
        $currentUser = User::find($currentUserId);

        if (!$currentUser || (int) $currentUser->role !== 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized operation.'], 403);
        }

        $request->validate([
            'selected_user_ids' => 'required|array',
            'selected_user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->input('selected_user_ids', []);
        $deletedNames = [];

        foreach ($userIds as $id) {
            // Prevent coordinator from deleting themselves
            if ((int)$id === (int)$currentUser->id) {
                continue;
            }

            $user = User::find($id);
            if ($user && in_array((int)$user->role, [1, 2])) {
                $name = $user->full_name ?: ($user->first_name . ' ' . $user->last_name);
                $deletedNames[] = $name;

                // Remove from professors table
                \App\Models\Professor::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->delete();

                AuditLogger::log(
                    'Faculty Pruning',
                    'delete',
                    'Pruned faculty account not found in FLSS: ' . $name . ' (' . $user->email . ')',
                    $user->id
                );

                $user->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($deletedNames) . ' missing faculty account(s) successfully removed.',
            'pruned_names' => $deletedNames,
        ]);
    }

    /**
     * Transfer OJT Coordinator designation to another faculty member.
     */
    public function transferCoordinatorRole(Request $request)
    {
        $currentUserId = Session::get('loginId') ?? Auth::id();
        $currentUser = User::find($currentUserId);

        if (!$currentUser || (int) $currentUser->role !== 1) {
            return response()->json(['success' => false, 'message' => 'Unauthorized operation.'], 403);
        }

        $request->validate([
            'target_user_id' => 'required|exists:users,id',
        ]);

        $targetUser = User::find($request->target_user_id);

        if (!$targetUser || (int) $targetUser->role !== 2) {
            return response()->json(['success' => false, 'message' => 'Target user must be an active Professor.'], 400);
        }

        if ($targetUser->id === $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'You are already the OJT Coordinator.'], 400);
        }

        DB::transaction(function () use ($currentUser, $targetUser) {
            // Demote current coordinator to professor
            $currentUser->role = 2;
            $currentUser->save();

            // Promote target professor to coordinator
            $targetUser->role = 1;
            $targetUser->save();
        });

        AuditLogger::log(
            'Coordinator Designation Transfer',
            'update',
            'Transferred OJT Coordinator designation from ' . $currentUser->full_name . ' to ' . $targetUser->full_name,
            $currentUser->id
        );

        return response()->json([
            'success' => true,
            'message' => 'OJT Coordinator designation successfully transferred to ' . $targetUser->full_name . '!',
            'redirect' => route('professor_home')
        ]);
    }

    public function professorCreate(Request $request){
        $request->validate([
            'first_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'last_name' => ['required', 'regex:' . $this->nameValidationPattern()],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => $this->passwordRules(true),
        ], array_merge(
            $this->nameValidationMessages(),
            $this->passwordValidationMessages('password', 'password_confirmation'),
            [
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already in use.',
            ]
        ));

        $user = new User();
        $professor = new Professor();

        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->full_name = $user->first_name . ' ' . $user->last_name;
        $user->password = Hash::make($request->password);
        $user->role = 2;

        $professor->email = $request->email;
        $professor->full_name = $user->full_name;

        $res = $user->save();

        if ($res) {
            $professor->user_id = $user->id;
            $professor->save();

            AuditLogger::log(
                'Professor',
                'create',
                'Created professor account: ' . $professor->full_name,
                $user->id
            );
            return back()->with('success','You have registered successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }

    public function student_home()
    {
        if (Session::has('loginId')) {
            $user = User::where('id', Session::get('loginId'))->first();
            if (Schema::hasColumn('uploaded_files', 'class_id')) {
                $fileCount = UploadedFile::where(function ($query) {
                        $query->whereNull('class_id')
                              ->orWhere('class_id', 0);
                    })
                    ->count();
            } else {
                $fileCount = UploadedFile::count();
            }

            // TERMS MODAL LOGIC
            $showTerms = false;
            $lastAccepted = Session::get('termsAcceptedTime'); // timestamp of last acceptance

            if (!$lastAccepted || now()->diffInHours($lastAccepted) >= 24) { // 24 hours = 1 day
                $showTerms = true;
            }
            return view('students.student_home', compact('user', 'fileCount', 'showTerms'));
        }
        return redirect()->route('login');
    }

    public function professor_home(){
        $data = [];
        $userName = '';
        $loginId = Session::get('loginId');
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $class = [];
        if ($loginId) {
            $data = User::where('id', '=', $loginId)->first();
            $userName = $data->full_name;
            // Get all rooms (classes) where this professor is adviser
            $class = Classes::where('adviser_name', $userName)->get();
            // For each room, preload students
            foreach ($class as $room) {
                $room->students = User::where('status', 1)
                    ->whereHas('studentInfo', function ($query) use ($room, $data) {
                        $query->where('class_id', $room->id);
                        if (empty($room->school_year_start) || empty($room->school_year_end)) {
                            $query->orWhere(function ($legacy) use ($room, $data) {
                                $legacy->whereNull('class_id')
                                    ->where('course', $room->course)
                                    ->where('adviser_name', $data->full_name);
                            });
                        }
                    })
                    ->get();
            }
        }
        $roleCount = User::where('role', 0)
            ->where(function ($query) use ($userName, $loginId) {
                $query->whereHas('studentInfo', function ($studentQuery) use ($userName) {
                    $studentQuery->where('adviser_name', $userName);
                });
                if (!empty($loginId)) {
                    $query->orWhere('id', $loginId);
                }
            })
            ->where('created_at', '>=', $sixMonthsAgo)
            ->count();
        $fileCount = UploadedFile::all()->count();
        $currentYear = now()->year;
        $companies = Company::all();
        $stu = Student::all();
        $companies = $companies->filter(function ($company) use ($currentYear) {
            list($startYear, $endYear) = explode('-', $company->school_year);
            $startYear = (int) $startYear;
            $endYear = (int) $endYear;
            return $currentYear >= $startYear && $currentYear <= $startYear + 3;
        });
        $companyNames = $companies->pluck('company_name')->toArray();
        return view('professor.home', compact('companies','data', 'roleCount', 'fileCount', 'class'));
    }

    public function professorAnalytics()
    {
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        $students = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds, $data) {
                $query->whereIn('class_id', $classIds)
                      ->orWhere(function ($legacy) use ($data) {
                          $legacy->whereNull('class_id')
                              ->where('adviser_name', $data->full_name);
                      });
            })
            ->get();

        $totalStudents = $students->count();
        $approvedStudents = $students->where('status', 1)->count();
        $pendingApprovals = $students->where('status', 3)->count();
        $deniedStudents = $students->where('status', 2)->count();
        $inactiveStudents = $students->where('status', 0)->count();

        $classAnalytics = $classrooms->map(function ($room) use ($students) {
            $roomStudents = $students->filter(function ($student) use ($room) {
                return (string) optional($student->studentInfo)->class_id === (string) $room->id;
            });

            $requestTotal = OjtEvaluationRequest::where('class_id', $room->id)->count();
            $submitted = OjtEvaluationRequest::where('class_id', $room->id)->where('status', 'submitted')->count();

            return [
                'label' => $room->room,
                'total_students' => $roomStudents->count(),
                'submitted' => $submitted,
                'requests' => $requestTotal,
                'completion' => $requestTotal > 0 ? round(($submitted / $requestTotal) * 100) : 0,
            ];
        })->values();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $requestTotal = array_sum($requestStats);
        $sentRequests = $requestStats['sent'] ?? 0;
        $openedRequests = $requestStats['opened'] ?? 0;
        $submittedRequests = $requestStats['submitted'] ?? 0;
        $expiredRequests = $requestStats['expired'] ?? 0;
        $cancelledRequests = $requestStats['cancelled'] ?? 0;

        $requestAnalytics = [
            ['label' => 'Sent', 'count' => $sentRequests, 'class' => 'blue'],
            ['label' => 'Opened', 'count' => $openedRequests, 'class' => 'amber'],
            ['label' => 'Submitted', 'count' => $submittedRequests, 'class' => 'green'],
            ['label' => 'Expired', 'count' => $expiredRequests, 'class' => 'red'],
            ['label' => 'Cancelled', 'count' => $cancelledRequests, 'class' => 'purple'],
        ];

        $templateCount = FileCategory::when($professor, function ($query) use ($professor) {
            $query->where('professor_id', $professor->id);
        })->count();

        $profFileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $filePending = $profFileStats[0] ?? 0;
        $fileApproved = $profFileStats[1] ?? 0;
        $fileDenied = $profFileStats[2] ?? 0;

        $monthlyActivity = collect(range(5, 0))->map(function ($offset) use ($classIds) {
            $month = Carbon::now()->subMonths($offset);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'label' => $month->format('M Y'),
                'submitted' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('submitted_at', [$start, $end])->count(),
                'sent' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('emailed_at', [$start, $end])->count(),
            ];
        })->values();

        $maxSubmitted = max(1, (int) $monthlyActivity->max('submitted'));
        $maxSent = max(1, (int) $monthlyActivity->max('sent'));

        $monthlyActivity = $monthlyActivity->map(function ($item) use ($maxSubmitted, $maxSent) {
            return [
                'label' => $item['label'],
                'submitted' => $item['submitted'],
                'sent' => $item['sent'],
                'submitted_percentage' => round(($item['submitted'] / $maxSubmitted) * 100),
                'sent_percentage' => round(($item['sent'] / $maxSent) * 100),
            ];
        })->values();

        $analyticsInsights = $this->buildProfessorAnalyticsInsights(
            $classAnalytics,
            $requestAnalytics,
            $totalStudents,
            $approvedStudents,
            $pendingApprovals,
            $submittedRequests,
            $requestTotal,
            $filePending,
            $fileApproved,
            $fileDenied,
            $monthlyActivity
        );

        return view('professor.analytics', compact(
            'data',
            'classrooms',
            'classAnalytics',
            'requestAnalytics',
            'totalStudents',
            'approvedStudents',
            'pendingApprovals',
            'deniedStudents',
            'inactiveStudents',
            'requestTotal',
            'submittedRequests',
            'templateCount',
            'filePending',
            'fileApproved',
            'fileDenied',
            'monthlyActivity',
            'analyticsInsights'
        ));
    }

    public function professorAnalyticsPrint()
    {
        $data = [];
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        $students = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds, $data) {
                $query->whereIn('class_id', $classIds)
                    ->orWhere(function ($legacy) use ($data) {
                        $legacy->whereNull('class_id')
                            ->where('adviser_name', $data->full_name);
                    });
            })
            ->get();

        $totalStudents = $students->count();
        $approvedStudents = $students->where('status', 1)->count();
        $pendingApprovals = $students->where('status', 3)->count();
        $deniedStudents = $students->where('status', 2)->count();
        $inactiveStudents = $students->where('status', 0)->count();

        $classAnalytics = $classrooms->map(function ($room) use ($students) {
            $roomStudents = $students->filter(function ($student) use ($room) {
                return (string) optional($student->studentInfo)->class_id === (string) $room->id;
            });

            $requestTotal = OjtEvaluationRequest::where('class_id', $room->id)->count();
            $submitted = OjtEvaluationRequest::where('class_id', $room->id)->where('status', 'submitted')->count();

            return [
                'label' => $room->room,
                'total_students' => $roomStudents->count(),
                'submitted' => $submitted,
                'requests' => $requestTotal,
                'completion' => $requestTotal > 0 ? round(($submitted / $requestTotal) * 100) : 0,
            ];
        })->values();

        $requestStats = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $requestTotal = array_sum($requestStats);
        $sentRequests = $requestStats['sent'] ?? 0;
        $openedRequests = $requestStats['opened'] ?? 0;
        $submittedRequests = $requestStats['submitted'] ?? 0;
        $expiredRequests = $requestStats['expired'] ?? 0;
        $cancelledRequests = $requestStats['cancelled'] ?? 0;

        $requestAnalytics = [
            ['label' => 'Sent', 'count' => $sentRequests, 'class' => 'blue'],
            ['label' => 'Opened', 'count' => $openedRequests, 'class' => 'amber'],
            ['label' => 'Submitted', 'count' => $submittedRequests, 'class' => 'green'],
            ['label' => 'Expired', 'count' => $expiredRequests, 'class' => 'red'],
            ['label' => 'Cancelled', 'count' => $cancelledRequests, 'class' => 'purple'],
        ];

        $templateCount = FileCategory::when($professor, function ($query) use ($professor) {
            $query->where('professor_id', $professor->id);
        })->count();

        $profFileStats = FileRequirement::where('adviser', $data->full_name)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $filePending = $profFileStats[0] ?? 0;
        $fileApproved = $profFileStats[1] ?? 0;
        $fileDenied = $profFileStats[2] ?? 0;

        $monthlyActivity = collect(range(5, 0))->map(function ($offset) use ($classIds) {
            $month = Carbon::now()->subMonths($offset);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            return [
                'label' => $month->format('M Y'),
                'submitted' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('submitted_at', [$start, $end])->count(),
                'sent' => OjtEvaluationRequest::whereIn('class_id', $classIds)->whereBetween('emailed_at', [$start, $end])->count(),
            ];
        })->values();

        $maxSubmitted = max(1, (int) $monthlyActivity->max('submitted'));
        $maxSent = max(1, (int) $monthlyActivity->max('sent'));

        $monthlyActivity = $monthlyActivity->map(function ($item) use ($maxSubmitted, $maxSent) {
            return [
                'label' => $item['label'],
                'submitted' => $item['submitted'],
                'sent' => $item['sent'],
                'submitted_percentage' => round(($item['submitted'] / $maxSubmitted) * 100),
                'sent_percentage' => round(($item['sent'] / $maxSent) * 100),
            ];
        })->values();

        $analyticsInsights = $this->buildProfessorAnalyticsInsights(
            $classAnalytics,
            $requestAnalytics,
            $totalStudents,
            $approvedStudents,
            $pendingApprovals,
            $submittedRequests,
            $requestTotal,
            $filePending,
            $fileApproved,
            $fileDenied,
            $monthlyActivity
        );

        return view('professor.analytics_print', compact(
            'data',
            'classrooms',
            'classAnalytics',
            'requestAnalytics',
            'totalStudents',
            'approvedStudents',
            'pendingApprovals',
            'deniedStudents',
            'inactiveStudents',
            'requestTotal',
            'submittedRequests',
            'templateCount',
            'filePending',
            'fileApproved',
            'fileDenied',
            'monthlyActivity',
            'analyticsInsights'
        ));
    }

    // JSON endpoint for AJAX-driven charting and filters
    public function professorAnalyticsData(Request $request)
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        // optional class filter
        $filterClass = $request->query('class_id');
        if ($filterClass) {
            $classIds = array_intersect($classIds, [(int)$filterClass]);
        }

        // date range: default last 6 months
        $end = $request->query('end') ? Carbon::parse($request->query('end'))->endOfMonth() : Carbon::now()->endOfMonth();
        $start = $request->query('start') ? Carbon::parse($request->query('start'))->startOfMonth() : Carbon::now()->subMonths(5)->startOfMonth();

        // Build cache key from parameters
        $cacheKey = 'prof_analytics_' . $data->id . '_' . md5(implode(',', $classIds) . $start->format('Y-m-d') . $end->format('Y-m-d'));

        $chartData = Cache::remember($cacheKey, 60, function () use ($classIds, $start, $end) {
            // aggregated counts grouped by year-month for emailed_at (sent) and submitted_at (submitted)
            $sentRows = OjtEvaluationRequest::select(
                DB::raw("YEAR(emailed_at) as y"),
                DB::raw("MONTH(emailed_at) as m"),
                DB::raw('COUNT(*) as total')
            )->whereIn('class_id', $classIds)
             ->whereNotNull('emailed_at')
             ->whereBetween('emailed_at', [$start, $end])
             ->groupBy('y','m')
             ->get()
             ->keyBy(function($r){ return $r->y.'-'.str_pad($r->m,2,'0',STR_PAD_LEFT); });

            $submittedRows = OjtEvaluationRequest::select(
                DB::raw("YEAR(submitted_at) as y"),
                DB::raw("MONTH(submitted_at) as m"),
                DB::raw('COUNT(*) as total')
            )->whereIn('class_id', $classIds)
             ->whereNotNull('submitted_at')
             ->whereBetween('submitted_at', [$start, $end])
             ->groupBy('y','m')
             ->get()
             ->keyBy(function($r){ return $r->y.'-'.str_pad($r->m,2,'0',STR_PAD_LEFT); });

            $period = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $period[] = $cursor->format('Y-m');
                $cursor->addMonth();
            }

            $labels = [];
            $sent = [];
            $submitted = [];

            foreach ($period as $p) {
                [$y,$m] = explode('-', $p);
                $labels[] = Carbon::createFromDate((int)$y,(int)$m,1)->format('M Y');
                $sent[] = isset($sentRows[$p]) ? (int)$sentRows[$p]->total : 0;
                $submitted[] = isset($submittedRows[$p]) ? (int)$submittedRows[$p]->total : 0;
            }

            return ["labels" => $labels, "sent" => $sent, "submitted" => $submitted];
        });

        return response()->json($chartData);
    }

    public function professorAnalyticsDrilldown(Request $request)
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $year = $request->query('year');
        $month = $request->query('month');
        $class_id = $request->query('class_id');
        $status = $request->query('status');
        $q = trim((string) $request->query('q', ''));
        $page = $request->query('page', 1);
        $perPage = 20;

        if (!$year || !$month) {
            return response()->json(['error' => 'Year and month required'], 400);
        }

        $professor = Professor::where('user_id', $data->id)->first();
        $classrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();

        if ($class_id) {
            $classIds = array_intersect($classIds, [(int)$class_id]);
        }

        if (empty($classIds)) {
            return response()->json(['data' => [], 'total' => 0, 'per_page' => $perPage, 'current_page' => $page]);
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $items = OjtEvaluationRequest::whereIn('class_id', $classIds)
            ->whereBetween('submitted_at', [$start, $end])
            ->select('id', 'student_id', 'company', 'status', 'submitted_at', 'created_at')
            ->with('student:id,first_name,last_name')
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->whereHas('student', function ($studentQuery) use ($q) {
                        $studentQuery->where('first_name', 'like', '%' . $q . '%')
                            ->orWhere('last_name', 'like', '%' . $q . '%');
                    })->orWhere('company', 'like', '%' . $q . '%');
                });
            })
            ->orderByDesc('submitted_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $items->items(),
            'total' => $items->total(),
            'per_page' => $perPage,
            'current_page' => $page,
        ]);
    }

    public function pending(){
        $data=array();
        if(Session::has('loginId')){
            $data=User::where('id','=', Session::get('loginId'))->first();
        }
        return view('students.pending', compact('data'));
    }

    protected function buildCoordinatorDashboardInsights(int $recentStudents, int $professors, int $templates, int $announcements, int $pendingStudents, int $approvedStudents, int $pendingRequirements, int $partnerCompanies, int $placedStudents, int $unplacedStudents, int $expiredMoaCount)
    {
        $highlights = [
            'Dashboard coverage shows ' . $recentStudents . ' recent student records and ' . $professors . ' professor accounts.',
            'Operational resources include ' . $templates . ' uploaded templates, ' . $partnerCompanies . ' partner companies, and ' . $announcements . ' posted announcements.',
            'Placement coverage shows ' . $placedStudents . ' students with assigned companies.',
        ];

        $watchouts = [];
        if ($pendingStudents > 0) {
            $watchouts[] = $pendingStudents . ' student account' . ($pendingStudents === 1 ? '' : 's') . ' still need approval review.';
        }
        if ($pendingRequirements > 0) {
            $watchouts[] = $pendingRequirements . ' requirement file' . ($pendingRequirements === 1 ? '' : 's') . ' are pending review.';
        }
        if ($expiredMoaCount > 0) {
            $watchouts[] = $expiredMoaCount . ' MOA record' . ($expiredMoaCount === 1 ? '' : 's') . ' may need renewal follow-up.';
        }
        if ($unplacedStudents > 0) {
            $watchouts[] = $unplacedStudents . ' recent student record' . ($unplacedStudents === 1 ? '' : 's') . ' are not yet matched with company placement data.';
        }

        $actions = [
            'Review pending student approvals and requirement files first.',
            'Check placement coverage for students without company assignments.',
            'Use the MOA page to prioritize renewal follow-up for expired agreements.',
        ];

        return app(ReportAiInsightService::class)->summarize('coordinator_dashboard', [
            'total_records' => $recentStudents,
            'approved_students' => $approvedStudents,
            'pending_students' => $pendingStudents,
            'pending_files' => $pendingRequirements,
            'total_companies' => $partnerCompanies,
            'records_with_ojt' => $placedStudents,
            'missing_ojt' => $unplacedStudents,
            'expired_moa' => $expiredMoaCount,
            'uploaded_templates' => $templates,
            'announcements' => $announcements,
        ], $highlights, $watchouts, $actions);
    }

    protected function passwordRules(bool $confirmed = false): array
    {
        $rules = [
            'required',
            'string',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/',
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    protected function nameValidationPattern(): string
    {
        return "/^[\\p{L}]+(?:[ '\\-][\\p{L}]+)*$/u";
    }

    protected function nameValidationMessages(): array
    {
        return [
            'first_name.regex' => "First name may only contain letters, spaces, apostrophes, and hyphens.",
            'middle_name.regex' => "Middle name may only contain letters, spaces, apostrophes, and hyphens.",
            'last_name.regex' => "Last name may only contain letters, spaces, apostrophes, and hyphens.",
        ];
    }

    protected function studentNumberValidationPattern(): string
    {
        return "/^\\d{4}-\\d{5}-TG-[01]$/";
    }

    protected function studentNumberValidationMessages(): array
    {
        return [
            'studentNum.regex' => 'Student number must follow this format: YYYY-12345-TG-0 or YYYY-12345-TG-1.',
        ];
    }

    protected function yearAndSectionValidationPattern(): string
    {
        return "/^\\d+-\\d+$/";
    }

    protected function yearAndSectionValidationMessages(): array
    {
        return [
            'year_and_section.required' => 'Year and section is required.',
            'year_and_section.regex' => 'Year and section must follow this format: 4-1.',
        ];
    }

    protected function passwordValidationMessages(string $field = 'password', string $confirmField = 'password_confirmation'): array
    {
        return [
            $field . '.min' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
            $field . '.regex' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and one of these symbols: ! @ # $ % ^ & *.',
            $field . '.confirmed' => 'Password confirmation does not match.',
            $confirmField . '.required' => 'Please confirm your password.',
            $confirmField . '.same' => 'Password confirmation does not match.',
        ];
    }

    protected function countExpiredMoaRecords(): int
    {
        return Company::all()->filter(function ($company) {
            if (!empty($company->valid_until)) {
                try {
                    return Carbon::parse($company->valid_until)->isPast();
                } catch (\Throwable $e) {
                    return false;
                }
            }

            $parts = explode('-', str_replace(' ', '', (string) ($company->school_year ?? '0-0')));
            $startYear = (int) ($parts[0] ?? 0);

            return $startYear > 0 && ((now()->year - $startYear) > 3);
        })->count();
    }

    protected function buildCoordinatorAnalyticsInsights($studentStatusAnalytics, $fileStatusAnalytics, $courseAnalytics, $topCompanies, int $totalStudents, int $partnerCompanies, int $placedStudents)
    {
        $studentStats = collect($studentStatusAnalytics);
        $fileStats = collect($fileStatusAnalytics);
        $courseStats = collect($courseAnalytics);
        $companyStats = collect($topCompanies);

        $approved = (int) ($studentStats->firstWhere('label', 'Approved students')['count'] ?? 0);
        $pending = (int) ($studentStats->firstWhere('label', 'Pending students')['count'] ?? 0);
        $pendingFiles = (int) ($fileStats->firstWhere('label', 'Pending files')['count'] ?? 0);
        $topCourse = $courseStats->sortByDesc('count')->first();
        $topCompany = $companyStats->sortByDesc('count')->first();

        $highlights = [
            'Student coverage shows ' . $totalStudents . ' records with ' . $approved . ' approved students.',
            'Partner coverage includes ' . $partnerCompanies . ' companies and ' . $placedStudents . ' placed students.',
        ];

        if (!empty($topCourse['label'])) {
            $highlights[] = 'Largest course group: ' . $topCourse['label'] . ' (' . $topCourse['count'] . ').';
        }

        if (!empty($topCompany['label'])) {
            $highlights[] = 'Top partner company: ' . $topCompany['label'] . ' (' . $topCompany['count'] . ' placements).';
        }

        $watchouts = [];
        if ($pending > 0) {
            $watchouts[] = $pending . ' student account' . ($pending === 1 ? '' : 's') . ' still need approval or placement review.';
        }
        if ($pendingFiles > 0) {
            $watchouts[] = $pendingFiles . ' requirement file' . ($pendingFiles === 1 ? '' : 's') . ' are still pending review.';
        }

        $actions = [
            'Review pending student approvals before the next intake cycle.',
            'Check placement balance across top partner companies.',
            'Use the analytics chart filters to find weak course coverage.'
        ];

        return app(ReportAiInsightService::class)->summarize('coordinator_analytics', [
            'total_records' => $totalStudents,
            'total_companies' => $partnerCompanies,
            'records_with_ojt' => $placedStudents,
            'course' => $topCourse['label'] ?? null,
        ], $highlights, $watchouts, $actions);
    }

    protected function buildProfessorAnalyticsInsights($classAnalytics, $requestAnalytics, int $totalStudents, int $approvedStudents, int $pendingApprovals, int $submittedRequests, int $requestTotal, int $filePending, int $fileApproved, int $fileDenied, $monthlyActivity)
    {
        $classStats = collect($classAnalytics);
        $requestStats = collect($requestAnalytics);
        $activityStats = collect($monthlyActivity);

        $topClass = $classStats->sortByDesc('completion')->first();
        $sent = (int) ($requestStats->firstWhere('label', 'Sent')['count'] ?? 0);
        $opened = (int) ($requestStats->firstWhere('label', 'Opened')['count'] ?? 0);
        $pendingFiles = (int) $filePending;
        $latestMonth = $activityStats->last();

        $highlights = [
            'Advisee coverage shows ' . $totalStudents . ' students, ' . $approvedStudents . ' approved and ' . $pendingApprovals . ' pending.',
            'Evaluation flow shows ' . $submittedRequests . ' submitted evaluations out of ' . $requestTotal . ' requests.',
        ];

        if (!empty($topClass['label'])) {
            $highlights[] = 'Best completion class: ' . $topClass['label'] . ' at ' . $topClass['completion'] . '%.';
        }

        if ($latestMonth) {
            $highlights[] = 'Latest month activity: ' . $latestMonth['sent'] . ' sent and ' . $latestMonth['submitted'] . ' submitted.';
        }

        $watchouts = [];
        if ($pendingApprovals > 0) {
            $watchouts[] = $pendingApprovals . ' student account' . ($pendingApprovals === 1 ? '' : 's') . ' still need approval.';
        }
        if ($pendingFiles > 0) {
            $watchouts[] = $pendingFiles . ' file requirement' . ($pendingFiles === 1 ? '' : 's') . ' are still pending.';
        }
        if ($sent > $opened && $opened > 0) {
            $watchouts[] = 'Some evaluation links were sent but not opened yet, so follow-up may be needed.';
        }

        $actions = [
            'Follow up with classes that have low evaluation completion.',
            'Review pending files alongside student approval status.',
            'Use monthly activity to time reminders before deadlines.'
        ];

        return app(ReportAiInsightService::class)->summarize('professor_analytics', [
            'total_records' => $totalStudents,
            'records_with_ojt' => $submittedRequests,
            'missing_ojt' => max(0, $requestTotal - $submittedRequests),
            'course' => 'Professor advisories',
        ], $highlights, $watchouts, $actions);
    }
}
