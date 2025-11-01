<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['name'] : '';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "codelearn_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Fetch courses for dropdown (if user is logged in)
$courses = [];
if ($isLoggedIn) {
    $coursesQuery = "SELECT course_id, title FROM courses ORDER BY title";
    $coursesResult = $conn->query($coursesQuery);
    if ($coursesResult) {
        while($course = $coursesResult->fetch_assoc()) {
            $courses[] = $course;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Content & Feedback - CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background: #f8f9fa;
    color: #333;
    line-height: 1.6;
}

/* ========== HEADER STYLES ========== */
.header {
    background: white;
    padding: 15px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

.logo {
    display: flex;
    align-items: center;
    font-size: 24px;
    font-weight: bold;
    color: #0c0682ff;
    text-decoration: none;
}

.logo i {
    margin-right: 8px;
    background: #060270ff;
    color: white;
    padding: 8px;
    border-radius: 8px;
}

.nav-menu {
    display: flex;
    list-style: none;
    align-items: center;
    gap: 30px;
}

.nav-menu li a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: color 0.3s;
}

.nav-menu li a:hover {
    color: #09046bff;
}

/* Auth Buttons */
.auth-buttons {
    display: flex;
    gap: 15px;
    align-items: center;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-block;
}

.btn-primary {
    background: #ebedf0ff;
    color: white;
}

.btn-primary:hover {
    background: #f2f2f5ff;
    transform: translateY(-2px);
}

.btn-outline {
    border: 2px solid #f1f1f5ff;
    color: #dcdee1ff;
    background: transparent;
}

.btn-outline:hover {
    background: #f3f3f8ff;
    color: white;
}

/* User Profile Dropdown - Desktop */
.user-profile {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px 16px;
    border-radius: 50px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    color: #333;
}

.user-profile:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.profile-pic {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.user-info {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.user-name {
    font-weight: 600;
    font-size: 14px;
    line-height: 1.2;
}

.user-email {
    font-size: 12px;
    opacity: 0.8;
    line-height: 1.2;
}

.dropdown-arrow {
    margin-left: 8px;
    transition: transform 0.3s ease;
    font-size: 12px;
    opacity: 0.8;
}

.user-profile.active .dropdown-arrow {
    transform: rotate(180deg);
}

/* Dropdown Menu */
.dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    min-width: 280px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px) scale(0.95);
    transition: all 0.3s ease;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.dropdown.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.dropdown-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    color: white;
    text-align: center;
}

.dropdown-profile-pic {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 24px;
    margin: 0 auto 12px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.dropdown-user-name {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 4px;
}

.dropdown-user-email {
    font-size: 13px;
    opacity: 0.9;
}

.dropdown-menu {
    padding: 8px 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.dropdown-item:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.05));
    color: #667eea;
    padding-left: 24px;
}

.dropdown-item i {
    width: 20px;
    font-size: 14px;
    margin-right: 12px;
    opacity: 0.7;
}

.dropdown-item.logout {
    color: #e74c3c;
}

.dropdown-item.logout:hover {
    background: linear-gradient(90deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
    color: #c0392b;
}

.mobile-only {
    display: none;
}

.desktop-only {
    display: flex;
}

/* Hamburger Menu */
.hamburger {
    display: none;
    flex-direction: column;
    cursor: pointer;
}

.hamburger span {
    width: 25px;
    height: 3px;
    background: #333;
    margin: 3px 0;
    transition: 0.3s;
}

/* ========== MAIN CONTENT STYLES ========== */
.container {
    max-width: 1200px;
    margin: 100px auto 40px;
    padding: 0 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 50px;
}

.page-title {
    font-size: 48px;
    margin-bottom: 15px;
    color: #1f2937;
    font-weight: 800;
}

.page-title .gradient {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle {
    color: #6b7280;
    font-size: 18px;
    max-width: 700px;
    margin: 0 auto;
}

/* Contact Section */
.contact-section {
    background: white;
    border-radius: 16px;
    padding: 50px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    margin-bottom: 60px;
}

.contact-header {
    text-align: center;
    margin-bottom: 40px;
}

.contact-title {
    font-size: 32px;
    margin-bottom: 10px;
    color: #1f2937;
    font-weight: 700;
}

.contact-subtitle {
    color: #6b7280;
    font-size: 16px;
}

.contact-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
}

.contact-method {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.method-title {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 5px;
}

.method-detail {
    font-size: 16px;
    color: #6366f1;
    font-weight: 500;
}

.method-description {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.5;
}

/* Feedback Section */
.section-divider {
    text-align: center;
    margin: 60px 0 40px;
}

.section-divider h2 {
    font-size: 36px;
    margin-bottom: 10px;
    color: #1f2937;
}

.section-divider p {
    color: #6b7280;
    font-size: 16px;
}

.feedback-section {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
}

.feedback-header {
    text-align: center;
    margin-bottom: 30px;
}

.feedback-header h3 {
    font-size: 28px;
    margin-bottom: 10px;
    color: #1f2937;
}

.feedback-header p {
    color: #6b7280;
    font-size: 15px;
}

/* Form Styles */
.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1f2937;
    font-size: 14px;
}

.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease;
    font-family: inherit;
    outline: none;
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.form-select {
    background: white;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

/* Star Rating - FIXED VERSION */
.rating-group {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 10px;
}

.star {
    font-size: 36px;
    color: #d1d5db;
    cursor: pointer;
    transition: color 0.2s ease, transform 0.2s ease;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.star:hover,
.star.active {
    color: #fbbf24;
    transform: scale(1.1);
}

.star:active {
    transform: scale(0.95);
}

.submit-button {
    width: 100%;
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    padding: 15px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.submit-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
}

.error-message,
.success-message {
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
}

.error-message {
    background: #fee;
    color: #c33;
    border: 1px solid #fcc;
}

.success-message {
    background: #efe;
    color: #2a7;
    border: 1px solid #cfc;
}

/* ========== FOOTER STYLES ========== */
.footer {
    background: #1a1a1a;
    color: white;
    padding: 60px 0 30px;
    margin-top: 80px;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.footer-brand h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.footer-brand p {
    color: #ccc;
    line-height: 1.6;
}

.footer-section h4 {
    margin-bottom: 20px;
    color: white;
}

.footer-section a {
    display: block;
    color: #ccc;
    text-decoration: none;
    margin-bottom: 10px;
    transition: color 0.3s;
}

.footer-section a:hover {
    color: #6c63ff;
}

.footer-bottom {
    border-top: 1px solid #333;
    padding-top: 20px;
    text-align: center;
    color: #ccc;
}

/* ========== MODAL STYLES ========== */
.auth-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(3px);
}

.auth-modal-content {
    background: white;
    margin: 8% auto;
    border-radius: 16px;
    width: 90%;
    max-width: 380px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.auth-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    text-align: center;
    color: white;
}

.modal-close {
    position: absolute;
    right: 15px;
    top: 15px;
    color: white;
    font-size: 20px;
    cursor: pointer;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.modal-close:hover {
    background: rgba(255,255,255,0.2);
}

.modal-logo {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.modal-subtitle {
    font-size: 13px;
    opacity: 0.9;
}

.auth-forms {
    padding: 25px;
}

.auth-forms .form-group {
    margin-bottom: 15px;
}

.auth-forms .form-group input {
    width: 100%;
    padding: 12px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
    outline: none;
    box-sizing: border-box;
    background: white;
}

.auth-forms .form-group input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-tabs {
    display: flex;
    margin-bottom: 20px;
    background: #f5f5f5;
    border-radius: 6px;
    padding: 3px;
}

.tab-btn {
    flex: 1;
    padding: 8px 12px;
    border: none;
    background: transparent;
    color: #666;
    font-weight: 500;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.tab-btn.active {
    background: white;
    color: #333;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-content {
    display: none;
}

.form-content.active {
    display: block;
}

.divider {
    text-align: center;
    margin: 15px 0;
    position: relative;
    color: #999;
    font-size: 12px;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #eee;
}

.divider span {
    background: white;
    padding: 0 12px;
    position: relative;
}

.auth-submit {
    width: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin: 15px 0 10px;
}

.auth-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.forgot-link {
    text-align: center;
    margin-top: 10px;
}

.forgot-link a {
    color: #667eea;
    text-decoration: none;
    font-size: 13px;
}

a.btn.btn-outline {
    color: black;
}
a.btn.btn-primary {
    color: black;
}

/* Profile Modal */
.profile-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

.profile-modal.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.profile-modal-content {
    background: white;
    margin: 3% auto;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { 
        opacity: 0; 
        transform: translateY(-30px) scale(0.9); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

.profile-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 25px;
    text-align: center;
    color: white;
}

.profile-modal-close {
    position: absolute;
    right: 20px;
    top: 20px;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.profile-modal-close:hover {
    background: rgba(255,255,255,0.2);
    transform: rotate(90deg);
}

.profile-modal-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.profile-modal-subtitle {
    font-size: 14px;
    opacity: 0.9;
}

.profile-form {
    padding: 30px;
}

.current-profile-pic {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 36px;
    margin: 0 auto 20px;
    border: 4px solid #f0f0f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.form-buttons {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.btn-save {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-cancel {
    flex: 1;
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
    padding: 14px 20px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel:hover {
    background: #e9ecef;
    border-color: #ccc;
}

.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 15px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: 15px;
}

.close:hover {
    color: #000;
}

.modal h2 {
    margin-bottom: 20px;
    color: #333;
}

.support-options {
    display: grid;
    gap: 15px;
}

.support-option {
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 15px;
}

.support-option:hover {
    border-color: #6c63ff;
    background: #f8f9ff;
}

.support-icon {
    width: 40px;
    height: 40px;
    background: #6c63ff;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: none;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* ========== RESPONSIVE STYLES ========== */
@media (max-width: 768px) {
    .desktop-only {
        display: none !important;
    }

    .mobile-only {
        display: block !important;
    }
    
    .nav-container {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 15px;
        padding: 0 15px;
    }
    
    .logo {
        grid-column: 1;
        font-size: 20px;
    }
    
    .hamburger {
        grid-column: 3;
        display: flex;
        z-index: 1001;
    }
    
    .hamburger.active span:nth-child(1) {
        transform: rotate(-45deg) translate(-5px, 6px);
    }
    
    .hamburger.active span:nth-child(2) {
        opacity: 0;
    }
    
    .hamburger.active span:nth-child(3) {
        transform: rotate(45deg) translate(-5px, -6px);
    }
    
    .nav-menu {
        position: fixed;
        top: 70px;
        left: -100%;
        width: 100%;
        height: calc(100vh - 70px);
        background: white;
        flex-direction: column;
        justify-content: start;
        align-items: center;
        padding-top: 0;
        transition: left 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        overflow-y: auto;
        z-index: 999;
        grid-column: 1 / -1;
    }
        
    .nav-menu.active {
        left: 0;
    }
    
    .nav-menu li {
        width: 100%;
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .nav-menu li a {
        display: block;
        padding: 10px 20px;
        width: 100%;
    }
    
    .auth-buttons {
        flex-direction: column;
        width: 90%;
        padding: 0;
        gap: 10px;
        margin: 15px auto 10px auto;
        border-top: none;
        padding-top: 10px;
    }
    
    .auth-buttons-mobile {
        display: flex !important;
        flex-direction: column;
        gap: 10px;
        padding: 15px 20px;
    }
    
    .auth-buttons .btn {
        width: 100%;
        text-align: center;
    }
    
    .auth-buttons-mobile .btn {
        width: 100%;
        text-align: center;
    }
    
    .logout-link {
        color: #e74c3c !important;
    }
    
    .logout-link:hover {
        background: rgba(231, 76, 60, 0.1) !important;
        color: #c0392b !important;
    }
    
    .container {
        margin-top: 80px;
        padding: 0 15px;
    }
    
    .page-title {
        font-size: 32px;
    }
    
    .page-subtitle {
        font-size: 16px;
        padding: 0 10px;
    }
    
    .contact-section {
        padding: 30px 20px;
        margin-bottom: 40px;
    }
    
    .contact-methods {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .feedback-section {
        padding: 30px 20px;
    }
    
    .feedback-header h3 {
        font-size: 24px;
    }
    
    .footer {
        padding: 40px 0 20px;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 30px;
        text-align: center;
    }
    
    .footer-brand,
    .footer-section {
        text-align: center;
    }
    
    .auth-modal-content,
    .profile-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .auth-forms,
    .profile-form {
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .nav-container {
        padding: 0 10px;
        gap: 10px;
    }
    
    .logo {
        font-size: 18px;
    }
    
    .hamburger span {
        width: 22px;
        height: 2.5px;
    }
    
    .container {
        padding: 0 10px;
    }
    
    .page-title {
        font-size: 28px;
        line-height: 1.3;
    }
    
    .page-subtitle {
        font-size: 14px;
    }
    
    .star {
        font-size: 28px;
    }
    
    .submit-button {
        padding: 12px;
        font-size: 15px;
    }
}

@media (min-width: 769px) {
    .nav-container {
        display: flex;
        justify-content: space-between;
    }
    
    .hamburger {
        display: none;
    }
    
    .nav-menu {
        display: flex;
        flex-direction: row;
        position: static;
        height: auto;
        width: auto;
        background: transparent;
        box-shadow: none;
    }
}
.search-container {
            position: relative;
            margin: 0 20px;
        }

        .search-box {
            padding: 10px 40px 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            width: 300px;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-box:focus {
            border-color: #08035bff;
        }

        .search-icon, .mic-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            cursor: pointer;
        }

        .search-icon {
            right: 35px;
        }

        .mic-icon {
            right: 10px;
            color: #050505ff;
        }
</style>
</head>
<body data-logged-in="<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>">

<header class="header">
    <nav class="nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-code"></i>
            CodeLearn
        </a>
        
            <!-- Search Box -->
        <div class="search-container">
        <input type="text" class="search-box" placeholder="Search courses..." id="searchBox">
        <!-- <i class="fas fa-search search-icon" id="searchIcon" title="Search"></i> -->
        <i class="fas fa-microphone mic-icon" id="micIcon" title="Voice Search"></i>
    </div>

        <div class="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <ul class="nav-menu" id="navMenu">
            <li><a href="./courses.php">Courses</a></li>
            <li><a href="./about.php">About</a></li>
            <li><a href="./contact.php">Contact</a></li>
            <li><a href="./certificates.php">Certificate</a></li>
            <li><a href="./pricing.php">Pricing</a></li>
            
            <?php if ($isLoggedIn): ?>
                <li class="mobile-only"><a href="#" onclick="openProfileModal(); event.preventDefault(); return false;"><i class="fas fa-user"></i> Profile</a></li>
                <li class="mobile-only"><a href="pricing.php"><i class="fas fa-crown"></i> Plans & Pricing</a></li>
                <li class="mobile-only"><a href="#" onclick="openModal('helpModal'); event.preventDefault(); return false;"><i class="fas fa-question-circle"></i> Help & Support</a></li>
                <li class="mobile-only"><a href="#" onclick="event.preventDefault(); logout(); return false;" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <?php else: ?>
                <li class="auth-buttons-mobile mobile-only">
                    <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                    <!-- <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a> -->
                </li>
            <?php endif; ?>
        </ul>
        
        <?php if (!$isLoggedIn): ?>
            <div class="auth-buttons desktop-only">
                <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                <!-- <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a> -->
            </div>
        <?php endif; ?>
        
        <?php if ($isLoggedIn): ?>
            <div class="user-profile desktop-only" id="userProfileBtn">
                <div class="profile-pic"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name">
                        <?php echo $userName; ?>
                        <?php 
                        $userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';
                        if ($userPlan === 'pro'): ?>
                            <i class="fas fa-crown" style="color: #3B82F6; margin-left: 4px; font-size: 12px;" title="Pro Member"></i>
                        <?php elseif ($userPlan === 'team'): ?>
                            <i class="fas fa-star" style="color: #8B5CF6; margin-left: 4px; font-size: 12px;" title="Max Member"></i>
                        <?php endif; ?>
                    </div>
                    <div class="user-email"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'; ?></div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
                
                <div class="dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-profile-pic"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                        <div class="dropdown-user-name"><?php echo $userName; ?></div>
                        <div class="dropdown-user-email"><?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'; ?></div>
                        
                        <?php
                        $userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';
                        if ($userPlan === 'pro'): ?>
                            <div style="margin-top: 12px; background: linear-gradient(135deg, #3B82F6, #8B5CF6); color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-crown"></i> Pro Member
                            </div>
                        <?php elseif ($userPlan === 'team'): ?>
                            <div style="margin-top: 12px; background: linear-gradient(135deg, #8B5CF6, #A855F7); color: white; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-star"></i> Max Member
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 12px; background: #E5E7EB; color: #6B7280; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fas fa-user"></i> Free Plan
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dropdown-menu">
                        <a href="#" onclick="openProfileModal(); event.stopPropagation(); return false;" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                        <a href="pricing.php" class="dropdown-item">
                            <i class="fas fa-crown"></i>
                            <span>Plans & Pricing</span>
                        </a>
                        <a href="#" onclick="openModal('helpModal'); event.stopPropagation(); return false;" class="dropdown-item">
                            <i class="fas fa-question-circle"></i>
                            <span>Help & Support</span>
                        </a>
                        <a href="#" onclick="logout(); event.stopPropagation(); return false;" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <div class="page-header">
        <h1 class="page-title">Learning <span class="gradient">Content</span></h1>
        <p class="page-subtitle">Explore our comprehensive courses and share your feedback</p>
    </div>

    <div class="contact-section">
        <div class="contact-header">
            <h2 class="contact-title">How Can We Help?</h2>
            <p class="contact-subtitle">Choose the best way to reach us based on your needs</p>
        </div>

        <div class="contact-methods">
            <div class="contact-method">
                <div class="method-title">Email Us</div>
                <div class="method-detail">learnsparktutorial@gmail.com</div>
                <div class="method-description">Send us an email and we'll respond within 24 hours</div>
            </div>

            <div class="contact-method">
                <div class="method-title">Call Us</div>
                <div class="method-detail">+91 9934567889</div>
                <div class="method-description">Mon-Fri from 9am to 6pm PST</div>
            </div>

            <div class="contact-method">
                <div class="method-title">Visit Us</div>
                <div class="method-detail">Surat, India</div>
                <div class="method-description">123 Innovation Street, Tech District</div>
            </div>

            <div class="contact-method">
                <div class="method-title">Social Media</div>
                <div class="method-detail">@CodeLearnHQ</div>
                <div class="method-description">Follow us for updates and community discussions</div>
            </div>
        </div>
    </div>

    <div class="section-divider">
        <h2>Share Your Feedback</h2>
        <p>Help us improve by sharing your thoughts and experiences</p>
    </div>

    <div class="feedback-section">
        <div class="feedback-header">
            <h3>We Value Your Opinion</h3>
            <p>Your feedback helps us create better learning experiences</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php 
                switch($_GET['error']) {
                    case 'not_logged_in':
                        echo 'Please log in to submit feedback.';
                        break;
                    case 'course_required':
                        echo 'Please select a course.';
                        break;
                    case 'missing_fields':
                        echo 'Please fill in all fields.';
                        break;
                    case 'invalid_rating':
                        echo 'Please select a valid rating (1-5 stars).';
                        break;
                    case 'invalid_course':
                        echo 'Selected course is invalid.';
                        break;
                    case 'submission_failed':
                        echo 'Failed to submit feedback. Please try again.';
                        break;
                    default:
                        echo 'An error occurred. Please try again.';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Thank you for your feedback! Your review has been submitted successfully and will appear on our About page.
            </div>
        <?php endif; ?>

        <?php if (!$isLoggedIn): ?>
            <div class="error-message">
                Please <a href="#" onclick="openAuthModal(); return false;" style="color: #667eea; text-decoration: underline;">sign in</a> to submit feedback.
            </div>
        <?php else: ?>
            <form id="feedbackForm" method="POST" action="submit_feedback.php">
                <div class="form-group">
                    <label class="form-label">Select Course *</label>
                    <select class="form-select" name="course_id" required>
                        <option value="">Choose a course...</option>
                        <?php foreach($courses as $course): ?>
                            <option value="<?php echo $course['course_id']; ?>">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Rating *</label>
                    <div class="rating-group" id="ratingGroup">
                        <span class="star" data-rating="1" onclick="setRating(1)">☆</span>
                        <span class="star" data-rating="2" onclick="setRating(2)">☆</span>
                        <span class="star" data-rating="3" onclick="setRating(3)">☆</span>
                        <span class="star" data-rating="4" onclick="setRating(4)">☆</span>
                        <span class="star" data-rating="5" onclick="setRating(5)">☆</span>
                    </div>
                    <input type="hidden" name="rating" id="userRating" value="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Your Feedback *</label>
                    <textarea class="form-textarea" name="comment" placeholder="Share your thoughts about the course..." required></textarea>
                </div>

                <button type="submit" class="submit-button">Submit Feedback</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-brand">
                <h3>CodeLearn</h3>
                <p>Empowering developers worldwide with AI-powered learning experiences.</p>
            </div>
            
            <div class="footer-section">
                <h4>Courses</h4>
                <a href="courses.php">Python</a>
                <a href="courses.php">JavaScript</a>
                <a href="courses.php">React</a>
                <a href="courses.php">Node.js</a>
            </div>
            
            <div class="footer-section">
                <h4>Company</h4>
                <a href="./about.php">About</a>
                <a href="./contact.php">Contact</a>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <a href="#">Help Center</a>
                <a href="./trem-of-ser.php">Terms of Service</a>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2024 CodeLearn. All rights reserved. Made with love for developers worldwide.</p>
        </div>
    </div>
</footer>

<div id="profileModal" class="profile-modal">
    <div class="profile-modal-content">
        <div class="profile-modal-header">
            <span class="profile-modal-close" onclick="closeProfileModal()">&times;</span>
            <div class="profile-modal-title">Edit Profile</div>
            <div class="profile-modal-subtitle">Update your profile information</div>
        </div>
        
        <div class="profile-form">
            <div id="profileMessage" class="message"></div>
            
            <form id="profileForm" onsubmit="saveProfile(event)">
                <div class="profile-image-section">
                    <div class="current-profile-pic" id="profilePicDisplay">
                        <span id="profileInitials"><?php echo strtoupper(substr($userName, 0, 1)); ?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="profileName">
                        <i class="fas fa-user"></i> Full Name
                    </label>
                    <input type="text" id="profileName" class="form-input" value="<?php echo htmlspecialchars($userName); ?>" required minlength="2" maxlength="50">
                </div>

                <div class="form-group">
                    <label class="form-label" for="profileEmail">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input type="email" id="profileEmail" class="form-input" value="<?php echo htmlspecialchars(isset($_SESSION['email']) ? $_SESSION['email'] : 'user@codelearn.com'); ?>" readonly style="background: #f5f5f5; cursor: not-allowed;">
                    <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">Email cannot be changed</small>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn-cancel" onclick="closeProfileModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="helpModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('helpModal')">&times;</span>
        <h2>Help & Support</h2>
        <div class="support-options">
            <div class="support-option" onclick="window.open('mailto:support@codelearn.com')">
                <div class="support-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <h4>Email Support</h4>
                    <p>Send us an email at support@codelearn.com</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="authModal" class="auth-modal">
    <div class="auth-modal-content">
        <div class="auth-modal-header">
            <span class="modal-close" onclick="closeAuthModal()">&times;</span>
            <div class="modal-logo">CodeLearn</div>
            <div class="modal-subtitle">Start your coding journey</div>
        </div>
        
        <div class="auth-forms">
            <div class="form-tabs">
                <button class="tab-btn active" onclick="showAuthForm('login')">Sign In</button>
                <button class="tab-btn" onclick="showAuthForm('signup')">Sign Up</button>
            </div>
            
            <div id="errorMessage" class="error-message" style="display: none;"></div>
            <div id="successMessage" class="success-message" style="display: none;"></div>
            
            <div id="loginForm" class="form-content active">
                <div class="divider"><span>or</span></div>
                
                <form onsubmit="handleAuth(event, 'login')">
                    <div class="form-group">
                        <input type="email" id="loginEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                        <i class="fas fa-eye" id="loginPasswordToggle" 
                        onclick="togglePasswordVisibility('loginPassword', 'loginPasswordToggle')" 
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; font-size: 14px;"></i>
                    </div>
                    <button type="submit" class="auth-submit">Sign In</button>
                </form>
                
                <div class="forgot-link">
                    <a href="./forget-pass-page.php">Forgot Password?</a>
                </div>
            </div>
            
            <div id="signupForm" class="form-content">
                <div class="divider"><span>or</span></div>
                
                <form onsubmit="handleAuth(event, 'signup')">
                    <div class="form-group">
                        <input type="text" id="signupName" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="signupEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group" style="position: relative;">
                        <input type="password" id="signupPassword" name="password" placeholder="Password" required minlength="6">
                        <i class="fas fa-eye" id="signupPasswordToggle" 
                        onclick="togglePasswordVisibility('signupPassword', 'signupPasswordToggle')" 
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #999; font-size: 14px;"></i>
                    </div>
                    
                    <button type="submit" class="auth-submit">Sign Up</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// ========== STAR RATING SYSTEM - FIXED VERSION ==========
let selectedRating = 0;

// Direct onclick function for star rating
function setRating(rating) {
    selectedRating = rating;
    document.getElementById('userRating').value = rating;
    updateStars(rating);
}

function updateStars(rating) {
    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.textContent = '★';
            star.classList.add('active');
        } else {
            star.textContent = '☆';
            star.classList.remove('active');
        }
    });
}

// Hover effects
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star');
    const ratingGroup = document.getElementById('ratingGroup');
    
    if (stars.length > 0) {
        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                updateStars(rating);
            });
        });
        
        if (ratingGroup) {
            ratingGroup.addEventListener('mouseleave', function() {
                updateStars(selectedRating);
            });
        }
    }
    
    // Form validation
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', function(e) {
            if (selectedRating === 0) {
                e.preventDefault();
                alert('⭐ Please select a rating before submitting!');
                return false;
            }
            
            const courseSelect = this.querySelector('select[name="course_id"]');
            if (courseSelect && !courseSelect.value) {
                e.preventDefault();
                alert('📚 Please select a course!');
                return false;
            }
            
            const comment = this.querySelector('textarea[name="comment"]');
            if (comment && !comment.value.trim()) {
                e.preventDefault();
                alert('✍️ Please write your feedback!');
                return false;
            }
        });
    }
});

// ========== MOBILE MENU ==========
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    const hamburger = document.querySelector('.hamburger');
    navMenu.classList.toggle('active');
    hamburger.classList.toggle('active');
    
    if (navMenu.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}

// ========== USER DROPDOWN ==========
function toggleDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('userDropdown');
    const userProfile = document.querySelector('.user-profile');
    
    dropdown.classList.toggle('active');
    userProfile.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-menu li a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                const navMenu = document.getElementById('navMenu');
                const hamburger = document.querySelector('.hamburger');
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
    });

    const userProfile = document.querySelector('.user-profile');
    if (userProfile) {
        userProfile.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleDropdown(event);
        });
    }

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const userProfile = document.querySelector('.user-profile');
        
        if (dropdown && userProfile) {
            if (!userProfile.contains(event.target)) {
                dropdown.classList.remove('active');
                userProfile.classList.remove('active');
            }
        }
    });

    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});

// ========== LOGOUT FUNCTION ==========
function logout() {
    showUserLogoutConfirmation('Confirm Logout', 'Are you sure you want to logout?');
}

function showUserLogoutConfirmation(title, message) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        z-index: 10000; opacity: 0; transition: all 0.3s ease;
    `;

    const modalContent = document.createElement('div');
    modalContent.innerHTML = `
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">👋</div>
            <div>
                <h3 style="margin: 0; color: #1f2937; font-size: 20px; font-weight: 700;">Confirm Logout ?</h3>
                <p style="margin: 4px 0 0 0; color: #6b7280; font-size: 14px;">Thank you for learning with CodeLearn.</p>
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <button id="cancelBtn" style="flex: 1; padding: 12px; border: 1px solid #e5e7eb; background: #f9fafb; color: #374151; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Stay</button>
            <button id="confirmBtn" style="flex: 1; padding: 12px; border: none; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">Logout</button>
        </div>
    `;
    modalContent.style.cssText = `
        background: white; padding: 24px; border-radius: 16px; max-width: 360px; width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: translateY(20px) scale(0.95); transition: all 0.3s ease;
    `;

    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    document.getElementById('cancelBtn').onclick = function() {
        closeModal();
    };

    document.getElementById('confirmBtn').onclick = function() {
        this.innerHTML = '<div style="width: 16px; height: 16px; border: 2px solid white; border-top: 2px solid transparent; border-radius: 50%; animation: spin 1s linear infinite;"></div>';
        this.disabled = true;
        
        fetch('logout.php', { method: 'POST' })
        .then(() => {
            closeModal();
            showLogoutToast();
            setTimeout(() => window.location.href = 'index.php', 2000);
        })
        .catch(() => {
            window.location.href = 'logout.php';
        });
    };

    function closeModal() {
        modal.style.opacity = '0';
        modalContent.style.transform = 'translateY(20px) scale(0.95)';
        setTimeout(() => {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
            document.body.style.overflow = 'auto';
        }, 300);
    }

    modal.onclick = function(e) {
        if (e.target === modal) closeModal();
    };

    if (!document.getElementById('logout-spinner-style')) {
        const style = document.createElement('style');
        style.id = 'logout-spinner-style';
        style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';
        document.head.appendChild(style);
    }

    setTimeout(() => {
        modal.style.opacity = '1';
        modalContent.style.transform = 'translateY(0) scale(1)';
    }, 10);
}

function showLogoutToast() {
    const toast = document.createElement('div');
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">✓</div>
            <div>
                <div style="font-weight: 700; font-size: 15px; color: #111827;">Logout successful!</div>
                <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">Thanks for visiting CodeLearn</div>
            </div>
        </div>
    `;
    toast.style.cssText = `
        position: fixed; top: 24px; right: 24px; background: white; 
        padding: 16px 20px; border-radius: 12px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10001; transform: translateX(400px); transition: all 0.4s ease;
        border: 1px solid #f3f4f6; min-width: 280px;
    `;

    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 400);
    }, 1800);
}

// ========== PROFILE MODAL ==========
function openProfileModal() {
    document.getElementById('profileModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProfileModal() {
    document.getElementById('profileModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

async function saveProfile(event) {
    event.preventDefault();
    
    const name = document.getElementById('profileName').value.trim();
    
    if (!name) {
        showProfileMessage('Please enter your name', 'error');
        return;
    }

    if (name.length < 2) {
        showProfileMessage('Name must be at least 2 characters', 'error');
        return;
    }

    const saveBtn = event.target.querySelector('.btn-save');
    const originalHTML = saveBtn.innerHTML;
    
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    try {
        const formData = new FormData();
        formData.append('action', 'update_profile');
        formData.append('name', name);
        
        const response = await fetch('update_profile.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showProfileMessage('Profile updated successfully!', 'success');
            
            document.querySelectorAll('.user-name, .dropdown-user-name').forEach(el => {
                el.textContent = name;
            });
            
            const initial = name.charAt(0).toUpperCase();
            document.querySelectorAll('.profile-pic, .dropdown-profile-pic, #profileInitials').forEach(el => {
                el.textContent = initial;
            });
            
            setTimeout(() => {
                closeProfileModal();
                location.reload();
            }, 2000);
        } else {
            showProfileMessage(result.message || 'Failed to update profile', 'error');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showProfileMessage('Network error. Please try again.', 'error');
    } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalHTML;
    }
}

function showProfileMessage(message, type) {
    const messageDiv = document.getElementById('profileMessage');
    messageDiv.textContent = message;
    messageDiv.className = 'message ' + type;
    messageDiv.style.display = 'block';
}

// ========== AUTH MODAL ==========
function openAuthModal() {
    document.getElementById('authModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeAuthModal() {
    document.getElementById('authModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    clearMessages();
}

function showAuthForm(formType) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    document.querySelectorAll('.form-content').forEach(form => form.classList.remove('active'));
    document.getElementById(formType + 'Form').classList.add('active');
    
    clearMessages();
}

function showMessage(message, type) {
    clearMessages();
    document.getElementById(type + 'Message').textContent = message;
    document.getElementById(type + 'Message').style.display = 'block';
}

function clearMessages() {
    document.getElementById('errorMessage').style.display = 'none';
    document.getElementById('successMessage').style.display = 'none';
}

async function handleAuth(event, action) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    formData.append('action', action);
    
    const submitBtn = form.querySelector('.auth-submit');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.textContent = action === 'login' ? 'Signing In...' : 'Signing Up...';
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            setTimeout(() => {
                window.location.href = result.redirect || 'contact.php';
            }, 1500);
        } else {
            showMessage(result.message, 'error');
        }
        
    } catch (error) {
        showMessage('Network error. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// ========== HELP MODAL ==========
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// ========== WINDOW EVENTS ==========
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if (window.innerWidth > 768) {
            const navMenu = document.getElementById('navMenu');
            const hamburger = document.querySelector('.hamburger');
            const dropdown = document.getElementById('userDropdown');
            
            if (navMenu) {
                navMenu.classList.remove('active');
            }
            if (hamburger) {
                hamburger.classList.remove('active');
            }
            if (dropdown) {
                dropdown.classList.remove('active');
            }
            document.body.style.overflow = 'auto';
        }
    }, 250);
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('userDropdown');
        const navMenu = document.getElementById('navMenu');
        const hamburger = document.querySelector('.hamburger');
        
        if (dropdown && dropdown.classList.contains('active')) {
            dropdown.classList.remove('active');
            document.querySelector('.user-profile')?.classList.remove('active');
        }
        
        if (navMenu && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            hamburger?.classList.remove('active');
        }
        
        document.body.style.overflow = 'auto';
    }
});

window.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    const authModal = document.getElementById('authModal');
    if (event.target === authModal) {
        closeAuthModal();
    }
    
    const profileModal = document.getElementById('profileModal');
    if (event.target === profileModal) {
        closeProfileModal();
    }
});
// ==================== COURSE SEARCH FUNCTIONALITY ====================
async function searchCourse(query) {
    if (!query || query.trim() === '') {
        showSearchToast('Please enter a search term', 'error');
        return;
    }

    const searchBox = document.getElementById('searchBox');
    const originalPlaceholder = searchBox.placeholder;
    searchBox.placeholder = 'Searching...';
    searchBox.disabled = true;

    try {
        const formData = new FormData();
        formData.append('query', query);

        const response = await fetch('search_courses.php', {
            method: 'POST',
            body: formData
        });

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get response text first for debugging
        const responseText = await response.text();
        console.log('Response:', responseText); // Debug log

        // Try to parse JSON
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (e) {
            console.error('JSON parse error:', e);
            console.error('Response text:', responseText);
            throw new Error('Invalid JSON response from server');
        }

        if (result.success) {
            showSearchToast(`Found: ${result.course_title}`, 'success');
            
            // Check if user is logged in (you can pass this from PHP)
            const isLoggedIn = document.body.dataset.loggedIn === 'true';
            
            if (!isLoggedIn) {
                setTimeout(() => {
                    // Call your auth modal function
                    if (typeof openAuthModal === 'function') {
                        openAuthModal();
                    } else {
                        window.location.href = result.redirect_url;
                    }
                }, 1500);
            } else {
                setTimeout(() => {
                    window.location.href = result.redirect_url;
                }, 1500);
            }
        } else {
            let message = result.message || 'Search failed';
            if (result.suggestions && result.suggestions.length > 0) {
                message += '\n\nDid you mean: ' + result.suggestions.join(', ') + '?';
            }
            showSearchToast(message, 'error');
        }
    } catch (error) {
        console.error('Search error:', error);
        showSearchToast('Search failed: ' + error.message, 'error');
    } finally {
        searchBox.placeholder = originalPlaceholder;
        searchBox.disabled = false;
        searchBox.focus();
    }
}

function showSearchToast(message, type) {
    // Remove any existing toasts first
    const existingToasts = document.querySelectorAll('.search-toast');
    existingToasts.forEach(toast => toast.remove());

    const toast = document.createElement('div');
    toast.className = 'search-toast';
    const icon = type === 'success' ? '✓' : '⚠️';
    const bgColor = type === 'success' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)';
    
    toast.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; background: ${bgColor}; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px; flex-shrink: 0;">${icon}</div>
            <div style="flex: 1;">
                <div style="font-weight: 700; font-size: 15px; color: #111827; white-space: pre-line; word-break: break-word;">${message}</div>
            </div>
        </div>
    `;
    toast.style.cssText = `
        position: fixed; top: 90px; right: 24px; background: white; 
        padding: 16px 20px; border-radius: 12px; 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 10001; transform: translateX(400px); transition: all 0.4s ease;
        border: 1px solid #f3f4f6; min-width: 300px; max-width: 400px;
    `;

    document.body.appendChild(toast);
    
    setTimeout(() => toast.style.transform = 'translateX(0)', 100);
    setTimeout(() => {
        toast.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 400);
    }, 4000);
}

// ==================== VOICE SEARCH ====================
document.addEventListener('DOMContentLoaded', function() {
    const micIcon = document.getElementById('micIcon');
    const searchBox = document.getElementById('searchBox');
    const searchIcon = document.getElementById('searchIcon');
    
    if (micIcon && searchBox) {
        // Voice Search Setup
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            micIcon.addEventListener('click', function() {
                micIcon.classList.add('mic-active');
                searchBox.placeholder = '🎤 Listening...';
                try {
                    recognition.start();
                } catch (e) {
                    console.error('Recognition start error:', e);
                    searchBox.placeholder = 'Search courses...';
                    micIcon.classList.remove('mic-active');
                }
            });

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                searchBox.value = transcript;
                searchBox.placeholder = 'Search courses...';
                micIcon.classList.remove('mic-active');
                
                // Automatically search after voice input
                searchCourse(transcript);
            };

            recognition.onerror = function(event) {
                console.log('Speech recognition error: ' + event.error);
                searchBox.placeholder = 'Search courses...';
                micIcon.classList.remove('mic-active');
                
                if (event.error === 'no-speech') {
                    showSearchToast('No speech detected. Please try again.', 'error');
                } else if (event.error === 'not-allowed') {
                    showSearchToast('Microphone access denied. Please enable it.', 'error');
                } else {
                    showSearchToast('Voice recognition error: ' + event.error, 'error');
                }
            };

            recognition.onend = function() {
                searchBox.placeholder = 'Search courses...';
                micIcon.classList.remove('mic-active');
            };
        } else {
            micIcon.addEventListener('click', function() {
                showSearchToast('Voice search not supported in your browser', 'error');
            });
        }

        // Text search on Enter key
        searchBox.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = searchBox.value.trim();
                if (searchTerm) {
                    searchCourse(searchTerm);
                }
            }
        });

        // Add search icon click handler
        if (searchIcon) {
            searchIcon.addEventListener('click', function() {
                const searchTerm = searchBox.value.trim();
                if (searchTerm) {
                    searchCourse(searchTerm);
                } else {
                    showSearchToast('Please enter a search term', 'error');
                }
            });
        }
    }
});
</script>
</body>
</html>