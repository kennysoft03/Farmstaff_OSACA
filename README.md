<div align="center">

<img src="assets/images/logo.png" alt="Farm Staff Registry Logo" width="120"/>

# Farm Staff Registry (FSR)

### *Trust. Transparency. Better Farms.*

**A centralised digital platform for agricultural workforce verification, trust scoring and employer reputation management in Ondo State, Nigeria.**

[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-EF4223?style=flat-square&logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](LICENSE)
[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.22681601.svg)](https://doi.org/10.5281/zenodo.22681601)
[![OSACA](https://img.shields.io/badge/Initiative-OSACA-darkgreen?style=flat-square)](https://farmstaff.ng)

---

[Features](#-features) • [Screenshots](#-screenshots) • [Installation](#-installation) • [Usage](#-usage) • [Architecture](#-architecture) • [Research](#-research--citation) • [License](#-license)

</div>

---

## 📌 What is Farm Staff Registry?

The **Farm Staff Registry (FSR)** is a web-based platform developed for the **Ondo State Agricultural Commodities Association (OSACA)** that enables agricultural employers to:

- **Register** farm workers with verified identity documents and photographs
- **Track** complete employment history across multiple farms
- **Verify** practical agricultural skills with employer-backed ratings
- **Monitor** attendance and compute reliability scores
- **Report** workplace incidents through a confidential, admin-moderated pipeline
- **Check** any worker's background before hiring using their phone number or registry ID
- **Rate** farms anonymously — building a public Farm Reputation Score

Each worker receives a single central profile and a dynamic **Trust Score (0–100)** that reflects their verified work performance, conduct and attendance history across all employers on the platform.

---

## 🚨 Problem Being Addressed

Agricultural labour markets in Nigeria — and across Sub-Saharan Africa — are predominantly informal. Employers hiring farm workers face:

| Challenge | Impact |
|-----------|--------|
| No verifiable identity records | Risk of hiring unknown individuals |
| No employment history | Repeat misconduct goes undetected |
| No skill verification | Mismatch between claimed and actual skills |
| No accountability mechanism | Misconduct without consequence |
| Information asymmetry | Employers and workers both operate blindly |

> *"The market for lemons"* — Akerlof (1970) — describes exactly this: when buyers cannot verify quality, bad actors drive out good ones. FSR directly solves this for agricultural labour markets.

The Farm Staff Registry creates a **trusted, centralised, cross-employer record** that benefits all parties — employers make better hiring decisions, good workers build verifiable reputations, and the agricultural labour market becomes more efficient and accountable.

---

## ✨ Features

### 👤 Worker Management
- Central worker profile with photo, ID verification and biographic data
- Auto-generated unique Registry ID (`FSR-XXXXXX`)
- Trust Score initialised at 50, dynamically adjusted by verified events
- Status management: Active · Inactive · Flagged · Suspended

### 📋 Employment History
- Portable work history ledger across all registered employers
- Role, start/end dates, duration, responsibilities and employer remarks
- Multi-employer timeline visible during background checks

### 🏆 Skills Verification
- Employer-submitted skill ratings (1–5 stars, proficiency levels)
- Admin verification badge for confirmed practical skills
- Common agricultural skill library (Tractor Driving, Harvesting, Spraying, etc.)

### 📅 Attendance Tracking
- Daily attendance logging (present / absent / late / half-day / leave)
- Monthly calendar view with check-in/check-out times
- Computed Attendance Score: `((Present + Late×0.5) / Total) × 100`

### ⚠️ Incident Reporting
- Confidential misconduct reports with evidence file upload
- Admin moderation gate — incidents only affect records after review
- Severity classification: Minor · Moderate · Severe
- Disciplinary points deducted from Trust Score on acceptance

### 💯 Trust Score System
- Multi-dimensional scoring: performance ratings + attendance + incidents
- Score range: 0–100 (High ≥70 · Medium 40–69 · Low <40)
- Full immutable score change log with reasons and timestamps
- Manual admin adjustment with audit trail

### 🔍 Background Check
- Search any worker by phone number or Registry ID before hiring
- Returns: identity, work history, skills, ratings, trust score, incident count
- Every search logged for audit purposes

### 🏚️ Farm Reputation Score
- Workers anonymously rate farms on: conditions, safety, payment, treatment
- Admin moderation before ratings affect public score
- Weighted average displayed on employer profile

### 🛡️ Admin Panel
- Full platform dashboard with KPIs and pending action alerts
- Worker and employer management with status controls
- Incident review queue with accept/reject workflow
- Reports and analytics with Chart.js visualisations
- Complete audit trail of all system activity
- Trust score management and skill verification

---

## 📸 Screenshots

> *Screenshots can be added to a `/docs/screenshots/` folder and referenced here.*

| Public Homepage | Employer Dashboard | Admin Panel |
|:-:|:-:|:-:|
| ![Homepage](docs/screenshots/homepage.png) | ![Dashboard](docs/screenshots/dashboard.png) | ![Admin](docs/screenshots/admin.png) |

| Worker Profile | Background Check | Incident Review |
|:-:|:-:|:-:|
| ![Worker](docs/screenshots/worker.png) | ![Check](docs/screenshots/bgcheck.png) | ![Incident](docs/screenshots/incident.png) |

---

## 🛠 Technologies Used

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Language | PHP | 8.1+ |
| Backend Framework | CodeIgniter | 3.1.x |
| Database | MySQL | 8.0+ |
| Web Server | Apache | 2.4+ |
| Admin UI | KT Metronic (Demo 1) | Bundled |
| Public UI | Custom CSS (farmstaff.css) | 1.0 |
| JavaScript | jQuery + Vanilla JS | 3.x |
| Charts | Chart.js | 3.x |
| Icons | Flaticon · Line Awesome · FontAwesome | Bundled |
| Password Hashing | PHP `password_hash()` | bcrypt cost 12 |
| Development Env | Laragon | 6.x |

---

## 📦 Installation

### Prerequisites

Before you begin, ensure you have:
- **PHP 8.1+** with extensions: `mysqli`, `mbstring`, `openssl`, `fileinfo`
- **MySQL 8.0+**
- **Apache 2.4+** with `mod_rewrite` enabled
- **Laragon** (recommended for Windows local development) or XAMPP/WAMP

---

### Step 1 — Clone the Repository

```bash
git clone https://github.com/kennysoft03/Farmstaff_OSACA.git
cd Farmstaff_OSACA
```

For **Laragon**, place the project in:
```
C:\laragon\www\Farmstaff\
```

---

### Step 2 — Create the Database

Open HeidiSQL, phpMyAdmin or MySQL CLI and run:

```sql
CREATE DATABASE farmstaff 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;
```

Then import the schema:
```bash
mysql -u root -p farmstaff < farmstaff_db.sql
```

Or use HeidiSQL: **File → Run SQL file** → select `farmstaff_db.sql`

---

### Step 3 — Configure the Database Connection

Copy the example config file:
```bash
cp application/config/env-database.example.php application/config/env-database.php
```

Edit `application/config/env-database.php` with your database credentials:

```php
$db['default'] = array(
    'hostname' => '127.0.0.1',
    'username' => 'root',        // your MySQL username
    'password' => '',            // your MySQL password
    'database' => 'farmstaff',   // database name
    'dbdriver' => 'mysqli',
    // ... (other settings remain unchanged)
);
```

> ⚠️ **Never commit `env-database.php`** — it is listed in `.gitignore`

---

### Step 4 — Set the Base URL

Edit `application/config/config.php`:

```php
$config['base_url'] = 'http://farmstaff.test:9090/';
```

Replace with your actual local or production URL.

---

### Step 5 — Set Directory Permissions

Ensure these directories are writable:

```bash
chmod 755 sessions/
chmod 755 uploads/
chmod 755 uploads/workers/
chmod 755 uploads/incidents/
chmod 755 uploads/employers/
chmod 755 application/logs/
```

On **Windows/Laragon**, these folders are writable by default.

---

### Step 6 — Configure Laragon Virtual Host (Windows)

In Laragon, the virtual host is created automatically when you place the project in `C:\laragon\www\Farmstaff\`.

Add to your `hosts` file (`C:\Windows\System32\drivers\etc\hosts`):
```
127.0.0.1  farmstaff.test
```

---

### Step 7 — Create the Default Admin User

Visit this URL in your browser:
```
http://farmstaff.test:9090/admin/create-admin
```

This creates the default administrator account.

---

## 🚀 How to Run It

### Local Development (Laragon)

1. Start **Laragon** and ensure Apache and MySQL are running
2. Visit: `http://farmstaff.test:9090/`

### Default Login Credentials

| Portal | URL | Username | Password |
|--------|-----|----------|----------|
| **Admin Panel** | `/admin` | `admin` | `Admin@1234` |
| **Employer Portal** | `/login` | *(register first)* | *(set during registration)* |

> ⚠️ **Change the admin password immediately** after first login.

### Key URLs

| Page | URL |
|------|-----|
| Public Homepage | `http://farmstaff.test:9090/` |
| Employer Login | `http://farmstaff.test:9090/login` |
| Employer Register | `http://farmstaff.test:9090/register` |
| Employer Dashboard | `http://farmstaff.test:9090/dashboard` |
| Background Check | `http://farmstaff.test:9090/dashboard/background-check` |
| Admin Login | `http://farmstaff.test:9090/admin` |
| Admin Dashboard | `http://farmstaff.test:9090/admin/dashboard` |

---

## 🌐 Deployment

### Shared Hosting (cPanel)

1. Upload all files to `/public_html/` or a subdirectory
2. Create a MySQL database and user via **cPanel → MySQL Databases**
3. Import `farmstaff_db.sql` via **phpMyAdmin**
4. Copy and edit `env-database.php` with your cPanel DB credentials
5. Update `base_url` in `config.php` to your live domain
6. Ensure **PHP 8.1** is selected in MultiPHP Manager
7. Ensure `mod_rewrite` is enabled (contact host if needed)
8. Set directory permissions (755 for uploads and sessions)

### Production Security Checklist

```
[ ] Set ENVIRONMENT = 'production' in index.php
[ ] Set db_debug = FALSE in env-database.php
[ ] Change default admin password from Admin@1234
[ ] Delete or restrict access to install.php
[ ] Configure HTTPS / SSL certificate
[ ] Enable CSRF protection in config.php
[ ] Set display_errors = 0 in PHP configuration
[ ] Ensure uploads/ is not directly executable
[ ] Set up regular database backups
[ ] Review .htaccess for production hardening
```

---

## 🏗 Architecture

```
┌─────────────────────────────────────────────┐
│              THREE PORTALS                  │
│  Public Site  │  Employer Portal  │  Admin  │
└───────┬───────┴─────────┬─────────┴────┬────┘
        │                 │              │
        └─────────────────▼──────────────┘
                    CodeIgniter 3 MVC
                 Controllers · Models · Views
                          │
                    Farmstaff_model
              (Trust Score · Reputation Engine
               Audit Logger · Notifications)
                          │
                    MySQL Database
                    (13 core tables)
```

**Full technical documentation:** [`docs/TECHNICAL_DOCUMENTATION.md`](docs/TECHNICAL_DOCUMENTATION.md)

### Database Tables

| Table | Purpose |
|-------|---------|
| `fs_admin_users` | Platform administrators |
| `fs_employers` | Registered farm employers |
| `fs_workers` | Central worker registry |
| `fs_work_history` | Portable employment ledger |
| `fs_skills` | Skill records and verification |
| `fs_attendance` | Daily attendance log |
| `fs_incidents` | Confidential misconduct reports |
| `fs_farm_ratings` | Worker ratings of employers |
| `fs_worker_ratings` | Employer ratings of workers |
| `fs_trust_score_log` | Immutable score change history |
| `fs_background_checks` | Search audit trail |
| `fs_audit_logs` | Full system activity log |
| `fs_notifications` | In-app notifications |

---

## 📚 Research / Project Background

This platform was developed as part of research into **digital trust frameworks for informal agricultural labour markets** in Sub-Saharan Africa, addressing the information asymmetry problem documented by Akerlof (1970) in the context of Nigerian agricultural employment.

### Research Context

> **Title:** *"A Digital Trust and Verification Framework for Agricultural Workforce Management: Design and Implementation of the Farm Staff Registry System"*
>
> **Organisation:** Ondo State Agricultural Commodities Association (OSACA), Ondo State, Nigeria
>
> **Problem Domain:** Informal agricultural labour markets, information asymmetry, workforce accountability
>
> **Novel Contributions:**
> 1. A centralised agricultural labour registry architecture for informal employment contexts in Sub-Saharan Africa
> 2. A multi-dimensional, event-driven Trust Score model (T(w) ∈ [0,100]) computed from heterogeneous employer inputs
> 3. A bidirectional reputation system providing mutual accountability for both workers and employers

### Trust Score Model

```
T(w) ∈ [0, 100],   T₀ = 50

Score Change = (rating - 3) × 5     [from performance ratings]
Score Change = -disciplinary_points  [from accepted incidents]

Bands:  High Trust ≥ 70
        Medium Trust 40–69
        Low Trust < 40
```

### Related Work
- Akerlof, G.A. (1970). The market for lemons. *Quarterly Journal of Economics*
- Resnick, P. et al. (2000). Reputation systems. *Communications of the ACM*
- Dellarocas, C. (2003). The digitization of word-of-mouth. *Management Science*
- FAO (2023). Agricultural employment in Nigeria

---

## 📄 How to Cite

If you use this software or reference it in academic work, please cite it as:

### Software Citation (APA)
```
[Author(s)]. (2026). Farm Staff Registry (FSR) [Computer software]. 
Ondo State Agricultural Commodities Association (OSACA). 
https://doi.org/10.5281/zenodo.22681601
```

### Software Citation (BibTeX)
```bibtex
@software{farmstaff_registry_2026,
  author       = {{OSACA Development Team}},
  title        = {{Farm Staff Registry (FSR): A Centralised Digital Platform 
                   for Agricultural Workforce Verification and Trust Scoring}},
  year         = {2026},
  publisher    = {Ondo State Agricultural Commodities Association},
  url          = {https://github.com/kennysoft03/Farmstaff_OSACA},
  doi          = {10.5281/zenodo.22681601},
  note         = {Version 1.0.0}
}
```

### Research Paper Citation
> *(Update this section when the associated journal paper is published)*
```
[Author(s)]. (2026). A Digital Trust and Verification Framework for 
Agricultural Workforce Management. [Journal Name]. 
https://doi.org/10.5281/zenodo.22681601
```

### Zenodo Record
🔗 **[https://doi.org/10.5281/zenodo.22681601](https://doi.org/10.5281/zenodo.22681601)**

---

## 🤝 Contributing

Contributions, issues and feature requests are welcome.

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m 'Add: description of feature'`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

### Planned Future Features
- [ ] Mobile app (React Native) for offline attendance capture
- [ ] Blockchain integration for immutable work history (Hyperledger Fabric)
- [ ] ML-based trust prediction from historical patterns
- [ ] NIN API integration for real-time identity verification (NIMC)
- [ ] Multi-state expansion beyond Ondo State
- [ ] Worker self-service portal for profile visibility
- [ ] SMS notifications for workers and employers

---

## 📁 Project Structure

```
Farmstaff/
├── application/
│   ├── config/          # Configuration files
│   ├── controllers/     # Home · Employer · Farmadmin
│   ├── models/          # Farmstaff_model (core)
│   ├── helpers/         # ci_helper
│   └── views/
│       ├── public/      # Public website views
│       ├── employer/    # Employer dashboard views
│       └── admin/       # Admin panel views
├── assets/              # Public CSS, JS, images
├── assetsa/             # KT Metronic admin template
├── uploads/             # User-uploaded files
├── sessions/            # Server-side session storage
├── docs/                # Technical documentation
│   └── TECHNICAL_DOCUMENTATION.md
├── .gitignore
├── farmstaff_db.sql     # Database schema (import once, then delete)
├── index.php            # CodeIgniter front controller
└── README.md
```

---

## 📜 License

```
MIT License

Copyright (c) 2026 Ondo State Agricultural Commodities Association (OSACA)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

---

<div align="center">

**Farm Staff Registry** — Built with ❤️ for the farmers and workers of Ondo State

*An initiative of the [Ondo State Agricultural Commodities Association (OSACA)](https://osaca.ng)*

</div>
