@extends('layouts.app')

@section('title', 'Fee Assignments')
@section('page-title', 'Fee Assignments')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Assign Fees to Students</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('fees.assignments.store') }}" id="assignFeesForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Program</label>
                                    <select class="form-select" id="programSelect">
                                        <option value="">All Programs</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}">{{ $program->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Fee Structures</label>
                                    <select name="fee_structure_ids[]" class="form-select" multiple required>
                                        @foreach($feeStructures as $structure)
                                            <option value="{{ $structure->id }}">
                                                {{ $structure->feeHead->name }} - {{ $structure->program->name }} ({{ $structure->academic_year }}) - ₹{{ $structure->amount }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Hold Ctrl to select multiple</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Select Students</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label for="selectAll" class="form-check-label fw-bold">Select All</label>
                                </div>
                                <div id="studentsList">
                                    <p class="text-muted">Select a program to load students</p>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Assign Fees
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('programSelect').addEventListener('change', function() {
    const programId = this.value;
    const studentsList = document.getElementById('studentsList');
    
    if (!programId) {
        studentsList.innerHTML = '<p class="text-muted">Select a program to load students</p>';
        return;
    }
    
    // Simple AJAX call to load students
    fetch(`/api/students?program_id=${programId}`)
        .then(response => response.json())
        .then(response => {
            const data = response.data.data || response.data || [];
            let html = '';
            data.forEach(student => {
                html += `
                    <div class="form-check">
                        <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="${student.id}" id="student_${student.id}">
                        <label class="form-check-label" for="student_${student.id}">
                            ${student.first_name} ${student.last_name} (${student.roll_number})
                        </label>
                    </div>
                `;
            });
            studentsList.innerHTML = html;
        })
        .catch(() => {
            studentsList.innerHTML = '<p class="text-danger">Error loading students</p>';
        });
});

document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

document.getElementById('assignFeesForm').addEventListener('submit', function(e) {
    const checkedStudents = document.querySelectorAll('.student-checkbox:checked');
    if (checkedStudents.length === 0) {
        e.preventDefault();
        alert('Please select at least one student');
        return false;
    }
});
</script>
@endsection