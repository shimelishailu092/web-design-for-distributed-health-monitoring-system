# Distributed Health Monitoring System

A web-based distributed system designed to monitor patient health data, manage medical reports, and facilitate communication between patients, doctors, and administrators.

## 🚀 Features

- **User Roles & Authentication**:
  - **Patients**: Register, login, view health data, and access reports.
  - **Doctors**: Login, view assigned patients, and manage medical reports.
  - **Admins**: System management and oversight.
- **Health Monitoring**: Integration with sensors to track vital health metrics.
- **Reporting System**: Upload and view medical reports (PDF/images).
- **Responsive Design**: User-friendly interface built with HTML5, CSS3, and JavaScript.

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP
- **Database**: MySQL / MariaDB
- **Server**: Apache (via XAMPP/WAMP)

## 📂 Directory Structure

- `admin/`, `doctor/`, `patient/` - Portals for different user roles.
- `api/` - Backend logic and API endpoints.
- `assets/` - CSS, JS, and image resources.
- `config/` - Configuration files.
- `database/` - SQL scripts for database schema and setup.
- `uploads/` - Directory for storing uploaded medical reports.

## ⚙️ Installation & Setup

1.  **Prerequisites**:
    - Install [XAMPP](https://www.apachefriends.org/) or any PHP/MySQL local server environment.

2.  **Clone/Place Project**:
    - Copy the project folder `web-based-distributed-health-monitoring-system` to your server's root directory (e.g., `C:\xampp\htdocs\`).

3.  **Database Configuration**:
    - Open **phpMyAdmin** (`http://localhost/phpmyadmin/`).
    - Create a new database (e.g., `health_monitoring`).
    - Import the schema from `database/tables.sql`.
    - Update the database credentials in `database/db.php` or `config/db.php` if your MySQL password is not empty.

4.  **Run the Application**:
    - Start Apache and MySQL in XAMPP Control Panel.
    - Open your browser and navigate to:
        ```
        http://localhost/web-based-distributed-health-monitoring-system/
        ```

## 📝 Usage

- **Home Page**: Access general information, blog, and contact details.
- **Login/Register**: Use the navigation menu to access role-specific portals.
- **Dashboard**: Upon login, users are redirected to their respective dashboards to view relevant health data.

## 🤝 Contributing

1.  Fork the repository.
2.  Create a feature main branch.
3.  Commit your changes (`git commit -m 'Add some NewFeature'`).
4.  Push to the branch (`git push origin feature/NewFeature`).
5.  Open a Pull Request.
