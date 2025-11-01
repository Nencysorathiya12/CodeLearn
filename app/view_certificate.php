<?php
session_start();
require_once './config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userPlan = isset($_SESSION['plan']) ? $_SESSION['plan'] : 'free';

// Check if user is Pro or Max
if ($userPlan !== 'pro' && $userPlan !== 'team') {
    header('Location: pricing.php');
    exit();
}

// Get course details
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

if (!$courseId) {
    header('Location: certificates.php');
    exit();
}

// Get certificate details
$stmt = $conn->prepare("
    SELECT cert.certificate_number, cert.issued_date, c.title
    FROM certificates cert
    INNER JOIN courses c ON cert.course_id = c.course_id
    WHERE cert.course_id = ? AND cert.user_id = ?
");
$stmt->bind_param("ii", $courseId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: certificates.php');
    exit();
}

$course = $result->fetch_assoc();
$stmt->close();

$certNumber = $course['certificate_number'];
$issueDate = date('Y', strtotime($course['issued_date']));
$issuedDateFull = date('F d, Y', strtotime($course['issued_date']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?php echo htmlspecialchars($course['title']); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', serif;
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .certificate-wrapper {
            background: white;
            width: 1200px;
            height: 848px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            background: white;
            border: 3px solid #d4af37;
            overflow: hidden;
        }

        /* Left Geometric Section */
        .left-decoration {
            position: absolute;
            left: 0;
            top: 0;
            width: 30%;
            height: 100%;
            overflow: hidden;
            background: #2d2d2d;
            clip-path: polygon(0 0, 100% 0, 70% 100%, 0 100%);
        }

        .geo-shape-1 {
            position: absolute;
            width: 0;
            height: 0;
            border-left: 300px solid #d4af37;
            border-top: 180px solid transparent;
            border-bottom: 180px solid transparent;
            left: -50px;
            top: -80px;
            transform: rotate(-10deg);
        }

        .geo-shape-2 {
            position: absolute;
            width: 0;
            height: 0;
            border-right: 180px solid white;
            border-top: 120px solid transparent;
            border-bottom: 120px solid transparent;
            left: 20px;
            top: 0px;
        }

        .geo-shape-3 {
            position: absolute;
            width: 0;
            height: 0;
            border-right: 300px solid #d4af37;
            border-top: 180px solid transparent;
            border-bottom: 180px solid transparent;
            right: -80px;
            bottom: -80px;
            transform: rotate(-10deg);
        }

        .geo-shape-4 {
            position: absolute;
            width: 0;
            height: 0;
            border-left: 180px solid white;
            border-top: 120px solid transparent;
            border-bottom: 120px solid transparent;
            right: 0px;
            bottom: 0px;
        }

        /* Gold border frame */
        .gold-border {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 2px solid #d4af37;
            pointer-events: none;
        }

        /* Right Content Section */
        .content-section {
            position: absolute;
            right: 0;
            top: 0;
            width: 70%;
            height: 100%;
            padding: 80px 60px 60px 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .certificate-content {
            position: relative;
            z-index: 2;
        }

        .cert-heading {
            font-size: 4.5rem;
            font-weight: 400;
            letter-spacing: 12px;
            margin-bottom: 5px;
            color: #2c2c2c;
            font-family: 'Times New Roman', serif;
        }

        .cert-subheading {
            font-size: 2rem;
            color: #d4af37;
            letter-spacing: 8px;
            font-weight: 300;
            margin-bottom: 50px;
        }

        .presented-to {
            font-size: 1rem;
            letter-spacing: 4px;
            color: #666;
            margin-bottom: 25px;
        }

        .recipient-name {
            font-family: 'Brush Script MT', 'Lucida Handwriting', cursive;
            font-size: 4rem;
            color: #d4af37;
            margin: 25px 0;
            border-bottom: 2px solid #2c2c2c;
            padding-bottom: 15px;
            display: inline-block;
            width: 100%;
        }

        .cert-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            margin: 35px 0;
        }

        .course-name {
            font-weight: 700;
            color: #2c2c2c;
        }

        /* Bottom Footer Section */
        .cert-footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: end;
            margin-top: auto;
            padding-top: 40px;
        }

        /* Certificate metadata - Left Side */
        .cert-meta {
            text-align: left;
            font-size: 0.85rem;
            color: #999;
            letter-spacing: 1px;
            line-height: 1.8;
        }

        /* Signature Section - Right Side */
        .signature-section {
            text-align: center;
            justify-self: end;
        }

        /* Handwritten Signature */
        .signature-handwritten {
            font-family: 'Brush Script MT', 'Lucida Handwriting', 'Dancing Script', cursive;
            font-size: 2.8rem;
            color: #031348ff;
            margin-bottom: 10px;
            font-weight: 400;
            font-style: italic;
            line-height: 1;
        }

        .signature-line {
            border-top: 2px solid #080808ff;
            width: 280px;
            margin: 0 auto 12px auto;
        }

        .signature-name {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #0a0a0aff;
            margin-bottom: 3px;
        }

        .signature-title {
            font-size: 0.9rem;
            color: #070707ff;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Award Seal - Top Right Corner */
        .award-seal {
            width: 140px;
            height: 140px;
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            border: 10px solid #d4af37;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            position: absolute;
            top: 50px;
            right: 50px;
            z-index: 10;
        }

        .seal-year {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .seal-text {
            font-size: 0.75rem;
            letter-spacing: 3px;
            margin-top: 5px;
        }

        /* Action Buttons */
        .action-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            gap: 15px;
            z-index: 1000;
        }

        .action-btn {
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            font-size: 14px;
        }

        .btn-download {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-back {
            background: white;
            color: #333;
            border: 2px solid #ddd;
        }

        .btn-back:hover {
            background: #f8f9fa;
            border-color: #bbb;
        }

        @media print {
            body {
                background: white;
            }
            .action-buttons {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .certificate-wrapper {
                width: 100%;
                height: auto;
                aspect-ratio: 1.414;
            }

            .left-decoration {
                width: 30%;
            }
            
            .content-section {
                width: 70%;
                padding: 40px 30px;
            }
            
            .cert-heading {
                font-size: 2.5rem;
                letter-spacing: 6px;
            }
            
            .cert-subheading {
                font-size: 1.2rem;
                letter-spacing: 4px;
            }
            
            .recipient-name {
                font-size: 2.5rem;
            }
            
            .award-seal {
                width: 100px;
                height: 100px;
            }
            
            .action-buttons {
                flex-direction: column;
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate-container" id="certificate">
            <!-- Gold Border Frame -->
            <div class="gold-border"></div>

            <!-- Left Geometric Decoration -->
            <div class="left-decoration">
                <div class="geo-shape-1"></div>
                <div class="geo-shape-2"></div>
                <div class="geo-shape-3"></div>
                <div class="geo-shape-4"></div>
            </div>

            <!-- Award Seal - Top Right Corner -->
            <div class="award-seal">
                <div class="seal-year"><?php echo $issueDate; ?></div>
                <div class="seal-text">AWARD</div>
            </div>

            <!-- Right Content Section -->
            <div class="content-section">
                <div class="certificate-content">
                    <h1 class="cert-heading">CERTIFICATE</h1>
                    <h2 class="cert-subheading">OF APPRECIATION</h2>

                    <p class="presented-to">PROUDLY PRESENTED TO</p>

                    <h2 class="recipient-name"><?php echo htmlspecialchars($userName); ?></h2>

                    <p class="cert-description">
                        This certificate is awarded to <?php echo htmlspecialchars($userName); ?> in recognition of their hard work
                        and dedication in completing the <span class="course-name"><?php echo htmlspecialchars($course['title']); ?></span> course
                        by CodeLearn.
                    </p>
                </div>

                <!-- Footer with Date/Number and Signature -->
                <div class="cert-footer">
                    <div class="cert-meta">
                        <div>Issued: <?php echo $issuedDateFull; ?></div>
                        <div>Certificate No: <?php echo $certNumber; ?></div>
                    </div>

                    <div class="signature-section">
                        <div class="signature-handwritten">CodeLearn Team.</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">CODELEARN TEAM</div>
                        <div class="signature-title">Official Certification</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="certificates.php" class="action-btn btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="downloadCertificate()" class="action-btn btn-download">
            <i class="fas fa-download"></i> Save as PNG
        </button>
    </div>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        function downloadCertificate() {
            const button = document.querySelector('.btn-download');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            button.disabled = true;
            
            const certificate = document.getElementById('certificate');
            
            html2canvas(certificate, {
                scale: 3,
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true,
                allowTaint: true,
                width: 1200,
                height: 848
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Certificate_<?php echo preg_replace('/[^a-zA-Z0-9]/', '_', $course['title']); ?>_<?php echo $certNumber; ?>.png';
                link.href = canvas.toDataURL('image/png', 1.0);
                link.click();
                
                button.innerHTML = originalText;
                button.disabled = false;
            }).catch(error => {
                console.error('Error generating certificate:', error);
                alert('Error generating certificate. Please try again.');
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
    </script>
</body>
</html>