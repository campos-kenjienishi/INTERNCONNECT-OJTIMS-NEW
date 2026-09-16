# INTERNCONNECT (OJTIMS) — REST API & Integration Specification

**System Title:** INTERNCONNECT: Integrated On-the-Job Training Information Management System  
**Institution:** Polytechnic University of the Philippines – Taguig Branch  
**Version:** `1.0.0`  
**Base Production URL:** `https://internconnect.undraftedbsit2027.com`  
**Base Local URL:** `http://127.0.0.1:8000`

---

## 📌 Table of Contents
1. [Authentication & Security Architecture](#1-authentication--security-architecture)
2. [External University Microservices](#2-external-university-microservices)
   - [2.1. Institutional Identity Provider (IdP) API](#21-institutional-identity-provider-idp-api)
   - [2.2. FLSS API (Faculty Loading & Scheduling)](#22-flss-api-faculty-loading--scheduling)
   - [2.3. GUISIS API (Guidance & Student Information)](#23-guisis-api-guidance--student-information)
   - [2.4. PUPTAS API (Academic Programs & Tagging)](#24-puptas-api-academic-programs--tagging)
   - [2.5. Google Gemini Cloud AI API](#25-google-gemini-cloud-ai-api)
3. [Internal Application API & AJAX Endpoints](#3-internal-application-api--ajax-endpoints)
   - [3.1. Authentication & Onboarding Gateway](#31-authentication--onboarding-gateway)
   - [3.2. 2-Phase Document Engine (Students)](#32-2-phase-document-engine-students)
   - [3.3. Tokenized Supervisor Evaluation](#33-tokenized-supervisor-evaluation)
   - [3.4. MOA & Partner Management (Coordinator)](#34-moa--partner-management-coordinator)
   - [3.5. Analytics & Reporting Services](#35-analytics--reporting-services)
   - [3.6. 24/7 AI Virtual Assistant Endpoint](#36-247-ai-virtual-assistant-endpoint)
4. [Standard HTTP Status Codes & Error Handling](#4-standard-http-status-codes--error-handling)

---

## 1. Authentication & Security Architecture

INTERNCONNECT enforces three distinct authentication mechanisms depending on user role and client type:

```text
┌─────────────────────────────────────────────────────────────────────────────────┐
│                        AUTHENTICATION GATEWAY MECHANISMS                        │
├───────────────────────┬──────────────────────────┬──────────────────────────────┤
│ Mechanism             │ Target User / Role       │ Format / Header              │
├───────────────────────┼──────────────────────────┼──────────────────────────────┤
│ 1. Institutional JWT  │ Students, Faculty, Admin │ Authorization: Bearer <JWT>  │
│ 2. Laravel Session    │ Authenticated Portals    │ Cookie: internconnect_session│
│ 3. Cryptographic URL  │ External Company Superv. │ /evaluation/form/{token}     │
└───────────────────────┴──────────────────────────┴──────────────────────────────┘
```

* **Role-Based Access Control (RBAC):**
  * `Role 0`: Student / Trainee
  * `Role 1`: OJT Coordinator (Super Administrator)
  * `Role 2`: OJT Subject Professor / Academic Adviser
* **Cross-Site Request Forgery (CSRF):** All state-changing `POST`, `PUT`, and `DELETE` requests require the `X-CSRF-TOKEN` request header or `_token` body parameter.

---

## 2. External University Microservices

### 2.1. Institutional Identity Provider (IdP) API

The platform communicates with the centralized **PUP-Taguig Identity Provider** to provide seamless Single Sign-On (SSO) and JWT credential verification.

* **Base URL:** `https://identity-provider.isaxbsit2027.com`
* **Configuration:** `config/services.php` (`idp`)

#### Endpoints:
```http
GET  /api/v1/auth/authorize
POST /api/v1/auth/token
GET  /api/v1/me
```

#### JWT Token Verification:
When the IdP redirects back to `/auth/callback`, the backend decodes the payload:
```json
{
  "sub": "2021-00123-TG-0",
  "email": "student@iskolarngbayan.pup.edu.ph",
  "name": "Juan Dela Cruz",
  "role": "student",
  "campus": "PUP Taguig Branch",
  "iat": 1726530000,
  "exp": 1726533600
}
```

---

### 2.2. FLSS API (Faculty Loading & Scheduling)

Synchronizes faculty teaching assignments, advisory section allocations, and active course sections.

* **Service Class:** `app/Services/FacultyApiService.php`
* **Sync Route:** `POST /coordinator/sync-faculty`

#### Request:
```http
POST /coordinator/sync-faculty HTTP/1.1
Host: internconnect.undraftedbsit2027.com
X-CSRF-TOKEN: <token>
Content-Type: application/json
```

#### Response (200 OK):
```json
{
  "success": true,
  "message": "Faculty loading synchronized successfully from FLSS.",
  "data": {
    "synced_professors": 14,
    "active_sections": 28,
    "academic_year": "2025-2026",
    "semester": "1"
  }
}
```

---

### 2.3. GUISIS API (Guidance & Student Information)

Validates student practicum eligibility, academic units, and program enrollment clearance before allowing internship onboarding.

* **Service Class:** `app/Services/GuidanceApiService.php`
* **Sync Route:** `POST /coordinator/sync-users-guisis`

#### Response (200 OK):
```json
{
  "success": true,
  "message": "Student records and eligibility successfully verified with GUISIS.",
  "data": {
    "total_students_verified": 320,
    "eligible_trainees": 315,
    "ineligible_holds": 5
  }
}
```

---

### 2.4. PUPTAS API (Academic Programs & Tagging)

Standardizes university degree programs, official program codes, and curriculum offerings.

* **Base URL:** `https://puptas.undraftedbsit2027.com`
* **Service Class:** `app/Services/PuptasApiService.php`
* **Sync Route:** `POST /coordinator/sync-programs-puptas`

#### Response (200 OK):
```json
{
  "success": true,
  "message": "Academic programs standardized and synchronized with PUPTAS.",
  "data": {
    "programs_updated": [
      {"code": "BSIT", "title": "Bachelor of Science in Information Technology"},
      {"code": "BSME", "title": "Bachelor of Science in Mechanical Engineering"},
      {"code": "BSEE", "title": "Bachelor of Science in Electrical Engineering"},
      {"code": "BSBA-HRM", "title": "BSBA Major in Human Resource Management"},
      {"code": "DOMT", "title": "Diploma in Office Management Technology"}
    ]
  }
}
```

---

### 2.5. Google Gemini Cloud AI API

Provides generative natural language processing for the 24/7 student/faculty chatbot and synthesizes executive cohort risk insights.

* **Service Class:** `app/Services/ChatbotAiService.php` & `app/Services/ReportAiInsightService.php`
* **Model:** `gemini-1.5-flash` / `gemini-pro`

#### Outbound AI Prompt Schema:
```json
{
  "contents": [
    {
      "role": "user",
      "parts": [
        {
          "text": "System Context: INTERNCONNECT OJTIMS PUP Taguig.\nStudent Query: What are the required documents for Basic Phase?"
        }
      ]
    }
  ],
  "generationConfig": {
    "temperature": 0.2,
    "maxOutputTokens": 600
  }
}
```

---

## 3. Internal Application API & AJAX Endpoints

### 3.1. Authentication & Onboarding Gateway

#### Check Email Availability
```http
GET /check-email-availability?email=student@example.com
```
**Response (200 OK):**
```json
{
  "available": true
}
```

#### Onboarding Submission
```http
POST /onboarding/{email}
```
**Payload:**
```json
{
  "firstName": "Juan",
  "lastName": "Dela Cruz",
  "studentNumber": "2021-00123-TG-0",
  "course": "BSIT",
  "section": "4-1",
  "contactNumber": "09171234567"
}
```

---

### 3.2. 2-Phase Document Engine (Students)

#### Upload Compliance Requirement
```http
POST /uploadfile
Content-Type: multipart/form-data
```
**Payload (Form Data):**
* `file`: `[PDF File, max 10MB]`
* `category_id`: `4` (e.g. Medical Clearance)
* `phase`: `basic` | `other`

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Requirement uploaded successfully.",
  "file_url": "/storage/requirements/med_clearance_202100123.pdf",
  "phase": "basic",
  "status": "pending_approval"
}
```

---

### 3.3. Tokenized Supervisor Evaluation

Company supervisors submit digital performance assessments through a secure, single-use URL token without logging in.

#### 1. Retrieve Evaluation Form
```http
GET /evaluation/form/{token}
```
**Response (200 OK / HTML View):**  
Renders the student details, company profile, and active rubric criteria.

#### 2. Submit Supervisor Evaluation
```http
POST /evaluation/form/{token}
```
**Payload:**
```json
{
  "supervisor_name": "Engr. Roberto Santos",
  "supervisor_position": "Senior Systems Engineer",
  "supervisor_email": "rsantos@partnercorp.com",
  "scores": {
    "criteria_1_competence": 5,
    "criteria_2_punctuality": 4,
    "criteria_3_teamwork": 5,
    "criteria_4_initiative": 4,
    "criteria_5_quality_of_work": 5
  },
  "overall_rating": 94.5,
  "qualitative_remarks": "Demonstrated exceptional problem-solving and full-stack development skills during practicum.",
  "rendered_hours": 300
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Evaluation submitted successfully. The token has been invalidated.",
  "redirect_url": "/evaluation/submitted"
}
```

---

### 3.4. MOA & Partner Management (Coordinator)

#### Upload Notarized Company MOA
```http
POST /uploadMOA
Content-Type: multipart/form-data
```
**Payload:**
* `company_name`: `"Accenture Philippines"`
* `moa_file`: `[Notarized MOA PDF]`
* `date_notarized`: `"2025-01-15"`
* `validity_years`: `3`

#### Process Student MOA Unlock Request
```http
POST /moa/unlock-requests/{id}/approve
POST /moa/unlock-requests/{id}/deny
```
**Payload (Deny only):**
```json
{
  "denial_reason": "Provided company address does not match existing notarized contract."
}
```

---

### 3.5. Analytics & Reporting Services

#### Retrieve Coordinator Analytics Dataset
```http
GET /analytics/data?year=2025&semester=1
```
**Response (200 OK):**
```json
{
  "total_enrolled_interns": 420,
  "basic_phase_completed": 380,
  "other_phase_unlocked": 365,
  "evaluations_completed": 310,
  "active_partner_moas": 85,
  "expired_moas": 8,
  "course_breakdown": {
    "BSIT": {"total": 150, "completed": 142},
    "BSME": {"total": 120, "completed": 105},
    "BSEE": {"total": 90, "completed": 82},
    "BSBA": {"total": 60, "completed": 51}
  }
}
```

#### Export Masterlists & Expired MOA Reports
```http
GET /analytics/export/csv
GET /analytics/export/pdf
GET /ExpiredMOAReports?format=pdf
```
**Response:** Binary Stream (`Content-Type: application/pdf` / `text/csv`).

---

### 3.6. 24/7 AI Virtual Assistant Endpoint

#### Submit Chatbot Query
```http
POST /chatbot/message
Content-Type: application/json
```
**Payload:**
```json
{
  "message": "How do I unlock the Other Phase requirements?"
}
```
**Response (200 OK):**
```json
{
  "success": true,
  "reply": "InternConnect organizes document submissions into **2 distinct phases**:\n\n1. 🟢 **Basic Phase**: Prerequisite clearances + Notarized MOA.\n2. 🔒 **Other Phase**: Locked by default and **automatically unlocks** once all Basic Requirements and your MOA are verified by your professor.",
  "suggestions": [
    "View requirement status",
    "How to link my company MOA",
    "Contact OJT Coordinator"
  ]
}
```

---

## 4. Standard HTTP Status Codes & Error Handling

All JSON API responses return standard RFC 7807 problem details or standardized response payloads:

| HTTP Status Code | Description | Typical Scenario |
| :--- | :--- | :--- |
| **`200 OK`** | Success | Request succeeded; returned JSON data or file stream. |
| **`201 Created`** | Resource Created | New document, classroom, or user account registered. |
| **`400 Bad Request`** | Malformed Request | Missing required parameters or invalid file formats. |
| **`401 Unauthorized`** | Authentication Required | Expired JWT token or missing session cookie. |
| **`403 Forbidden`** | Access Denied | RBAC permission failure (e.g. Student accessing Coordinator panel). |
| **`404 Not Found`** | Resource Not Found | Invalid supervisor evaluation token or nonexistent record. |
| **`419 Page Expired`** | CSRF Token Mismatch | Session timed out; CSRF token regeneration required. |
| **`422 Unprocessable Entity`** | Validation Error | Form inputs failed validation rules (e.g. PDF exceeds 10MB). |
| **`500 Internal Server Error`** | Server Exception | Uncaught exception; logged into `storage/logs/laravel.log`. |

---

### Standard Error Response Schema:
```json
{
  "success": false,
  "error_code": "RESOURCE_LOCKED",
  "message": "Other Phase requirements are locked until all Basic Phase clearances are verified.",
  "timestamp": "2026-09-17T00:07:00Z"
}
```

---
*For support or integration questions, contact the INTERNCONNECT Development Team at `camposkenjienishi@gmail.com`.*
