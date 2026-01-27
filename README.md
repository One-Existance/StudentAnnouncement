# Student Announcement System

A comprehensive PHP-based web application for managing university announcements with role-based access control. Built using MVC architecture with secure authentication and modern web development practices.

## 📋 Table of Contents

- [Features](#features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Security Features](#security-features)
- [Development Guidelines](#development-guidelines)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Core Functionality
- **Role-Based Authentication**: Secure login system with three user roles (Admin, Lecturer, Student)
- **Announcement Management**: Create, view, and manage announcements with rich text support
- **Department & Course Management**: Organize announcements by academic departments and courses
- **View Tracking**: Track which students have viewed announcements
- **RESTful API**: JSON API endpoints for programmatic access
- **Responsive Design**: Mobile-friendly web interface

### User Roles & Permissions

#### 👨‍🎓 **Students**
- View all active announcements
- Browse announcements by department or course
- View announcement details with read tracking
- Access personal dashboard

#### 👨‍🏫 **Lecturers**
- All student permissions
- Create new announcements
- Post to specific departments and courses
- View and manage their own announcements
- Access lecturer dashboard

#### 👨‍💼 **Administrators**
- Full system access
- Manage users (create admins, lecturers, students)
- Manage departments and courses
- Create announcements for any department
- View system statistics and analytics
- Access comprehensive admin dashboard

## 🛠 Technology Stack

### Backend
- **PHP 7.4+**: Server-side scripting language
- **MariaDB/MySQL**: Relational database management system
- **PDO**: PHP Data Objects for secure database interactions

### Frontend
- **HTML5**: Semantic markup structure
- **CSS3**: Responsive styling and layouts
- **JavaScript**: Client-side interactivity (minimal, focused on UX)

### Architecture
- **MVC Pattern**: Model-View-Controller architecture for clean separation of concerns
- **UUID**: Universally Unique Identifiers for secure, non-sequential IDs
- **Session Management**: PHP sessions for user authentication
- **Prepared Statements**: SQL injection prevention

### Development Tools
- **Git**: Version control system
- **Composer**: PHP dependency management (future use)
- **XAMPP**: Local development environment

## 📁 Project Structure

```
StudentAnnouncement/
├── 📁 api/                          # REST API endpoints
│   ├── create_announcement.php     # POST: Create new announcement
│   ├── get_announcements.php       # GET: Retrieve announcements
│   ├── get_departments.php         # GET: Retrieve departments
│   └── get_users.php               # GET: Retrieve users
├── 📁 config/                       # Configuration files
│   ├── db.php                      # Database connection (NOT in repo)
│   └── db.sample.php               # Sample database config
├── 📁 controllers/                  # Business logic layer
│   ├── AnnouncementController.php  # Announcement operations
│   ├── AuthController.php          # Authentication & authorization
│   ├── CourseController.php        # Course management
│   ├── DepartmentController.php    # Department management
│   └── UserController.php          # User management
├── 📁 includes/                     # Helper functions and utilities
│   └── helpers.php                 # Utility functions
├── 📁 models/                       # Data access layer
│   ├── Announcement.php            # Announcement data model
│   ├── AnnouncementView.php        # View tracking model
│   ├── Course.php                  # Course data model
│   ├── Department.php              # Department data model
│   └── User.php                    # User data model
├── 📁 public/                       # Web interface (publicly accessible)
│   ├── index.php                   # Application entry point
│   ├── login.php                   # User authentication
│   ├── logout.php                  # User logout
│   ├── admin_dashboard.php         # Administrator dashboard
│   ├── lecturer_dashboard.php      # Lecturer dashboard
│   ├── student_dashboard.php       # Student dashboard
│   ├── announcements.php           # Announcement listing
│   ├── create_announcement.php     # Announcement creation form
│   ├── view_announcement.php       # Announcement detail view
│   ├── departments.php             # Department management
│   └── unauthorized.php            # Access denied page
├── 📄 setup_sample_data.php         # Database seeding script
├── 📄 .gitignore                    # Git ignore rules
└── 📄 README.md                     # This documentation
```

## 🗄 Database Schema

### Tables Overview

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `users` | User accounts and authentication | id, name, email, role, department, avatar |
| `departments` | Academic departments | id, name, description |
| `courses` | Course information | id, course_name, course_code, department_id |
| `announcements` | Announcement posts | id, title, message, posted_by, department_id, course_id |
| `announcement_views` | Read tracking | id, announcement_id, student_id, viewed_at |

### Detailed Schema

#### users
```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('admin','lecturer','student') NOT NULL,
    department VARCHAR(255),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### departments
```sql
CREATE TABLE departments (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### courses
```sql
CREATE TABLE courses (
    id CHAR(36) PRIMARY KEY,
    course_name VARCHAR(255) NOT NULL,
    course_code VARCHAR(50) NOT NULL UNIQUE,
    department_id CHAR(36) NOT NULL,
    lecturer_id CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (lecturer_id) REFERENCES users(id)
);
```

#### announcements
```sql
CREATE TABLE announcements (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    posted_by CHAR(36) NOT NULL,
    department_id CHAR(36) NOT NULL,
    course_id CHAR(36),
    attachment VARCHAR(255),
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id),
    FOREIGN KEY (department_id) REFERENCES departments(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);
```

#### announcement_views
```sql
CREATE TABLE announcement_views (
    id CHAR(36) PRIMARY KEY,
    announcement_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (announcement_id) REFERENCES announcements(id),
    FOREIGN KEY (student_id) REFERENCES users(id),
    UNIQUE KEY unique_view (announcement_id, student_id)
);
```

## 🚀 Installation

### Prerequisites
- **XAMPP** (or similar Apache + MariaDB/MySQL + PHP stack)
- **PHP 7.4 or higher**
- **MariaDB/MySQL 10.0 or higher**
- **Git** (for cloning the repository)

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/One-Existance/StudentAnnouncementSystem.git
   cd StudentAnnouncementSystem
   ```

2. **Setup XAMPP Environment**
   - Copy the project to your XAMPP htdocs folder:
     ```
     C:\xampp\htdocs\StudentAnnouncementSystem
     ```
   - Start XAMPP Control Panel
   - Start Apache and MySQL services

3. **Database Setup**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create a new database: `STUDENT_ANNOUNCEMENT`
   - Import the schema (see Database Schema section above)

4. **Configuration**
   ```bash
   # Copy sample config
   cp config/db.sample.php config/db.php

   # Edit config/db.php with your database credentials
   nano config/db.php
   ```

5. **Sample Data Setup** (Optional but recommended)
   ```bash
   php setup_sample_data.php
   ```

6. **Access the Application**
   - Open your browser: http://localhost/StudentAnnouncementSystem/public/
   - Login with sample accounts (see Usage section)

## ⚙ Configuration

### Database Configuration
Edit `config/db.php` with your database credentials:

```php
<?php
$host = 'localhost';           // Database host
$db = 'STUDENT_ANNOUNCEMENT';  // Database name
$user = 'root';                // Database username
$pass = '';                    // Database password (leave empty for XAMPP default)
```

### Environment Variables (Future Enhancement)
For production deployment, consider using environment variables:
```bash
DB_HOST=localhost
DB_NAME=student_announcement
DB_USER=your_db_user
DB_PASS=your_secure_password
```

## 📖 Usage

### Demo Accounts
After running `setup_sample_data.php`, use these accounts:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@university.edu | password |
| Lecturer | lecturer@university.edu | password |
| Student | student@university.edu | password |

### User Workflows

#### Student Workflow
1. Login with student credentials
2. View dashboard with recent announcements
3. Browse all announcements or filter by department
4. Click on announcements to view details (automatically tracked)

#### Lecturer Workflow
1. Login with lecturer credentials
2. View dashboard with system overview
3. Create new announcements via "Create Announcement"
4. Specify department and optionally course
5. View announcement statistics

#### Admin Workflow
1. Login with admin credentials
2. Access full admin dashboard
3. Manage users, departments, and courses
4. Create announcements for any department
5. View system-wide statistics

## 🔌 API Documentation

### Authentication
All API endpoints require authentication. Include session cookies or use API keys (future feature).

### Endpoints

#### Get Announcements
```http
GET /api/get_announcements.php?type=all
GET /api/get_announcements.php?type=active
GET /api/get_announcements.php?type=single&id={uuid}
GET /api/get_announcements.php?type=department&id={dept_uuid}
GET /api/get_announcements.php?type=course&id={course_uuid}
```

**Response:**
```json
{
  "success": true,
  "announcements": [
    {
      "id": "uuid",
      "title": "Announcement Title",
      "message": "Announcement content...",
      "posted_by": "user_name",
      "department_name": "Department Name",
      "created_at": "2024-01-27 10:00:00",
      "views_count": 15
    }
  ]
}
```

#### Create Announcement
```http
POST /api/create_announcement.php
Content-Type: application/json
Authorization: Bearer {session_token}
```

**Request Body:**
```json
{
  "title": "New Announcement",
  "message": "Announcement content here...",
  "department_id": "dept-uuid",
  "course_id": "course-uuid", // optional
  "expiry_date": "2024-12-31" // optional
}
```

**Response:**
```json
{
  "success": true,
  "message": "Announcement created successfully",
  "id": "announcement-uuid"
}
```

#### Get Departments
```http
GET /api/get_departments.php
GET /api/get_departments.php?id={uuid}
```

#### Get Users
```http
GET /api/get_users.php?type=all
GET /api/get_users.php?type=single&id={uuid}
GET /api/get_users.php?type=email&email=user@university.edu
GET /api/get_users.php?type=role&role=student
```

## 🔒 Security Features

### Authentication & Authorization
- **Session-based authentication** with secure session management
- **Role-based access control** (RBAC) with granular permissions
- **Password hashing** (currently using simple demo passwords)
- **CSRF protection** via session tokens

### Data Security
- **Prepared statements** prevent SQL injection
- **Input validation** on all forms and API endpoints
- **XSS prevention** with HTML escaping
- **UUID identifiers** prevent enumeration attacks

### Best Practices
- **Secure coding patterns** throughout the application
- **Error handling** without information disclosure
- **File upload restrictions** (future feature)
- **Rate limiting** considerations (future feature)

## 💻 Development Guidelines

### Code Style
- **PSR-4** autoloading standard (prepared for future Composer integration)
- **Consistent naming** conventions (camelCase for methods, PascalCase for classes)
- **PHPDoc comments** for all classes and methods
- **Meaningful variable names** and clear code structure

### MVC Architecture
```
User Request → Controller → Model → Database
                      ↓
Response ← View ← Controller ← Model
```

### File Organization
- **Controllers**: Handle HTTP requests, validate input, call models
- **Models**: Database operations, business logic
- **Views**: Presentation layer (PHP templates in public/)
- **Config**: Environment-specific settings
- **Includes**: Shared utilities and helpers

### Development Workflow
1. **Feature Branch**: Create feature branches for new work
2. **Testing**: Test functionality before committing
3. **Commit Messages**: Use clear, descriptive commit messages
4. **Pull Requests**: Use PRs for code review (when collaborating)

### Adding New Features
1. **Plan the feature** and identify required changes
2. **Update models** for new data structures
3. **Create/update controllers** for business logic
4. **Update views** for user interface
5. **Test thoroughly** across all user roles
6. **Update documentation**

## 🤝 Contributing

### Getting Started
1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Make your changes
4. Test thoroughly
5. Commit your changes: `git commit -m 'Add feature description'`
6. Push to the branch: `git push origin feature-name`
7. Submit a pull request

### Code Review Process
- All submissions require review
- Maintain code quality and security standards
- Ensure backward compatibility
- Update documentation for new features

### Reporting Issues
- Use GitHub Issues for bug reports and feature requests
- Include detailed steps to reproduce bugs
- Specify your environment (PHP version, database, etc.)

## 📄 License

This project is provided as-is for educational purposes. See individual file headers for specific licensing information.

## 🙏 Acknowledgments

- Built with PHP and modern web development practices
- Designed for educational institutions
- Focus on security, usability, and maintainability

## 📞 Support

For questions or support:
- Create an issue on GitHub
- Check the documentation in this README
- Review the code comments for implementation details

---

**Happy coding! 🎓**