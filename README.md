# DOLE MIMAROPA E-Portal

A web-based **Electronic Portal System** developed for the **Department of Labor and Employment – MIMAROPA Regional Office** to provide centralized access to digital services, HR service requests, client tracking, and administrative management.

---

## 📌 Project Overview

The **DOLE MIMAROPA E-Portal** is designed to streamline and digitize selected services and internal processes of the DOLE MIMAROPA Regional Office.

The system provides a centralized platform where employees, clients, and authorized administrators can access services, submit requests, monitor request status, and manage service transactions.

The portal aims to reduce manual processing, improve request monitoring, provide timely notifications, and maintain organized digital records.

---

## 🎯 Objectives

* Digitize selected DOLE MIMAROPA services and processes.
* Provide an accessible online portal for clients and employees.
* Reduce manual and paper-based transactions.
* Improve monitoring and tracking of service requests.
* Provide automated email notifications.
* Centralize HR service request records.
* Generate reports for administrative monitoring.
* Improve transparency and visibility of request status.
* Provide a responsive and user-friendly interface.

---

## 🛠️ Technology Stack

### Backend

* **PHP**
* **CodeIgniter 4**
* **MySQL / MariaDB**

### Frontend

* **HTML5**
* **CSS3**
* **JavaScript**
* **Bootstrap 5**
* **Bootstrap Icons**
* **Manrope Font**

### Development Tools

* **Visual Studio Code**
* **WampServer**
* **Git**
* **GitHub**

### Other Technologies

* SMTP / Email Service
* AJAX / Fetch API
* PDF Generation
* CSV Export
* Session Authentication

---

## 🏗️ System Architecture

The system follows the **Model-View-Controller (MVC)** architecture provided by CodeIgniter 4.

```text
┌─────────────────────────────────────────┐
│             USER / CLIENT              │
│         Employee / Client / HR         │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│              FRONTEND                  │
│ HTML5 │ CSS3 │ Bootstrap 5 │ JavaScript│
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│          CODEIGNITER 4                  │
│                                         │
│  Routes → Controllers → Models → Views │
└──────────────────┬──────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────┐
│             DATABASE                   │
│            MySQL / MariaDB             │
└─────────────────────────────────────────┘
```

---

# 📂 Main System Modules

## 1. Client Portal

The client portal provides access to available digital services and allows clients to submit and monitor requests.

### Features

* Client login
* Service request submission
* Request reference number
* Request status tracking
* Client information management
* Email notifications
* Client Satisfaction Measurement (CSM)

---

## 2. Employee Portal

The employee portal provides authorized DOLE personnel with access to internal services and information.

### Features

* Employee authentication
* Employee dashboard
* HR service requests
* Request monitoring
* Service history
* Account/session management

---

## 3. HR Service Request System

The HR module manages employee/client requests for HR-related services.

### Request Information

The system can manage information such as:

* Reference Number
* Full Name
* Contact Number
* Email Address
* Date Requested
* Service Availed
* Period of Service
* Position
* Office / Division
* Certification Details
* Date Received
* Date Released
* Status
* Remarks

---

# 🔄 Request Status Workflow

The HR request follows a structured processing workflow.

```text
                ┌─────────────┐
                │    NEW      │
                │ APPLICATION │
                └──────┬──────┘
                       │
                       ▼
                ┌─────────────┐
                │ PROCESSING  │
                └──────┬──────┘
                       │
             ┌─────────┴─────────┐
             │                   │
             ▼                   ▼
      ┌──────────────┐    ┌─────────────┐
      │ FOR RELEASE  │    │   REJECTED  │
      └──────┬───────┘    └─────────────┘
             │
             ▼
      ┌──────────────┐
      │  COMPLETED   │
      └──────────────┘
```

---

# 🔢 Reference Number

Each request is assigned a unique reference number.

Example:

```text
HR-20260826143025
```

The reference number allows users and administrators to easily identify and track individual requests.

---

# 📊 Dashboard

The administrative dashboard provides an overview of HR service requests.

### Dashboard Metrics

* New Applications
* Processing
* For Release
* Completed
* Rejected

The dashboard allows authorized personnel to quickly monitor the current status of requests.

---

# 🔎 Search and Filtering

The request management module supports searching and filtering by:

* Reference Number
* Full Name
* Email Address
* Contact Number
* Service Availed
* Status
* Year
* Service Type

Pagination is also implemented for better performance when displaying large numbers of records.

---

# 📧 Email Notification System

The system supports automated email notifications for important request events.

### Notifications include:

* Request submission confirmation
* Request processing notification
* For Release notification
* Completion notification
* Rejection notification

Email notifications help clients stay informed about the progress of their requests.

---

# 🔐 Authentication & Security

The system implements session-based authentication using CodeIgniter 4.

### Security features include:

* User authentication
* Password hashing
* Password verification
* Session management
* Session regeneration
* Account status validation
* Role-based access
* Protected administrative functions
* Server-side validation
* Client-side form validation

Passwords are stored using secure password hashing rather than plain text.

---

# 🗄️ Database

The system uses **MySQL / MariaDB** as its relational database.

The database stores information related to:

```text
Users
  │
  ├── Accounts
  ├── Roles
  └── Status
       
HR Requests
  │
  ├── Request Information
  ├── Employment History
  ├── Status
  ├── Remarks
  └── Processing Dates

Client Satisfaction
  │
  ├── Feedback
  ├── Ratings
  └── Service Evaluation
```

---

# 📈 Reports

The system provides reporting functionality for administrative monitoring.

### Available Reports

* HR Service Request Reports
* Filtered Request Reports
* PDF Reports
* CSV Export
* Printable Reports

Reports can be filtered according to:

* Year
* Month
* Service Availed
* Search Keyword

---

# 🖨️ PDF & CSV Export

Authorized users can export filtered records for documentation and reporting purposes.

### PDF

Used for:

* Official reports
* Printing
* Documentation
* Administrative records

### CSV

Used for:

* Data analysis
* Spreadsheet processing
* Record management
* Data archiving

---

# 📝 Client Satisfaction Measurement

The portal includes a **Client Satisfaction Measurement (CSM)** component.

Clients can provide feedback regarding the service they received.

The CSM functionality supports the collection of:

* Client information
* Service information
* Satisfaction ratings
* Feedback
* Comments

This can help the office evaluate service delivery and identify areas for improvement.

---

# 📱 Responsive Design

The portal uses responsive web design principles to support different screen sizes.

Supported devices include:

* Desktop computers
* Laptops
* Tablets
* Mobile devices

The interface uses **Bootstrap 5** together with custom CSS for responsive layouts.

---

# 🎨 User Interface

The system follows a minimalist government/corporate design approach using DOLE-inspired branding.

### Design characteristics

* Clean interface
* Responsive layout
* Minimalist components
* Consistent typography
* Bootstrap components
* Modal dialogs
* Loading screens
* Confirmation dialogs
* Status indicators
* Responsive tables

Primary interface color:

```text
DOLE Blue
#10058C
```

---

# 📁 Project Structure

The project follows the CodeIgniter 4 structure.

```text
eportal-mimaropav2/
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
│   │   └── fonts/
│   └── index.php
│
├── writable/
│   ├── cache/
│   ├── logs/
│   ├── session/
│   └── uploads/
│
├── tests/
│
├── .env
├── composer.json
├── spark
└── README.md
```

---

# ⚙️ Installation

## Requirements

Before installing the system, make sure the following are available:

* PHP 8.x or compatible version
* MySQL / MariaDB
* Apache
* Composer
* Git
* WampServer or another PHP development environment

---

## 1. Clone the Repository

```bash
git clone https://github.com/rmaranan02dolemimaropa-hub/eportal-mimaropav2.git
```

Navigate to the project directory:

```bash
cd eportal-mimaropav2
```

---

## 2. Install Dependencies

Run:

```bash
composer install
```

---

## 3. Configure Environment

Copy the environment file:

```bash
cp env .env
```

For Windows, you can manually copy:

```text
env
```

and rename the copy to:

```text
.env
```

---

## 4. Configure Application

Edit `.env` and configure the application settings.

Example:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://eportal.test:8080/'
```

---

# 🗄️ Database Configuration

Configure the database connection in `.env`.

Example:

```ini
database.default.hostname = localhost
database.default.database = eportal
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

Create the database first:

```sql
CREATE DATABASE eportal;
```

Then configure the database credentials according to your environment.

---

# 🚀 Running the Application

Using CodeIgniter's development server:

```bash
php spark serve
```

The application will normally be available at:

```text
http://localhost:8080
```

For the local WampServer configuration, the project may be accessed through:

```text
http://eportal.test:8080/
```

---

# 🔑 User Roles

The system supports different levels of access.

Example roles include:

```text
Administrator
HR Administrator
Employee
Client
```

Access to certain modules and functions is controlled according to the user's role and account status.

---

# 🧩 Important Components

### Controller

Example:

```text
EportalController
```

Handles major portal operations such as:

* Dashboard
* Login
* Request submission
* Request processing
* Request updates
* Reports
* PDF export
* CSV export

### Models

Examples:

```text
HrRequestModel
HrEmploymentHistoryModel
ClientSatisfactionModel
UserModel
```

These models handle database operations for their respective modules.

### Mailer

The system includes mail functionality for sending:

```text
Request Confirmation
Processing Updates
Release Notifications
Completion Notifications
Rejection Notifications
```

---

# 🔒 Security Recommendations

For production deployment:

* Use HTTPS.
* Never commit `.env` to GitHub.
* Use strong database credentials.
* Use strong administrator passwords.
* Restrict database access.
* Keep CodeIgniter and dependencies updated.
* Configure appropriate file permissions.
* Disable development mode.
* Regularly back up the database.
* Protect uploaded files.
* Configure production error handling.

---

# 🌐 Deployment

The application can be deployed on a PHP-compatible web server.

Recommended production environment:

```text
Internet
    │
    ▼
Apache / Web Server
    │
    ▼
CodeIgniter 4
    │
    ├── Controllers
    ├── Models
    ├── Views
    └── Libraries
    │
    ▼
MySQL / MariaDB
```

The web server's document root should point to the CodeIgniter:

```text
/public
```

directory.

---

# 🧪 Development Environment

The project was developed and tested using:

```text
Operating System : Windows
Web Server       : Apache
PHP              : 8.x
Framework        : CodeIgniter 4
Database         : MySQL / MariaDB
IDE              : Visual Studio Code
Local Server     : WampServer
Version Control  : Git
Repository       : GitHub
```

---

# 📌 Project Status

```text
Development / Continuous Improvement
```

The system may continue to receive:

* Bug fixes
* Security improvements
* UI improvements
* Additional services
* Additional reports
* Performance improvements
* New administrative features

---

# 👨‍💻 Developer

**DOLE MIMAROPA IT Development**

Department of Labor and Employment
MIMAROPA Regional Office

---

# 📄 License

This project is intended for **DOLE MIMAROPA internal and official use**.

Unauthorized copying, redistribution, modification, or deployment outside the intended environment may be restricted.

---

# 📞 Support

For system-related concerns, configuration issues, bug reports, or feature requests, coordinate with the designated **IT/System Administrator** of DOLE MIMAROPA.

---

## ⭐ DOLE MIMAROPA E-Portal

**One Portal. Multiple Digital Services.**

```text
DOLE MIMAROPA
Electronic Portal System
```
