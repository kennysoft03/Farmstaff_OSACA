# Farm Staff Registry — Technical Documentation

**Version:** 1.0.0  
**Organisation Case Study:** Ondo State Agricultural Commodities Association (OSACA)  
**Platform:** Farm Staff Registry (FSR)  
**Base URL:** http://farmstaff.test:9090/  
**Document Date:** September 2026  

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [System Architecture](#2-system-architecture)
3. [Technology Stack](#3-technology-stack)
4. [Database Design](#4-database-design)
5. [Module Descriptions](#5-module-descriptions)
6. [API / Controller Reference](#6-controller--routing-reference)
7. [Trust Score Algorithm](#7-trust-score-algorithm)
8. [Farm Reputation Score Algorithm](#8-farm-reputation-score-algorithm)
9. [Security Design](#9-security-design)
10. [File & Directory Structure](#10-file--directory-structure)
11. [Installation & Setup](#11-installation--setup)
12. [User Roles & Access Control](#12-user-roles--access-control)
13. [Key Business Rules](#13-key-business-rules)
14. [Error Handling & Logging](#14-error-handling--logging)
15. [Deployment Notes](#15-deployment-notes)

---

## 1. System Overview

### 1.1 Purpose

The **Farm Staff Registry (FSR)** is a centralised digital platform designed for the  
**Ondo State Agricultural Commodities Association (OSACA)** to enable agricultural  
employers to register, verify, monitor and manage farm workers across Ondo State, Nigeria.

The system addresses the documented information asymmetry in informal agricultural  
labour markets — where employers have no reliable mechanism to verify a prospective  
worker's identity, employment history, skills or conduct record before hiring.

### 1.2 Core Objectives

| # | Objective |
|---|-----------|
| 1 | Provide a single verified identity record per farm worker |
| 2 | Enable cross-employer employment history tracking |
| 3 | Compute and maintain a dynamic Trust Score per worker |
| 4 | Enable confidential incident reporting with admin moderation |
| 5 | Publish a Farm Reputation Score for each registered employer |
| 6 | Provide employer background-check capability before hiring |
| 7 | Maintain a full tamper-evident audit trail of all system actions |

### 1.3 System Portals

The platform consists of three distinct portals:

```
┌─────────────────────────────────────────────────────────────┐
│                    FARM STAFF REGISTRY                       │
├───────────────┬──────────────────────┬──────────────────────┤
│  PUBLIC SITE  │  EMPLOYER PORTAL     │  ADMIN PANEL         │
│  /            │  /dashboard          │  /admin              │
│               │                      │                      │
│  Homepage     │  Worker Management   │  Platform Overview   │
│  About        │  Background Check    │  Incident Review     │
│  Transparency │  Attendance          │  Rating Moderation   │
│  Resources    │  Incidents           │  Reports             │
│  Auth pages   │  Skill Tracking      │  Audit Trail         │
└───────────────┴──────────────────────┴──────────────────────┘
```

---

## 2. System Architecture

### 2.1 High-Level Architecture

```
┌──────────────────────────────────────────────────────────────┐
│                     CLIENT LAYER                             │
│  Web Browser (Desktop / Mobile)                              │
│  HTML5 · Bootstrap / KT Metronic · Vanilla JS / jQuery       │
└────────────────────────┬─────────────────────────────────────┘
                         │ HTTP/HTTPS
┌────────────────────────▼─────────────────────────────────────┐
│                 PRESENTATION LAYER                           │
│  Apache Web Server · mod_rewrite (.htaccess)                 │
│  PHP 8.1+ · CodeIgniter 3 (MVC Framework)                   │
│                                                              │
│  Controllers:  Home · Employer · Farmadmin                   │
│  Views:        public/ · employer/ · admin/                  │
│  Helpers:      ci_helper · url · form                        │
└────────────────────────┬─────────────────────────────────────┘
                         │
┌────────────────────────▼─────────────────────────────────────┐
│                  BUSINESS LOGIC LAYER                        │
│  Farmstaff_model (core model)                                │
│  Trust Score Engine                                          │
│  Reputation Score Engine                                     │
│  Audit Logger                                                │
│  Notification Manager                                        │
└────────────────────────┬─────────────────────────────────────┘
                         │
┌────────────────────────▼─────────────────────────────────────┐
│                    DATA LAYER                                │
│  MySQL 8.0  ·  Database: farmstaff                           │
│  13 tables  ·  InnoDB engine  ·  utf8mb4 charset             │
│  Foreign key constraints · Indexed joins                     │
└──────────────────────────────────────────────────────────────┘
```

### 2.2 MVC Pattern

```
Request URL
    │
    ▼
index.php  →  CI Router  →  Controller
                                │
                         ┌──────┴──────┐
                         │             │
                       Model          View
                    (DB queries)   (HTML output)
                         │
                      MySQL
```

### 2.3 Request Flow

```
1. Browser sends:   GET /dashboard/workers
2. .htaccess:       Rewrite → index.php?/dashboard/workers
3. CI Router:       Matches route → Employer::workers()
4. Controller:      Calls require_login() guard
5. Controller:      Calls fsmodel->get_workers_by_employer()
6. Model:           Executes parameterised MySQL query
7. Controller:      Passes $data array to view
8. View:            Renders HTML with KT Metronic template
9. Browser:         Displays rendered page
```

---

## 3. Technology Stack

| Component | Technology | Version | Purpose |
|---|---|---|---|
| Server Language | PHP | 8.1+ | Backend logic |
| Framework | CodeIgniter | 3.1.x | MVC structure |
| Web Server | Apache | 2.4+ | HTTP server |
| Database | MySQL | 8.0+ | Data persistence |
| Frontend CSS | KT Metronic | Demo1 | Admin/Employer UI |
| Frontend CSS | Custom (farmstaff.css) | 1.0 | Public website UI |
| Frontend JS | jQuery | 3.x | DOM manipulation |
| Frontend JS | Bootstrap | 4.x (bundled) | UI components |
| Icons | Flaticon / Line Awesome / FontAwesome | — | UI icons |
| Charts | Chart.js | 3.x | Reports charts |
| Password Hashing | PHP password_hash() | bcrypt cost 12 | Auth security |
| Session Storage | File-based | CI3 default | User sessions |
| Development Env | Laragon | 6.x | Local server |

---

## 4. Database Design

### 4.1 Entity Relationship Overview

```
fs_admin_users
      │
      │ reviews
      ▼
fs_incidents ◄──── fs_workers ◄──── fs_employers
                       │                 │
              ┌────────┼────────┐        │ rates
              │        │        │        ▼
        fs_skills  fs_work_  fs_    fs_farm_
                   history  attendance  ratings
                       │
                  fs_worker_
                   ratings
                       │
              fs_trust_score_log
```

### 4.2 Table Definitions

---

#### `fs_admin_users`
Platform administrators.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| surname | VARCHAR(100) | NOT NULL | Surname |
| othernames | VARCHAR(100) | NOT NULL | Other names |
| email | VARCHAR(150) | UNIQUE, NOT NULL | Email address |
| username | VARCHAR(80) | UNIQUE, NOT NULL | Login username |
| password | VARCHAR(255) | NOT NULL | bcrypt hash |
| role | ENUM | NOT NULL | superadmin / admin / moderator |
| status | TINYINT(1) | DEFAULT 1 | 1=active, 0=inactive |
| last_login | DATETIME | NULL | Last successful login |
| created_at | DATETIME | DEFAULT NOW | Record creation timestamp |
| updated_at | DATETIME | ON UPDATE NOW | Last update timestamp |

---

#### `fs_employers`
Registered employer/farm accounts.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| farm_name | VARCHAR(200) | NOT NULL | Farm/business name |
| contact_person | VARCHAR(200) | NOT NULL | Contact person full name |
| email | VARCHAR(150) | UNIQUE, NOT NULL | Login email |
| phone | VARCHAR(20) | UNIQUE, NOT NULL | Phone number |
| password | VARCHAR(255) | NOT NULL | bcrypt hash |
| address | TEXT | NULL | Farm address |
| lga | VARCHAR(100) | NULL | Local Government Area |
| state | VARCHAR(100) | DEFAULT 'Ondo' | State |
| farm_type | VARCHAR(100) | NULL | Type of farming |
| farm_size | VARCHAR(100) | NULL | Size description |
| reg_number | VARCHAR(50) | NULL | CAC/RC number |
| logo | VARCHAR(255) | NULL | Logo image path |
| status | ENUM | DEFAULT 'active' | pending/active/suspended |
| email_verified | TINYINT(1) | DEFAULT 0 | Email verification flag |
| reputation_score | DECIMAL(3,2) | DEFAULT 0.00 | Computed farm reputation |
| total_ratings | INT(11) | DEFAULT 0 | Number of approved ratings |
| verification_token | VARCHAR(100) | NULL | Email verification token |
| created_at | DATETIME | DEFAULT NOW | Registration timestamp |
| updated_at | DATETIME | ON UPDATE NOW | Last update |

---

#### `fs_workers`
Central worker registry profiles.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| worker_id | VARCHAR(20) | UNIQUE, NOT NULL | FSR-XXXXXX registry ID |
| firstname | VARCHAR(100) | NOT NULL | First name |
| lastname | VARCHAR(100) | NOT NULL | Last name |
| othername | VARCHAR(100) | NULL | Other name |
| gender | ENUM | NULL | male/female/other |
| dob | DATE | NULL | Date of birth |
| phone | VARCHAR(20) | UNIQUE, NOT NULL | Primary phone |
| alt_phone | VARCHAR(20) | NULL | Alternative phone |
| email | VARCHAR(150) | NULL | Email address |
| address | TEXT | NULL | Residential address |
| lga | VARCHAR(100) | NULL | Local Government Area |
| state | VARCHAR(100) | DEFAULT 'Ondo' | State |
| photo | VARCHAR(255) | NULL | Profile photo path |
| id_type | ENUM | NULL | NIN/Voters Card/etc |
| id_number | VARCHAR(100) | NULL | ID document number |
| id_document | VARCHAR(255) | NULL | ID scan file path |
| skills | TEXT | NULL | JSON skill tags |
| trust_score | DECIMAL(4,2) | DEFAULT 50.00 | Current trust score |
| disciplinary_points | INT(11) | DEFAULT 0 | Active demerits |
| status | ENUM | DEFAULT 'active' | active/inactive/flagged/suspended |
| registered_by | INT(11) | FK→fs_employers | Registering employer |
| created_at | DATETIME | DEFAULT NOW | Registration timestamp |
| updated_at | DATETIME | ON UPDATE NOW | Last update |

---

#### `fs_work_history`
Employment history records (portable across employers).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| worker_id | INT(11) | FK→fs_workers | Worker reference |
| employer_id | INT(11) | FK→fs_employers | Employer reference |
| role | VARCHAR(150) | NOT NULL | Job role/position |
| start_date | DATE | NOT NULL | Employment start |
| end_date | DATE | NULL | Employment end (NULL if current) |
| is_current | TINYINT(1) | DEFAULT 0 | Current employment flag |
| duration_days | INT(11) | NULL | Computed duration in days |
| responsibilities | TEXT | NULL | Key duties |
| leaving_reason | VARCHAR(255) | NULL | Reason for leaving |
| employer_remarks | TEXT | NULL | Employer comments |
| status | ENUM | DEFAULT 'active' | active/completed/terminated |
| created_at | DATETIME | DEFAULT NOW | Record created |
| updated_at | DATETIME | ON UPDATE NOW | Last update |

---

#### `fs_skills`
Worker skill records verified by employers.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| worker_id | INT(11) | FK→fs_workers | Worker reference |
| employer_id | INT(11) | FK→fs_employers | Verifying employer |
| skill_name | VARCHAR(150) | NOT NULL | Skill description |
| proficiency | ENUM | DEFAULT 'intermediate' | beginner/intermediate/skilled/expert |
| rating | TINYINT(1) | DEFAULT 3 | 1–5 star rating |
| verified | TINYINT(1) | DEFAULT 0 | Admin-verified flag |
| verified_by | INT(11) | NULL | Admin user ID |
| notes | TEXT | NULL | Additional notes |
| created_at | DATETIME | DEFAULT NOW | Record created |

---

#### `fs_attendance`
Daily attendance records per worker per employer.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| worker_id | INT(11) | FK→fs_workers | Worker reference |
| employer_id | INT(11) | FK→fs_employers | Employer reference |
| work_history_id | INT(11) | NULL | Related work history |
| attendance_date | DATE | NOT NULL | Date of attendance |
| status | ENUM | NOT NULL | present/absent/late/half-day/leave |
| check_in | TIME | NULL | Check-in time |
| check_out | TIME | NULL | Check-out time |
| notes | VARCHAR(255) | NULL | Notes |
| created_at | DATETIME | DEFAULT NOW | Record created |

**Constraint:** Upsert logic — duplicate (worker_id, employer_id, attendance_date) updates existing record.

---

#### `fs_incidents`
Confidential misconduct/incident reports.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | INT(11) | PK, AUTO_INCREMENT | Primary key |
| worker_id | INT(11) | FK→fs_workers | Subject worker |
| employer_id | INT(11) | FK→fs_employers | Reporting employer |
| incident_type | ENUM | NOT NULL | theft/misconduct/assault/etc |
| incident_date | DATE | NOT NULL | Date incident occurred |
| description | TEXT | NOT NULL | Full incident description |
| evidence_file | VARCHAR(255) | NULL | Uploaded evidence path |
| severity | ENUM | DEFAULT 'moderate' | minor/moderate/severe |
| status | ENUM | DEFAULT 'pending' | pending/reviewed/accepted/rejected/appealed |
| admin_notes | TEXT | NULL | Admin review notes |
| reviewed_by | INT(11) | NULL | Admin user ID |
| reviewed_at | DATETIME | NULL | Review timestamp |
| disciplinary_points | INT(11) | DEFAULT 0 | Points deducted on acceptance |
| created_at | DATETIME | DEFAULT NOW | Report submitted |
| updated_at | DATETIME | ON UPDATE NOW | Last update |

---

#### `fs_farm_ratings`
Worker ratings of employer/farm (anonymous by default).

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Primary key |
| employer_id | INT FK | Rated farm |
| worker_id | INT FK | Rating worker |
| work_history_id | INT | Related employment |
| overall_rating | TINYINT(1) | 1–5 overall score |
| working_conditions | TINYINT(1) | Dimension score |
| safety | TINYINT(1) | Dimension score |
| payment_promptness | TINYINT(1) | Dimension score |
| treatment | TINYINT(1) | Dimension score |
| review_text | TEXT | Written review |
| is_anonymous | TINYINT(1) DEFAULT 1 | Anonymity flag |
| status | ENUM DEFAULT 'pending' | pending/approved/rejected |
| reviewed_by | INT | Admin reviewer |
| created_at | DATETIME | Submission time |

---

#### `fs_worker_ratings`
Employer ratings of worker performance.

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Primary key |
| worker_id | INT FK | Rated worker |
| employer_id | INT FK | Rating employer |
| overall_rating | TINYINT(1) | 1–5 overall |
| work_quality | TINYINT(1) | Dimension score |
| punctuality | TINYINT(1) | Dimension score |
| teamwork | TINYINT(1) | Dimension score |
| reliability | TINYINT(1) | Dimension score |
| review_text | TEXT | Written review |
| would_rehire | TINYINT(1) | 1=yes, 0=no |
| status | ENUM DEFAULT 'approved' | Auto-approved |
| created_at | DATETIME | Submission time |

---

#### `fs_trust_score_log`
Immutable log of all trust score changes.

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Primary key |
| worker_id | INT FK | Worker affected |
| old_score | DECIMAL(4,2) | Score before change |
| new_score | DECIMAL(4,2) | Score after change |
| change_amount | DECIMAL(4,2) | Delta (positive or negative) |
| change_reason | VARCHAR(255) | Human-readable reason |
| reference_id | INT | Source record ID |
| reference_type | ENUM | incident/rating/attendance/manual |
| created_by | INT | Actor (admin or employer ID) |
| created_at | DATETIME | Change timestamp |

---

#### `fs_background_checks`
Audit of all employer worker searches.

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Primary key |
| employer_id | INT FK | Searching employer |
| worker_id | INT | Found worker (NULL if not found) |
| search_query | VARCHAR(100) | Query string |
| search_type | ENUM | phone/worker_id/name |
| result_found | TINYINT(1) | 1=found, 0=not found |
| ip_address | VARCHAR(45) | Requester IP |
| created_at | DATETIME | Search timestamp |

---

#### `fs_audit_logs`
System-wide activity audit trail.

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Primary key |
| actor_type | ENUM | admin/employer/system |
| actor_id | INT | Actor's ID |
| actor_name | VARCHAR(200) | Actor's display name |
| action | VARCHAR(100) | Action code (e.g. login, register_worker) |
| module | VARCHAR(100) | System module |
| target_id | INT | Target record ID |
| target_type | VARCHAR(50) | Target record type |
| description | TEXT | Full description |
| ip_address | VARCHAR(45) | Client IP |
| user_agent | VARCHAR(500) | Browser agent |
| created_at | DATETIME | Action timestamp |

---

#### `fs_notifications`
In-app notifications for admins and employers.

| Column | Type | Description |
|--------|------|-------------|
| id | INT PK | Primary key |
| recipient_type | ENUM | admin/employer |
| recipient_id | INT | Recipient ID |
| title | VARCHAR(255) | Notification title |
| message | TEXT | Full message |
| type | ENUM | info/warning/success/danger |
| is_read | TINYINT(1) DEFAULT 0 | Read flag |
| link | VARCHAR(500) | Action URL |
| created_at | DATETIME | Created timestamp |

---

## 5. Module Descriptions

### 5.1 Worker Registration & Central Profile

**Purpose:** Create a single verified identity record for each worker.  
**Accessed by:** Employer (registering), Admin (viewing/managing)  
**Key logic:**
- Auto-generates unique `worker_id` in format `FSR-XXXXXX00` (hex + numeric suffix, collision-checked)
- Trust score initialised at **50** (neutral midpoint)
- Simultaneously creates first `fs_work_history` record for the registering employer
- Accepts photo upload (JPEG/PNG ≤2MB) and ID document scan (JPEG/PNG/PDF ≤3MB)
- One central profile exists per phone number — system rejects duplicate phone registrations

---

### 5.2 Work History Tracking

**Purpose:** Portable employment ledger across all farms.  
**Key logic:**
- Each employment period stored as a row in `fs_work_history`
- `is_current=1` marks active employment; setting new current record auto-closes previous
- `duration_days` computed from `(end_date - start_date)` in days
- Work history visible to any employer who runs a background check (read-only)
- Employers can only add/edit their own employment records

---

### 5.3 Skills Verification

**Purpose:** Build a credible, employer-verified skills portfolio.  
**Key logic:**
- Employers add skills with proficiency level (beginner → expert) and 1–5 star rating
- Skills remain `verified=0` until an admin reviews and marks `verified=1`
- Verified skills displayed with a checkmark on public profile
- Common agricultural skill suggestions provided (Tractor Driving, Spraying, Harvesting, etc.)

---

### 5.4 Attendance Evaluation

**Purpose:** Track daily presence and compute a reliability score.  
**Attendance Score Formula:**

```
Attendance Score (%) = ((Present + Late × 0.5) / Total Records) × 100
```

**Status values:** present · absent · late · half-day · leave  
**Key logic:**
- Upsert pattern prevents duplicate records for same worker/employer/date
- Monthly view with calendar navigation
- Attendance score feeds indirectly into trust score computation
- Bulk entry supported via array POST

---

### 5.5 Confidential Incident Reporting

**Purpose:** Structured misconduct reporting with admin moderation gate.  
**Incident Types:** theft · misconduct · assault · insubordination · negligence · fraud · other  
**Severity:** minor · moderate · severe  
**Workflow:**

```
Employer submits report (status=pending)
         │
         ▼
Admin reviews evidence and description
         │
    ┌────┴────┐
    │         │
  Accept     Reject
    │         │
    ▼         ▼
Disciplinary  No effect
points        on worker
deducted      record
from trust
score
```

**Key rule:** Incidents ONLY affect trust score after admin acceptance. Pending reports have zero impact.

---

### 5.6 Trust Score System

See [Section 7](#7-trust-score-algorithm) for full algorithm documentation.

---

### 5.7 Worker Background Check

**Purpose:** Enable pre-hire verification of any worker in the registry.  
**Search types:** Phone number · Registry ID (FSR-XXXXXX)  
**Results shown:**
- Identity (name, photo, LGA, ID type)
- Work history summary (roles, farms, dates)
- Skills (verified and unverified)
- Performance ratings (approved only)
- Accepted incident count (number only, not details)
- Current trust score and band

**Audit:** Every search is logged in `fs_background_checks` with employer ID, query, result flag and IP address.

---

### 5.8 Farm Reputation Score

See [Section 8](#8-farm-reputation-score-algorithm) for full algorithm.  
**Visibility:** Public on employer profile. Displayed on public website stats.

---

### 5.9 Admin Moderation

**Scope:** Incidents · Farm ratings · Skill verification · Trust score manual adjustment  
**Access:** Superadmin · Admin · Moderator roles  
**Key capabilities:**
- Review incident reports with evidence files
- Accept (with disciplinary points) or reject incidents
- Approve/reject anonymous farm ratings
- Manually adjust trust scores with reason and audit trail
- Verify worker skills

---

## 6. Controller / Routing Reference

### 6.1 Public Routes (`Home` controller)

| Route | Method | Action | Auth |
|-------|--------|--------|------|
| `/` | GET | Homepage with platform stats | None |
| `/about` | GET | About OSACA and FSR | None |
| `/for-transparency` | GET | Transparency commitment page | None |
| `/resources` | GET | Help guides and resources | None |
| `/login` | GET/POST | Employer login | None |
| `/register` | GET/POST | Employer registration | None |
| `/logout` | GET | Destroy session | Employer |
| `/search-worker` | GET | Worker search | Employer |
| `/worker/profile/{id}` | GET | Worker profile view | Employer |

### 6.2 Employer Dashboard Routes (`Employer` controller)

| Route | Method | Action |
|-------|--------|--------|
| `/dashboard` | GET | Dashboard overview |
| `/dashboard/workers` | GET | Worker list with filters |
| `/dashboard/register-worker` | GET/POST | Register new worker |
| `/dashboard/worker/{id}` | GET | View worker full profile |
| `/dashboard/worker/{id}/edit` | GET/POST | Edit worker details |
| `/dashboard/worker/{id}/history` | GET | Work history list |
| `/dashboard/worker/{id}/add-history` | GET/POST | Add history record |
| `/dashboard/worker/{id}/skills` | GET/POST | Manage skills |
| `/dashboard/worker/{id}/attendance` | GET/POST | Log/view attendance |
| `/dashboard/worker/{id}/incident` | GET/POST | Report incident |
| `/dashboard/worker/{id}/rate` | GET/POST | Rate worker performance |
| `/dashboard/background-check` | GET/POST | Search any worker |
| `/dashboard/profile` | GET/POST | Update farm profile |
| `/dashboard/notifications` | GET | View notifications |

### 6.3 Admin Panel Routes (`Farmadmin` controller)

| Route | Method | Action |
|-------|--------|--------|
| `/admin` or `/admin/login` | GET/POST | Admin login |
| `/admin/logout` | GET | Admin logout |
| `/admin/dashboard` | GET | Platform dashboard |
| `/admin/workers` | GET | Worker registry list |
| `/admin/worker/{id}` | GET/POST | View/manage worker |
| `/admin/employers` | GET | Employer list |
| `/admin/employer/{id}` | GET/POST | View/manage employer |
| `/admin/incidents` | GET | Incident queue |
| `/admin/incident/{id}` | GET | View incident |
| `/admin/incident/{id}/review` | GET/POST | Review incident |
| `/admin/farm-ratings` | GET | Farm ratings queue |
| `/admin/farm-rating/{id}/review` | POST | Approve/reject rating |
| `/admin/reports` | GET | Analytics and reports |
| `/admin/audit` | GET | Audit trail |
| `/admin/trust-scores` | GET | Trust score overview |
| `/admin/settings` | GET/POST | System settings / admin users |
| `/admin/create-admin` | GET | One-time admin setup |

---

## 7. Trust Score Algorithm

### 7.1 Overview

Each worker starts with a neutral trust score of **T₀ = 50** on a scale of **0–100**.  
The score is updated by four event types:

| Event | Direction | Trigger |
|-------|-----------|---------|
| Positive performance rating | ↑ Increase | Employer submits worker rating |
| Negative performance rating | ↓ Decrease | Employer submits low worker rating |
| Accepted incident | ↓ Decrease | Admin accepts incident report |
| Manual admin adjustment | ↑ or ↓ | Admin acts with stated reason |

### 7.2 Rating Impact

When an employer submits a worker performance rating:

```
Score Change = (overall_rating - 3) × 5

Rating 5 stars  →  +10 points
Rating 4 stars  →  +5  points
Rating 3 stars  →   0  points (neutral)
Rating 2 stars  →  -5  points
Rating 1 star   →  -10 points
```

### 7.3 Incident Impact

When an admin accepts an incident with disciplinary points:

```
Score Change = -disciplinary_points

Recommended deductions by severity:
  Minor    →  -5  points
  Moderate → -10  points
  Severe   → -20 to -30 points
```

The admin enters the exact deduction during review, allowing proportionate judgement.

### 7.4 Score Boundaries

```php
$new_score = max(0, min(100, $old_score + $change));
```

Score is clamped: minimum **0**, maximum **100**.

### 7.5 Trust Score Bands

| Band | Range | Meaning |
|------|-------|---------|
| High Trust | 70–100 | Excellent record, recommended hire |
| Medium Trust | 40–69 | Some concerns, exercise judgement |
| Low Trust | 0–39 | Significant issues, verify carefully |

### 7.6 Score Log

Every score change writes an immutable record to `fs_trust_score_log` with:
- Old score, new score, change amount
- Human-readable reason
- Reference to the triggering record
- Actor who triggered the change
- Timestamp

### 7.7 Future: Temporal Decay

The current model is event-driven. A planned enhancement implements time-decay  
for disciplinary points, allowing rehabilitation:

```
Effective_deduction(t) = D × e^(-λt)

Where:
  D  = original deduction
  λ  = decay constant (e.g. λ = 0.1 per month)
  t  = months since incident was accepted
```

---

## 8. Farm Reputation Score Algorithm

### 8.1 Overview

Farm Reputation Scores reflect the quality of the employment environment  
from the perspective of workers who have worked there.

### 8.2 Moderation Gate

```
Worker submits rating (status = 'pending')
         │
         ▼
Admin reviews → Approve or Reject
         │
         ▼ (if approved)
Reputation score recomputed
```

Only **approved** ratings enter the score computation.

### 8.3 Score Computation

```php
// Called after each rating approval
$approved_ratings = SELECT overall_rating FROM fs_farm_ratings
                    WHERE employer_id = ? AND status = 'approved';

$avg = SUM(overall_rating) / COUNT(*)

UPDATE fs_employers
SET reputation_score = ROUND($avg, 2),
    total_ratings    = COUNT(*)
WHERE id = ?
```

### 8.4 Dimension Scores

Ratings collect five dimensions:

| Dimension | Column |
|-----------|--------|
| Overall | overall_rating |
| Working Conditions | working_conditions |
| Safety | safety |
| Payment Promptness | payment_promptness |
| Worker Treatment | treatment |

Dimension averages displayed on employer profile for transparency.

### 8.5 Score Display

```
0.0 – 1.9  ★☆☆☆☆  Poor
2.0 – 2.9  ★★☆☆☆  Below Average
3.0 – 3.9  ★★★☆☆  Average
4.0 – 4.5  ★★★★☆  Good
4.6 – 5.0  ★★★★★  Excellent
```

---

## 9. Security Design

### 9.1 Authentication

| Portal | Mechanism |
|--------|-----------|
| Employer | Email + password (bcrypt hash, cost 12) |
| Admin | Username + password (bcrypt hash, cost 12) |
| Session | File-based CI3 sessions, 24-hour expiry |

Session data stored in `FCPATH/sessions/`, not in cookies.

### 9.2 Password Policy

- Employer: minimum 8 characters
- Admin: minimum 8 characters
- Stored as `password_hash($password, PASSWORD_DEFAULT)` — bcrypt with auto-cost
- Never stored in plain text; never logged

### 9.3 Input Validation & Sanitisation

- All GET/POST inputs processed through CodeIgniter's `$this->input->post('field', TRUE)`  
  (XSS cleaning enabled via second parameter)
- Form validation rules defined in controllers using `form_validation` library
- Database queries use CodeIgniter Query Builder (parameterised — no raw SQL interpolation)

### 9.4 Access Control

Three-layer guard system:

```
Layer 1: Route-level guard
  require_login()  — checks session employer_id exists
  require_admin()  — checks session admin_id exists

Layer 2: Resource ownership
  Worker actions check: $worker['registered_by'] === $employer_id
  Admin actions check: admin role (superadmin/admin/moderator)

Layer 3: Admin moderation gate
  Incidents and ratings require explicit admin approval
  before they affect worker or employer records
```

### 9.5 File Upload Security

- Allowed types enforced per upload context:
  - Worker photo: `jpg|jpeg|png` ≤ 2MB
  - ID documents: `jpg|jpeg|png|pdf` ≤ 3MB
  - Incident evidence: `jpg|jpeg|png|pdf|doc|docx` ≤ 5MB
- Filenames encrypted (random name) on disk via CI3 upload library `encrypt_name=true`
- Upload directories outside web-accessible paths where possible

### 9.6 Sensitive Data Handling

- Incident reports visible only to the reporting employer and admin
- Worker trust score visible to employers running background checks
- Worker personal data (address, DOB, ID number) visible only to their own employer
- Farm ratings submitted anonymously by default (`is_anonymous = 1`)

### 9.7 CSRF & XSS

- CSRF protection configurable in `config.php` (`csrf_protection`)
- XSS cleaning applied globally on all input reads
- Output escaped with `htmlspecialchars()` / `html_escape()` in all views

---

## 10. File & Directory Structure

```
Farmstaff/
├── index.php                    # CI3 front controller
├── .htaccess                    # URL rewriting rules
├── install.php                  # One-time database installer (delete after setup)
├── farmstaff_db.sql             # Database schema (delete after import)
│
├── application/
│   ├── config/
│   │   ├── config.php           # Base URL, session settings
│   │   ├── database.php         # DB connection config
│   │   ├── env-database.php     # Environment-specific DB override
│   │   ├── routes.php           # URL routing table
│   │   ├── autoload.php         # Auto-loaded libraries/helpers
│   │   └── constants.php       # App constants (SITE_NAME, upload paths, etc.)
│   │
│   ├── controllers/
│   │   ├── Home.php             # Public website pages
│   │   ├── Employer.php         # Employer portal (dashboard, workers, etc.)
│   │   ├── Farmadmin.php        # Admin panel
│   │   └── Admin.php            # Legacy controller (unused)
│   │
│   ├── models/
│   │   └── Farmstaff_model.php  # Core data model (all FSR business logic)
│   │
│   ├── helpers/
│   │   └── ci_helper.php        # Custom helper functions
│   │
│   └── views/
│       ├── public/              # Public website views
│       │   ├── layout/
│       │   │   ├── header.php   # Public header + navbar + SEO meta
│       │   │   └── footer.php   # Public footer + scripts
│       │   ├── auth/
│       │   │   ├── login.php    # Employer login
│       │   │   └── register.php # Employer registration
│       │   ├── partials/
│       │   │   └── worker_profile_card.php
│       │   ├── home.php
│       │   ├── about.php
│       │   ├── transparency.php
│       │   ├── resources.php
│       │   ├── search_worker.php
│       │   ├── worker_profile.php
│       │   └── 404.php
│       │
│       ├── employer/            # Employer dashboard views
│       │   ├── layout/
│       │   │   ├── header.php   # KT Metronic header + sidebar
│       │   │   └── footer.php   # KT Metronic footer + JS
│       │   ├── dashboard.php
│       │   ├── workers.php
│       │   ├── register_worker.php
│       │   ├── view_worker.php
│       │   ├── edit_worker.php
│       │   ├── work_history.php
│       │   ├── add_work_history.php
│       │   ├── skills.php
│       │   ├── attendance.php
│       │   ├── report_incident.php
│       │   ├── rate_worker.php
│       │   ├── background_check.php
│       │   ├── farm_ratings.php
│       │   ├── profile.php
│       │   └── notifications.php
│       │
│       └── admin/               # Admin panel views
│           ├── layout/
│           │   ├── header.php   # KT Metronic admin header
│           │   └── footer.php   # KT Metronic admin footer
│           ├── login.php
│           ├── dashboard.php
│           ├── workers.php
│           ├── view_worker.php
│           ├── employers.php
│           ├── view_employer.php
│           ├── incidents.php
│           ├── view_incident.php
│           ├── review_incident.php
│           ├── farm_ratings.php
│           ├── reports.php
│           ├── audit.php
│           ├── trust_scores.php
│           └── settings.php
│
├── assets/                      # Public frontend assets
│   ├── css/
│   │   └── farmstaff.css        # Custom public website CSS
│   ├── js/
│   │   └── farmstaff.js         # Custom JS (dropdowns, skills tags, etc.)
│   └── images/
│       ├── logo.png             # Colour logo
│       ├── logo_white.png       # White logo (dark backgrounds)
│       ├── farvicon.png         # Browser favicon
│       └── hero-bg.jpg          # Homepage hero background
│
├── assetsa/                     # KT Metronic admin template assets
│   ├── css/demo1/               # Theme CSS bundles and skins
│   ├── js/demo1/                # Theme JS bundles
│   ├── vendors/                 # Third-party libraries
│   └── media/                   # Media files (logos, bg images)
│
├── uploads/                     # User-uploaded files
│   ├── workers/                 # Worker photos
│   │   └── ids/                 # Worker ID documents
│   ├── incidents/               # Incident evidence files
│   └── employers/               # Employer logos
│
├── sessions/                    # CI3 session files (server-side)
│
└── docs/
    └── TECHNICAL_DOCUMENTATION.md   # This document
```

---

## 11. Installation & Setup

### 11.1 Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | 8.1+ |
| MySQL | 8.0+ |
| Apache | 2.4+ with mod_rewrite |
| PHP Extensions | mysqli, mbstring, openssl, fileinfo |

### 11.2 Step-by-Step Installation

**Step 1 — Clone / deploy files**
```
Place all files in your web server root:
  Laragon:  C:\laragon\www\Farmstaff\
  cPanel:   /public_html/
```

**Step 2 — Create database**
```sql
CREATE DATABASE farmstaff CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Step 3 — Import schema**
```
Import farmstaff_db.sql into the farmstaff database via HeidiSQL, phpMyAdmin or CLI:
mysql -u root -p farmstaff < farmstaff_db.sql
```

**Step 4 — Configure database connection**  
Edit `application/config/env-database.php`:
```php
$db['default'] = array(
    'hostname' => '127.0.0.1',
    'username' => 'root',         // your MySQL username
    'password' => '',             // your MySQL password
    'database' => 'farmstaff',    // database name
    'dbdriver' => 'mysqli',
    // ...
);
```

**Step 5 — Set base URL**  
Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://farmstaff.test:9090/';
```

**Step 6 — Set directory permissions**
```
sessions/         → writable (755)
uploads/          → writable (755)
application/logs/ → writable (755)
```

**Step 7 — Create default admin user**  
Visit: `http://farmstaff.test:9090/admin/create-admin`

**Step 8 — Login**
```
URL:      http://farmstaff.test:9090/admin
Username: admin
Password: Admin@1234
```

**Step 9 — Delete installer files**
```
Delete: install.php
Delete: farmstaff_db.sql  (or move outside web root)
```

---

## 12. User Roles & Access Control

### 12.1 Role Hierarchy

```
OSACA Superadmin
    │
    ├── Admin
    │     └── Full platform management
    │
    ├── Moderator
    │     └── Review incidents and ratings only
    │
    └── Employer (separate session)
          └── Own workers and farm data only
```

### 12.2 Permission Matrix

| Action | Superadmin | Admin | Moderator | Employer |
|--------|-----------|-------|-----------|----------|
| View all workers | ✅ | ✅ | ✅ | Own only |
| Edit worker status | ✅ | ✅ | ❌ | Own only |
| Adjust trust score | ✅ | ✅ | ❌ | ❌ |
| Review incidents | ✅ | ✅ | ✅ | ❌ |
| Approve farm ratings | ✅ | ✅ | ✅ | ❌ |
| Verify skills | ✅ | ✅ | ❌ | ❌ |
| View audit logs | ✅ | ✅ | ❌ | ❌ |
| Create admin users | ✅ | ❌ | ❌ | ❌ |
| Register workers | ❌ | ❌ | ❌ | ✅ |
| Background check | ❌ | ✅ | ❌ | ✅ |
| Submit incidents | ❌ | ❌ | ❌ | ✅ |

---

## 13. Key Business Rules

| # | Rule |
|---|------|
| BR-01 | One registry profile per phone number — duplicate phone rejected at registration |
| BR-02 | Worker ID (FSR-XXXXXX) is permanent and cannot be changed after generation |
| BR-03 | Trust score initialises at 50 for every new worker |
| BR-04 | Incidents do NOT affect trust score until admin reviews and accepts them |
| BR-05 | Farm ratings do NOT affect reputation score until admin approves them |
| BR-06 | Only the employer who registered a worker can add/edit that worker's records |
| BR-07 | Any registered employer can run a background check on any worker |
| BR-08 | Trust score cannot go below 0 or above 100 |
| BR-09 | Setting a new current work history record auto-closes all previous current records for the same worker/employer pair |
| BR-10 | Attendance upsert: duplicate worker/employer/date updates the existing record |
| BR-11 | Background checks are logged regardless of whether a result is found |
| BR-12 | All admin actions are written to the audit log with timestamp and IP |
| BR-13 | Farm rating anonymity: `is_anonymous=1` by default; worker identity hidden from employer in rating list |
| BR-14 | Superadmin accounts cannot be deleted or status-changed by non-superadmin users |

---

## 14. Error Handling & Logging

### 14.1 Application Logs

CI3 logs to `application/logs/log-YYYY-MM-DD.php`

Log levels used:
- `log_message('error', ...)` — Exceptions, upload failures, DB errors
- `log_message('info', ...)`  — Successful operations, uploads
- `log_message('debug', ...)` — Development-only tracing

### 14.2 Database Error Handling

`db_debug` set to `TRUE` in development (`env-database.php`).  
Set to `FALSE` in production to prevent SQL exposure.

### 14.3 404 Handling

Route: `$route['404_override'] = 'Home/not_found';`  
Renders a branded 404 page with navigation back to homepage.

### 14.4 Flash Messages

User-facing errors and confirmations delivered via CI3 flash data:
```php
$this->session->set_flashdata('success', 'Message here');
$this->session->set_flashdata('error',   'Error here');
```
Displayed in layout headers, auto-dismissed after 5 seconds via JS.

---

## 15. Deployment Notes

### 15.1 Production Checklist

```
[ ] Set ENVIRONMENT = 'production' in index.php
[ ] Set db_debug = FALSE in env-database.php
[ ] Set display_errors = 0 in PHP config
[ ] Delete install.php
[ ] Delete farmstaff_db.sql from web root
[ ] Set strong admin password (change from Admin@1234)
[ ] Configure HTTPS / SSL certificate
[ ] Set base_url to production domain in config.php
[ ] Ensure sessions/ and uploads/ are writable
[ ] Set up cron job for trust score decay (future feature)
[ ] Configure email for notifications (SMTP settings in config)
[ ] Enable CI3 CSRF protection in config.php
[ ] Review and tighten .htaccess for production server
```

### 15.2 Shared Hosting (cPanel) Deployment

```
1. Upload all files to /public_html/ (or subdirectory)
2. Update base_url in config.php to match your domain
3. Create MySQL database and user via cPanel
4. Import farmstaff_db.sql via phpMyAdmin
5. Update env-database.php with cPanel DB credentials
6. Ensure PHP 8.1 selected in MultiPHP Manager
7. Ensure mod_rewrite enabled (Contact host if not)
```

### 15.3 Environment Constants (constants.php)

```php
define('SITE_NAME',         'Farm Staff Registry');
define('SITE_TAGLINE',      'Trust. Transparency. Better Farms.');
define('SITE_EMAIL',        'info@farmstaff.ng');
define('OSACA_NAME',        'Ondo State Agricultural Commodities Association (OSACA)');
define('UPLOAD_WORKERS',    'uploads/workers/');
define('UPLOAD_INCIDENTS',  'uploads/incidents/');
define('UPLOAD_EMPLOYERS',  'uploads/employers/');
define('WORKER_ID_PREFIX',  'FSR');
define('TRUST_SCORE_MAX',   100);
define('TRUST_SCORE_START', 50);
```

---

## Appendix A — Worker ID Generation Logic

```php
private function _generate_worker_id()
{
    do {
        // FSR- + 6 uppercase hex chars + 2 digits = e.g. FSR-A3F9C112
        $id = WORKER_ID_PREFIX . '-'
            . strtoupper(substr(uniqid(), -6))
            . rand(10, 99);
    } while (
        $this->db->where('worker_id', $id)
                 ->count_all_results('fs_workers') > 0
    );
    return $id;
}
```

Collision probability is negligible but the loop ensures uniqueness is guaranteed.

---

## Appendix B — Attendance Score Calculation

```php
public function get_attendance_summary($worker_id, $employer_id)
{
    $rows    = // all attendance records for worker/employer
    $total   = count($rows);
    $present = count(array_filter($rows, fn($r) => $r['status'] === 'present'));
    $late    = count(array_filter($rows, fn($r) => $r['status'] === 'late'));
    $absent  = count(array_filter($rows, fn($r) => $r['status'] === 'absent'));

    // Late counts as half-present (0.5 weight)
    $score = $total > 0
        ? round((($present + ($late * 0.5)) / $total) * 100)
        : 0;

    return compact('total', 'present', 'late', 'absent', 'score');
}
```

---

## Appendix C — Key Helper Functions (ci_helper.php)

| Function | Signature | Description |
|----------|-----------|-------------|
| `fs_truncate` | `(string $text, int $length)` | Strip tags and truncate text |
| `fs_stars` | `(float $rating, int $max)` | Render star characters |
| `fs_trust_label` | `(float $score)` | Return trust band label and colour |
| `fs_status_badge` | `(string $status)` | Render HTML badge for status |
| `fs_time_ago` | `(string $datetime)` | Human-readable relative time |
| `fs_duration` | `(int $days)` | Days to "X months Y years" string |
| `fs_worker_photo` | `(string $photo, int $size)` | Render worker avatar HTML |
| `fs_paginate` | `(int $total, int $limit, int $page, string $base_url)` | Render pagination HTML |

---

*End of Technical Documentation*  
*Farm Staff Registry v1.0.0 — OSACA, Ondo State, Nigeria*
