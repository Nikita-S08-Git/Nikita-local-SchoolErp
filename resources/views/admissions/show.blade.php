@extends('layouts.app')

@section('title', 'Admission Details')
@section('page-title', 'Admission Application')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-file-earmark-person me-2"></i>Admission Application Details</h3>
                <div>
                    <a href="{{ route('admissions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Application Status Card -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Application Status</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <span class="badge bg-{{ 
                                    $admission->status === 'applied' ? 'warning' : 
                                    ($admission->status === 'verified' ? 'success' : 
                                    ($admission->status === 'enrolled' ? 'primary' : 'danger'))
                                }} fs-6">
                                    {{ ucfirst($admission->status) }}
                                </span>
                            </div>
                            <h4>{{ $admission->application_no }}</h4>
                            <p class="text-muted mb-1">Applied on: {{ $admission->created_at->format('d M Y') }}</p>
                            
                            @if($admission->verified_at)
                                <p class="text-muted mb-1">Verified on: {{ $admission->verified_at->format('d M Y') }}</p>
                            @endif
                            
                            @if($admission->rejected_at)
                                <p class="text-danger mb-1">Rejected on: {{ $admission->rejected_at->format('d M Y') }}</p>
                            @endif
                            
                            <hr>
                            <p class="text-muted mb-0">Please save your Application Number for tracking.</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($admission->status === 'applied')
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-success" onclick="verifyAdmission({{ $admission->id }})">
                                        <i class="bi bi-check-circle me-1"></i> Verify Admission
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="rejectAdmission({{ $admission->id }})">
                                        <i class="bi bi-x-circle me-1"></i> Reject Admission
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($admission->status === 'verified')
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Enroll Student</h5>
                            </div>
                            <div class="card-body">
                                <p>This admission is verified and ready to be enrolled as a student.</p>
                                <button type="button" class="btn btn-primary w-100" onclick="enrollAdmission({{ $admission->id }})">
                                    <i class="bi bi-person-plus me-1"></i> Enroll Now
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Details -->
                <div class="col-lg-8">
                    <!-- Personal Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person me-2"></i>Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted">Full Name</label>
                                    <p class="mb-0 fw-bold">{{ $admission->full_name }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted">Date of Birth</label>
                                    <p class="mb-0">{{ $admission->date_of_birth->format('d M Y') }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted">Gender</label>
                                    <p class="mb-0">{{ ucfirst($admission->gender) }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted">Blood Group</label>
                                    <p class="mb-0">{{ $admission->blood_group ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted">Religion</label>
                                    <p class="mb-0">{{ $admission->religion ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="text-muted">Category</label>
                                    <p class="mb-0">{{ ucfirst($admission->category) }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Aadhar Number</label>
                                    <p class="mb-0">{{ $admission->aadhar_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Mobile Number</label>
                                    <p class="mb-0">{{ $admission->mobile_number }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Email Address</label>
                                    <p class="mb-0">{{ $admission->email }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Current Address</label>
                                    <p class="mb-0">{{ $admission->current_address }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Permanent Address</label>
                                    <p class="mb-0">{{ $admission->permanent_address ?? $admission->current_address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-book me-2"></i>Academic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Program</label>
                                    <p class="mb-0">{{ $admission->program->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Division</label>
                                    <p class="mb-0">{{ $admission->division->division_name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Academic Session</label>
                                    <p class="mb-0">{{ $admission->academicSession->session_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Year</label>
                                    <p class="mb-0">{{ $admission->academic_year }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Education -->
                    @if($admission->tenth_board || $admission->twelfth_board)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-journal-bookmark me-2"></i>Previous Education</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">10th Board</label>
                                    <p class="mb-0">{{ $admission->tenth_board ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">10th Percentage</label>
                                    <p class="mb-0">{{ $admission->tenth_percentage ? $admission->tenth_percentage . '%' : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">12th Board</label>
                                    <p class="mb-0">{{ $admission->twelfth_board ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">12th Percentage</label>
                                    <p class="mb-0">{{ $admission->twelfth_percentage ? $admission->twelfth_percentage . '%' : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Uploaded Documents -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-paperclip me-2"></i>Uploaded Documents</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($admission->photo_path)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Student Photo</label>
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $admission->photo_path) }}" 
                                             alt="Student Photo" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $admission->photo_path) }}" 
                                               target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i> View Full
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($admission->signature_path)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Student Signature</label>
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $admission->signature_path) }}" 
                                             alt="Signature" class="img-thumbnail" style="max-width: 200px; max-height: 80px;">
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $admission->signature_path) }}" 
                                               target="_blank" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye me-1"></i> View Full
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($admission->twelfth_marksheet_path)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">12th Marksheet</label>
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $admission->twelfth_marksheet_path) }}" 
                                           target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                                        </a>
                                    </div>
                                </div>
                                @endif

                                @if($admission->cast_certificate_path)
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted">Cast Certificate</label>
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $admission->cast_certificate_path) }}" 
                                           target="_blank" class="btn btn-outline-primary">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                                        </a>
                                    </div>
                                </div>
                                @endif

                                @if(!$admission->photo_path && !$admission->signature_path && !$admission->twelfth_marksheet_path && !$admission->cast_certificate_path)
                                <div class="col-12">
                                    <p class="text-muted mb-0"><i class="bi bi-info-circle me-2"></i>No documents uploaded.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reason -->
                    @if($admission->rejection_reason)
                    <div class="card shadow-sm mb-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Rejection Reason</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $admission->rejection_reason }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Verify Admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to verify this admission application?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>Once verified, the student can be enrolled in the system.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="verifyForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="bi bi-check me-1"></i>Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Admission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Please provide a reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x me-1"></i>Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verifyAdmission(id) {
    document.getElementById('verifyForm').action = `/admissions/${id}/verify`;
    new bootstrap.Modal(document.getElementById('verifyModal')).show();
}

function rejectAdmission(id) {
    document.getElementById('rejectForm').action = `/admissions/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

function enrollAdmission(id) {
    if(confirm('Are you sure you want to enroll this student? This will create a student account.')) {
        fetch(`/admissions/${id}/enroll`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Student enrolled successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endpush
@endsection
