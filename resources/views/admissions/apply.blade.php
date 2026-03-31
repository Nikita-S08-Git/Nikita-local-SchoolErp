
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission - School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }
        .application-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.98);
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 1;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #e9ecef;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-1px);
        }
        .form-label {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .btn-apply {
            border-radius: 12px;
            padding: 14px 40px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .school-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .section-title {
            color: #667eea;
            font-weight: 700;
            font-size: 1.2rem;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
        }
        .section-title i {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .required-field::after {
            content: " *";
            color: #e53e3e;
        }
        /* Error styling */
        .form-control.error, .form-select.error {
            border-color: #e53e3e !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23e53e3e'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23e53e3e' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .form-control.error:focus, .form-select.error:focus {
            border-color: #e53e3e !important;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25) !important;
        }
        .form-control.valid, .form-select.valid {
            border-color: #38a169 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2338a169' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.66-.11.11z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .error-message {
            color: #e53e3e;
            font-size: 0.8rem;
            margin-top: 6px;
            display: none;
            font-weight: 500;
        }
        .error-message.show {
            display: block;
            animation: shake 0.3s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .file-upload-wrapper {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
        }
        .file-upload-wrapper:hover {
            border-color: #667eea;
            background: #f0f4ff;
            transform: translateY(-2px);
        }
        .file-upload-wrapper input[type="file"] {
            display: none;
        }
        .card {
            border: none;
            border-radius: 16px;
            background: #f8fafc;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .input-group-text {
            background: #f0f4ff;
            border: 2px solid #e9ecef;
            border-radius: 12px 0 0 12px;
            color: #667eea;
        }
        .form-control:focus + .input-group-text,
        .form-select:focus + .input-group-text {
            border-color: #667eea;
        }
        /* Progress indicator */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e2e8f0;
            z-index: 0;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
            flex: 1;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #a0aec0;
            transition: all 0.3s ease;
        }
        .step.active .step-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: #fff;
            transform: scale(1.1);
        }
        .step-label {
            margin-top: 8px;
            font-size: 0.85rem;
            color: #718096;
            font-weight: 500;
        }
        .step.active .step-label {
            color: #667eea;
        }
        /* Floating labels */
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            opacity: 1;
            transform: scale(0.85) translateY(-0.65rem);
            color: #667eea;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .application-card {
                border-radius: 16px;
            }
            .btn-apply {
                padding: 12px 30px;
                font-size: 1rem;
        }
        /* ============================================
           SUCCESS BOX STYLES - PREMIUM DESIGN
           ============================================ */
        .success-box {
            background: #ffffff;
            border-radius: 28px;
            border: 3px solid #10b981;
            box-shadow: 0 25px 80px rgba(16, 185, 129, 0.3);
            overflow: hidden;
            animation: slideInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 45px 50px;
            display: flex;
            align-items: center;
            gap: 28px;
            position: relative;
            overflow: hidden;
        }
        .success-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            animation: shimmer 4s infinite linear;
        }
        @keyframes shimmer {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30%, -30%) rotate(180deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }
        .success-animation {
            position: relative;
            z-index: 1;
        }
        .checkmark-circle {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .checkmark {
            width: 45px;
            height: 45px;
            background: #ffffff;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'/%3E%3C/svg%3E") no-repeat center;
            -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'/%3E%3C/svg%3E") no-repeat center;
            animation: checkmarkPop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.4s both;
        }
        @keyframes checkmarkPop {
            0% { transform: scale(0) rotate(-45deg); opacity: 0; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }
        .success-text {
            position: relative;
            z-index: 1;
            color: #ffffff;
        }
        .success-title {
            font-size: 2rem;
            font-weight: 800;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 15px rgba(0,0,0,0.2);
            letter-spacing: -0.5px;
        }
        .success-subtitle {
            font-size: 1.05rem;
            margin: 0;
            opacity: 0.95;
            font-weight: 400;
        }
        .success-body {
            padding: 40px;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        }
        .important-message {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            padding: 22px 28px;
            border-radius: 18px;
            border-left: 5px solid #10b981;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            color: #065f46;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
            margin-bottom: 30px;
        }
        .important-message i {
            font-size: 1.7rem;
            flex-shrink: 0;
            color: #10b981;
        }
        .important-message strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1rem;
        }
        .important-message small {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        /* Details Cards */
        .details-card {
            background: #ffffff;
            border-radius: 22px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            margin-bottom: 24px;
        }
        .details-card:last-child {
            margin-bottom: 0;
        }
        .details-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
            border-color: #10b981;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 24px 32px;
            display: flex;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid #cbd5e1;
        }
        .header-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
            flex-shrink: 0;
        }
        .header-icon i {
            font-size: 1.6rem;
            color: #ffffff;
        }
        .header-text h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.3px;
        }
        .header-text span {
            font-size: 0.88rem;
            color: #64748b;
            display: block;
            margin-top: 5px;
            font-weight: 400;
        }
        .card-body-custom {
            padding: 32px;
        }
        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .info-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 22px 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.3s ease;
        }
        .info-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15);
            border-color: #10b981;
        }
        .info-item.highlight-item {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-color: #10b981;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.2);
        }
        .info-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .info-icon i {
            font-size: 1.3rem;
            color: #ffffff;
        }
        .info-content {
            flex: 1;
            min-width: 0;
        }
        .info-content label {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: block;
            margin-bottom: 8px;
        }
        .info-content .value {
            font-size: 1.08rem;
            color: #1e293b;
            font-weight: 600;
            word-break: break-word;
            display: block;
            line-height: 1.4;
        }
        .info-content .value.highlight {
            color: #10b981;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        /* Credential Grid */
        .credential-grid {
            display: flex;
            flex-direction: column;
            gap: 22px;
        }
        .credential-item {
            background: #f8fafc;
            padding: 24px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .credential-item:hover {
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        .credential-label {
            font-size: 0.88rem;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-action-group {
            display: flex;
            gap: 12px;
        }
        .credential-input {
            flex: 1;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 1.05rem;
            font-family: 'Courier New', monospace;
            background: #ffffff;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .credential-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.12);
        }
        .btn-action {
            padding: 16px 26px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            color: #10b981;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.92rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .btn-action:hover {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
        }
        .btn-action:active {
            transform: translateY(-1px);
        }
        .btn-action.btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #ffffff;
        }
        .btn-action.btn-secondary:hover {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
        }
        .btn-primary {
            padding: 16px 32px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.45);
            color: #ffffff;
        }
        /* Alert Boxes */
        .alert-box {
            padding: 24px 28px;
            border-radius: 16px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            font-size: 0.92rem;
            margin-top: 24px;
            line-height: 1.6;
        }
        .alert-box i {
            font-size: 1.6rem;
            flex-shrink: 0;
        }
        .alert-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.15);
        }
        .alert-info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #93c5fd;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
        }
        .alert-info h5 {
            margin: 0 0 16px 0;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .info-icon-large {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .info-icon-large i {
            font-size: 2rem;
        }
        .steps-list {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }
        .steps-list li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 18px;
            padding: 16px 18px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .steps-list li:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .steps-list li:last-child {
            margin-bottom: 0;
        }
        .steps-list li i {
            color: #10b981;
            font-size: 1.3rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .steps-list li div {
            flex: 1;
        }
        .steps-list li strong {
            display: block;
            margin-bottom: 5px;
            font-size: 0.95rem;
            color: #1e293b;
        }
        .steps-list li span {
            font-size: 0.87rem;
            opacity: 0.85;
            line-height: 1.5;
        }
        /* Login Section */
        .login-section {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            padding: 28px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
        }
        .login-label {
            font-size: 0.88rem;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .url-display {
            margin-bottom: 18px;
        }
        .url-input {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 1.05rem;
            background: #ffffff;
            font-family: 'Courier New', monospace;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .url-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.12);
        }
        .action-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        /* Action Section */
        .action-section {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 35px;
            padding-top: 35px;
            border-top: 2px solid #e2e8f0;
        }
        .btn-print {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(100, 116, 139, 0.3);
        }
        .btn-print:hover {
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
            box-shadow: 0 6px 20px rgba(100, 116, 139, 0.4);
        }
        /* Responsive */
        @media (max-width: 768px) {
            .success-header {
                padding: 30px 25px;
                flex-direction: column;
                text-align: center;
            }
            .success-title {
                font-size: 1.5rem;
            }
            .success-subtitle {
                font-size: 0.95rem;
            }
            .success-body {
                padding: 25px;
            }
            .info-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
            .input-action-group,
            .action-buttons {
                flex-wrap: wrap;
            }
            .btn-action {
                flex: 1;
                min-width: 130px;
                justify-content: center;
                font-size: 0.88rem;
                padding: 14px 20px;
            }
            .action-section {
                flex-direction: column;
            }
            .action-section .btn-action {
                width: 100%;
                justify-content: center;
            }
            .card-header-custom {
                padding: 20px 24px;
            }
            .card-body-custom {
                padding: 24px;
            }
        }
        /* Print Styles */
        @media print {
            .success-box {
                border: 3px solid #10b981;
                box-shadow: none;
            }
            .success-header {
                background: #10b981 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .btn-action,
            .btn-primary,
            .action-section {
                display: none !important;
            }
            .details-card {
                break-inside: avoid;
                page-break-inside: avoid;
                border: 1px solid #cbd5e1;
            }
            .info-item {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center py-4">
            <div class="col-md-10 col-lg-8">
                <div class="card application-card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="school-logo">
                                <i class="bi bi-mortarboard-fill text-white fs-1"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">Apply for Admission</h3>
                            <p class="text-muted mb-0">Fill out the form below to apply for admission</p>
                        </div>

                        @if(session('success'))
                        <!-- Success Details Box -->
                        <div class="success-box mb-5">
                            <!-- Success Header with Animation -->
                            <div class="success-header">
                                <div class="success-animation">
                                    <div class="checkmark-circle">
                                        <div class="checkmark"></div>
                                    </div>
                                </div>
                                <div class="success-text">
                                    <h3 class="success-title">🎉 Admission Submitted Successfully!</h3>
                                    <p class="success-subtitle">Your application has been received and processed</p>
                                </div>
                            </div>

                            <div class="success-body">
                                <!-- Important Message -->
                                <div class="important-message mb-4">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <div>
                                        <strong>Important:</strong> {{ session('success') }}
                                        <br><small>Please save the information below for future reference.</small>
                                    </div>
                                </div>

                                @if(session('student_details'))
                                    @php $details = session('student_details'); @endphp

                                    <!-- Student Information Card -->
                                    <div class="details-card primary-card mb-4">
                                        <div class="card-header-custom">
                                            <div class="header-icon">
                                                <i class="bi bi-person-badge-fill"></i>
                                            </div>
                                            <div class="header-text">
                                                <h4>Student Information</h4>
                                                <span>Your personal and admission details</span>
                                            </div>
                                        </div>
                                        <div class="card-body-custom">
                                            <div class="info-grid">
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-person"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Full Name</label>
                                                        <span class="value">{{ $details['full_name'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item highlight-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-card-heading"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Admission Number</label>
                                                        <span class="value highlight">{{ $details['admission_number'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-envelope"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Email Address</label>
                                                        <span class="value">{{ $details['email'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-phone"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Mobile Number</label>
                                                        <span class="value">{{ $details['mobile_number'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-mortarboard"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Program</label>
                                                        <span class="value">{{ $details['program'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-people"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Division</label>
                                                        <span class="value">{{ $details['division'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-calendar-check"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Academic Year</label>
                                                        <span class="value">{{ $details['academic_year'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon">
                                                        <i class="bi bi-calendar-event"></i>
                                                    </div>
                                                    <div class="info-content">
                                                        <label>Admission Date</label>
                                                        <span class="value">{{ $details['admission_date'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Login Credentials Card -->
                                    <div class="details-card credentials-card mb-4">
                                        <div class="card-header-custom">
                                            <div class="header-icon">
                                                <i class="bi bi-key-fill"></i>
                                            </div>
                                            <div class="header-text">
                                                <h4>Login Credentials</h4>
                                                <span>Save these credentials securely</span>
                                            </div>
                                        </div>
                                        <div class="card-body-custom">
                                            <div class="credential-grid">
                                                <div class="credential-item">
                                                    <label class="credential-label">
                                                        <i class="bi bi-envelope"></i> Student Email
                                                    </label>
                                                    <div class="input-action-group">
                                                        <input type="text" class="credential-input" id="studentEmail" value="{{ session('student_email') }}" readonly>
                                                        <button class="btn-action" onclick="copyToClipboard('studentEmail')" title="Copy Email">
                                                            <i class="bi bi-clipboard"></i> <span>Copy</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="credential-item">
                                                    <label class="credential-label">
                                                        <i class="bi bi-lock"></i> Temporary Password
                                                    </label>
                                                    <div class="input-action-group">
                                                        <input type="password" class="credential-input" id="tempPassword" value="{{ session('temp_password') }}" readonly>
                                                        <button class="btn-action" onclick="copyToClipboard('tempPassword')" title="Copy Password">
                                                            <i class="bi bi-clipboard"></i> <span>Copy</span>
                                                        </button>
                                                        <button class="btn-action" onclick="togglePasswordVisibility()" title="Show/Hide">
                                                            <i class="bi bi-eye" id="toggleEye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert-box alert-warning">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                <div>
                                                    <strong>Security Notice:</strong> These credentials are temporary. Please change your password after your first login for security reasons.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Student Login Card -->
                                    <div class="details-card login-card mb-4">
                                        <div class="card-header-custom">
                                            <div class="header-icon">
                                                <i class="bi bi-box-arrow-in-right-fill"></i>
                                            </div>
                                            <div class="header-text">
                                                <h4>Student Portal Login</h4>
                                                <span>Access your student dashboard</span>
                                            </div>
                                        </div>
                                        <div class="card-body-custom">
                                            <div class="login-section">
                                                <label class="login-label">
                                                    <i class="bi bi-link-45deg"></i> Login URL
                                                </label>
                                                <div class="url-display">
                                                    <input type="text" class="url-input" id="loginUrl" value="http://127.0.0.1:8000/student/login" readonly>
                                                </div>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-secondary" onclick="copyToClipboard('loginUrl')">
                                                        <i class="bi bi-clipboard"></i> Copy URL
                                                    </button>
                                                    <a href="http://127.0.0.1:8000/student/login" target="_blank" class="btn-primary">
                                                        <i class="bi bi-box-arrow-up-right"></i> Login to Portal
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Next Steps Guide -->
                                    <div class="alert-box alert-info">
                                        <div class="info-icon-large">
                                            <i class="bi bi-info-circle-fill"></i>
                                        </div>
                                        <div class="info-content">
                                            <h5>Next Steps - Getting Started</h5>
                                            <ol class="steps-list">
                                                <li>
                                                    <i class="bi bi-check-circle"></i>
                                                    <div>
                                                        <strong>Save your credentials</strong>
                                                        <span>Store your login details in a secure location</span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <i class="bi bi-check-circle"></i>
                                                    <div>
                                                        <strong>Login to student portal</strong>
                                                        <span>Use the credentials above to access your dashboard</span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <i class="bi bi-check-circle"></i>
                                                    <div>
                                                        <strong>Change your password</strong>
                                                        <span>Update your temporary password for security</span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <i class="bi bi-check-circle"></i>
                                                    <div>
                                                        <strong>Complete your profile</strong>
                                                        <span>Add any additional information required</span>
                                                    </div>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="action-section">
                                        <button class="btn-action btn-print" onclick="window.print()">
                                            <i class="bi bi-printer"></i> Print Details
                                        </button>
                                        <a href="{{ route('admissions.apply.form') }}" class="btn-action btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Back to Form
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('admissions.apply') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Personal Information -->
                            <div class="section-title">
                                <i class="bi bi-person-badge me-2"></i>Personal Information
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label fw-semibold required-field">
                                        <i class="bi bi-person me-2"></i>First Name
                                    </label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                           id="first_name" name="first_name" value="{{ old('first_name') }}"
                                           placeholder="Enter first name (letters only)" required pattern="[a-zA-Z\s]+"
                                           title="Only letters are allowed">
                                    <span class="error-message" id="first_name_error"></span>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="middle_name" class="form-label fw-semibold">
                                        <i class="bi bi-person me-2"></i>Middle Name
                                    </label>
                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror"
                                           id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                           placeholder="Enter middle name (letters only)" pattern="[a-zA-Z\s]+"
                                           title="Only letters are allowed">
                                    <span class="error-message" id="middle_name_error"></span>
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="last_name" class="form-label fw-semibold required-field">
                                        <i class="bi bi-person me-2"></i>Last Name
                                    </label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                           id="last_name" name="last_name" value="{{ old('last_name') }}"
                                           placeholder="Enter last name (letters only)" required pattern="[a-zA-Z\s]+"
                                           title="Only letters are allowed">
                                    <span class="error-message" id="last_name_error"></span>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar-date me-2"></i>Date of Birth
                                    </label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                           required max="{{ date('Y-m-d', strtotime('-5 years')) }}">
                                    <span class="error-message" id="date_of_birth_error"></span>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label fw-semibold required-field">
                                        <i class="bi bi-gender-ambiguous me-2"></i>Gender
                                    </label>
                                    <select class="form-select @error('gender') is-invalid @enderror"
                                            id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <span class="error-message" id="gender_error"></span>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="blood_group" class="form-label fw-semibold">
                                        <i class="bi bi-droplet me-2"></i>Blood Group
                                    </label>
                                    <select class="form-select @error('blood_group') is-invalid @enderror" 
                                            id="blood_group" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                        <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                    </select>
                                    @error('blood_group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="religion" class="form-label fw-semibold">
                                        <i class="bi bi-bookmark me-2"></i>Religion
                                    </label>
                                    <select class="form-select @error('religion') is-invalid @enderror" 
                                            id="religion" name="religion">
                                        <option value="">Select Religion</option>
                                        <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Muslim" {{ old('religion') == 'Muslim' ? 'selected' : '' }}>Muslim</option>
                                        <option value="Christian" {{ old('religion') == 'Christian' ? 'selected' : '' }}>Christian</option>
                                        <option value="Sikh" {{ old('religion') == 'Sikh' ? 'selected' : '' }}>Sikh</option>
                                        <option value="Buddhist" {{ old('religion') == 'Buddhist' ? 'selected' : '' }}>Buddhist</option>
                                        <option value="Jain" {{ old('religion') == 'Jain' ? 'selected' : '' }}>Jain</option>
                                        <option value="Other" {{ old('religion') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('religion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label fw-semibold required-field">
                                        <i class="bi bi-people me-2"></i>Category
                                    </label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="obc" {{ old('category') == 'obc' ? 'selected' : '' }}>OBC</option>
                                        <option value="sc" {{ old('category') == 'sc' ? 'selected' : '' }}>SC</option>
                                        <option value="st" {{ old('category') == 'st' ? 'selected' : '' }}>ST</option>
                                        <option value="ews" {{ old('category') == 'ews' ? 'selected' : '' }}>EWS</option>
                                    </select>
                                    <span class="error-message" id="category_error"></span>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="aadhar_number" class="form-label fw-semibold">
                                        <i class="bi bi-person-vcard me-2"></i>Aadhar Number
                                    </label>
                                    <input type="text" class="form-control @error('aadhar_number') is-invalid @enderror" 
                                           id="aadhar_number" name="aadhar_number" value="{{ old('aadhar_number') }}" 
                                           placeholder="Enter 12-digit Aadhar number" maxlength="12" pattern="\d{12}"
                                           title="Must be 12 digits">
                                    @error('aadhar_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="section-title">
                                <i class="bi bi-telephone me-2"></i>Contact Information
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold required-field">
                                        <i class="bi bi-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}"
                                           placeholder="Enter email address" required>
                                    <span class="error-message" id="email_error"></span>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="mobile_number" class="form-label fw-semibold required-field">
                                        <i class="bi bi-phone me-2"></i>Mobile Number
                                    </label>
                                    <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror"
                                           id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}"
                                           placeholder="Enter 10-digit mobile (start with 6-9)" required pattern="[6-9]\d{9}"
                                           maxlength="10" title="Must be 10 digits, starting with 6-9">
                                    <span class="error-message" id="mobile_number_error"></span>
                                    @error('mobile_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="current_address" class="form-label fw-semibold required-field">
                                    <i class="bi bi-house me-2"></i>Current Address
                                </label>
                                <textarea class="form-control @error('current_address') is-invalid @enderror"
                                          id="current_address" name="current_address" rows="2"
                                          placeholder="Enter complete address" required minlength="10">{{ old('current_address') }}</textarea>
                                <span class="error-message" id="current_address_error"></span>
                                @error('current_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="permanent_address" class="form-label fw-semibold">
                                    <i class="bi bi-house-door me-2"></i>Permanent Address
                                </label>
                                <textarea class="form-control @error('permanent_address') is-invalid @enderror"
                                          id="permanent_address" name="permanent_address" rows="2"
                                          placeholder="Enter permanent address (same as current if not specified)">{{ old('permanent_address') }}</textarea>
                                <span class="error-message" id="permanent_address_error"></span>
                                @error('permanent_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Academic Information -->
                            <div class="section-title">
                                <i class="bi bi-book me-2"></i>Academic Information
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="program_id" class="form-label fw-semibold required-field">
                                        <i class="bi bi-mortarboard me-2"></i>Program / Course
                                    </label>
                                    <select class="form-select @error('program_id') is-invalid @enderror" 
                                            id="program_id" name="program_id" required>
                                        <option value="">Select Program</option>
                                        <option value="1" {{ old('program_id') == '1' ? 'selected' : '' }}>B.Com</option>
                                        <option value="2" {{ old('program_id') == '2' ? 'selected' : '' }}>B.Sc</option>
                                        <option value="3" {{ old('program_id') == '3' ? 'selected' : '' }}>BBA</option>
                                        <option value="4" {{ old('program_id') == '4' ? 'selected' : '' }}>BA</option>
                                        <option value="5" {{ old('program_id') == '5' ? 'selected' : '' }}>BCA</option>
                                    </select>
                                    @error('program_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="division_id" class="form-label fw-semibold required-field">
                                        <i class="bi bi-people me-2"></i>Division / Class
                                    </label>
                                    <select class="form-select @error('division_id') is-invalid @enderror"
                                            id="division_id" name="division_id" required disabled>
                                        <option value="">Select Program First</option>
                                    </select>
                                    @error('division_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Select program to see available divisions</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="academic_session_id" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar-event me-2"></i>Academic Session
                                    </label>
                                    <select class="form-select @error('academic_session_id') is-invalid @enderror" 
                                            id="academic_session_id" name="academic_session_id" required>
                                        <option value="">Select Academic Year</option>
                                        <option value="1" {{ old('academic_session_id') == '1' ? 'selected' : '' }}>2025-2026</option>
                                        <option value="2" {{ old('academic_session_id') == '2' ? 'selected' : '' }}>2026-2027</option>
                                    </select>
                                    @error('academic_session_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="academic_year" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar me-2"></i>Year of Admission
                                    </label>
                                    <select class="form-select @error('academic_year') is-invalid @enderror" 
                                            id="academic_year" name="academic_year" required>
                                        <option value="">Select Year</option>
                                        <option value="FY" {{ old('academic_year') == 'FY' ? 'selected' : '' }}>First Year</option>
                                        <option value="SY" {{ old('academic_year') == 'SY' ? 'selected' : '' }}>Second Year</option>
                                        <option value="TY" {{ old('academic_year') == 'TY' ? 'selected' : '' }}>Third Year</option>
                                    </select>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Documents -->
                            <div class="section-title">
                                <i class="bi bi-paperclip me-2"></i>Upload Documents
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-camera me-2"></i>Student Photo
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('photo').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload photo</p>
                                        <small class="text-muted">Max 2MB. JPG, PNG</small>
                                        <input type="file" id="photo" name="photo" accept="image/*" onchange="previewFile(this, 'photoPreview')">
                                    </div>
                                    <img id="photoPreview" class="img-thumbnail mt-2" style="display:none; max-width: 150px;">
                                    @error('photo')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-pen me-2"></i>Student Signature
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('signature').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload signature</p>
                                        <small class="text-muted">Max 1MB. JPG, PNG</small>
                                        <input type="file" id="signature" name="signature" accept="image/*" onchange="previewFile(this, 'signaturePreview')">
                                    </div>
                                    <img id="signaturePreview" class="img-thumbnail mt-2" style="display:none; max-width: 200px;">
                                    @error('signature')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-text me-2"></i>12th Marksheet
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('twelfth_marksheet').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload 12th marksheet</p>
                                        <small class="text-muted">Max 5MB. PDF, JPG, PNG</small>
                                        <input type="file" id="twelfth_marksheet" name="twelfth_marksheet" accept=".pdf,image/*">
                                    </div>
                                    <small id="twelfthFileName" class="text-muted"></small>
                                    @error('twelfth_marksheet')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-file-earmark-ruled me-2"></i>Cast Certificate
                                    </label>
                                    <div class="file-upload-wrapper" onclick="document.getElementById('cast_certificate').click()">
                                        <i class="bi bi-cloud-upload fs-2 text-muted"></i>
                                        <p class="mb-1">Click to upload cast certificate</p>
                                        <small class="text-muted">Max 5MB. PDF, JPG, PNG</small>
                                        <input type="file" id="cast_certificate" name="cast_certificate" accept=".pdf,image/*">
                                    </div>
                                    <small id="castFileName" class="text-muted"></small>
                                    @error('cast_certificate')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-apply w-100 mb-3 mt-4">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none text-primary fw-semibold">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ============================================
        // REAL-TIME VALIDATION WITH RED ERROR INDICATORS
        // ============================================
        
        // Helper function to show error
        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + '_error');
            
            field.classList.remove('valid');
            field.classList.add('error');
            
            if (errorSpan) {
                errorSpan.textContent = message;
                errorSpan.classList.add('show');
            }
        }
        
        // Helper function to clear error
        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorSpan = document.getElementById(fieldId + '_error');
            
            field.classList.remove('error');
            
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.remove('show');
            }
        }
        
        // Helper function to show valid
        function showValid(fieldId) {
            const field = document.getElementById(fieldId);
            field.classList.remove('error');
            field.classList.add('valid');
            
            const errorSpan = document.getElementById(fieldId + '_error');
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.remove('show');
            }
        }
        
        // Auto-fill permanent address if same as current
        document.getElementById('current_address').addEventListener('change', function() {
            if (!document.getElementById('permanent_address').value) {
                document.getElementById('permanent_address').value = this.value;
            }
        });

        // Preview uploaded image
        function previewFile(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Show filename for non-image files
        document.getElementById('twelfth_marksheet').addEventListener('change', function() {
            var fileName = this.files[0]?.name;
            document.getElementById('twelfthFileName').textContent = fileName || '';
        });

        document.getElementById('cast_certificate').addEventListener('change', function() {
            var fileName = this.files[0]?.name;
            document.getElementById('castFileName').textContent = fileName || '';
        });

        // ============================================
        // NAME VALIDATIONS - Only Letters
        // ============================================
        document.getElementById('first_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value.trim().length === 0) {
                showError('first_name', 'First name is required');
            } else if (this.value.trim().length < 2) {
                showError('first_name', 'First name must be at least 2 characters');
            } else {
                showValid('first_name');
            }
        });
        
        document.getElementById('first_name').addEventListener('blur', function(e) {
            if (this.value.trim().length === 0) {
                showError('first_name', 'First name is required');
            } else if (this.value.trim().length < 2) {
                showError('first_name', 'First name must be at least 2 characters');
            } else {
                clearError('first_name');
            }
        });
        
        document.getElementById('middle_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
        
        document.getElementById('last_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            if (this.value.trim().length === 0) {
                showError('last_name', 'Last name is required');
            } else if (this.value.trim().length < 2) {
                showError('last_name', 'Last name must be at least 2 characters');
            } else {
                showValid('last_name');
            }
        });
        
        document.getElementById('last_name').addEventListener('blur', function(e) {
            if (this.value.trim().length === 0) {
                showError('last_name', 'Last name is required');
            } else if (this.value.trim().length < 2) {
                showError('last_name', 'Last name must be at least 2 characters');
            } else {
                clearError('last_name');
            }
        });

        // ============================================
        // DATE OF BIRTH VALIDATION
        // ============================================
        document.getElementById('date_of_birth').addEventListener('change', function(e) {
            const dob = new Date(this.value);
            const today = new Date();
            const minAge = 5;
            const minDate = new Date(today.getFullYear() - minAge, today.getMonth(), today.getDate());
            
            if (dob > minDate) {
                showError('date_of_birth', 'Student must be at least 5 years old');
            } else {
                showValid('date_of_birth');
            }
        });

        // ============================================
        // GENDER VALIDATION
        // ============================================
        document.getElementById('gender').addEventListener('change', function(e) {
            if (this.value === '') {
                showError('gender', 'Please select a gender');
            } else {
                showValid('gender');
            }
        });

        // ============================================
        // CATEGORY VALIDATION
        // ============================================
        document.getElementById('category').addEventListener('change', function(e) {
            if (this.value === '') {
                showError('category', 'Please select a category');
            } else {
                showValid('category');
            }
        });

        // ============================================
        // EMAIL VALIDATION
        // ============================================
        document.getElementById('email').addEventListener('blur', function(e) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(this.value)) {
                showError('email', 'Please enter a valid email address');
            } else {
                showValid('email');
            }
        });

        // ============================================
        // MOBILE NUMBER VALIDATION - 10 digits, starts with 6-9
        // ============================================
        document.getElementById('mobile_number').addEventListener('input', function(e) {
            // Remove all non-digits
            this.value = this.value.replace(/[^\d]/g, '');
            
            // Limit to 10 digits
            if (this.value.length > 10) {
                this.value = this.value.substring(0, 10);
            }
            
            // Real-time validation
            if (this.value.length > 0 && this.value.length < 10) {
                showError('mobile_number', 'Mobile number must be exactly 10 digits');
            } else if (this.value.length === 10 && !/^[6-9]/.test(this.value)) {
                showError('mobile_number', 'Mobile number must start with 6, 7, 8, or 9');
            } else if (this.value.length === 10) {
                showValid('mobile_number');
            }
        });
        
        document.getElementById('mobile_number').addEventListener('blur', function(e) {
            if (this.value.length === 0) {
                showError('mobile_number', 'Mobile number is required');
            } else if (this.value.length < 10) {
                showError('mobile_number', 'Mobile number must be exactly 10 digits (you entered ' + this.value.length + ')');
            } else if (!/^[6-9]\d{9}$/.test(this.value)) {
                if (!/^[6-9]/.test(this.value)) {
                    showError('mobile_number', 'Mobile number must start with 6, 7, 8, or 9');
                } else {
                    showError('mobile_number', 'Invalid mobile number format');
                }
            } else {
                clearError('mobile_number');
            }
        });

        // ============================================
        // AADHAR VALIDATION - 12 digits
        // ============================================
        document.getElementById('aadhar_number').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^\d]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.substring(0, 12);
            }
        });
        
        document.getElementById('aadhar_number').addEventListener('blur', function(e) {
            if (this.value.length > 0 && this.value.length !== 12) {
                showError('aadhar_number', 'Aadhar number must be exactly 12 digits');
            } else if (this.value.length === 12) {
                showValid('aadhar_number');
            } else {
                clearError('aadhar_number');
            }
        });

        // ============================================
        // ADDRESS VALIDATION
        // ============================================
        document.getElementById('current_address').addEventListener('blur', function(e) {
            if (this.value.trim().length === 0) {
                showError('current_address', 'Current address is required');
            } else if (this.value.trim().length < 10) {
                showError('current_address', 'Address must be at least 10 characters');
            } else {
                showValid('current_address');
            }
        });

        // ============================================
        // FORM SUBMISSION VALIDATION
        // ============================================
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            let firstErrorField = null;
            
            // Validate First Name
            const firstName = document.getElementById('first_name');
            if (firstName.value.trim().length < 2) {
                showError('first_name', 'First name is required (min 2 characters)');
                isValid = false;
                if (!firstErrorField) firstErrorField = firstName;
            } else {
                clearError('first_name');
            }
            
            // Validate Last Name
            const lastName = document.getElementById('last_name');
            if (lastName.value.trim().length < 2) {
                showError('last_name', 'Last name is required (min 2 characters)');
                isValid = false;
                if (!firstErrorField) firstErrorField = lastName;
            } else {
                clearError('last_name');
            }
            
            // Validate Date of Birth
            const dob = document.getElementById('date_of_birth');
            if (!dob.value) {
                showError('date_of_birth', 'Date of birth is required');
                isValid = false;
                if (!firstErrorField) firstErrorField = dob;
            } else {
                clearError('date_of_birth');
            }
            
            // Validate Gender
            const gender = document.getElementById('gender');
            if (!gender.value) {
                showError('gender', 'Please select a gender');
                isValid = false;
                if (!firstErrorField) firstErrorField = gender;
            } else {
                clearError('gender');
            }
            
            // Validate Category
            const category = document.getElementById('category');
            if (!category.value) {
                showError('category', 'Please select a category');
                isValid = false;
                if (!firstErrorField) firstErrorField = category;
            } else {
                clearError('category');
            }
            
            // Validate Email
            const email = document.getElementById('email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email.value)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
                if (!firstErrorField) firstErrorField = email;
            } else {
                clearError('email');
            }
            
            // Validate Mobile Number
            const mobile = document.getElementById('mobile_number');
            if (mobile.value.length !== 10 || !/^[6-9]\d{9}$/.test(mobile.value)) {
                showError('mobile_number', 'Mobile number must be 10 digits starting with 6-9');
                isValid = false;
                if (!firstErrorField) firstErrorField = mobile;
            } else {
                clearError('mobile_number');
            }
            
            // Validate Current Address
            const currentAddress = document.getElementById('current_address');
            if (currentAddress.value.trim().length < 10) {
                showError('current_address', 'Address must be at least 10 characters');
                isValid = false;
                if (!firstErrorField) firstErrorField = currentAddress;
            } else {
                clearError('current_address');
            }

            // If invalid, prevent submission and scroll to first error
            if (!isValid) {
                e.preventDefault();
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstErrorField.focus();
                }
            }
        });

        // Copy to Clipboard Function
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            element.select();
            element.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(element.value).then(function() {
                showToast('Copied to clipboard!', 'success');
            }, function(err) {
                showToast('Failed to copy', 'danger');
            });
        }

        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('tempPassword');
            const eyeIcon = document.getElementById('toggleEye');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }

        // Print Credentials
        function printCredentials() {
            window.print();
        }

        // Show Toast Notification
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} alert-dismissible fade show mb-2`;
            toast.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Create Toast Container
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
        }

        // Dynamic Division Loading based on Program Selection (via API)
        const programSelect = document.getElementById('program_id');
        const divisionSelect = document.getElementById('division_id');

        programSelect.addEventListener('change', function() {
            const programId = this.value;
            console.log('Program selected:', programId);

            // Clear division dropdown
            divisionSelect.innerHTML = '<option value="">Select Division</option>';

            if (programId) {
                // Enable division dropdown
                divisionSelect.disabled = false;
                divisionSelect.innerHTML = '<option value="">Loading divisions...</option>';

                // Fetch divisions from API (public endpoint)
                fetch(`/api/divisions/public?program_id=${programId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    divisionSelect.innerHTML = '<option value="">Select Division</option>';
                    
                    if (data.success && data.data && data.data.length > 0) {
                        data.data.forEach(function(division) {
                            const option = document.createElement('option');
                            option.value = division.id;
                            option.textContent = division.division_name || division.name;

                            // Preserve old selection
                            if (division.id == '{{ old('division_id') }}') {
                                option.selected = true;
                            }

                            divisionSelect.appendChild(option);
                        });
                        console.log('Loaded', data.data.length, 'divisions');
                    } else {
                        divisionSelect.innerHTML = '<option value="">No divisions available</option>';
                        divisionSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching divisions:', error);
                    divisionSelect.innerHTML = '<option value="">Error loading divisions</option>';
                    divisionSelect.disabled = true;
                    
                    // Show error in console for debugging
                    console.error('Full error:', error);
                });
            } else {
                // Disable division dropdown if no program selected
                divisionSelect.disabled = true;
                divisionSelect.innerHTML = '<option value="">Select Program First</option>';
            }
        });

        // Trigger change on page load to preserve old selection
        if (programSelect.value) {
            programSelect.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>
