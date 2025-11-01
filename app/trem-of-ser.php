<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service | CodeLearn</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
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
            color: #667eea;
            text-decoration: none;
        }

        .logo i {
            margin-right: 8px;
            background: #667eea;
            color: white;
            padding: 8px;
            border-radius: 8px;
        }

        .back-btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95));
            padding: 80px 20px 60px;
            text-align: center;
            color: white;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
        }

        .hero-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        /* Main Content */
        .container {
            max-width: 900px;
            margin: -40px auto 60px;
            padding: 0 20px;
        }

        .content-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            position: relative;
        }

        .last-updated {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #667eea;
            font-size: 1.5rem;
        }

        .section-content {
            color: #555;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .section-content p {
            margin-bottom: 15px;
        }

        .highlight-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .highlight-box strong {
            color: #667eea;
            font-size: 1.1rem;
        }

        ul {
            margin: 15px 0;
            padding-left: 25px;
        }

        ul li {
            margin: 10px 0;
            color: #555;
        }

        ul li strong {
            color: #333;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .warning-box i {
            color: #ffc107;
            margin-right: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }

        .info-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .info-card i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 10px;
        }

        .info-card h4 {
            color: #333;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .info-card p {
            color: #666;
            font-size: 0.95rem;
            margin: 0;
        }

        .contact-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            margin-top: 40px;
        }

        .contact-section h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .contact-section p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            opacity: 0.95;
        }

        .contact-btn {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 40px 20px 20px;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #667eea;
        }

        .footer p {
            color: #ccc;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .content-card {
                padding: 30px 20px;
            }

            .section-title {
                font-size: 1.4rem;
            }

            .section-content {
                font-size: 1rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .nav-container {
                flex-direction: column;
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 60px 15px 40px;
            }

            .hero h1 {
                font-size: 1.75rem;
            }

            .content-card {
                padding: 25px 15px;
            }

            .section-title {
                font-size: 1.2rem;
            }

            .contact-section {
                padding: 30px 20px;
            }

            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
        }

        /* Scroll Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-code"></i>
                CodeLearn
            </a>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-icon">
            <i class="fas fa-file-contract"></i>
        </div>
        <h1>Terms of Service</h1>
        <p>Please read these terms carefully before using CodeLearn</p>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="content-card fade-in">
            <span class="last-updated">
                <i class="fas fa-clock"></i> Last Updated: October 2024
            </span>

            <!-- Introduction -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-handshake"></i>
                    Welcome to CodeLearn
                </h2>
                <div class="section-content">
                    <p>By accessing and using CodeLearn, you agree to be bound by these Terms of Service. If you disagree with any part of these terms, please do not use our platform.</p>
                    
                    <div class="highlight-box">
                        <strong><i class="fas fa-info-circle"></i> Quick Summary:</strong>
                        <p style="margin-top: 10px;">CodeLearn provides AI-powered coding education. Use it responsibly, respect intellectual property, and enjoy learning!</p>
                    </div>
                </div>
            </div>

            <!-- Account & Usage -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    Account & Usage
                </h2>
                <div class="section-content">
                    <div class="info-grid">
                        <div class="info-card">
                            <i class="fas fa-user-plus"></i>
                            <h4>Account Creation</h4>
                            <p>You must provide accurate information when creating your account</p>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Account Security</h4>
                            <p>Keep your password secure and confidential at all times</p>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-user-check"></i>
                            <h4>Age Requirement</h4>
                            <p>Users must be 13+ years old to use CodeLearn</p>
                        </div>
                    </div>

                    <ul>
                        <li><strong>One Account Per Person:</strong> Each user should maintain only one account</li>
                        <li><strong>Account Responsibility:</strong> You're responsible for all activities under your account</li>
                        <li><strong>Prohibited Activities:</strong> No spamming, hacking, or abusive behavior</li>
                    </ul>
                </div>
            </div>

            <!-- Content & Intellectual Property -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-copyright"></i>
                    Content & Intellectual Property
                </h2>
                <div class="section-content">
                    <p><strong>Our Content:</strong> All course materials, code examples, videos, and content are owned by CodeLearn and protected by copyright laws.</p>
                    
                    <div class="highlight-box">
                        <strong>What You Can Do:</strong>
                        <ul style="margin-top: 10px;">
                            <li>✓ Learn from our courses for personal use</li>
                            <li>✓ Practice coding exercises and examples</li>
                            <li>✓ Share your certificates on social media</li>
                            <li>✗ Redistribute or resell our content</li>
                            <li>✗ Copy courses for commercial purposes</li>
                        </ul>
                    </div>

                    <p><strong>Your Code:</strong> Code you write using our platform remains yours, but we may display it as examples (anonymously) to help other learners.</p>
                </div>
            </div>

            <!-- Subscriptions & Payments -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Subscriptions & Payments
                </h2>
                <div class="section-content">
                    <div class="info-grid">
                        <div class="info-card">
                            <i class="fas fa-gift"></i>
                            <h4>Free Plan</h4>
                            <p>Access basic courses forever, no credit card required</p>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-crown"></i>
                            <h4>Pro Plan</h4>
                            <p>Monthly/yearly subscription with auto-renewal</p>
                        </div>
                        <div class="info-card">
                            <i class="fas fa-undo"></i>
                            <h4>Refund Policy</h4>
                            <p>14-day money-back guarantee on paid plans</p>
                        </div>
                    </div>

                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> Subscriptions auto-renew. Cancel anytime before renewal to avoid charges.
                    </div>
                </div>
            </div>

            <!-- Platform Rules -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-gavel"></i>
                    Platform Rules
                </h2>
                <div class="section-content">
                    <p>To maintain a positive learning environment, we prohibit:</p>
                    <ul>
                        <li><strong>Harassment:</strong> No bullying, threatening, or abusive behavior</li>
                        <li><strong>Cheating:</strong> Don't share answers or complete others' assignments</li>
                        <li><strong>Spam:</strong> No unsolicited promotional content</li>
                        <li><strong>Illegal Activities:</strong> Don't use the platform for anything illegal</li>
                        <li><strong>Account Sharing:</strong> Don't share your login credentials</li>
                    </ul>

                    <p style="margin-top: 15px;"><strong>Violation Consequences:</strong> Warning → Temporary Suspension → Permanent Ban</p>
                </div>
            </div>

            <!-- Disclaimers -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Disclaimers & Limitations
                </h2>
                <div class="section-content">
                    <p>CodeLearn provides educational content "as is" without warranties:</p>
                    
                    <div class="highlight-box">
                        <ul style="margin: 0;">
                            <li>We strive for accuracy but can't guarantee error-free content</li>
                            <li>Platform availability may be interrupted for maintenance</li>
                            <li>We're not liable for any career outcomes or job placements</li>
                            <li>Use of AI features is subject to fair use policies</li>
                        </ul>
                    </div>

                    <p><strong>Limitation of Liability:</strong> Our total liability is limited to the amount you paid for your subscription in the last 12 months.</p>
                </div>
            </div>

            <!-- Changes & Termination -->
            <div class="section">
                <h2 class="section-title">
                    <i class="fas fa-sync-alt"></i>
                    Changes & Termination
                </h2>
                <div class="section-content">
                    <p><strong>We May Update These Terms:</strong> We'll notify you via email or platform notification at least 30 days before major changes take effect.</p>
                    
                    <p><strong>Account Termination:</strong></p>
                    <ul>
                        <li><strong>By You:</strong> Cancel anytime from your account settings</li>
                        <li><strong>By Us:</strong> We may terminate accounts that violate these terms</li>
                    </ul>

                    <p>Upon termination, you lose access to paid features but can export your progress data.</p>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="contact-section">
                <h3>Questions About These Terms?</h3>
                <p>Our support team is here to help clarify anything</p>
                <a href="contact.php" class="contact-btn">
                    <i class="fas fa-envelope"></i> Contact Support
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="privacy-policy.php">Privacy Policy</a>
                <a href="terms-of-service.php">Terms of Service</a>
                <a href="contact.php">Contact</a>
            </div>
            <p>&copy; 2024 CodeLearn. All rights reserved. Made with ❤️ for developers worldwide.</p>
        </div>
    </footer>

    <script>
        // Fade-in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>