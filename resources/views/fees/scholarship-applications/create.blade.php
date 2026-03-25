@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Apply for Scholarship</h2>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Eligibility Criteria:</strong> Only students belonging to SC, ST, OBC, and EWS categories are eligible for scholarships. General category students are not eligible.
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('fees.scholarship-applications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Student *</label>
                    <select name="student_id" class="form-select" required id="studentSelect">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            @if($student->category !== 'general')
                                <option value="{{ $student->id }}" data-category="{{ $student->category }}">
                                    {{ $student->full_name }} ({{ $student->admission_number }}) - {{ strtoupper($student->category) }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small class="text-muted">Only SC, ST, OBC, and EWS category students are eligible</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Scholarship *</label>
                    <select name="scholarship_id" class="form-select" required>
                        <option value="">Select Scholarship</option>
                        @foreach($scholarships as $scholarship)
                            <option value="{{ $scholarship->id }}">
                                {{ $scholarship->name }} - {{ $scholarship->discount_value }}{{ $scholarship->discount_type === 'percentage' ? '%' : '₹' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Supporting Documents *</label>
                    <input type="file" name="documents" class="form-control" accept=".pdf,.jpg,.png" required>
                    <small class="text-muted">Upload caste certificate, income certificate, etc. (Max 5MB)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Application</button>
                <a href="{{ route('fees.scholarship-applications.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
// Show category warning on student selection
document.getElementById('studentSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const category = selectedOption.getAttribute('data-category');
    
    if (category && category !== 'general') {
        // Show success message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success mt-2';
        alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>Student is eligible for scholarship (Category: ' + category.toUpperCase() + ')';
        
        // Remove existing alerts
        const existingAlert = this.parentElement.querySelector('.alert');
        if (existingAlert) existingAlert.remove();
        
        this.parentElement.appendChild(alertDiv);
    }
});
</script>
@endsection
