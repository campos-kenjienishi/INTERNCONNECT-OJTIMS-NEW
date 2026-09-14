<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiClient;

class ChatbotAiService
{
    protected GeminiClient $geminiClient;

    public const SUPPORT_EMAIL = 'internconnect.ojtims@gmail.com';
    public const SUPPORT_FACEBOOK = 'https://www.facebook.com/profile.php?id=61593939354633';
    public const SUPPORT_FB_NAME = 'InternConnect: On-the-Job Training Information Management System';
    public const GUISIS_URL = 'https://www.guisis.dllbsit2027.com/';
    public const IDP_URL = 'https://identity-provider.isaxbsit2027.com/';

    public function __construct(?GeminiClient $geminiClient = null)
    {
        $this->geminiClient = $geminiClient ?: new GeminiClient();
    }

    /**
     * Process user query and return structured response
     */
    public function reply(string $message, ?string $userRole = null, array $history = []): array
    {
        $cleanMessage = $this->sanitizeInput($message);

        if ($cleanMessage === '') {
            return [
                'reply' => 'Hello! I am your InternConnect AI Assistant. How can I help you navigate the system or answer questions about your OJT requirements today?',
                'actions' => [],
                'suggestions' => [
                    'Where do I submit my MOA?',
                    'How do I sync my profile with GuiSIS?',
                    'What are the OJT requirements?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Check rule-based instant fast matches (navigation & support keywords)
        $fastMatch = $this->checkRuleBasedMatches($cleanMessage, $userRole);
        if ($fastMatch !== null) {
            return $fastMatch;
        }

        // Cache frequent questions for fast response and quota conservation
        $cacheKey = 'chatbot:ask:' . md5(strtolower($userRole ?? 'guest') . '|' . strtolower($cleanMessage));
        $cached = Cache::get($cacheKey);
        if ($cached && is_array($cached)) {
            return $cached;
        }

        // Query Gemini with strict privacy guardrails
        $systemPrompt = $this->buildSystemPrompt($userRole);
        $fullPrompt = $systemPrompt . "\n\nUser Question: " . $cleanMessage;

        try {
            $rawResponse = $this->geminiClient->generate($fullPrompt);

            if ($rawResponse !== null) {
                $decoded = $this->extractJson($rawResponse);
                if ($decoded && isset($decoded['reply'])) {
                    $result = [
                        'reply' => (string) $decoded['reply'],
                        'actions' => is_array($decoded['actions'] ?? null) ? $decoded['actions'] : [],
                        'suggestions' => is_array($decoded['suggestions'] ?? null) ? array_slice($decoded['suggestions'], 0, 3) : [],
                        'escalate' => (bool) ($decoded['escalate'] ?? false),
                        'source' => 'gemini'
                    ];

                    Cache::put($cacheKey, $result, 600); // 10 minutes cache
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('ChatbotAiService exception', ['error' => $e->getMessage()]);
        }

        // Fallback response if Gemini is unavailable
        return $this->buildFallbackResponse($cleanMessage, $userRole);
    }

    /**
     * Sanitize user input to strip potential accidental PII/credentials
     */
    protected function sanitizeInput(string $input): string
    {
        $input = trim($input);
        // Strip student numbers pattern (e.g. 2022-00123-TG-0)
        $input = preg_replace('/\b\d{4}-\d{5}-[A-Z]{2}-\d\b/i', '[REDACTED_STUDENT_NUMBER]', $input);
        // Strip potential email addresses
        $input = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[REDACTED_EMAIL]', $input);
        // Strip phone numbers
        $input = preg_replace('/(\+?63|0)9\d{9}/', '[REDACTED_PHONE]', $input);

        return substr($input, 0, 500);
    }

    /**
     * Build strict privacy-guarded system prompt
     */
    protected function buildSystemPrompt(?string $userRole): string
    {
        $roleName = match ($userRole) {
            'student', '3' => 'STUDENT',
            'professor', 'faculty', '2' => 'PROFESSOR / FACULTY ADVISER',
            'coordinator', 'admin', '1' => 'OJT COORDINATOR',
            default => 'GENERAL USER / LANDING PAGE VISITOR'
        };

        $roleContext = match ($userRole) {
            'student', '0', '3' => "The user is a STUDENT.
- ALLOWED TOPICS:
  1. ACCOUNT & GUISIS SYNC: Syncing profile details from GuiSIS (/student/accountinfo). What is GuiSIS (Guidance Information System hosted at https://www.guisis.dllbsit2027.com/). If a student doesn't have a GuiSIS account or records are missing, instruct them to visit https://www.guisis.dllbsit2027.com/ and complete their Individual Inventory Record (IIR) form, then return to Account Settings to sync. Updating contact details, emergency contacts, and setting a local fallback password.
  2. MY CLASS & ADVISER: Viewing enrolled class section, course schedule, room assignment, and designated OJT Faculty Adviser (/student/class).
  3. NOTARIZED MOA & PARTNER COMPANIES: Submitting a new Notarized MOA PDF, linking to an active partner company, or declaring School In-House OJT (/student/MOA). Requesting coordinator unlock for approved MOAs.
  4. OJT PLACEMENT INFO: Encoding host company address, assigned department, internship role, supervisor contact (name, email, phone), work modality (Onsite/Hybrid/Remote), and weekly training schedule (/student/ojtinfo).
  5. REQUIREMENTS SUBMISSION & 2 PHASES: The student requirements process is divided into 2 distinct phases: (1) Basic Phase (Basic Requirements: Resume, Medical Clearance, Good Moral, Consent Form, Endorsement Letter, Acceptance Letter + submitting Notarized MOA or In-House OJT), which is always open; and (2) Other Phase (Other Requirements: DTRs, Weekly Reports, Certificate of Completion, Final Narrative Report), which is gated and unlocks automatically once all Basic Requirements and Notarized MOA are completed. Handling denied requirements by reviewing professor remarks and re-uploading.
  6. SUPERVISOR EVALUATION: Sending the digital evaluation form link to the company supervisor's email (/student/evaluation), resending if not received, and viewing released evaluation grades.
  7. DOWNLOADABLE FORMS & CLASS FILES: Official templates and institutional forms uploaded by your OJT Coordinator can be accessed and downloaded on the Downloadable Files page (/student/files). Meanwhile, class-specific files, announcements, and syllabus materials uploaded by your Professor can be viewed on your My Class page (/student/class).
- FORBIDDEN TOPICS: Coordinator administrative tools (FLSS faculty sync, global MOA partner creation, database backups, audit logs, student directory cohort sync, system maintenance), faculty class management. If asked, you MUST DECLINE and set escalate: true.",
            'professor', 'faculty', '2' => "The user is a PROFESSOR / FACULTY ADVISER.
- ALLOWED TOPICS:
  1. CLASS & SECTION MANAGEMENT: Creating new class sections/rooms, viewing enrolled student rosters, archiving completed semester rooms, unarchiving rooms, and uploading class announcements, guidelines, and learning materials (/professor/class).
  2. STUDENT REQUIREMENTS REVIEW: Monitoring student submission progress by class section (/professor/requirement-status), inspecting uploaded PDF documents, approving verified submissions, denying invalid documents with specific remarks/feedback for student revision, and batch-approving with 'Approve All'.
  3. SUPERVISOR EVALUATIONS: Reviewing completed supervisor digital performance evaluations, inspecting criteria scores and feedback, and printing/exporting evaluation summaries (/professor/evaluation).
  4. ADVISER ANALYTICS & REPORTS: Viewing compliance rates, requirement progress charts, placement work modalities (Onsite/Hybrid/Remote), and exporting data to CSV, PDF, or printable format (/professor/analytics).
  5. FACULTY ACCOUNT SETTINGS: Updating faculty contact information and setting or changing local passwords (/professor/accountinfo).
  6. ALL STUDENTS DIRECTORY: Viewing and searching the master student list across classes (/allStudents).
- FORBIDDEN TOPICS: Coordinator administrative tools (FLSS faculty sync, global MOA partner creation, database backups, audit logs, student directory cohort sync, global system maintenance). If asked, you MUST DECLINE and set escalate: true.",
            'coordinator', 'admin', '1' => "The user is an OJT COORDINATOR (Full Administrative Access).
- ALLOWED TOPICS:
  1. DASHBOARD & GLOBAL OVERVIEW: Institutional metrics overview, student compliance count, MOA status summaries, and active rooms (/dashboard).
  2. FACULTY & FLSS SYNC: Syncing faculty advisers and class schedule loads from FLSS (/coordinator/sync-faculty), syncing accounts from IdP (/coordinator/sync-users-idp) and GuiSIS (/coordinator/sync-users-guisis), pruning inactive faculty, and transferring coordinator role (/professorTab).
  3. PARTNER COMPANIES & MOA MANAGEMENT: Adding partner host companies, tracking MOA start and expiration dates, verifying notarized MOA PDF uploads, assigning students to companies (/MOA), and reviewing student MOA unlock requests (/moa/unlock-requests).
  4. STUDENT DIRECTORY & COMPLIANCE: Searching and filtering student cohorts across sections, viewing individual requirements submissions (/coordinator/student-requirements), toggling School In-House OJT status, and batch-syncing student records from GuiSIS (/studentLists).
  5. REPORTS & EXPIRED MOA MANAGEMENT: Generating comprehensive OJT student reports (/OJTReports), generating Expired MOA reports with CSV/PDF exports (/reportsExpired), and sending automated email notices to companies with expiring MOAs.
  6. INSTITUTIONAL ANALYTICS: Viewing university-wide requirement completion charts, modality breakdowns (Onsite/Hybrid/Remote), and exporting analytics reports (/analytics).
  7. DOWNLOADABLE FORMS & TEMPLATES: Uploading and managing official university OJT forms, templates, and guidelines accessible by students (/uploadpage).
  8. SYSTEM MAINTENANCE & AUDIT TRAIL: Managing academic programs/courses, viewing audit logs of administrative actions (/maintenance, /auditlog).
  9. COORDINATOR ACCOUNT SETTINGS: Updating profile details, contact information, and local password (/accountinfo).",
            default => "The user is a VISITOR or GUEST on the InternConnect Landing Page (/) or Login Gateway (/login-gateway).\n- ALLOWED TOPICS:
1. HOW TO GO TO THE MAIN WEBSITE / LOGIN: Click 'Launch Portal' in the top navigation bar or go to the Login Gateway (/login-gateway). Select either 'Student Portal' (/login-gateway?portal=student) or 'Faculty & Staff Portal' (/login-gateway?portal=faculty).
2. PORTAL ROLES:
   - Student Portal: For OJT students to submit MOA, upload requirements, encode training details, and request supervisor evaluations.
   - Faculty & Staff Portal: For Professors (manage class lists, monitor student progress) and OJT Coordinators (manage MOA, sync faculty/students, audit trail).
3. IDENTITY PROVIDER (IdP):
   - What is IdP: Centralized Single Sign-On (SSO) system hosted separately at https://identity-provider.isaxbsit2027.com/.
   - How to create / register an IdP account: Since IdP is a separate system, users without an account must register on the IdP portal. They can click 'Sign In with Identity Provider (IdP)' on the Login Gateway (/login-gateway) or go directly to https://identity-provider.isaxbsit2027.com/ to register. After creating their IdP account, logging into InternConnect will lead first-time students through the 2-step onboarding page (/onboarding) that syncs GuiSIS academic records.
   - What if IdP is down / unreachable: Click 'Use Local Credentials' (/login) to sign in with your email and local password. If you don't have a local password yet, click 'Forgot Password?' (/forgot) to create one via email reset.
4. LOCAL CREDENTIALS:
   - What are Local Credentials: Direct email & password stored securely in InternConnect as a backup login method during IdP outages or university network maintenance.
   - How to get/set Local Credentials: After logging in via IdP, set a local password in Account Settings (/student/accountinfo or /professor/accountinfo), or click 'Forgot Password?' (/forgot) on the local login page.
5. WHAT IS INTERNCONNECT (OJTIMS): Centralized On-the-Job Training Information Management System for PUP Taguig Campus.
6. DEVELOPER TEAM: Developed by 'Team Wards', a five-member BSIT 4-1 student developer team from PUP Taguig ('Guided to Create, Driven to Innovate').
7. CONTACT & SUPPORT: Message via Facebook Messenger ('InternConnect: On-the-Job Training Information Management System') or email 'internconnect.ojtims@gmail.com'."
        };

        return <<<PROMPT
You are "Bud", the friendly and helpful golden retriever mascot and official "OJT Buddy" for InternConnect (PUP Taguig OJT Information Management System).
When greeted, you can introduce yourself: "Hi, my name is Bud, your OJT Buddy! 🐾". You are cheerful, encouraging, clear, and professional.

CURRENT USER ROLE: {$roleName}
{$roleContext}

STRICT ROLE-BASED ACCESS CONTROL (MANDATORY RULE):
You must ONLY provide answers and navigation that belong to the user's specific role ({$roleName}).
1. If a STUDENT asks about coordinator or faculty actions (e.g., how to approve MOAs, sync faculty via FLSS, edit system maintenance, perform database backups, or view other students' records), you MUST POLITELY REFUSE:
   "I cannot answer questions regarding Coordinator or Faculty administrative tools, as this is outside student permissions. For administrative inquiries, please contact your OJT Coordinator."
   and set "escalate": true.
2. If a PROFESSOR asks about Coordinator-only maintenance/backups/FLSS sync, you MUST POLITELY REFUSE:
   "I cannot answer questions regarding Coordinator-level system maintenance or global university settings, as this requires OJT Coordinator privileges."
   and set "escalate": true.

CRITICAL FORMATTING & NAVIGATION RULES (STRICT RULES):
1. ZERO RAW URLS IN TEXT: NEVER include raw URLs, paths, or route slugs (such as `/login`, `/forgot`, `/student/requirements`, `https://...`) directly in your markdown reply text. Always refer to features, views, and destinations by their clear, natural names (e.g., "Login Gateway", "Requirements page", "Account Settings", "Forgot Password page").
2. ACTION BUTTONS FOR NAVIGATION: All links, page transitions, and external sites MUST be provided exclusively through the `"actions"` button array with descriptive labels.

BUG REPORTING & TECHNICAL ISSUES:
When users ask about bugs, glitches, system errors, upload failures, or technical difficulties:
1. Provide quick troubleshooting (hard refresh Ctrl+F5, check PDF under 10MB, try incognito mode).
2. Instruct them to message Team Wards (developers) with proof and complete context: a screenshot/photo of the error, their Student Number, the current page, and their browser/device.
3. Set escalate: true and provide direct Facebook Messenger and Email buttons.

OFFICIAL SUPPORT ESCALATION:
- Email: internconnect.ojtims@gmail.com
- Facebook: https://www.facebook.com/profile.php?id=61593939354633

RESPONSE FORMAT (MUST BE VALID JSON):
{
  "reply": "Your clear, concise markdown response (max 3-4 sentences, no raw URL paths).",
  "actions": [
    { "label": "Button Label", "url": "/page/url", "icon": "fas fa-arrow-right" }
  ],
  "suggestions": [
    "Suggested question 1",
    "Suggested question 2",
    "Suggested question 3"
  ],
  "escalate": false
}
PROMPT;
    }

    /**
     * Fast rule-based matcher for common queries
     */
    protected function checkRuleBasedMatches(string $message, ?string $userRole): ?array
    {
        $lower = strtolower($message);
        $isStudent = ($userRole === 'student' || $userRole === '0' || $userRole === '3');
        $isProfessor = ($userRole === 'professor' || $userRole === 'faculty' || $userRole === '2');

        // Check if a Student is probing Coordinator/Admin operations
        if ($isStudent) {
            $coordinatorProbes = ['flss', 'faculty sync', 'sync faculty', 'manage companies', 'add company', 'approve moa', 'audit log', 'maintenance', 'backup database', 'delete student', 'assign professor', 'coordinator dashboard', 'idp sync', 'sync idp', 'sync guisis pool'];
            foreach ($coordinatorProbes as $probe) {
                if (str_contains($lower, $probe)) {
                    return [
                        'reply' => 'I cannot answer questions regarding Coordinator or Faculty administrative tools, as this is outside student permissions. For administrative inquiries, please contact your OJT Coordinator.',
                        'actions' => [
                            [
                                'label' => 'Message on Facebook',
                                'url' => self::SUPPORT_FACEBOOK,
                                'icon' => 'fab fa-facebook-f',
                                'external' => true
                            ],
                            [
                                'label' => 'Send Support Email',
                                'url' => 'mailto:' . self::SUPPORT_EMAIL,
                                'icon' => 'fas fa-envelope',
                                'external' => true
                            ]
                        ],
                        'suggestions' => [
                            'Where do I submit my MOA?',
                            'How do I sync my profile?',
                            'View partner companies'
                        ],
                        'escalate' => true,
                        'source' => 'system'
                    ];
                }
            }
        }

        // Check if a Professor is probing Coordinator-only maintenance operations
        if ($isProfessor) {
            $adminProbes = ['flss', 'faculty sync', 'maintenance', 'backup database', 'system settings', 'coordinator dashboard'];
            foreach ($adminProbes as $probe) {
                if (str_contains($lower, $probe)) {
                    return [
                        'reply' => 'I cannot answer questions regarding Coordinator-level system maintenance or global university settings, as this requires OJT Coordinator privileges.',
                        'actions' => [
                            [
                                'label' => 'Message on Facebook',
                                'url' => self::SUPPORT_FACEBOOK,
                                'icon' => 'fab fa-facebook-f',
                                'external' => true
                            ]
                        ],
                        'suggestions' => [
                            'View class list',
                            'Check student requirement status',
                            'Student evaluations'
                        ],
                        'escalate' => true,
                        'source' => 'system'
                    ];
                }
            }
        }

        // Professor-specific quick matches
        if ($isProfessor) {
            // Class & Room Management
            if (str_contains($lower, 'create room') || str_contains($lower, 'create class') || str_contains($lower, 'manage class') || str_contains($lower, 'archive room') || str_contains($lower, 'unarchive') || str_contains($lower, 'class list') || str_contains($lower, 'post announcement') || str_contains($lower, 'upload material') || str_contains($lower, 'class announcement') || str_contains($lower, 'my classes')) {
                return [
                    'reply' => 'On the **Class** management page, you can create new section rooms, view enrolled student rosters per class, archive or unarchive semester sections, and upload announcements and syllabus files for your students.',
                    'actions' => [
                        ['label' => 'Go to Class Management', 'url' => '/professor/class', 'icon' => 'fas fa-chalkboard-teacher'],
                        ['label' => 'View Student Requirements', 'url' => '/professor/requirement-status', 'icon' => 'fas fa-tasks']
                    ],
                    'suggestions' => [
                        'How do I review student requirements?',
                        'View supervisor evaluations',
                        'Faculty analytics'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Student Requirement Status & Grading
            if (str_contains($lower, 'requirement status') || str_contains($lower, 'review requirement') || str_contains($lower, 'approve requirement') || str_contains($lower, 'deny requirement') || str_contains($lower, 'approve all') || str_contains($lower, 'grade requirement') || str_contains($lower, 'check submissions') || str_contains($lower, 'student submissions') || str_contains($lower, 'verify documents')) {
                return [
                    'reply' => 'On the **Student Requirement Status** page, you can select your class section to inspect uploaded student PDFs, approve verified documents, deny submissions with specific feedback/remarks for student revision, or click **"Approve All"** to batch-verify all pending submissions.',
                    'actions' => [
                        ['label' => 'Go to Student Requirement Status', 'url' => '/professor/requirement-status', 'icon' => 'fas fa-tasks'],
                        ['label' => 'Class Management', 'url' => '/professor/class', 'icon' => 'fas fa-chalkboard-teacher']
                    ],
                    'suggestions' => [
                        'How do I view supervisor evaluations?',
                        'Class management',
                        'Faculty analytics'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Supervisor Evaluations
            if (str_contains($lower, 'evaluation') || str_contains($lower, 'supervisor evaluation') || str_contains($lower, 'view evaluation') || str_contains($lower, 'evaluation score') || str_contains($lower, 'evaluation rating') || str_contains($lower, 'eval rubric')) {
                return [
                    'reply' => 'On the **Evaluation** page, you can track completed digital supervisor evaluations for your students, review numerical scores and rubric feedback, and print or export evaluation summary records.',
                    'actions' => [
                        ['label' => 'Go to Evaluation Portal', 'url' => '/professor/evaluation', 'icon' => 'fas fa-star-half-alt']
                    ],
                    'suggestions' => [
                        'Check student requirement status',
                        'Faculty analytics',
                        'Class management'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Faculty Analytics & Reporting
            if (str_contains($lower, 'analytics') || str_contains($lower, 'statistics') || str_contains($lower, 'compliance chart') || str_contains($lower, 'export pdf') || str_contains($lower, 'export csv') || str_contains($lower, 'print analytics') || str_contains($lower, 'submission rate') || str_contains($lower, 'modality stats')) {
                return [
                    'reply' => 'On the **Faculty Analytics** page, you can visualize student requirement completion rates, document submission timelines, and placement modalities (Onsite/Hybrid/Remote). You can also export the reports to CSV, PDF, or printable format.',
                    'actions' => [
                        ['label' => 'Go to Faculty Analytics', 'url' => '/professor/analytics', 'icon' => 'fas fa-chart-pie']
                    ],
                    'suggestions' => [
                        'Check student requirement status',
                        'View supervisor evaluations',
                        'Class management'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Master Student Directory
            if (str_contains($lower, 'all student') || str_contains($lower, 'search student') || str_contains($lower, 'master list') || str_contains($lower, 'student roster')) {
                return [
                    'reply' => 'You can search, filter, and view all enrolled OJT students across different class sections on the **All Students** page.',
                    'actions' => [
                        ['label' => 'Go to All Students Directory', 'url' => '/allStudents', 'icon' => 'fas fa-users']
                    ],
                    'suggestions' => [
                        'Class management',
                        'Student requirement status',
                        'Faculty analytics'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Faculty Account Settings
            if (str_contains($lower, 'account settings') || str_contains($lower, 'change password') || str_contains($lower, 'update contact') || str_contains($lower, 'faculty account') || str_contains($lower, 'edit profile')) {
                return [
                    'reply' => 'You can update your faculty profile details, contact phone number, and set or change your local fallback password in **Faculty Account Settings**.',
                    'actions' => [
                        ['label' => 'Go to Faculty Account Settings', 'url' => '/professor/accountinfo', 'icon' => 'fas fa-user-cog']
                    ],
                    'suggestions' => [
                        'Class management',
                        'Student requirement status',
                        'Faculty analytics'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }
        }

        // Coordinator-specific quick matches
        if ($userRole === 'coordinator' || $userRole === '1') {
            // Dashboard Overview
            if (str_contains($lower, 'dashboard') || str_contains($lower, 'overview') || str_contains($lower, 'metrics')) {
                return [
                    'reply' => 'The **Coordinator Dashboard** provides real-time institutional metrics, including total enrolled students, active partner companies, active class rooms, and pending requirement submissions across all departments.',
                    'actions' => [
                        ['label' => 'Go to Coordinator Dashboard', 'url' => '/dashboard', 'icon' => 'fas fa-tachometer-alt'],
                        ['label' => 'View Analytics', 'url' => '/analytics', 'icon' => 'fas fa-chart-pie']
                    ],
                    'suggestions' => [
                        'How do I sync faculty from FLSS?',
                        'Manage partner companies',
                        'View expired MOA reports'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // FLSS Faculty & User Sync
            if (str_contains($lower, 'flss') || str_contains($lower, 'sync faculty') || str_contains($lower, 'professor tab') || str_contains($lower, 'sync professor') || str_contains($lower, 'prune faculty') || str_contains($lower, 'transfer role') || str_contains($lower, 'transfer coordinator') || str_contains($lower, 'faculty sync')) {
                return [
                    'reply' => 'On the **Professors** management tab, you can sync faculty advisers and class schedule assignments directly from the **FLSS (Faculty Load and Schedule System)**, sync accounts from IdP or GuiSIS, prune inactive faculty records, and transfer the Coordinator role.',
                    'actions' => [
                        ['label' => 'Go to Faculty FLSS Sync', 'url' => '/professorTab', 'icon' => 'fas fa-chalkboard-teacher']
                    ],
                    'suggestions' => [
                        'Where do I manage partner companies?',
                        'How to sync students from GuiSIS?',
                        'View expired MOA reports'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // MOA Unlock Requests
            if (str_contains($lower, 'unlock request') || str_contains($lower, 'moa unlock') || str_contains($lower, 'student unlock') || str_contains($lower, 'approve unlock') || str_contains($lower, 'deny unlock')) {
                return [
                    'reply' => 'On the **MOA Unlock Requests** page, you can review student requests to unlock their previously submitted or locked MOAs. You can inspect their submitted reason and either approve the request (to allow student re-upload) or deny it.',
                    'actions' => [
                        ['label' => 'Go to MOA Unlock Requests', 'url' => '/moa/unlock-requests', 'icon' => 'fas fa-unlock-alt'],
                        ['label' => 'Manage Partner Companies', 'url' => '/MOA', 'icon' => 'fas fa-file-contract']
                    ],
                    'suggestions' => [
                        'Manage partner companies',
                        'View expired MOA reports',
                        'Student directory'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Partner Companies & MOAs
            if (str_contains($lower, 'partner compan') || str_contains($lower, 'manage company') || str_contains($lower, 'manage moa') || str_contains($lower, 'company list') || str_contains($lower, 'add company') || str_contains($lower, 'assign student') || str_contains($lower, 'moa page')) {
                return [
                    'reply' => 'On the **Partner Companies (MOA)** page, you can add new partner host training establishments (HTEs), track MOA validity periods (start and expiration dates), verify notarized MOA PDF uploads, and assign enrolled students to companies.',
                    'actions' => [
                        ['label' => 'Go to Partner Companies & MOA', 'url' => '/MOA', 'icon' => 'fas fa-file-contract'],
                        ['label' => 'MOA Unlock Requests', 'url' => '/moa/unlock-requests', 'icon' => 'fas fa-unlock-alt']
                    ],
                    'suggestions' => [
                        'View expired MOA reports',
                        'How do I sync faculty from FLSS?',
                        'Student directory'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Student Directory, Requirements Compliance & Batch GuiSIS Sync
            if ((str_contains($lower, 'student') && (str_contains($lower, 'sync') || str_contains($lower, 'list') || str_contains($lower, 'director') || str_contains($lower, 'pool') || str_contains($lower, 'roster') || str_contains($lower, 'requirement'))) || str_contains($lower, 'in-house toggle')) {
                return [
                    'reply' => 'On the **Students Directory** page, you can search and filter student cohorts across sections, inspect compliance and submitted requirement files, toggle School In-House OJT status, and batch-sync student cohorts from GuiSIS.',
                    'actions' => [
                        ['label' => 'Go to Students Directory & Sync', 'url' => '/studentLists', 'icon' => 'fas fa-users'],
                        ['label' => 'Coordinator Student Requirements', 'url' => '/coordinator/student-requirements', 'icon' => 'fas fa-tasks']
                    ],
                    'suggestions' => [
                        'Manage partner companies',
                        'Faculty FLSS Sync',
                        'System Maintenance'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Reports & Expired MOA Notifications
            if (str_contains($lower, 'expired') || str_contains($lower, 'ojt report') || str_contains($lower, 'generate report') || str_contains($lower, 'send reminder') || str_contains($lower, 'company email') || str_contains($lower, 'expired moa')) {
                return [
                    'reply' => 'On the **Reports** portal, you can generate comprehensive OJT student reports and Expired MOA records (with CSV/PDF exports), as well as send automated email renewal notices to partner companies with expiring MOAs.',
                    'actions' => [
                        ['label' => 'Go to Expired MOA Reports', 'url' => '/reportsExpired', 'icon' => 'fas fa-file-excel'],
                        ['label' => 'Go to OJT Reports', 'url' => '/reports', 'icon' => 'fas fa-file-pdf']
                    ],
                    'suggestions' => [
                        'Manage partner companies',
                        'Coordinator Analytics',
                        'Student directory'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Institutional Analytics & Charts
            if (str_contains($lower, 'analytics') || str_contains($lower, 'statistics') || str_contains($lower, 'compliance chart') || str_contains($lower, 'completion rate') || str_contains($lower, 'modality stats') || str_contains($lower, 'export csv') || str_contains($lower, 'export pdf')) {
                return [
                    'reply' => 'On the **Coordinator Analytics** dashboard, you can visualize university-wide student placement rates, requirement completion trends across degree programs, and placement modality distributions (Onsite/Hybrid/Remote), with PDF and CSV export tools.',
                    'actions' => [
                        ['label' => 'Go to Coordinator Analytics', 'url' => '/analytics', 'icon' => 'fas fa-chart-pie']
                    ],
                    'suggestions' => [
                        'View expired MOA reports',
                        'Manage partner companies',
                        'Student directory'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Official Downloadable Templates Management
            if (str_contains($lower, 'upload template') || str_contains($lower, 'official form') || str_contains($lower, 'manage download') || str_contains($lower, 'downloadable file') || str_contains($lower, 'student template') || str_contains($lower, 'upload form') || str_contains($lower, 'upload file') || str_contains($lower, 'upload page')) {
                return [
                    'reply' => 'On the **Upload Templates** page, you can upload and manage official university OJT forms, waiver templates, and guidelines that students can view and download from their Downloadable Files page.',
                    'actions' => [
                        ['label' => 'Go to Upload Templates', 'url' => '/uploadpage', 'icon' => 'fas fa-file-upload']
                    ],
                    'suggestions' => [
                        'Manage partner companies',
                        'Student directory',
                        'System Maintenance'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // System Maintenance, Courses & Audit Trail
            if (str_contains($lower, 'maintenance') || str_contains($lower, 'backup') || str_contains($lower, 'audit') || str_contains($lower, 'course') || str_contains($lower, 'program') || str_contains($lower, 'audit log') || str_contains($lower, 'system log')) {
                return [
                    'reply' => 'In the **Maintenance** portal, you can configure academic degree programs and course offerings, while the **Audit Log** provides a complete chronological record of administrative actions and system events.',
                    'actions' => [
                        ['label' => 'Go to System Maintenance', 'url' => '/maintenance', 'icon' => 'fas fa-cogs'],
                        ['label' => 'View Audit Log', 'url' => '/auditlog', 'icon' => 'fas fa-history']
                    ],
                    'suggestions' => [
                        'Manage partner companies',
                        'Faculty FLSS Sync',
                        'Students directory'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }

            // Coordinator Account Settings
            if (str_contains($lower, 'account settings') || str_contains($lower, 'change password') || str_contains($lower, 'update contact') || str_contains($lower, 'coordinator account') || str_contains($lower, 'edit profile')) {
                return [
                    'reply' => 'You can update your coordinator profile details, contact phone number, and set or change your local login password in your **Account Settings**.',
                    'actions' => [
                        ['label' => 'Go to Account Settings', 'url' => '/accountinfo', 'icon' => 'fas fa-user-cog']
                    ],
                    'suggestions' => [
                        'Go to Coordinator Dashboard',
                        'Faculty FLSS Sync',
                        'Manage partner companies'
                    ],
                    'escalate' => false,
                    'source' => 'system'
                ];
            }
        }

        // Landing Page: Launch Portal / How to go to main website / Login Gateway / What to do here
        if (str_contains($lower, 'main website') || str_contains($lower, 'launch portal') || str_contains($lower, 'go to portal') || str_contains($lower, 'where do i login') || str_contains($lower, 'how to login') || str_contains($lower, 'how do i login') || str_contains($lower, 'how to log in') || str_contains($lower, 'how do i log in') || str_contains($lower, 'where to login') || str_contains($lower, 'where to log in') || str_contains($lower, 'what to do here') || str_contains($lower, 'how to start') || str_contains($lower, 'where to go') || str_contains($lower, 'login page') || str_contains($lower, 'gateway') || str_contains($lower, 'enter system')) {
            return [
                'reply' => 'To enter the main InternConnect system, click the **Launch Portal** button in the top navigation bar (or use the button below). From the Login Gateway, you can select whether to sign in as a **Student** or as a **Faculty / Coordinator**.',
                'actions' => [
                    [
                        'label' => 'Launch Portal Gateway',
                        'url' => '/login-gateway',
                        'icon' => 'fas fa-sign-in-alt'
                    ],
                    [
                        'label' => 'Student Login',
                        'url' => '/login-gateway?portal=student',
                        'icon' => 'fas fa-user-graduate'
                    ],
                    [
                        'label' => 'Faculty & Coordinator Login',
                        'url' => '/login-gateway?portal=faculty',
                        'icon' => 'fas fa-chalkboard-teacher'
                    ]
                ],
                'suggestions' => [
                    'What is InternConnect?',
                    'Who developed InternConnect?',
                    'How do I contact support?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Landing Page: What is InternConnect / OJTIMS / Features
        if (str_contains($lower, 'what is internconnect') || str_contains($lower, 'what is ojtims') || str_contains($lower, 'about internconnect') || str_contains($lower, 'about this site') || str_contains($lower, 'what does this system do') || str_contains($lower, 'what does internconnect do') || str_contains($lower, 'system features')) {
            return [
                'reply' => '**InternConnect: OJTIMS** is the centralized On-the-Job Training Information Management System for **PUP Taguig Campus**. It streamlines the entire internship journey, including MOA submissions, requirements verification, host company directory, and supervisor evaluations.',
                'actions' => [
                    [
                        'label' => 'Launch Portal',
                        'url' => '/login-gateway',
                        'icon' => 'fas fa-sign-in-alt'
                    ]
                ],
                'suggestions' => [
                    'How do I go to the main website?',
                    'Who developed this system?',
                    'Contact support'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Landing Page: Team Wards / Developers
        if (str_contains($lower, 'team wards') || str_contains($lower, 'who developed') || str_contains($lower, 'who created') || str_contains($lower, 'who made this') || str_contains($lower, 'developer team') || str_contains($lower, 'developers') || str_contains($lower, 'authors')) {
            return [
                'reply' => 'InternConnect was developed by **Team Wards**, a five-member student developer team of BSIT 4-1 students from **PUP Taguig Campus** under the motto: *"Guided to Create, Driven to Innovate"*. You can click the right arrow on the home hero card to view Team Wards!',
                'actions' => [
                    [
                        'label' => 'Launch Portal',
                        'url' => '/login-gateway',
                        'icon' => 'fas fa-sign-in-alt'
                    ]
                ],
                'suggestions' => [
                    'How do I go to the main website?',
                    'What is InternConnect?',
                    'Contact support'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login Gateway: What is IDP (Identity Provider)
        if (str_contains($lower, 'what is idp') || str_contains($lower, 'what is identity provider') || str_contains($lower, 'idp meaning') || str_contains($lower, 'explain idp') || str_contains($lower, 'about idp') || str_contains($lower, 'why use idp') || str_contains($lower, 'idp system')) {
            return [
                'reply' => '**Identity Provider (IdP)** is a separate centralized Single Sign-On (SSO) system. It enables students, faculty, and coordinators to authenticate across university portals using unified institutional credentials without managing separate passwords.',
                'actions' => [
                    [
                        'label' => 'IdP Portal Website',
                        'url' => 'https://identity-provider.isaxbsit2027.com/',
                        'icon' => 'fas fa-external-link-alt',
                        'external' => true
                    ],
                    [
                        'label' => 'Launch Portal Gateway',
                        'url' => '/login-gateway',
                        'icon' => 'fas fa-sign-in-alt'
                    ],
                    [
                        'label' => 'Use Local Login',
                        'url' => '/login',
                        'icon' => 'fas fa-key'
                    ]
                ],
                'suggestions' => [
                    'How do I create an IDP account?',
                    'IDP is down, what should I do?',
                    'What are Local Credentials?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login Gateway: How to create an IDP account / Register IDP / First-time setup
        if (str_contains($lower, 'create idp') || str_contains($lower, 'create an idp') || str_contains($lower, 'make an idp') || str_contains($lower, 'register idp') || str_contains($lower, 'get an idp') || str_contains($lower, 'how to get idp') || str_contains($lower, 'first time idp') || str_contains($lower, 'idp account') || str_contains($lower, 'idp link') || str_contains($lower, 'idp website') || str_contains($lower, 'idp portal') || str_contains($lower, 'dont have idp') || str_contains($lower, "don't have idp") || str_contains($lower, 'no idp account')) {
            return [
                'reply' => 'Because **Identity Provider (IdP)** is a separate external system, you will need to register on their portal if you do not have an account yet. You can visit the IdP Portal directly or click **"Sign In with Identity Provider (IdP)"** on the Login Gateway to be redirected there. Once registered, signing into InternConnect will guide first-time students through the onboarding process.',
                'actions' => [
                    [
                        'label' => 'Go to IdP Portal to Register',
                        'url' => 'https://identity-provider.isaxbsit2027.com/',
                        'icon' => 'fas fa-external-link-alt',
                        'external' => true
                    ],
                    [
                        'label' => 'Launch Portal Gateway',
                        'url' => '/login-gateway',
                        'icon' => 'fas fa-sign-in-alt'
                    ],
                    [
                        'label' => 'Use Local Login',
                        'url' => '/login',
                        'icon' => 'fas fa-key'
                    ]
                ],
                'suggestions' => [
                    'What is IDP?',
                    'IDP is down, what should I do?',
                    'What are Local Credentials?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login Gateway: IDP is Down / Unavailable / Error
        if (str_contains($lower, 'idp is down') || str_contains($lower, 'idp down') || str_contains($lower, 'idp offline') || str_contains($lower, 'idp error') || str_contains($lower, 'cannot connect to idp') || str_contains($lower, 'idp not working') || str_contains($lower, 'idp unavailable') || str_contains($lower, 'unable to connect to identity provider') || str_contains($lower, 'idp issue') || str_contains($lower, 'idp problem')) {
            return [
                'reply' => "If the Identity Provider (IdP) is offline or unavailable:\n1. Click **\"Use Local Credentials\"** on the Login Gateway (or use the button below).\n2. Sign in with your registered email and local password.\n3. If you don't have a local password yet, use the **\"Forgot / Reset Password\"** button below to set one via email reset.\n4. If you still need help, reach out to our support team.",
                'actions' => [
                    [
                        'label' => 'Use Local Login',
                        'url' => '/login',
                        'icon' => 'fas fa-key'
                    ],
                    [
                        'label' => 'Forgot / Reset Password',
                        'url' => '/forgot',
                        'icon' => 'fas fa-unlock-alt'
                    ],
                    [
                        'label' => 'Back to Gateway',
                        'url' => '/login-gateway',
                        'icon' => 'fas fa-arrow-left'
                    ]
                ],
                'suggestions' => [
                    'What are Local Credentials?',
                    'How do I get local credentials?',
                    'How do I contact support?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login Gateway: What are Local Credentials
        if (str_contains($lower, 'what is local credentials') || str_contains($lower, 'what are local credentials') || str_contains($lower, 'local credentials meaning') || str_contains($lower, 'what is local login') || str_contains($lower, 'explain local credentials') || str_contains($lower, 'local vs idp') || str_contains($lower, 'local password meaning')) {
            return [
                'reply' => '**Local Credentials** refer to your direct email and password stored securely within InternConnect. They provide a reliable fallback sign-in method so you can always access your account even when university Single Sign-On (IdP) is undergoing maintenance or experiencing downtime.',
                'actions' => [
                    [
                        'label' => 'Use Local Login',
                        'url' => '/login',
                        'icon' => 'fas fa-key'
                    ],
                    [
                        'label' => 'Set Local Password (Forgot)',
                        'url' => '/forgot',
                        'icon' => 'fas fa-unlock-alt'
                    ]
                ],
                'suggestions' => [
                    'How do I get local credentials?',
                    'IDP is down, what should I do?',
                    'What is IDP?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login Gateway: How to get / set Local Credentials
        if (str_contains($lower, 'how to get local credentials') || str_contains($lower, 'how do i get local credentials') || str_contains($lower, 'set local credentials') || str_contains($lower, 'create local password') || str_contains($lower, 'setup local credentials') || str_contains($lower, 'set local password') || str_contains($lower, 'how to get local password') || str_contains($lower, 'how do i get local password')) {
            return [
                'reply' => "You can set up local credentials in two ways:\n1. **From Account Settings**: After signing in via IdP, navigate to your **Account Settings** page and set a local password.\n2. **Via Forgot Password**: On the sign-in page, click **\"Forgot Password?\"** (or use the button below), enter your registered email, and create a password using the reset link sent to your inbox.",
                'actions' => [
                    [
                        'label' => 'Forgot Password (Set Password)',
                        'url' => '/forgot',
                        'icon' => 'fas fa-unlock-alt'
                    ],
                    [
                        'label' => 'Local Login Page',
                        'url' => '/login',
                        'icon' => 'fas fa-key'
                    ]
                ],
                'suggestions' => [
                    'What are Local Credentials?',
                    'IDP is down, what should I do?',
                    'What is IDP?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login Gateway: Role Buttons / Portal Selection (Student vs Faculty)
        if (str_contains($lower, 'role buttons') || str_contains($lower, 'which portal') || str_contains($lower, 'what portal') || str_contains($lower, 'student or faculty') || str_contains($lower, 'difference between student and faculty') || str_contains($lower, 'portal options') || str_contains($lower, 'select portal') || str_contains($lower, 'choose portal') || str_contains($lower, 'student portal vs') || str_contains($lower, 'faculty portal vs')) {
            return [
                'reply' => "InternConnect provides two dedicated user portals:\n- 🎓 **Student Portal**: For enrolled OJT students and trainees to submit MOAs, upload requirement files, and send supervisor evaluations.\n- 👨‍🏫 **Faculty & Staff Portal**: For Professors and Advisers to monitor student progress, and OJT Coordinators with administrative tools.\n\nYou can switch portals anytime using the **\"Change User Portal\"** button.",
                'actions' => [
                    [
                        'label' => 'Student Portal',
                        'url' => '/login-gateway?portal=student',
                        'icon' => 'fas fa-graduation-cap'
                    ],
                    [
                        'label' => 'Faculty & Staff Portal',
                        'url' => '/login-gateway?portal=faculty',
                        'icon' => 'fas fa-user-shield'
                    ]
                ],
                'suggestions' => [
                    'What is IDP?',
                    'What are Local Credentials?',
                    'IDP is down, what should I do?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Login / Auth: Forgot Password / Password Reset / Cannot Login
        if (str_contains($lower, 'forgot password') || str_contains($lower, 'reset password') || str_contains($lower, 'how to reset password') || str_contains($lower, 'change password') || str_contains($lower, 'cant log in') || str_contains($lower, 'cant login') || str_contains($lower, 'cannot log in') || str_contains($lower, 'cannot login') || str_contains($lower, 'locked out') || str_contains($lower, 'trouble logging in')) {
            return [
                'reply' => 'If you are having trouble logging in or forgot your local password, click **"Forgot Password?"** (or use the button below). Enter your registered PUP email address to receive a secure password reset link.',
                'actions' => [
                    [
                        'label' => 'Go to Forgot Password',
                        'url' => '/forgot',
                        'icon' => 'fas fa-unlock-alt'
                    ],
                    [
                        'label' => 'Local Login',
                        'url' => '/login',
                        'icon' => 'fas fa-key'
                    ]
                ],
                'suggestions' => [
                    'IDP is down, what should I do?',
                    'What are Local Credentials?',
                    'How do I contact support?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Landing Page: Student Registration / New Account
        if (str_contains($lower, 'how to register') || str_contains($lower, 'create account') || str_contains($lower, 'sign up') || str_contains($lower, 'new student') || str_contains($lower, 'how do i register') || str_contains($lower, 'first time user') || str_contains($lower, 'account activation')) {
            return [
                'reply' => 'If you are a student setting up your account, click **Launch Portal** -> **Student Portal**. First-time users can sign in with IdP for automated GuiSIS onboarding or register using their official **PUP Webmail** and **Student Number**.',
                'actions' => [
                    [
                        'label' => 'Go to Student Login',
                        'url' => '/login-gateway?portal=student',
                        'icon' => 'fas fa-user-graduate'
                    ],
                    [
                        'label' => 'Register Account',
                        'url' => '/registration',
                        'icon' => 'fas fa-user-plus'
                    ]
                ],
                'suggestions' => [
                    'What is IDP?',
                    'How do I create an IDP account?',
                    'How do I contact support?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Bugs / Glitches / System Errors / Upload Failures
        if (str_contains($lower, 'bug') || str_contains($lower, 'glitch') || str_contains($lower, 'error') || str_contains($lower, 'crash') || str_contains($lower, 'failed to upload') || str_contains($lower, 'upload error') || str_contains($lower, 'upload failed') || str_contains($lower, 'something went wrong') || str_contains($lower, 'page expired') || str_contains($lower, '419') || str_contains($lower, '500') || str_contains($lower, 'broken') || str_contains($lower, 'technical issue') || str_contains($lower, 'report issue') || str_contains($lower, 'report a bug') || str_contains($lower, 'system error') || str_contains($lower, 'blank screen') || str_contains($lower, 'not responding') || str_contains($lower, 'not working')) {
            return [
                'reply' => "If you are encountering a system glitch, error message, or unexpected behavior, you can try these quick troubleshooting steps:\n\n1. **Hard Refresh & Clear Cache**: Press `Ctrl + F5` (Windows) or `Cmd + Shift + R` (Mac) to reload fresh assets.\n2. **File Formats & Size**: For document uploads, verify your file is in **PDF format** and under **10MB**.\n3. **Try Incognito Mode**: Open a private window to bypass cached sessions.\n\n🛠️ **If the issue persists, please report it to our developer team (Team Wards)!** When messaging us, please include:\n- 📸 **Screenshot / proof** of the error\n- 👤 **Your Student Number** and current page\n- 💻 **Your device & browser** (e.g. Chrome on Windows, Safari on iOS)",
                'actions' => [
                    [
                        'label' => 'Message on Facebook (Send Proof)',
                        'url' => self::SUPPORT_FACEBOOK,
                        'icon' => 'fab fa-facebook-f',
                        'external' => true
                    ],
                    [
                        'label' => 'Email Developer Support',
                        'url' => 'mailto:' . self::SUPPORT_EMAIL,
                        'icon' => 'fas fa-envelope',
                        'external' => true
                    ]
                ],
                'suggestions' => [
                    'Where do I upload requirements?',
                    'How to sync with GuiSIS?',
                    'What are the OJT phases?'
                ],
                'escalate' => true,
                'source' => 'system'
            ];
        }

        // Contact / Socials / Help escalation
        if (str_contains($lower, 'contact support') || str_contains($lower, 'contact coordinator') || str_contains($lower, 'contact us') || str_contains($lower, 'how to contact') || str_contains($lower, 'facebook') || str_contains($lower, 'support email') || str_contains($lower, 'social') || str_contains($lower, 'talk to human') || str_contains($lower, 'coordinator email') || str_contains($lower, 'location') || str_contains($lower, 'office hours') || ($lower === 'contact' || $lower === 'help')) {
            return [
                'reply' => 'You can reach out to our OJT Coordinators directly through our official channels. Message us on Facebook, send an email, or visit our office at PUP Taguig Campus (Mon-Fri, 8AM-5PM).',
                'actions' => [
                    [
                        'label' => 'Message on Facebook',
                        'url' => self::SUPPORT_FACEBOOK,
                        'icon' => 'fab fa-facebook-f',
                        'external' => true
                    ],
                    [
                        'label' => 'Send Email',
                        'url' => 'mailto:' . self::SUPPORT_EMAIL,
                        'icon' => 'fas fa-envelope',
                        'external' => true
                    ]
                ],
                'suggestions' => [
                    'Where do I upload requirements?',
                    'How to sync with GuiSIS?',
                    'View partner companies'
                ],
                'escalate' => true,
                'source' => 'system'
            ];
        }

        // Module 4: Supervisor Contact Details in OJT Info
        if ($isStudent && (str_contains($lower, 'why supervisor') || str_contains($lower, 'supervisor email') || str_contains($lower, 'supervisor contact in ojt') || str_contains($lower, 'why enter supervisor'))) {
            return [
                'reply' => "Your supervisor's email and contact details are required so that InternConnect can send them the secure digital **Supervisor Evaluation Form** upon completing your internship hours, and so your faculty adviser can verify your placement.",
                'actions' => [
                    [
                        'label' => 'Go to OJT Information',
                        'url' => '/student/ojtinfo',
                        'icon' => 'fas fa-user-tie'
                    ],
                    [
                        'label' => 'Go to Evaluation',
                        'url' => '/student/evaluation',
                        'icon' => 'fas fa-star-half-alt'
                    ]
                ],
                'suggestions' => [
                    'Where do I encode my OJT Info?',
                    'How do I send evaluation to supervisor?',
                    'Where do I upload requirements?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 4: OJT Information / Company Details / Supervisor / Schedule / Placement
        if (str_contains($lower, 'ojt info') || str_contains($lower, 'ojt details') || str_contains($lower, 'update company') || str_contains($lower, 'supervisor contact') || str_contains($lower, 'internship role') || str_contains($lower, 'assigned department') || str_contains($lower, 'schedule') || str_contains($lower, 'modality') || str_contains($lower, 'encode ojt') || str_contains($lower, 'training details') || str_contains($lower, 'placement')) {
            return [
                'reply' => 'On the **OJT Information** page, you can encode and update your Host Training Establishment (HTE) information, including company address, assigned department, internship role, supervisor name, email, contact number, work modality (Onsite, Hybrid, or Remote), and weekly training schedule.',
                'actions' => [
                    [
                        'label' => 'Go to OJT Information',
                        'url' => '/student/ojtinfo',
                        'icon' => 'fas fa-layer-group'
                    ]
                ],
                'suggestions' => [
                    'How do I submit my MOA?',
                    'Where do I upload requirements?',
                    'What are the OJT phases?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 5: Denied Requirements & Re-uploading
        if ($isStudent && (str_contains($lower, 'denied') || str_contains($lower, 'rejected requirement') || str_contains($lower, 'requirement denied') || str_contains($lower, 'document denied') || str_contains($lower, 're-upload') || str_contains($lower, 'reupload') || str_contains($lower, 'failed requirement') || str_contains($lower, 'declined requirement') || str_contains($lower, 'professor remark'))) {
            return [
                'reply' => 'If a requirement is denied, check the **remarks and feedback** left by your professor on the **Requirements** page to see what needs revision. Once you have corrected the document, click **Re-upload** to submit your updated PDF for review.',
                'actions' => [
                    [
                        'label' => 'Go to Requirements (Re-upload)',
                        'url' => '/student/requirements',
                        'icon' => 'fas fa-redo-alt'
                    ]
                ],
                'suggestions' => [
                    'Who is my assigned adviser?',
                    'Where do I download templates?',
                    'What are the OJT phases?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 5: Requirement Status Badges (Pending, Approved, Denied)
        if ($isStudent && (str_contains($lower, 'status badge') || str_contains($lower, 'status of requirement') || str_contains($lower, 'pending requirement') || str_contains($lower, 'approved requirement') || str_contains($lower, 'know if approved') || str_contains($lower, 'check status') || str_contains($lower, 'submission status') || (str_contains($lower, 'status') && str_contains($lower, 'requirement')))) {
            return [
                'reply' => "Each document on the **Requirements** page features a real-time review status badge:\n- 🟡 **Pending**: Awaiting review by your faculty adviser.\n- 🟢 **Approved**: Verified and accepted.\n- 🔴 **Denied**: Requires revision based on professor feedback.",
                'actions' => [
                    [
                        'label' => 'Go to Requirements',
                        'url' => '/student/requirements',
                        'icon' => 'fas fa-tasks'
                    ]
                ],
                'suggestions' => [
                    'What if my requirement is denied?',
                    'What are the OJT phases?',
                    'How to request supervisor evaluation?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 2: Professor Uploads / Class Files / Announcements
        if ($isStudent && (str_contains($lower, 'professor upload') || str_contains($lower, 'adviser upload') || str_contains($lower, 'class file') || str_contains($lower, 'class material') || str_contains($lower, 'class announcement') || str_contains($lower, 'syllabus') || (str_contains($lower, 'professor') && (str_contains($lower, 'upload') || str_contains($lower, 'file') || str_contains($lower, 'announcement'))))) {
            return [
                'reply' => 'Files, announcements, and learning materials uploaded by your Professor can be viewed and downloaded directly on your **My Class** page.',
                'actions' => [
                    [
                        'label' => 'Go to My Class',
                        'url' => '/student/class',
                        'icon' => 'fas fa-clipboard'
                    ]
                ],
                'suggestions' => [
                    'Where do I download coordinator templates?',
                    'Where do I upload requirements?',
                    'How to submit MOA?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // My Class & Adviser Info
        if (str_contains($lower, 'my class') || str_contains($lower, 'adviser') || str_contains($lower, 'professor') || str_contains($lower, 'section') || str_contains($lower, 'room')) {
            $url = $isStudent ? '/student/class' : '/professor/class';
            $label = $isStudent ? 'Go to My Class' : 'Go to Class List';

            return [
                'reply' => 'You can view your enrolled class section, course schedule, and assigned faculty adviser details on your class page.',
                'actions' => [
                    [
                        'label' => $label,
                        'url' => $url,
                        'icon' => 'fas fa-clipboard'
                    ]
                ],
                'suggestions' => [
                    'Where do I upload requirements?',
                    'View OJT Information',
                    'Contact support'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 2: Downloadable Templates / Coordinator Uploads / Official Forms
        if (str_contains($lower, 'download') || str_contains($lower, 'template') || str_contains($lower, 'coordinator upload') || str_contains($lower, 'forms') || str_contains($lower, 'official files') || str_contains($lower, 'blank form')) {
            $actions = [];
            if ($isStudent) {
                $actions = [
                    ['label' => 'Go to Downloadable Files', 'url' => '/student/files', 'icon' => 'fas fa-download'],
                    ['label' => 'Go to My Class', 'url' => '/student/class', 'icon' => 'fas fa-clipboard']
                ];
            } elseif ($isProfessor) {
                $actions = [
                    ['label' => 'Go to Class Materials', 'url' => '/professor/class', 'icon' => 'fas fa-clipboard']
                ];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions = [
                    ['label' => 'Go to Upload Templates', 'url' => '/uploadpage', 'icon' => 'fas fa-file-upload'],
                    ['label' => 'Coordinator Dashboard', 'url' => '/dashboard', 'icon' => 'fas fa-chart-line']
                ];
            } else {
                $actions = [
                    ['label' => 'Launch Portal Gateway', 'url' => '/login-gateway', 'icon' => 'fas fa-sign-in-alt']
                ];
            }

            return [
                'reply' => ($userRole === 'coordinator' || $userRole === '1')
                    ? 'On the **Upload Templates** page, you can upload and manage official university OJT forms, waiver templates, and guidelines that students can view and download on their Downloadable Files page.'
                    : 'Official forms and document templates uploaded by your OJT Coordinator (such as MOA templates, submission vouchers, and guideline forms) can be downloaded from the **Downloadable Files** page. Meanwhile, materials uploaded by your Professor are located on the **My Class** page.',
                'actions' => $actions,
                'suggestions' => ($userRole === 'coordinator' || $userRole === '1')
                    ? [
                        'Manage partner companies',
                        'Student directory',
                        'Coordinator Dashboard'
                    ]
                    : [
                        'Where do I submit my MOA?',
                        'Where do I upload requirements?',
                        'View partner companies'
                    ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 6: Resend Evaluation Link / Email Not Received / Cancel Request
        if ($isStudent && (str_contains($lower, 'resend evaluation') || str_contains($lower, 'resend link') || str_contains($lower, 'did not receive') || str_contains($lower, 'didnt receive') || str_contains($lower, "didn't receive") || str_contains($lower, 'not receive') || str_contains($lower, 'not received') || str_contains($lower, 'supervisor email not received') || str_contains($lower, 'cancel evaluation'))) {
            return [
                'reply' => 'If your supervisor hasn\'t received the evaluation link, go to the **Evaluation** page and click **"Resend Evaluation Link"**. If there was a typo in the email address, you can cancel the pending request and re-send it with the corrected email address.',
                'actions' => [
                    [
                        'label' => 'Go to Evaluation Portal',
                        'url' => '/student/evaluation',
                        'icon' => 'fas fa-paper-plane'
                    ]
                ],
                'suggestions' => [
                    'How do I send evaluation to supervisor?',
                    'Where do I check my evaluation score?',
                    'Contact support'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 6: View Evaluation Score / Grade / Rubric Results
        if ($isStudent && (str_contains($lower, 'evaluation score') || str_contains($lower, 'evaluation grade') || str_contains($lower, 'view evaluation') || str_contains($lower, 'check evaluation') || str_contains($lower, 'supervisor rating') || str_contains($lower, 'evaluation result') || str_contains($lower, 'grade in evaluation') || str_contains($lower, 'evaluation rubric'))) {
            return [
                'reply' => 'Once your supervisor completes the rubric and your faculty adviser reviews and releases the result, you can view the complete numerical rating and rubric breakdown on your **Evaluation** page.',
                'actions' => [
                    [
                        'label' => 'Go to Evaluation Portal',
                        'url' => '/student/evaluation',
                        'icon' => 'fas fa-award'
                    ]
                ],
                'suggestions' => [
                    'Where do I upload requirements?',
                    'What are the OJT phases?',
                    'Who is my assigned adviser?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 6: Send Digital Evaluation Link / General Evaluation Portal
        if (str_contains($lower, 'evaluation') || str_contains($lower, 'grade') || str_contains($lower, 'eval') || str_contains($lower, 'supervisor grade') || str_contains($lower, 'send evaluation')) {
            $actions = [];
            if ($isStudent) {
                $actions = [['label' => 'Go to Evaluation Portal', 'url' => '/student/evaluation', 'icon' => 'fas fa-star-half-alt']];
            } elseif ($isProfessor) {
                $actions = [['label' => 'Go to Evaluation Portal', 'url' => '/professor/evaluation', 'icon' => 'fas fa-star-half-alt']];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions = [['label' => 'Coordinator Dashboard', 'url' => '/dashboard', 'icon' => 'fas fa-chart-line']];
            } else {
                $actions = [['label' => 'Launch Portal', 'url' => '/login-gateway', 'icon' => 'fas fa-sign-in-alt']];
            }

            return [
                'reply' => $isStudent
                    ? 'On the **Evaluation** page, you can enter your company supervisor\'s name and email address to send them the digital evaluation form link, track their submission status, and view your released grade.'
                    : 'Supervisor evaluations can be tracked and generated directly from the Evaluation portal.',
                'actions' => $actions,
                'suggestions' => [
                    'What if my supervisor didn\'t get the email?',
                    'Where do I view my evaluation score?',
                    'What are the OJT phases?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Requirement Phases & Workflow (2 Phases: Basic Phase & Other Phase)
        if (str_contains($lower, 'phase') || str_contains($lower, 'explain the phase') || str_contains($lower, 'explain requirement') || str_contains($lower, 'requirement phase') || str_contains($lower, 'basic phase') || str_contains($lower, 'other phase') || str_contains($lower, 'basic requirement') || str_contains($lower, 'other requirement') || str_contains($lower, 'how many phase') || str_contains($lower, 'stages of ojt') || str_contains($lower, 'ojt step') || str_contains($lower, 'ojt process') || str_contains($lower, 'ojt roadmap') || str_contains($lower, 'why locked') || str_contains($lower, 'why is it locked') || str_contains($lower, 'unlock requirement') || str_contains($lower, 'unlock other')) {
            $actions = [];
            if ($isStudent) {
                $actions = [
                    ['label' => 'Go to Requirements', 'url' => '/student/requirements', 'icon' => 'fas fa-cloud-upload-alt'],
                    ['label' => 'Go to Notarized MOA', 'url' => '/student/MOA', 'icon' => 'fas fa-file-contract']
                ];
            } elseif ($isProfessor) {
                $actions = [
                    ['label' => 'Student Requirement Status', 'url' => '/professor/requirement-status', 'icon' => 'fas fa-tasks'],
                    ['label' => 'Class List', 'url' => '/professor/class', 'icon' => 'fas fa-users']
                ];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions = [
                    ['label' => 'Student Directory', 'url' => '/studentLists', 'icon' => 'fas fa-users'],
                    ['label' => 'Partner Companies', 'url' => '/MOA', 'icon' => 'fas fa-file-contract']
                ];
            } else {
                $actions = [
                    ['label' => 'Student Portal Login', 'url' => '/login-gateway?portal=student', 'icon' => 'fas fa-graduation-cap']
                ];
            }

            return [
                'reply' => "InternConnect organizes document submissions into **2 distinct phases**:\n\n1. 🟢 **Basic Phase (Basic Requirements)**: These are **always open** and required before starting your internship. They include your prerequisite clearances (*Resume, Medical Clearance, Good Moral, Consent Form, Endorsement Letter, Acceptance Letter*) plus submitting your **Notarized MOA** (or declaring **School In-House OJT**).\n\n2. 🔒 **Other Phase (Other Requirements)**: These are **locked by default** and will **automatically unlock** once all Basic Requirements and your Notarized MOA are completed. They include your ongoing training and completion documents (*DTRs, Weekly Reports, Certificate of Completion, Final Narrative Report*).",
                'actions' => $actions,
                'suggestions' => [
                    'How do I submit my MOA?',
                    'Where do I upload requirements?',
                    'What is School In-House OJT?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 3: School In-House OJT
        if ($isStudent && (str_contains($lower, 'in-house') || str_contains($lower, 'inhouse') || str_contains($lower, 'in house') || str_contains($lower, 'school ojt') || str_contains($lower, 'campus ojt') || str_contains($lower, 'on-campus ojt'))) {
            return [
                'reply' => 'If you are completing your internship on campus within PUP offices or laboratories, go to the **Notarized MOA** page and toggle the **"School In-House OJT"** option. This marks your placement as internal campus training, waiving the external company MOA requirement.',
                'actions' => [
                    [
                        'label' => 'Go to Notarized MOA (Toggle In-House)',
                        'url' => '/student/MOA',
                        'icon' => 'fas fa-university'
                    ]
                ],
                'suggestions' => [
                    'Where do I encode my OJT Info?',
                    'Where do I upload requirements?',
                    'What are the OJT phases?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 3: Request Unlock for MOA
        if ($isStudent && (str_contains($lower, 'unlock moa') || str_contains($lower, 'moa is locked') || str_contains($lower, 'locked moa') || str_contains($lower, 'edit locked') || str_contains($lower, 'change locked') || str_contains($lower, 'request unlock'))) {
            return [
                'reply' => 'If your MOA submission is locked or already approved, click the **"Request Unlock"** button on the **Notarized MOA** page and provide your reason. Your OJT Coordinator will review the request and unlock it for editing.',
                'actions' => [
                    [
                        'label' => 'Go to Notarized MOA',
                        'url' => '/student/MOA',
                        'icon' => 'fas fa-unlock-alt'
                    ]
                ],
                'suggestions' => [
                    'How do I submit a new MOA?',
                    'Where do I download forms?',
                    'Contact coordinator'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 3: MOA Submission Voucher
        if ($isStudent && (str_contains($lower, 'voucher') || str_contains($lower, 'submission voucher') || str_contains($lower, 'print voucher') || str_contains($lower, 'voucher code'))) {
            return [
                'reply' => 'After uploading your notarized MOA in the system, you can generate and print the **MOA Submission Voucher**. Attach this printed voucher to your physical notarized MOA hardcopy when submitting it to your OJT Coordinator as proof of online submission.',
                'actions' => [
                    [
                        'label' => 'Go to Notarized MOA (Print Voucher)',
                        'url' => '/student/MOA',
                        'icon' => 'fas fa-receipt'
                    ]
                ],
                'suggestions' => [
                    'How do I submit my MOA?',
                    'What are the OJT phases?',
                    'Where do I encode my OJT Info?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 3: Link Existing MOA / Active Partner Companies
        if ($isStudent && (str_contains($lower, 'link moa') || str_contains($lower, 'link existing moa') || str_contains($lower, 'link partner') || str_contains($lower, 'link company') || str_contains($lower, 'partner company') || str_contains($lower, 'partner companies') || str_contains($lower, 'active partner'))) {
            return [
                'reply' => 'If your host company is already an approved university partner, go to the **Notarized MOA** page, browse the active partner companies list, search your company name, and click **"Link MOA"** to attach your placement without needing a separate upload.',
                'actions' => [
                    [
                        'label' => 'Go to Notarized MOA & Partners',
                        'url' => '/student/MOA',
                        'icon' => 'fas fa-link'
                    ]
                ],
                'suggestions' => [
                    'How do I upload a new MOA?',
                    'What is School In-House OJT?',
                    'Where do I encode my OJT Info?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 3: General Notarized MOA Upload / Submissions
        if (str_contains($lower, 'moa') || str_contains($lower, 'notarized') || str_contains($lower, 'company') || str_contains($lower, 'companies') || str_contains($lower, 'hte')) {
            $actions = [];
            if ($isStudent) {
                $actions = [['label' => 'Go to Notarized MOA & Partners', 'url' => '/student/MOA', 'icon' => 'fas fa-file-contract']];
            } elseif ($isProfessor) {
                $actions = [['label' => 'View Class List', 'url' => '/professor/class', 'icon' => 'fas fa-users']];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions = [['label' => 'Manage Partner Companies & MOAs', 'url' => '/MOA', 'icon' => 'fas fa-file-contract']];
            } else {
                $actions = [['label' => 'Launch Portal', 'url' => '/login-gateway', 'icon' => 'fas fa-sign-in-alt']];
            }

            return [
                'reply' => $isStudent
                    ? 'On the **Notarized MOA** page, you can submit a new Notarized MOA PDF, link to an existing university partner company, declare School In-House OJT, or generate your MOA submission voucher.'
                    : 'InternConnect maintains an updated directory of partner host training establishments (HTEs) with active Memorandums of Agreement (MOA).',
                'actions' => $actions,
                'suggestions' => [
                    'How do I link an existing MOA?',
                    'What is School In-House OJT?',
                    'What are the OJT phases?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 5: General Requirements Upload & Category Tracking
        if (str_contains($lower, 'requirement') || str_contains($lower, 'submit requirement') || str_contains($lower, 'upload requirement') || str_contains($lower, 'medical clearance') || str_contains($lower, 'endorsement letter') || str_contains($lower, 'dtr') || str_contains($lower, 'parent consent') || str_contains($lower, 'insurance') || str_contains($lower, 'narrative report') || str_contains($lower, 'completion certificate') || str_contains($lower, 'upload document')) {
            $actions = [];
            if ($isStudent) {
                $actions = [['label' => 'Go to Requirements Submission', 'url' => '/student/requirements', 'icon' => 'fas fa-cloud-upload-alt']];
            } elseif ($isProfessor) {
                $actions = [['label' => 'Go to Student Requirement Status', 'url' => '/professor/requirement-status', 'icon' => 'fas fa-tasks']];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions = [['label' => 'Coordinator Student Requirements', 'url' => '/studentLists', 'icon' => 'fas fa-users']];
            } else {
                $actions = [['label' => 'Student Portal Login', 'url' => '/login-gateway?portal=student', 'icon' => 'fas fa-graduation-cap']];
            }

            return [
                'reply' => $isStudent
                    ? 'On the **Requirements** page, you can upload and track your required OJT documents across both requirement phases (Basic Requirements and Other Requirements). Click **"Upload Document"**, select your document type, attach your PDF file, and submit.'
                    : 'You can review and evaluate submitted student requirement documents per class section.',
                'actions' => $actions,
                'suggestions' => [
                    'Explain the requirement phases',
                    'What if my requirement is denied?',
                    'How do I submit my MOA?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 1: What is GuiSIS (Guidance Information System)
        if (str_contains($lower, 'what is guisis') || str_contains($lower, 'what is guidance system') || str_contains($lower, 'explain guisis') || str_contains($lower, 'guisis meaning') || str_contains($lower, 'about guisis') || str_contains($lower, 'guisis portal') || str_contains($lower, 'guisis link')) {
            $actions = [
                [
                    'label' => 'Go to GuiSIS Portal',
                    'url' => self::GUISIS_URL,
                    'icon' => 'fas fa-external-link-alt',
                    'external' => true
                ]
            ];
            if ($isStudent) {
                $actions[] = ['label' => 'Account Settings & Sync', 'url' => '/student/accountinfo', 'icon' => 'fas fa-user-cog'];
            } elseif ($isProfessor) {
                $actions[] = ['label' => 'Faculty Account Settings', 'url' => '/professor/accountinfo', 'icon' => 'fas fa-user-cog'];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions[] = ['label' => 'GuiSIS Student Directory Sync', 'url' => '/studentLists', 'icon' => 'fas fa-users'];
            } else {
                $actions[] = ['label' => 'Student Portal Login', 'url' => '/login-gateway?portal=student', 'icon' => 'fas fa-graduation-cap'];
            }

            return [
                'reply' => '**GuiSIS (Guidance Information System)** is the university\'s centralized guidance and student record management system. InternConnect integrates with GuiSIS so your official student number, degree program, section, and academic profile can be automatically synchronized with one click in your Account Settings.',
                'actions' => $actions,
                'suggestions' => [
                    'What if I don\'t have a GuiSIS account?',
                    'How do I sync my profile with GuiSIS?',
                    'Where do I see my assigned adviser?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 1: No GuiSIS Account / Missing Record / IIR (Individual Inventory Record) Form
        if (str_contains($lower, 'no guisis') || str_contains($lower, 'dont have guisis') || str_contains($lower, "don't have guisis") || str_contains($lower, 'guisis account') || str_contains($lower, 'iir') || str_contains($lower, 'inventory record') || str_contains($lower, 'fill guisis') || str_contains($lower, 'answer guisis') || str_contains($lower, 'guisis form') || str_contains($lower, 'guisis error') || str_contains($lower, 'not in guisis')) {
            $actions = [
                [
                    'label' => 'Go to GuiSIS Website (Fill IIR)',
                    'url' => self::GUISIS_URL,
                    'icon' => 'fas fa-external-link-alt',
                    'external' => true
                ]
            ];
            if ($isStudent) {
                $actions[] = ['label' => 'Account Settings (Sync)', 'url' => '/student/accountinfo', 'icon' => 'fas fa-sync-alt'];
            } elseif ($isProfessor) {
                $actions[] = ['label' => 'Faculty Account Settings', 'url' => '/professor/accountinfo', 'icon' => 'fas fa-user-cog'];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions[] = ['label' => 'GuiSIS Sync in Students List', 'url' => '/studentLists', 'icon' => 'fas fa-users'];
            } else {
                $actions[] = ['label' => 'Student Portal Login', 'url' => '/login-gateway?portal=student', 'icon' => 'fas fa-graduation-cap'];
            }

            return [
                'reply' => "If you do not have a GuiSIS account yet or your record isn't syncing, you need to visit the **GuiSIS Portal** and answer your **Individual Inventory Record (IIR)** form. Once your IIR is submitted there, return to InternConnect and click **\"Sync with GuiSIS\"** in your Account Settings.",
                'actions' => $actions,
                'suggestions' => [
                    'What is GuiSIS?',
                    'How do I sync my profile with GuiSIS?',
                    'Where do I see my assigned adviser?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 1: How to Sync Profile with GuiSIS
        if (str_contains($lower, 'how to sync') || str_contains($lower, 'how do i sync') || str_contains($lower, 'sync with guisis') || str_contains($lower, 'sync profile') || str_contains($lower, 'sync guidance') || str_contains($lower, 'guisis sync') || str_contains($lower, 'sync my profile') || str_contains($lower, 'sync records')) {
            $actions = [];
            if ($isStudent) {
                $actions = [
                    ['label' => 'Go to Account & Sync Profile', 'url' => '/student/accountinfo', 'icon' => 'fas fa-sync-alt'],
                    ['label' => 'Go to GuiSIS Portal', 'url' => self::GUISIS_URL, 'icon' => 'fas fa-external-link-alt', 'external' => true]
                ];
            } elseif ($isProfessor) {
                $actions = [
                    ['label' => 'Faculty Account Settings', 'url' => '/professor/accountinfo', 'icon' => 'fas fa-user-cog'],
                    ['label' => 'Go to GuiSIS Portal', 'url' => self::GUISIS_URL, 'icon' => 'fas fa-external-link-alt', 'external' => true]
                ];
            } elseif ($userRole === 'coordinator' || $userRole === '1') {
                $actions = [
                    ['label' => 'Sync Students from GuiSIS', 'url' => '/studentLists', 'icon' => 'fas fa-users'],
                    ['label' => 'Go to GuiSIS Portal', 'url' => self::GUISIS_URL, 'icon' => 'fas fa-external-link-alt', 'external' => true]
                ];
            } else {
                $actions = [
                    ['label' => 'Student Portal Login', 'url' => '/login-gateway?portal=student', 'icon' => 'fas fa-graduation-cap'],
                    ['label' => 'Go to GuiSIS Portal', 'url' => self::GUISIS_URL, 'icon' => 'fas fa-external-link-alt', 'external' => true]
                ];
            }

            return [
                'reply' => ($userRole === 'coordinator' || $userRole === '1')
                    ? 'As an OJT Coordinator, you can batch-synchronize student cohorts and section records from GuiSIS via the Students Directory page.'
                    : 'To sync your academic records, go to your **Account Settings** page and click the **"Sync with GuiSIS"** button. This will automatically pull your verified student number, degree program, section, and contact information directly from the Guidance system.',
                'actions' => $actions,
                'suggestions' => [
                    'What if I don\'t have a GuiSIS account?',
                    'How do I update my password?',
                    'Where do I see my class section?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Module 1: Student Account Settings / Change Password / Contact Info
        if ($isStudent && (str_contains($lower, 'update contact') || str_contains($lower, 'change contact') || str_contains($lower, 'update phone') || str_contains($lower, 'change phone') || str_contains($lower, 'emergency contact') || str_contains($lower, 'account settings') || str_contains($lower, 'edit profile') || str_contains($lower, 'edit account'))) {
            return [
                'reply' => 'You can update your personal phone number, emergency contact details, home address, and set or change your local password directly in your **Account Settings**.',
                'actions' => [
                    [
                        'label' => 'Go to Account Settings',
                        'url' => '/student/accountinfo',
                        'icon' => 'fas fa-user-edit'
                    ]
                ],
                'suggestions' => [
                    'How do I sync with GuiSIS?',
                    'What are Local Credentials?',
                    'Where do I view my class?'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        // Voice input dictation
        if (str_contains($lower, 'voice') || str_contains($lower, 'microphone') || str_contains($lower, 'mic') || str_contains($lower, 'dictat')) {
            return [
                'reply' => 'InternConnect features built-in voice input dictation! Click the red microphone icon on any input field to speak. You can also open the **Voice Guide** from the floating Plus menu for formatting commands.',
                'actions' => [],
                'suggestions' => [
                    'Where do I submit my MOA?',
                    'How do I sync my profile?',
                    'Contact support'
                ],
                'escalate' => false,
                'source' => 'system'
            ];
        }

        return null;
    }

    /**
     * Fallback response if AI is unavailable
     */
    protected function buildFallbackResponse(string $message, ?string $userRole): array
    {
        $isLanding = ($userRole === 'guest' || $userRole === 'visitor' || empty($userRole));
        $actions = $isLanding
            ? [
                [
                    'label' => 'Launch Portal Gateway',
                    'url' => '/login-gateway',
                    'icon' => 'fas fa-sign-in-alt'
                ]
            ]
            : [
                [
                    'label' => 'Go to OJT Information',
                    'url' => '/student/ojtinfo',
                    'icon' => 'fas fa-layer-group'
                ]
            ];

        return [
            'reply' => 'I am your InternConnect Guide. If you have specific inquiries about your OJT records or need assistance with approvals, please connect with our OJT Coordinators directly.',
            'actions' => $actions,
            'suggestions' => $isLanding
                ? [
                    'How do I go to the main website?',
                    'What is InternConnect?',
                    'Who developed this system?'
                ]
                : [
                    'Where do I submit my MOA?',
                    'How to sync my profile with GuiSIS?',
                    'View partner companies'
                ],
            'escalate' => true,
            'source' => 'fallback'
        ];
    }

    /**
     * Safely extract JSON object from AI string
     */
    protected function extractJson(string $text): ?array
    {
        $text = trim($text);

        // Remove markdown code fences if present
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $text, $matches)) {
            $text = $matches[1];
        }

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $slice = substr($text, $start, $end - $start + 1);
            $decoded = json_decode($slice, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }
}
