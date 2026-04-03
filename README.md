\# 🎓 SkillMaster Learning Management System (LMS)



\[!\[PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)

\[!\[MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-blue.svg)](https://mysql.com)

\[!\[Bootstrap Version](https://img.shields.io/badge/Bootstrap-5.0-purple.svg)](https://getbootstrap.com)

\[!\[License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)



A comprehensive, feature-rich Learning Management System built for educational institutions. This system provides three separate portals for Administrators, Instructors, and Students with role-based access control.



\## 📋 Table of Contents



\- \[Features](#-features)

\- \[Technology Stack](#-technology-stack)

\- \[System Requirements](#-system-requirements)

\- \[Installation Guide](#-installation-guide)

\- \[Database Setup](#-database-setup)

\- \[Configuration](#-configuration)

\- \[User Portals](#-user-portals)

\- \[Default Credentials](#-default-credentials)

\- \[Project Structure](#-project-structure)

\- \[Features in Detail](#-features-in-detail)

\- \[Screenshots](#-screenshots)

\- \[Troubleshooting](#-troubleshooting)

\- \[Future Enhancements](#-future-enhancements)

\- \[License](#-license)

\- \[Contact](#-contact)



\## ✨ Features



\### 🏫 \*\*Admin Portal\*\* (Hidden Portal)

\- System dashboard with analytics

\- Instructor application review and approval

\- Course approval workflow

\- Student enrollment verification

\- User management (suspend/activate)

\- System settings configuration

\- Activity logs viewer



\### 👨‍🏫 \*\*Instructor Portal\*\*

\- Course creation and management

\- Module and material organization

\- Assignment creation and grading

\- Student progress tracking

\- Announcement posting

\- Discussion forums

\- Gradebook management



\### 👩‍🎓 \*\*Student Portal\*\*

\- Course browsing and enrollment

\- Mock payment simulation

\- Assignment submission

\- Quiz taking (coming soon)

\- Grade viewing

\- Course progress tracking

\- Discussion participation

\- Profile management



\### 🔐 \*\*Security Features\*\*

\- Role-based access control (RBAC)

\- CSRF protection

\- Password hashing (bcrypt)

\- Session management

\- Activity logging

\- Password reset via email



\### 📧 \*\*Email Integration\*\*

\- SMTP support (Gmail, Outlook, etc.)

\- Welcome emails for students and instructors

\- Password reset emails

\- Course approval notifications

\- Assignment graded notifications



\## 🛠 Technology Stack



| Component | Technology |

|-----------|------------|

| \*\*Backend\*\* | PHP 8.2+ |

| \*\*Database\*\* | MySQL 5.7+ / MariaDB 10.4+ |

| \*\*Frontend\*\* | HTML5, CSS3, JavaScript |

| \*\*CSS Framework\*\* | Bootstrap 5 |

| \*\*JavaScript\*\* | jQuery, Owl Carousel, WOW.js |

| \*\*Email\*\* | PHPMailer |

| \*\*Environment\*\* | Dotenv |

| \*\*Logging\*\* | Monolog |

| \*\*Server\*\* | Apache / XAMPP / WAMP |



\## 💻 System Requirements



\### Minimum Requirements

\- \*\*Web Server:\*\* Apache 2.4+ (XAMPP/WAMP recommended)

\- \*\*PHP:\*\* Version 8.2 or higher

\- \*\*MySQL:\*\* Version 5.7 or higher (MariaDB 10.4+)

\- \*\*Browser:\*\* Chrome, Firefox, Edge, Safari (latest versions)



\### PHP Extensions Required

\- PDO MySQL

\- OpenSSL

\- MBString

\- FileInfo

\- JSON

\- Session



\## 📥 Installation Guide



\### Step 1: Clone the Repository

```bash

git clone https://github.com/yourusername/SkillMaster-LMS.git

cd SkillMaster-LMS

