# 🗳️ Online Voting System - PHP Based

A secure and intuitive web-based voting application developed using PHP and MySQL, designed to streamline the election process for organizations, institutions, or student bodies.

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/HTML)
[![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)](https://developer.mozilla.org/en-US/docs/Web/CSS)
[![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

## 📋 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [File Structure](#-file-structure)
- [Security Features](#-security-features)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🔐 Authentication & Authorization
- **Multi-role System**: Admin and User roles with different permissions
- **Secure Login**: Password hashing using PHP's `password_hash()` function
- **Session Management**: Secure session handling for user authentication
- **Logout Functionality**: Proper session destruction on logout

### 🗳️ Voting System
- **One Vote Per User**: Prevents duplicate voting through database constraints
- **Real-time Candidate Display**: Dynamic loading of candidates with profile images
- **Vote Confirmation**: Immediate feedback on successful voting
- **Dark/Light Theme**: Toggle between themes for better user experience

### 👨‍💼 Admin Dashboard
- **User Management**: Add, view, and delete users
- **Candidate Management**: Add candidates with profile images
- **Role Assignment**: Assign admin or user roles to new accounts
- **Real-time Updates**: Dynamic user table updates

### 📊 Results Management
- **Live Results**: Real-time vote counting and display
- **Results Publishing**: Admin can publish final results
- **Visual Representation**: Clean table format for results display

### 🎨 User Interface
- **Responsive Design**: Works on desktop and mobile devices
- **Modern UI**: Glassmorphism effects and gradient backgrounds
- **Interactive Elements**: Hover effects and smooth transitions
- **Accessibility**: Proper form labels and semantic HTML

## 📸 Screenshots

### Login Page
![Login Page](https://via.placeholder.com/800x400/007bff/ffffff?text=Login+Page)

### Voting Interface
![Voting Interface](https://via.placeholder.com/800x400/28a745/ffffff?text=Voting+Interface)

### Admin Dashboard
![Admin Dashboard](https://via.placeholder.com/800x400/dc3545/ffffff?text=Admin+Dashboard)

### Results Page
![Results Page](https://via.placeholder.com/800x400/ffc107/000000?text=Results+Page)

## 🔧 Prerequisites

Before running this application, make sure you have the following installed:

- **PHP** 7.4 or higher
- **MySQL** 5.7 or higher (or MariaDB 10.2+)
- **Web Server** (Apache/Nginx) or PHP built-in server
- **PHP Extensions**:
  - `mysqli` (for database connectivity)
  - `session` (for session management)
  - `fileinfo` (for file uploads)

## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/yourusername/Online_Voting_System-PHP-Based.git
cd Online_Voting_System-PHP-Based
```

### Step 2: Set Up Web Server
#### Option A: Using PHP Built-in Server (Development)
```bash
php -S localhost:8000
```

#### Option B: Using Apache/Nginx
1. Copy the project files to your web server's document root
2. Ensure the web server has read/write permissions for the `uploads/` directory

### Step 3: Database Setup
Follow the [Database Setup](#-database-setup) section below.

## 🗄️ Database Setup

### Step 1: Create Database
1. Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line)
2. Run the SQL commands from `setup.sql`:

```sql
-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);

-- Candidates table
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    votes INT DEFAULT 0
);

-- Votes table
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),
    UNIQUE (user_id)
);

-- Insert default admin user
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$ZMaFykEC0DQQJvCiUaOYjeIhldfjqYBSSHgxukt5r1Ba4fXijfLkS', 'admin');
```

### Step 2: Update Database Configuration
Edit `db_connect.php` with your database credentials:

```php
<?php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "voting_system";
// ... rest of the file
?>
```

## ⚙️ Configuration

### Default Admin Credentials
- **Username**: `admin`
- **Password**: `admin123` (hashed in database)

### File Upload Settings
- **Upload Directory**: `uploads/`
- **Allowed Formats**: JPG, JPEG, PNG, GIF, WEBP
- **Maximum Size**: 2MB per image
- **Naming Convention**: Unique filenames with `img_` prefix

### Session Configuration
- **Session Timeout**: Default PHP session timeout
- **Security**: Session data stored server-side

## 📖 Usage

### For Administrators

1. **Login**: Use admin credentials to access the admin dashboard
2. **Add Users**: Create new voter accounts with username and password
3. **Add Candidates**: Add candidates with profile images
4. **Monitor Voting**: View real-time voting progress
5. **Publish Results**: Make results public when voting ends

### For Voters

1. **Login**: Use credentials provided by admin
2. **View Candidates**: See all available candidates with photos
3. **Cast Vote**: Click the vote button for your preferred candidate
4. **Confirmation**: Receive confirmation of successful voting
5. **Logout**: Securely log out when finished

### For Candidates

1. **Profile Management**: Update profile information and images
2. **View Results**: Check vote counts (if permitted by admin)

## 📁 File Structure

```
Online_Voting_System-PHP-Based/
├── 📄 index.html              # Main login page
├── 📄 vote.html               # Voting interface
├── 📄 admin_dashboard.html    # Admin control panel
├── 📄 publish_results.html    # Results display page
├── 📄 thank_you.html          # Vote confirmation page
├── 📄 backend.php             # Main backend logic
├── 📄 db_connect.php          # Database connection
├── 📄 update_password.php     # Password update utility
├── 📄 setup.sql               # Database schema
├── 📁 uploads/                # Candidate profile images
└── 📄 README.md               # This file
```

## 🔒 Security Features

### Authentication Security
- **Password Hashing**: Uses PHP's `password_hash()` with bcrypt
- **SQL Injection Prevention**: Prepared statements for all database queries
- **Session Security**: Server-side session management
- **Input Validation**: Sanitization of all user inputs

### File Upload Security
- **File Type Validation**: Only allows specific image formats
- **Size Limits**: Maximum 2MB file size
- **Unique Naming**: Prevents filename conflicts
- **Directory Protection**: Uploads stored outside web root (recommended)

### Data Protection
- **One Vote Per User**: Database-level constraint prevents duplicate votes
- **Role-based Access**: Admin-only functions protected
- **CSRF Protection**: Form-based security measures

## 🔌 API Endpoints

### Authentication
- `POST /backend.php` - User login
- `POST /backend.php` - User logout

### Admin Functions
- `POST /backend.php` - Add new user/candidate
- `POST /backend.php` - Delete user
- `GET /backend.php?get_users=true` - Get all users

### Voting Functions
- `POST /backend.php` - Cast vote
- `GET /backend.php?get_results=true` - Get election results

### Response Format
```json
{
    "status": "success|error",
    "message": "Response message",
    "redirect": "optional_redirect_url"
}
```

## 🛠️ Development

### Adding New Features
1. Create new HTML pages for UI components
2. Add corresponding PHP logic in `backend.php`
3. Update database schema if needed
4. Test thoroughly before deployment

### Customization
- **Styling**: Modify CSS in HTML files for visual changes
- **Functionality**: Edit `backend.php` for logic changes
- **Database**: Update `setup.sql` for schema modifications

### Testing
- Test all user roles and permissions
- Verify vote counting accuracy
- Check file upload functionality
- Test responsive design on different devices

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Contribution Guidelines
- Follow existing code style and formatting
- Add comments for complex logic
- Test your changes thoroughly
- Update documentation if needed

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

If you encounter any issues or have questions:

1. **Check Issues**: Look for similar problems in the GitHub issues
2. **Create Issue**: Open a new issue with detailed description
3. **Contact**: Reach out to the maintainers

### Common Issues

#### Database Connection Error
- Verify database credentials in `db_connect.php`
- Ensure MySQL service is running
- Check database name matches `setup.sql`

#### File Upload Issues
- Verify `uploads/` directory exists and is writable
- Check file size and format restrictions
- Ensure proper file permissions

#### Session Problems
- Verify PHP session extension is enabled
- Check server configuration for session handling

## 🙏 Acknowledgments

- **Bootstrap**: For responsive UI components
- **jQuery**: For AJAX functionality
- **PHP Community**: For excellent documentation and examples

---

**⭐ Star this repository if you find it helpful!**

**🔗 Share with others who might benefit from this voting system!**
