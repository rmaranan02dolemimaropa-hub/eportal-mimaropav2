# DOLE MIMAROPA E-Portal System

> **Department of Labor and Employment – MIMAROPA Regional Office**
> **Electronic Portal for Digital Service Requests and Client Assistance**

The **DOLE MIMAROPA E-Portal System** is a web-based application developed to provide a centralized and efficient platform for managing digital service requests, client transactions, and internal Human Resource (HR) service processes of the **Department of Labor and Employment – MIMAROPA Regional Office**.

The system aims to reduce manual processing, improve request monitoring, provide real-time status tracking, and streamline communication between clients and the responsible DOLE personnel.

---

## 📌 System Overview

The DOLE MIMAROPA E-Portal provides an online platform where users can:

* Submit requests for available DOLE services
* Monitor the status of submitted requests
* Receive request reference numbers
* Receive email notifications regarding request updates
* Allow authorized personnel to process and manage requests
* Track processing, release, completion, and rejection dates
* Manage users and system access
* Generate reports
* Collect client satisfaction feedback
* Print service-related documents and reports

The system follows a centralized workflow:

```text
Client
   │
   ▼
Submit Service Request
   │
   ▼
Generate Reference Number
   │
   ▼
HR/Admin Review
   │
   ▼
Processing
   │
   ├──► Rejected
   │
   └──► For Released
             │
             ▼
         Completed
             │
             ▼
       Client Feedback
```

---

# ✨ Features

## 👤 Client Portal

Clients can access the portal to submit and monitor their service requests.

### Available Functions

* Online service request submission
* Personal information entry
* Service selection
* Employment information
* Request date selection
* Automatic reference number generation
* Request status monitoring
* Email notifications
* Client Satisfaction Measurement (CSM)
* Printable service documents

---

## 🏢 HR / Administrator Portal

Authorized personnel can manage and process submitted requests.

### Dashboard

The dashboard provides an overview of requests categorized by status:

* **New Application**
* **Processing**
* **For Released**
* **Completed**
* **Rejected**

The dashboard allows administrators to quickly monitor the current workload and request status.

---

## 📋 Service Request Management

Authorized users can:

* View submitted requests
* Search requests
* Filter requests
* Update request status
* Add processing remarks
* Record processing dates
* Record release dates
* Record completion dates
* Record rejection dates
* Delete requests when authorized
* View complete request information

### Searchable Information

Requests can be searched using:

* Reference Number
* Full Name
* Email Address
* Contact Number
* Service Availed
* Status

---

# 📝 HR Services

The system supports various HR-related services, including:

* Service Record
* Certificate of Employment
* Certificate of Employment with Compensation
* Certification of No Pending Case
* Certification of Last Salary Received
* Certification of Leave Credits
* Certification of Leave Without Pay (LWOP)
* Individual Performance Commitment and Review (IPCR)
* 201 Files / Documents
* Loan Assistance
* Leave Balance Inquiry
* Other HR-related requests

Additional services can be added through the system configuration and database.

---

# 📊 Reports

The system includes a reporting module for monitoring and analyzing service requests.

### Report Filters

Reports can be filtered by:

* Year
* Month
* Service Availed
* Search Keyword

### Export Functions

The system supports:

* PDF Export
* CSV Export
* Printable reports

This allows authorized personnel to generate reports for monitoring, documentation, and administrative purposes.

---

# 📧 Email Notification

The system can send email notifications to clients when important changes occur in their request.

Notifications may be sent for:

### New Request

Confirmation that the request has been successfully submitted.

### Processing

Notification that the request is currently being processed.

### For Release

Notification that the requested document/service is ready for release.

### Completed

Notification that the request has been completed.

### Rejected

Notification that the request has been rejected, including applicable remarks.

---

# ⭐ Client Satisfaction Measurement

After completion of a service request, clients may be provided with a **Client Satisfaction Measurement (CSM)** form.

The system supports:

* Client feedback submission
* Satisfaction rating
* Comments and suggestions
* English CSM form
* Tagalog CSM form
* Printable CSM questionnaire

The collected feedback can be used for service improvement and monitoring.

---

# 🔐 User Management

The administrator module provides user account management.

Authorized administrators can:

* Create users
* Update users
* Delete users
* Activate/deactivate accounts
* Assign user roles
* Manage account access

### Example Roles

```text
Administrator
HR Administrator
HR Personnel
Client
```

Access to system functions should be restricted according to the user's assigned role.

---

# 🔑 Authentication & Security

The system implements authentication and session management for protected pages.

Security mechanisms include:

* Username and password authentication
* Password hashing
* Password verification
* Active/inactive account checking
* Session regeneration after login
* Protected administrative pages
* Role-based access
* Server-side validation
* Client-side form validation
* Database query protection through CodeIgniter's database layer

Passwords should never be stored in plain text.

---

# 🛠️ Technology Stack

## Backend

* **PHP**
* **CodeIgniter 4**
* **MySQL / MariaDB**

## Frontend

* **HTML5**
* **CSS3**
* **JavaScript**
* **Bootstrap 5**
* **Bootstrap Icons**

## Development Tools

* Visual Studio Code
* WampServer
* Git
* GitHub

---

# 📁 Project Structure

The project follows the standard CodeIgniter 4 structure.

```text
eportal-mimaropa/
│
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Database/
│   │   ├── Migrations/
│   │   └── Seeds/
│   ├── Filters/
│   ├── Libraries/
│   ├── Models/
│   └── Views/
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── vendor/
│   └── index.php
│
├── system/
│
├── writable/
│
├── tests/
│
├── .env
├── composer.json
├── spark
└── README.md
```

---

# 💻 Installation

## 1. Clone the Repository

```bash
git clone https://github.com/your-organization/eportal-mimaropa.git
```

Navigate to the project directory:

```bash
cd eportal-mimaropa
```

---

## 2. Install Dependencies

Install the required PHP dependencies using Composer:

```bash
composer install
```

---

## 3. Configure Environment

Copy the environment template:

```bash
copy env .env
```

For Linux/macOS:

```bash
cp env .env
```

Open the `.env` file and configure the application.

Example:

```env
CI_ENVIRONMENT = development

app.baseURL = 'http://eportal.test:8080/'
```

---

# 🗄️ Database Configuration

Create a database in MySQL/MariaDB.

Example:

```sql
CREATE DATABASE eportal_mimaropa;
```

Configure the database connection in `.env`:

```env
database.default.hostname = localhost
database.default.database = eportal_mimaropa
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306
```

---

# 🧱 Database Migration

If migrations are configured, run:

```bash
php spark migrate
```

To view migration status:

```bash
php spark migrate:status
```

---

# 🌱 Database Seeding

If database seeders are available:

```bash
php spark db:seed DatabaseSeeder
```

---

# 🚀 Running the Application

The application can be started using CodeIgniter's development server:

```bash
php spark serve
```

The system will normally be available at:

```text
http://localhost:8080
```

For a local virtual host configuration, the project may be accessed through:

```text
http://eportal.test:8080/
```

---

# 🌐 Production Deployment

For production deployment, the web server's document root must point to the **public** directory.

Example:

```text
C:/wamp64/www/eportal-mimaropa/public
```

Do not expose the entire CodeIgniter project directory publicly.

The recommended structure is:

```text
Web Server
     │
     ▼
public/
     │
     ├── index.php
     ├── assets/
     └── ...
     
Application files
     │
     ├── app/
     ├── system/
     └── writable/
```

---

# ⚙️ Configuration

Important configuration files include:

```text
app/Config/App.php
app/Config/Database.php
app/Config/Email.php
app/Config/Routes.php
```

Environment-specific settings should preferably be placed in:

```text
.env
```

Do not commit sensitive credentials to GitHub.

---

# 📧 Email Configuration

The system can be configured to send email notifications through an SMTP server.

Example configuration:

```env
email.protocol = smtp
email.SMTPHost = smtp.example.com
email.SMTPUser = noreply@example.com
email.SMTPPass = your-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

> **Security Notice:** Never commit SMTP passwords, API keys, credentials, or other secrets to the repository.

---

# 🔄 Request Status Workflow

A service request follows the following workflow:

```text
NEW APPLICATION
       │
       ▼
   PROCESSING
       │
       ├──────────────► REJECTED
       │
       ▼
  FOR RELEASE
       │
       ▼
   COMPLETED
       │
       ▼
CLIENT FEEDBACK
```

### Status Description

| Status          | Description                             |
| --------------- | --------------------------------------- |
| New Application | Newly submitted request                 |
| Processing      | Request is currently being processed    |
| For Released    | Document/service is ready for release   |
| Completed       | Request has been successfully completed |
| Rejected        | Request was rejected                    |

---

# 🔢 Reference Number

Each submitted request receives a unique reference number.

Example format:

```text
HR-20260829143025
```

The reference number can be used by the client and authorized personnel to identify and monitor a request.

---

# 🔎 Request Search & Filtering

The request management page supports filtering using:

```text
Year
Status
Service Availed
```

It also supports keyword searching for:

```text
Reference Number
Full Name
Email Address
Contact Number
Service Availed
Status
```

---

# 🖨️ Printing

The system provides printable documents and reports for authorized users.

Supported outputs may include:

* Service documents
* CSM questionnaires
* Reports
* Request details
* English forms
* Tagalog forms

---

# 📱 Responsive Design

The E-Portal uses a responsive interface designed to work across:

* Desktop computers
* Laptops
* Tablets
* Mobile devices

The frontend uses Bootstrap responsive utilities together with custom CSS.

---

# 🎨 Design

The system follows a minimalist government/corporate interface inspired by the visual identity of DOLE MIMAROPA.

Primary system color:

```css
--dole-blue: #10058C;
```

The interface emphasizes:

* Clean layouts
* Readability
* Accessibility
* Consistent spacing
* Responsive components
* Simple navigation
* Clear status indicators

---

# 🧪 Development

During development, use:

```env
CI_ENVIRONMENT = development
```

For production:

```env
CI_ENVIRONMENT = production
```

Before deployment, make sure:

* Debug mode is disabled
* Production environment is enabled
* Database credentials are secured
* SMTP credentials are secured
* Writable permissions are configured
* Error logs are protected
* `.env` is not publicly accessible

---

# 🧹 Cache & Troubleshooting

Clear CodeIgniter cache when necessary:

```bash
php spark cache:clear
```

Check available Spark commands:

```bash
php spark
```

Check routes:

```bash
php spark routes
```

Check migration status:

```bash
php spark migrate:status
```

---

# 🐛 Common Issues

## 404 Page Not Found

Check the configured routes:

```text
app/Config/Routes.php
```

Then run:

```bash
php spark routes
```

Make sure the web server document root points to:

```text
public/
```

---

## Database Connection Error

Verify:

```text
Database Host
Database Name
Database Username
Database Password
Database Port
```

in `.env`.

---

## Email Not Sending

Check:

```text
SMTP Host
SMTP Username
SMTP Password
SMTP Port
SMTP Encryption
```

Also check the CodeIgniter logs:

```text
writable/logs/
```

---

# 🔒 Production Security Checklist

Before deploying the system:

* [ ] Set `CI_ENVIRONMENT=production`
* [ ] Disable debug mode
* [ ] Use HTTPS
* [ ] Secure database credentials
* [ ] Secure SMTP credentials
* [ ] Do not commit `.env`
* [ ] Use strong administrator passwords
* [ ] Restrict administrative access
* [ ] Configure proper file permissions
* [ ] Enable regular database backups
* [ ] Protect `writable/`
* [ ] Keep CodeIgniter and dependencies updated
* [ ] Review user roles and permissions
* [ ] Review server and application logs

---

# 💾 Backup & Recovery

Regular backups should be maintained for:

### Database

```text
eportal_mimaropa
```

### Application

```text
app/
public/
```

### Configuration

```text
.env
```

Sensitive configuration files should be backed up securely and must not be published in the repository.

---

# 🔀 Git Workflow

Recommended workflow:

```bash
git status
```

Create a feature branch:

```bash
git checkout -b feature/request-management
```

Commit changes:

```bash
git add .
git commit -m "Add request management improvements"
```

Push the branch:

```bash
git push origin feature/request-management
```

Merge changes into the main branch after testing.

---

# 📌 Recommended Commit Messages

Use clear and descriptive commit messages.

Examples:

```text
feat: add service request tracking
fix: resolve request modal data issue
fix: correct login session handling
feat: add PDF report export
feat: add client satisfaction measurement
refactor: improve request controller
style: update eportal dashboard UI
docs: update README
```

---

# 🧑‍💻 Development Guidelines

When modifying the system:

1. Follow the CodeIgniter 4 MVC structure.
2. Keep business logic inside controllers/services where appropriate.
3. Use models for database operations.
4. Validate all user input.
5. Avoid exposing sensitive information.
6. Use parameterized/database-builder queries.
7. Keep frontend JavaScript modular.
8. Maintain responsive design.
9. Test changes before deployment.
10. Document significant system changes.

---

# 📈 Future Enhancements

Potential future improvements include:

* [ ] Online client account registration
* [ ] SMS notifications
* [ ] QR-code request tracking
* [ ] Advanced analytics dashboard
* [ ] Digital document release
* [ ] Electronic signatures
* [ ] Automated document generation
* [ ] Expanded HR services
* [ ] Audit trail
* [ ] Two-factor authentication
* [ ] API integration
* [ ] Mobile application
* [ ] Enhanced reporting and analytics

---

# 👥 User Roles

| Role             | Access                                     |
| ---------------- | ------------------------------------------ |
| Client           | Submit and monitor service requests        |
| HR Personnel     | Process assigned requests                  |
| HR Administrator | Manage HR requests and users               |
| Administrator    | Manage system-wide configuration and users |

Access should be implemented according to the principle of **least privilege**.

---

# 📞 Support

For technical concerns, system maintenance, or account-related issues, contact the designated **DOLE MIMAROPA IT/System Administrator**.

---

# 📄 License

This system is developed for the internal and official use of the:

**Department of Labor and Employment – MIMAROPA Regional Office**

Unauthorized reproduction, distribution, modification, or use outside the authorized purpose of the system should be subject to applicable agency policies and regulations.

---

# 🏛️ DOLE MIMAROPA E-Portal

**One Portal. Multiple Digital Services.**

> Digitalizing service delivery through a centralized, efficient, and user-friendly electronic portal.
