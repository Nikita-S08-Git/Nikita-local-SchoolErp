<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Admission - School ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #007bff 0%, #1a1a1a 100%);
            min-height: 100vh;
        }
        .application-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 20px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-apply {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
        }
        .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }
        .school-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #007bff 0%, #1a1a1a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .section-title {
            color: #007bff;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #007bff;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        /* Error styling */
        .form-control.error, .form-select.error {
            border-color: #dc3545 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .form-control.error:focus, .form-select.error:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .form-control.valid, .form-select.valid {
            border-color: #198754 !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.66-.11.11z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .file-upload-wrapper {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .file-upload-wrapper:hover {
            border-color: #007bff;
            background: #f8f9fa;
        }
        .file-upload-wrapper input[type="file"] {
            display: none;
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
                        <div class="alert alert-success alert-dismissible fade show mb-4">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                    <input type="text" class="form-control @error('first_name') is-invalid @endif"
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
                                    <input type="text" class="form-control @error('middle_name') is-invalid @endif"
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
                                    <input type="text" class="form-control @error('last_name') is-invalid @endif"
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
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @endif"
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                           max="{{ date('Y-m-d') }}" required>
                                    <span class="error-message" id="date_of_birth_error"></span>
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label fw-semibold required-field">
                                        <i class="bi bi-gender-ambiguous me-2"></i>Gender
                                    </label>
                                    <select class="form-select @error('gender') is-invalid @endif"
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
                                    <select class="form-select @error('blood_group') is-invalid @endif" 
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
                                    <select class="form-select @error('religion') is-invalid @endif" 
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
                                    <select class="form-select @error('category') is-invalid @endif"
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
                                    <input type="text" class="form-control @error('aadhar_number') is-invalid @endif" 
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
                                    <input type="email" class="form-control @error('email') is-invalid @endif"
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
                                    <input type="tel" class="form-control @error('mobile_number') is-invalid @endif"
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
                                <textarea class="form-control @error('current_address') is-invalid @endif"
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
                                <textarea class="form-control @error('permanent_address') is-invalid @endif"
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
                                    <select class="form-select @error('program_id') is-invalid @endif" 
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
                                    <select class="form-select @error('division_id') is-invalid @endif" 
                                            id="division_id" name="division_id" required>
                                        <option value="">Select Division</option>
                                        <option value="1" {{ old('division_id') == '1' ? 'selected' : '' }}>A</option>
                                        <option value="2" {{ old('division_id') == '2' ? 'selected' : '' }}>B</option>
                                        <option value="3" {{ old('division_id') == '3' ? 'selected' : '' }}>C</option>
                                    </select>
                                    @error('division_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="academic_session_id" class="form-label fw-semibold required-field">
                                        <i class="bi bi-calendar-event me-2"></i>Academic Session
                                    </label>
                                    <select class="form-select @error('academic_session_id') is-invalid @endif" 
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
                                    <select class="form-select @error('academic_year') is-invalid @endif" 
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
            today.setHours(0, 0, 0, 0);
            const minAge = 5;
            const minDate = new Date(today.getFullYear() - minAge, today.getMonth(), today.getDate());
            
            if (dob > today) {
                showError('date_of_birth', 'Date of birth cannot be in the future');
            } else if (dob > minDate) {
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
    </script>
</body>
</html>
