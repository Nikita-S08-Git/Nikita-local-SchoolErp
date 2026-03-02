@extends('layouts.teacher')

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold" style="color: #667eea;">
                        <i class="bi bi-pencil-square me-2"></i>Edit Profile
                    </h2>
                    <p class="text-muted mb-0">Update your profile information</p>
                </div>
                <div>
                    <a href="{{ route('teacher.profile') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm" style="border-radius: 12px; border: none;">
                <div class="card-body p-4">
                    <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <h6 class="fw-bold mb-3 text-primary">
                            <i class="bi bi-person me-2"></i>Personal Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Employee ID</label>
                                    <input type="text" name="employee_id" class="form-control"
                                           value="{{ old('employee_id', $teacherProfile->employee_id) }}"
                                           placeholder="e.g., EMP001">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone', $teacherProfile->phone) }}"
                                           placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Alternate Phone</label>
                                    <input type="text" name="alternate_phone" class="form-control"
                                           value="{{ old('alternate_phone', $teacherProfile->alternate_phone) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Blood Group</label>
                                    <select name="blood_group" class="form-select">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" {{ old('blood_group', $teacherProfile->blood_group) == 'A+' ? 'selected' : '' }}>A+</option>
                                        <option value="A-" {{ old('blood_group', $teacherProfile->blood_group) == 'A-' ? 'selected' : '' }}>A-</option>
                                        <option value="B+" {{ old('blood_group', $teacherProfile->blood_group) == 'B+' ? 'selected' : '' }}>B+</option>
                                        <option value="B-" {{ old('blood_group', $teacherProfile->blood_group) == 'B-' ? 'selected' : '' }}>B-</option>
                                        <option value="AB+" {{ old('blood_group', $teacherProfile->blood_group) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                        <option value="AB-" {{ old('blood_group', $teacherProfile->blood_group) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                        <option value="O+" {{ old('blood_group', $teacherProfile->blood_group) == 'O+' ? 'selected' : '' }}>O+</option>
                                        <option value="O-" {{ old('blood_group', $teacherProfile->blood_group) == 'O-' ? 'selected' : '' }}>O-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control"
                                           value="{{ old('date_of_birth', $teacherProfile->date_of_birth?->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $teacherProfile->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $teacherProfile->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $teacherProfile->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Marital Status</label>
                                    <select name="marital_status" class="form-select">
                                        <option value="">Select Status</option>
                                        <option value="single" {{ old('marital_status', $teacherProfile->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $teacherProfile->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status', $teacherProfile->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status', $teacherProfile->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" name="photo" class="form-control" accept="image/*">
                                    @if($teacherProfile->photo_path)
                                        <small class="text-muted">Current photo exists. Upload new to replace.</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Professional Information -->
                        <h6 class="fw-bold mb-3 text-primary mt-4">
                            <i class="bi bi-briefcase me-2"></i>Professional Information
                        </h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Qualification</label>
                                    <input type="text" name="qualification" class="form-control" 
                                           value="{{ old('qualification', $teacherProfile->qualification) }}" 
                                           placeholder="e.g., M.Sc, M.Ed, Ph.D">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Specialization</label>
                                    <input type="text" name="specialization" class="form-control" 
                                           value="{{ old('specialization', $teacherProfile->specialization) }}" 
                                           placeholder="e.g., Mathematics, Physics">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Experience (Months)</label>
                                    <input type="number" name="experience_years" class="form-control" 
                                           value="{{ old('experience_years', $teacherProfile->experience_years) }}" 
                                           min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Joining Date</label>
                                    <input type="date" name="joining_date" class="form-control" 
                                           value="{{ old('joining_date', $teacherProfile->joining_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control" 
                                   value="{{ old('designation', $teacherProfile->designation) }}" 
                                   placeholder="e.g., Senior Lecturer, Assistant Professor">
                        </div>

                        <!-- Address -->
                        <h6 class="fw-bold mb-3 text-primary mt-4">
                            <i class="bi bi-geo-alt me-2"></i>Address
                        </h6>

                        <div class="mb-3">
                            <label class="form-label">Current Address</label>
                            <textarea name="current_address" class="form-control" rows="3">{{ old('current_address', $teacherProfile->current_address) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permanent Address</label>
                            <textarea name="permanent_address" class="form-control" rows="3">{{ old('permanent_address', $teacherProfile->permanent_address) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" 
                                           value="{{ old('city', $teacherProfile->city) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control" 
                                           value="{{ old('state', $teacherProfile->state) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control" 
                                           value="{{ old('pincode', $teacherProfile->pincode) }}">
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <h6 class="fw-bold mb-3 text-primary mt-4">
                            <i class="bi bi-exclamation-circle me-2"></i>Emergency Contact
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="form-control" 
                                           value="{{ old('emergency_contact_name', $teacherProfile->emergency_contact_name) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Relation</label>
                                    <input type="text" name="emergency_contact_relation" class="form-control" 
                                           value="{{ old('emergency_contact_relation', $teacherProfile->emergency_contact_relation) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="emergency_contact_phone" class="form-control" 
                                           value="{{ old('emergency_contact_phone', $teacherProfile->emergency_contact_phone) }}">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Changes
                            </button>
                            <a href="{{ route('teacher.profile') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
