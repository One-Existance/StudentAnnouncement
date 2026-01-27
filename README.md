# Student Announcement System

A PHP-based web application for managing announcements in an educational institution.

## Features

- **User Authentication**: Secure login system with role-based access control
- **Role-Based Permissions**:
  - **Students**: Can only view announcements
  - **Lecturers**: Can create and view announcements
  - **Administrators**: Full system access (manage users, departments, courses, announcements)
- **Create Announcements**: Post announcements to departments and courses (Lecturers & Admins only)
- **View Announcements**: Browse all active announcements with view tracking
- **Track Views**: Keep track of how many students have viewed announcements
- **Manage Departments**: Create and manage departments (Admins only)
- **Manage Courses**: Create courses and assign them to departments (Admins only)
- **User Management**: Create and manage users with different roles (Admins only)
- **RESTful API**: JSON API endpoints for programmatic access

## Project Structure

```
StudentAnnouncement/
├── config/
│   └── db.php                 # Database configuration
├── models/
│   ├── User.php               # User model
│   ├── Department.php         # Department model
│   ├── Course.php             # Course model
│   ├── Announcement.php       # Announcement model
│   └── AnnouncementView.php   # Announcement view tracking
├── controllers/
│   ├── AuthController.php         # Authentication and authorization
│   ├── UserController.php         # User operations
│   ├── DepartmentController.php   # Department operations
│   ├── CourseController.php       # Course operations
│   └── AnnouncementController.php # Announcement operations
├── public/
│   ├── index.php                  # Home page (redirects to login/dashboard)
│   ├── login.php                  # User login page
│   ├── logout.php                 # User logout
│   ├── unauthorized.php           # Access denied page
│   ├── admin_dashboard.php        # Administrator dashboard
│   ├── lecturer_dashboard.php     # Lecturer dashboard
│   ├── student_dashboard.php      # Student dashboard
│   ├── announcements.php          # View all announcements
│   ├── create_announcement.php    # Create new announcement
│   ├── view_announcement.php      # View single announcement
│   └── departments.php            # Manage departments
├── api/
│   ├── create_announcement.php # API: Create announcement
│   ├── get_announcements.php  # API: Get announcements
│   ├── get_departments.php    # API: Get departments
│   └── get_users.php          # API: Get users
├── includes/
│   └── helpers.php            # Helper functions
└── README.md                  # This file
```

## Database Setup

The application uses the following database schema:

### Tables

1. **users** - User accounts with roles (admin, lecturer, student)
2. **departments** - Department information
3. **courses** - Course information linked to departments
4. **announcements** - Announcement posts
5. **announcement_views** - Track which students have viewed announcements

## Installation

1. Copy the project to your XAMPP htdocs folder:
   ```
   d:\xampp\htdocs\StudentAnnouncement
   ```

2. Update the database credentials in `config/db.php`:
   ```php
   $host = 'localhost';
   $db = 'STUDENT_ANNOUNCEMENT';
   $user = 'root';
   $pass = '';
   ```

3. Import the database schema (ensure STUDENT_ANNOUNCEMENT database exists in MariaDB)

4. **Setup Sample Data** (Optional but recommended):
   ```
   php setup_sample_data.php
   ```
   This will create sample departments, courses, users, and announcements.

5. Access the application at:
   ```
   http://localhost/StudentAnnouncement/public/index.php
   ```

6. **First Time Setup**:
   - Login with admin account: admin@university.edu / password
   - Create departments and courses
   - Create additional user accounts
   - Start using the system!
   - Create additional user accounts
   - Start using the system!
## Usage

### User Authentication

1. **Login**: Access the system at `http://localhost/StudentAnnouncement/public/index.php`
2. **Demo Accounts**:
   - **Admin**: admin@university.edu / password
   - **Lecturer**: lecturer@university.edu / password
   - **Student**: student@university.edu / password

### Role-Based Access

#### Students
- View announcements on their dashboard
- Access announcements list
- View announcement details (views are tracked)

#### Lecturers
- All student permissions
- Create new announcements
- View their own announcements
- Post to departments and courses

#### Administrators
- All permissions
- Manage users (create admins, lecturers, students)
- Manage departments
- Manage courses
- Create announcements
- Full system overview and statistics

### Creating Sample Data

Before using the system, create some sample data through the admin dashboard:

1. Login as admin
2. Create departments (e.g., Computer Science, Mathematics)
3. Create courses and assign them to departments
4. Create lecturer and student accounts
5. Login as lecturer to create announcements
6. Login as student to view announcements

## API Endpoints

### Get Announcements
```
GET /api/get_announcements.php?type=all
GET /api/get_announcements.php?type=active
GET /api/get_announcements.php?type=single&id={id}
GET /api/get_announcements.php?type=department&id={dept_id}
GET /api/get_announcements.php?type=course&id={course_id}
```

### Create Announcement
```
POST /api/create_announcement.php
Content-Type: application/json

{
  "title": "Announcement Title",
  "message": "Announcement message",
  "posted_by": "user_id",
  "department_id": "dept_id",
  "course_id": "course_id" (optional),
  "attachment": "file_url" (optional),
  "expiry_date": "YYYY-MM-DD" (optional)
}
```

### Get Departments
```
GET /api/get_departments.php
GET /api/get_departments.php?id={id}
```

### Get Users
```
GET /api/get_users.php?type=all
GET /api/get_users.php?type=single&id={id}
GET /api/get_users.php?type=email&email={email}
GET /api/get_users.php?type=role&role={role}
```

## Technologies Used

- **Language**: PHP 7+
- **Database**: MariaDB/MySQL
- **Frontend**: HTML5, CSS3
- **Architecture**: MVC (Model-View-Controller)

## Security Considerations

- Input validation on all forms
- Prepared statements for SQL queries
- HTML escaping for output
- UUID-based identifiers

## Future Enhancements

- User authentication and authorization
- Email notifications for announcements
- Search functionality
- Advanced filtering and sorting
- File upload support
- User roles and permissions
- Announcement scheduling
- Rich text editor for announcements

## License

This project is provided as-is for educational purposes.

MariaDB [STUDENT_ANNOUNCEMENT]> SHOW TABLES;
+--------------------------------+
| Tables_in_student_announcement |
+--------------------------------+
| announcement_views             |
| announcements                  |
| courses                        |
| departments                    |
| users                          |
+--------------------------------+
5 rows in set (0.001 sec)

MariaDB [STUDENT_ANNOUNCEMENT]> DESCRIBE announcement_views;
+-----------------+-----------+------+-----+---------------------+-------+
| Field           | Type      | Null | Key | Default             | Extra |
+-----------------+-----------+------+-----+---------------------+-------+
| id              | char(36)  | NO   | PRI | NULL                |       |
| announcement_id | char(36)  | NO   | MUL | NULL                |       |
| student_id      | char(36)  | NO   | MUL | NULL                |       |
| viewed_at       | timestamp | NO   |     | current_timestamp() |       |
+-----------------+-----------+------+-----+---------------------+-------+
4 rows in set (0.041 sec)

MariaDB [STUDENT_ANNOUNCEMENT]> DESCRIBE announcements;
+---------------+--------------+------+-----+---------------------+-------+
| Field         | Type         | Null | Key | Default             | Extra |
+---------------+--------------+------+-----+---------------------+-------+
| id            | char(36)     | NO   | PRI | NULL                |       |
| title         | varchar(255) | NO   |     | NULL                |       |
| message       | text         | NO   |     | NULL                |       |
| posted_by     | char(36)     | NO   | MUL | NULL                |       |
| department_id | char(36)     | NO   | MUL | NULL                |       |
| course_id     | char(36)     | YES  | MUL | NULL                |       |
| attachment    | varchar(255) | YES  |     | NULL                |       |
| expiry_date   | date         | YES  |     | NULL                |       |
| created_at    | timestamp    | NO   |     | current_timestamp() |       |
+---------------+--------------+------+-----+---------------------+-------+
9 rows in set (0.032 sec)

MariaDB [STUDENT_ANNOUNCEMENT]> DESCRIBE courses;
+---------------+--------------+------+-----+---------------------+-------+
| Field         | Type         | Null | Key | Default             | Extra |
+---------------+--------------+------+-----+---------------------+-------+
| id            | char(36)     | NO   | PRI | NULL                |       |
| course_name   | varchar(255) | NO   |     | NULL                |       |
| course_code   | varchar(50)  | NO   | UNI | NULL                |       |
| department_id | char(36)     | NO   | MUL | NULL                |       |
| lecturer_id   | char(36)     | YES  | MUL | NULL                |       |
| created_at    | timestamp    | NO   |     | current_timestamp() |       |
+---------------+--------------+------+-----+---------------------+-------+
6 rows in set (0.033 sec)

MariaDB [STUDENT_ANNOUNCEMENT]> DESCRIBE departments;
+-------------+--------------+------+-----+---------------------+-------+
| Field       | Type         | Null | Key | Default             | Extra |
+-------------+--------------+------+-----+---------------------+-------+
| id          | char(36)     | NO   | PRI | NULL                |       |
| name        | varchar(255) | NO   | UNI | NULL                |       |
| description | text         | YES  |     | NULL                |       |
| created_at  | timestamp    | NO   |     | current_timestamp() |       |
+-------------+--------------+------+-----+---------------------+-------+
4 rows in set (0.028 sec)

MariaDB [STUDENT_ANNOUNCEMENT]> DESCRIBE users;
+------------+------------------------------------+------+-----+---------------------+-------+
| Field      | Type                               | Null | Key | Default             | Extra |
+------------+------------------------------------+------+-----+---------------------+-------+
| id         | char(36)                           | NO   | PRI | NULL                |       |
| name       | varchar(255)                       | NO   |     | NULL                |       |
| email      | varchar(255)                       | NO   | UNI | NULL                |       |
| role       | enum('admin','lecturer','student') | NO   |     | NULL                |       |
| department | varchar(255)                       | YES  |     | NULL                |       |
| avatar     | varchar(255)                       | YES  |     | NULL                |       |
| created_at | timestamp                          | NO   |     | current_timestamp() |       |
+------------+------------------------------------+------+-----+---------------------+-------+
7 rows in set (0.042 sec)

MariaDB [STUDENT_ANNOUNCEMENT]>





