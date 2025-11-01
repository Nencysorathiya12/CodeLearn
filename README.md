# 🎓 CodeLearn - Learning Management System

A modern, AI-powered learning platform for programming education.

## ✨ Features

- 🔐 **User Authentication** - Secure login/signup system
- 📚 **Course Management** - Multiple programming courses
- 💻 **Live Code Editor** - Practice coding in real-time
- 📜 **Certificates** - Earn certificates on course completion
- 🎯 **Quizzes** - Test your knowledge
- 👨‍💼 **Admin Panel** - Manage courses, users, and content
- 🤖 **AI Chatbot** - Get instant help
- 💳 **Payment Integration** - Stripe payment support

## 🛠️ Tech Stack

- **Backend**: PHP 8.x
- **Database**: MySQL 8.x
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Libraries**: 
  - PHPMailer (Email)
  - TCPDF (Certificate generation)
  - Stripe API (Payments)

## 📦 Installation

### Prerequisites

- XAMPP/WAMP (PHP 8.x + MySQL)
- Composer (optional)
- Modern web browser

### Setup Instructions

1. **Clone the repository**
```bash
   git clone https://github.com/yourusername/Learning_platform.git
   cd Learning_platform
```

2. **Configure Database**
   - Create database in phpMyAdmin:
```sql
     CREATE DATABASE codelearn_platform;
```
   - Import SQL file:
```sql
     mysql -u root -p codelearn_platform < database/codelearn_platform.sql
```

3. **Configure Application**
   - Copy `config.example.php` to `config.php`
```bash
     copy config.example.php config.php
```
   - Update database credentials in `config.php`:
```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'codelearn_platform');
     define('DB_USER', 'root');
     define('DB_PASS', '');
```

4. **Set Permissions**
```bash
   chmod 755 certificates/
   chmod 755 uploads/
```

5. **Access Application**
   - Open: `http://localhost/Learning_platform/`

## 📁 Project Structure
```
Learning_platform/
├── app/                    # Core application files
├── certificates/           # Generated certificates
├── database/              # SQL files
├── uploads/               # User uploads
├── vendor/                # Third-party libraries
├── config.php             # Configuration (gitignored)
├── router.php             # Route handler
└── index.php              # Entry point
```

## 🚀 Features Breakdown

### User Features
- Course enrollment and progress tracking
- Interactive code editor with syntax highlighting
- Quiz system with instant feedback
- Certificate generation on completion
- Profile management

### Admin Features
- User management
- Course creation and editing
- Quiz management
- Payment tracking
- Analytics dashboard

## 🔒 Security Features

- Password hashing (bcrypt)
- SQL injection prevention (PDO prepared statements)
- CSRF protection
- Session security
- Input validation and sanitization

## 🌐 Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge

## 📝 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**Your Name**
- GitHub: [@Nencysorathiya12](https://github.com/Nencysorathiya12/CodeLearn.git)


## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📞 Support

For support, email support@codelearn.com or open an issue on GitHub.

## 🙏 Acknowledgments

- Font Awesome for icons
- TCPDF for PDF generation
- PHPMailer for email functionality

---

**Made with ❤️ for developers**