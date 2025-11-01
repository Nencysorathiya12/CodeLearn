<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
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

/* Search Container */
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

.mic-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #050505ff;
    cursor: pointer;
}

.mic-active {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: translateY(-50%) scale(1); }
    50% { transform: translateY(-50%) scale(1.1); }
    100% { transform: translateY(-50%) scale(1); }
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
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    overflow-x: hidden;
}

/* Header Styles */
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
    background: #f2f2f5ff;
    color: white;
}

.btn-primary:hover {
    background: #f3f3f4ff;
    transform: translateY(-2px);
}

.btn-outline {
    border: 2px solid #f3f2f8ff;
    color: #f9f9feff;
    background: transparent;
}

.btn-outline:hover {
    background: #f8f8faff;
    color: white;
}

a.btn.btn-outline {
    color: black;
}

a.btn.btn-primary {
    color: black;
}

/* User Profile Dropdown Styles */
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
    position: relative;
}

.dropdown-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
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
    position: relative;
}

.dropdown-item:hover {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), rgba(102, 126, 234, 0.05));
    color: #667eea;
    padding-left: 24px;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.dropdown-item i {
    width: 20px;
    font-size: 14px;
    margin-right: 12px;
    opacity: 0.7;
}

.dropdown-item:hover i {
    opacity: 1;
    transform: scale(1.1);
}

.dropdown-item.logout {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    margin-top: 4px;
    color: #e74c3c;
}

.dropdown-item.logout:hover {
    background: linear-gradient(90deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.05));
    color: #c0392b;
}

/* Hamburger menu */
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

/* Login/Signup Modal Styles */
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
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.9); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}

.auth-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    text-align: center;
    color: white;
    position: relative;
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

.form-group {
    margin-bottom: 15px;
}

.form-group input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
    outline: none;
    box-sizing: border-box;
}

.form-group input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.form-group input::placeholder {
    color: #999;
    font-size: 13px;
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

.error-message {
    background: #fee;
    color: #c33;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
    display: none;
}

.success-message {
    background: #efe;
    color: #2a7;
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 13px;
    display: none;
}

/* Profile Edit Modal */
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
    position: relative;
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

.profile-image-section {
    text-align: center;
    margin-bottom: 30px;
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
    position: relative;
    overflow: hidden;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 600;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s;
    outline: none;
    background: #fafafa;
}

.form-input:focus {
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

.profile-pic-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #f0f0f0;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Help Modal */
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

/* Password Toggle */
.form-group i.fa-eye,
.form-group i.fa-eye-slash {
    transition: all 0.2s ease;
}

.form-group i.fa-eye:hover,
.form-group i.fa-eye-slash:hover {
    color: #667eea !important;
    transform: translateY(-50%) scale(1.1);
}

.logout-link {
    color: #e74c3c !important;
}

.logout-link:hover {
    background: rgba(231, 76, 60, 0.1) !important;
    color: #c0392b !important;
}

/* Responsive Design */
.desktop-only {
    display: flex;
}

.mobile-only {
    display: none;
}

.user-logged-in {
    display: none;
}

.user-logged-out {
    display: flex;
}

/* Mobile Styles */
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
    
    .logo i {
        padding: 6px;
        font-size: 16px;
    }
    
    .search-container {
        grid-column: 2;
        margin: 0;
        width: 100%;
        max-width: 300px;
        justify-self: center;
    }
    
    .search-box {
        width: 100%;
        padding: 8px 35px 8px 12px;
        font-size: 14px;
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
    
    .auth-buttons .btn,
    .auth-buttons-mobile .btn {
        width: 100%;
        text-align: center;
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
    
    .search-container {
        max-width: 200px;
    }
    
    .search-box {
        padding: 7px 30px 7px 10px;
        font-size: 13px;
    }
    
    .mic-icon {
        right: 8px;
        font-size: 13px;
    }
    
    .hamburger span {
        width: 22px;
        height: 2.5px;
    }
    
    .auth-modal-content {
        width: 95%;
        margin: 15% auto;
    }
    
    .auth-forms {
        padding: 18px;
    }
    
    .profile-modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .profile-form {
        padding: 20px;
    }
    
    .form-buttons {
        flex-direction: column;
        gap: 10px;
    }
}

@media (min-width: 769px) {
    .desktop-only {
        display: flex !important;
    }
    
    .mobile-only {
        display: none !important;
    }
}

         /* ========== RESPONSIVE STYLES ========== */ 
@media (max-width: 768px) {
    /* Hide desktop user profile on mobile */
    .desktop-only {
        display: none !important;
    }

    /* Show mobile menu items */
    .mobile-only {
        display: block !important;
    }
    
    /* Show mobile user profile menu */
    .user-profile-mobile {
        display: block !important;
        width: 100%;
        padding: 10px 0;
        border-top: 1px solid #e0e0e0;
        margin-top: 10px;
    }
    
    /* Mobile menu item icons */
    .mobile-only a i {
        width: 20px;
        font-size: 14px;
        margin-right: 10px;
    }
    /* Navigation Container - Mobile Layout */
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
    
    .logo i {
        padding: 6px;
        font-size: 16px;
    }
    
    /* Search Container - Center */
    .search-container {
        grid-column: 2;
        margin: 0;
        width: 100%;
        max-width: 300px;
        justify-self: center;
    }
    
    .search-box {
        width: 100%;
        padding: 8px 35px 8px 12px;
        font-size: 14px;
    }
    
    /* Hamburger - Right */
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
    
    /* Navigation Menu */
    /* Navigation Menu */
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
        padding-top: 0;  /* Already 0 - good */
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
        margin: 0;  /* Ensure no margin */
        padding: 0; /* Add this */
    }

    .nav-menu li a {
        display: block;
        padding: 10px 20px;  /* Reduce from 12px to 10px */
        width: 100%;
    }
    
    /* Auth Buttons in Mobile Menu */
    /* Auth Buttons in Mobile Menu */
    .auth-buttons {
        flex-direction: column;
        width: 90%;
        padding: 0;
        gap: 10px;
        margin: 15px auto 10px auto;  /* Top aur bottom margin reduce karo */
        border-top: none;  /* Border remove */
        padding-top: 10px;  /* Reduce from 20px */
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
    
    /* Main Content */
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
    
    /* Footer */
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
    
    /* Modals */
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

/* Extra Small Devices - Mobile Portrait */
@media (max-width: 480px) {
    .nav-container {
        padding: 0 10px;
        gap: 10px;
    }
    
    .logo {
        font-size: 18px;
    }
    
    .search-container {
        max-width: 200px;
    }
    
    .search-box {
        padding: 7px 30px 7px 10px;
        font-size: 13px;
    }
    
    .mic-icon {
        right: 8px;
        font-size: 13px;
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
    
    .contact-title {
        font-size: 24px;
    }
    
    .contact-subtitle {
        font-size: 14px;
    }
    
    .method-title {
        font-size: 16px;
    }
    
    .method-detail {
        font-size: 14px;
    }
    
    .method-description {
        font-size: 13px;
    }
    
    .section-divider h2 {
        font-size: 28px;
    }
    
    .section-divider p {
        font-size: 14px;
    }
    
    .feedback-header h3 {
        font-size: 20px;
    }
    
    .feedback-header p {
        font-size: 14px;
    }
    
    .form-label {
        font-size: 13px;
    }
    
    .form-textarea,
    .form-select {
        font-size: 14px;
        padding: 10px 12px;
    }
    
    .star {
        font-size: 28px;
    }
    
    .submit-button {
        padding: 12px;
        font-size: 15px;
    }
    
    .footer-brand h3 {
        font-size: 1.3rem;
    }
    
    .footer-brand p {
        font-size: 14px;
    }
    
    .footer-section h4 {
        font-size: 16px;
        margin-bottom: 15px;
    }
    
    .footer-section a {
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .footer-bottom p {
        font-size: 13px;
        padding: 0 10px;
    }
    
    .auth-modal-content {
        width: 95%;
        margin: 15% auto;
    }
    
    .modal-logo {
        font-size: 20px;
    }
    
    .modal-subtitle {
        font-size: 12px;
    }
    
    .auth-forms {
        padding: 18px;
    }
    
    .tab-btn {
        padding: 7px 10px;
        font-size: 13px;
    }
    
    .auth-forms .form-group input {
        padding: 11px 12px;
        font-size: 13px;
    }
    
    .auth-submit {
        padding: 11px;
        font-size: 13px;
    }
    
    .profile-modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .profile-modal-title {
        font-size: 20px;
    }
    
    .profile-modal-subtitle {
        font-size: 13px;
    }
    
    .profile-form {
        padding: 20px;
    }
    
    .current-profile-pic {
        width: 80px;
        height: 80px;
        font-size: 28px;
    }
    
    .form-input {
        padding: 12px 14px;
        font-size: 15px;
    }
    
    .btn-save,
    .btn-cancel {
        padding: 12px 16px;
        font-size: 15px;
    }
    
    .form-buttons {
        flex-direction: column;
        gap: 10px;
    }
}

/* Tablet Specific */
@media (min-width: 481px) and (max-width: 768px) {
    .search-container {
        max-width: 350px;
    }
    
    .search-box {
        font-size: 15px;
    }
    
    .contact-methods {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .page-title {
        font-size: 36px;
    }
    
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Large Tablets & Small Desktops */
@media (min-width: 769px) and (max-width: 1024px) {
    .nav-container {
        padding: 0 30px;
    }
    
    .search-box {
        width: 250px;
    }
    
    .contact-methods {
        gap: 25px;
    }
    
    .footer-content {
        gap: 35px;
    }
}

/* Large Screens */
@media (min-width: 769px) {
    .nav-container {
        display: flex;
        justify-content: space-between;
    }
    
    .search-container {
        position: relative;
        margin: 0 20px;
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
</style>
  
</head>
<body>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLearn Header</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="header-styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-code"></i>
                CodeLearn
            </a>
            
            <!-- Search Box -->
            <div class="search-container">
                <input type="text" class="search-box" placeholder="Search courses..." id="searchBox">
                <i class="fas fa-microphone mic-icon" id="micIcon" title="Voice Search"></i>
            </div>
            
            <!-- Hamburger -->
            <div class="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Navigation Menu -->
            <ul class="nav-menu" id="navMenu">
                <li><a href="./courses.php">Courses</a></li>
                <li><a href="./about.php">About</a></li>
                <li><a href="./contact.php">Contact</a></li>
                <li><a href="./certificates.php">Certificate</a></li>
                <li><a href="./pricing.php">Pricing</a></li>
                
                <!-- Mobile User Menu Items (shown when logged in) -->
                <li class="mobile-only user-logged-in"><a href="#" onclick="openProfileModal(); event.preventDefault(); return false;"><i class="fas fa-user"></i> Profile</a></li>
                <li class="mobile-only user-logged-in"><a href="pricing.php"><i class="fas fa-crown"></i> Plans & Pricing</a></li>
                <li class="mobile-only user-logged-in"><a href="#" onclick="openModal('helpModal'); event.preventDefault(); return false;"><i class="fas fa-question-circle"></i> Help & Support</a></li>
                <li class="mobile-only user-logged-in"><a href="#" onclick="event.preventDefault(); logout(); return false;" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                
                <!-- Mobile Auth Buttons (shown when logged out) -->
                <li class="auth-buttons-mobile mobile-only user-logged-out">
                    <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                    <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a>
                </li>
            </ul>
            
            <!-- Desktop Auth Buttons (shown when logged out) -->
            <div class="auth-buttons desktop-only user-logged-out">
                <a href="#" onclick="openAuthModal(); return false;" class="btn btn-outline">Sign In</a>
                <a href="#" onclick="openAuthModal(); return false;" class="btn btn-primary">Get Started</a>
            </div>
            
            <!-- Desktop User Profile (shown when logged in) -->
            <div class="user-profile desktop-only user-logged-in" id="userProfileBtn">
                <div class="profile-pic" id="headerProfilePic">U</div>
                <div class="user-info">
                    <div class="user-name" id="headerUserName">
                        User Name
                        <i class="fas fa-crown user-plan-icon" style="display: none;" title="Pro Member"></i>
                    </div>
                    <div class="user-email" id="headerUserEmail">user@codelearn.com</div>
                </div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
                
                <div class="dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-profile-pic" id="dropdownProfilePic">U</div>
                        <div class="dropdown-user-name" id="dropdownUserName">User Name</div>
                        <div class="dropdown-user-email" id="dropdownUserEmail">user@codelearn.com</div>
                        <div id="userPlanBadge" style="margin-top: 12px; display: none;"></div>
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
        </nav>
    </header>

    <!-- Login/Signup Modal -->
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
                
                <div id="errorMessage" class="error-message"></div>
                <div id="successMessage" class="success-message"></div>
                
                <!-- Login Form -->
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
                
                <!-- Signup Form -->
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

    <!-- Profile Edit Modal -->
    <div id="profileModal" class="profile-modal">
        <div class="profile-modal-content">
            <div class="profile-modal-header">
                <span class="profile-modal-close" onclick="closeProfileModal()">&times;</span>
                <div class="profile-modal-title">Edit Profile</div>
                <div class="profile-modal-subtitle">Update your name</div>
            </div>
            
            <div class="profile-form">
                <div id="profileMessage" class="message"></div>
                
                <form id="profileForm" onsubmit="saveProfile(event)">
                    <div class="profile-image-section">
                        <div class="current-profile-pic" id="profilePicDisplay">
                            <img id="profileImage" style="display: none;" class="profile-pic-preview">
                            <span id="profileInitials">U</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="profileName">
                            <i class="fas fa-user"></i> Full Name
                        </label>
                        <input type="text" id="profileName" name="name" class="form-input" required minlength="2" maxlength="50">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="profileEmail">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" id="profileEmail" class="form-input" readonly style="background: #f5f5f5; cursor: not-allowed;">
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

    <!-- Help Modal -->
    <div id="helpModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('helpModal')">&times;</span>
            <h2>Help & Support</h2>
            <div class="support-options">
                <div class="support-option">
                    <div class="support-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>
                        <h4>Live Chat</h4>
                        <p>Get instant help from our support team</p>
                    </div>
                </div>
                
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

    <script></script>
</body>
</html>
</body>
</html>