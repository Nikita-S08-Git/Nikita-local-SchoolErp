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
                                           required max="{{ date('Y-m-d', strtotime('-5 years')) }}">
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
        
        // Name validation - only letters
        document.getElementById('first_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
        document.getElementById('last_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
        document.getElementById('middle_name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
        });
        
        // Mobile validation - only digits, max 10
        document.getElementById('mobile_number').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^\d]/g, '');
            if (this.value.length > 10) {
                this.value = this.value.substring(0, 10);
            }
        });
        
        // Aadhar validation - only digits, max 12
        document.getElementById('aadhar_number').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^\d]/g, '');
            if (this.value.length > 12) {
                this.value = this.value.substring(0, 12);
            }
        });
    </script>
</body>
</html>
