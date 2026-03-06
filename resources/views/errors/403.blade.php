<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>403 - Access Forbidden | School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --dark-bg: #1a1d20;
            --text-light: #f8f9fa;
            --warning-color: #ffc107;
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
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
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
            color: var(--warning-color);
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
        
        .access-info {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .access-info p {
            color: #9ca3af;
            margin-bottom: 5px;
            font-size: 14px;
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
            <i class="bi bi-shield-lock"></i>
        </div>
        
        <div class="error-code">403</div>
        
        <h1 class="error-title">Access Forbidden</h1>
        
        <p class="error-message">
            You do not have permission to access this page. This area is restricted 
            to authorized personnel only. Please contact your administrator if you 
            believe this is an error.
        </p>
        
        <button onclick="window.history.back()" class="btn-home">
            <i class="bi bi-arrow-left me-2"></i>Go Back
        </button>
        
        <div class="access-info">
            <p>If you believe you should have access to this resource, please contact the system administrator.</p>
        </div>
        
        <div class="error-footer">
            <p class="mb-0">&copy; {{ date('Y') }} School ERP. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
