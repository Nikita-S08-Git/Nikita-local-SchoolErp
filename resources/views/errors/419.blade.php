<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>419 - Page Expired | School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --dark-bg: #1a1d20;
            --text-light: #f8f9fa;
            --info-color: #17a2b8;
        }
        
        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #0f1419 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .error-container {
            text-align: center;
            padding: 40px;
            max-width: 600px;
        }
        
        .error-code {
            font-size: 120px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--info-color) 0%, #138496 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
        }
        
        .error-title {
            color: var(--text-light);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #9ca3af;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-icon {
            font-size: 60px;
            color: var(--info-color);
            margin-bottom: 20px;
        }
        
        .btn-home {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: 500;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .error-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #6b7280;
            font-size: 14px;
        }
        
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text-light);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
        }
        
        .brand-logo i {
            font-size: 32px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .expired-info {
            background: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .expired-info p {
            color: #9ca3af;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .expired-info ul {
            text-align: left;
            color: #9ca3af;
            font-size: 14px;
            padding-left: 20px;
            margin-top: 10px;
        }
        
        .expired-info li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="brand-logo">
            <i class="bi bi-mortarboard-fill"></i>
            <span>School ERP</span>
        </div>
        
        <div class="error-icon">
            <i class="bi bi-clock-history"></i>
        </div>
        
        <div class="error-code">419</div>
        
        <h1 class="error-title">Page Expired</h1>
        
        <p class="error-message">
            Your session has expired due to inactivity. This is a security measure 
            to protect your account. Please try submitting the form again.
        </p>
        
        <button onclick="window.location.reload()" class="btn-home">
            <i class="bi bi-arrow-clockwise me-2"></i>Try Again
        </button>
        
        <div class="expired-info">
            <p><strong>To prevent this issue:</strong></p>
            <ul>
                <li>Complete your form submission within a reasonable time</li>
                <li>Disable browser extensions that may interfere with cookies</li>
                <li>Ensure cookies are enabled in your browser</li>
            </ul>
        </div>
        
        <div class="error-footer">
            <p class="mb-0">&copy; {{ date('Y') }} School ERP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
