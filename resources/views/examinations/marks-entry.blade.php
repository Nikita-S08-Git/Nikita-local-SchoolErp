@extends('layouts.app')

@section('title', 'Marks Entry')

@section('content')
<div class="container-fluid p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✏️ Marks Entry - {{ $examination->name }}</h5>
            <div id="draftStatus" class="badge bg-light text-dark">
                <span id="draftIndicator">●</span>
                <span id="draftText">Ready</span>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Exam Info Header -->
            @if($subject)
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <div>
                        <strong>Exam:</strong> {{ $examination->name }} &nbsp;|&nbsp;
                        <strong>Subject:</strong> {{ $subject->name }} ({{ $subject->code }})
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Warning:</strong> This exam does not have a subject assigned. Please edit the exam and assign a subject first.
            </div>
            @endif

            <form method="GET" class="mb-4" id="filterForm">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Division *</label>
                        <select name="division_id" id="divisionSelect" class="form-select" required>
                            <option value="">Select Division</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->division_name }} {{ $division->program ? ' - ' . $division->program->name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100" {{ !$subject ? 'disabled' : '' }}>Load Students</button>
                    </div>
                </div>
            </form>

            @if(count($students) > 0)
            <form id="marksForm" action="{{ route('examinations.save-marks', $examination) }}" method="POST">
                @csrf
                <input type="hidden" name="division_id" value="{{ request('division_id') }}">
                <input type="hidden" name="max_marks" value="100">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Marks Obtained (Max: 100)</th>
                                <th>Grade</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>{{ $student->roll_number }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>
                                    <input type="number" name="marks[{{ $student->id }}]" 
                                           class="form-control marks-input" 
                                           data-student-id="{{ $student->id }}"
                                           min="0" max="100" step="0.01"
                                           value="{{ $marks[$student->id]->marks_obtained ?? '' }}">
                                </td>
                                <td>
                                    <span class="badge bg-info grade-display" data-student-id="{{ $student->id }}">
                                        {{ $marks[$student->id]->grade ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if(isset($marks[$student->id]))
                                        <span class="badge bg-{{ $marks[$student->id]->result == 'pass' ? 'success' : 'danger' }}">
                                            {{ ucfirst($marks[$student->id]->result) }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" id="submitBtn">💾 Save Marks</button>
                    <button type="button" class="btn btn-warning" id="saveDraftBtn">💫 Save Draft</button>
                    <a href="{{ route('examinations.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
            @else
                <div class="alert alert-info">Please select division and subject to load students.</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const divisionSelect = document.getElementById('divisionSelect');
    const filterForm = document.getElementById('filterForm');
    
    // Auto-submit form when division changes
    divisionSelect.addEventListener('change', function() {
        filterForm.submit();
    });
    // Configuration
    const AUTO_SAVE_INTERVAL = 30000; // 30 seconds
    const STORAGE_KEY_PREFIX = 'marks_draft_';
    
    // Get form elements
    const examId = {{ $examination->id }};
    const subjectId = {{ $subject ? $subject->id : 'null' }};
    const marksForm = document.getElementById('marksForm');
    const draftStatus = document.getElementById('draftStatus');
    const draftIndicator = document.getElementById('draftIndicator');
    const draftText = document.getElementById('draftText');
    const submitBtn = document.getElementById('submitBtn');
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    
    // LocalStorage key for this specific exam/subject
    const storageKey = STORAGE_KEY_PREFIX + examId + '_' + subjectId;
    
    // Initialize
    loadDraftsFromServer();
    loadFromLocalStorage();
    
    // Auto-save timer
    let autoSaveTimer = setInterval(saveDraft, AUTO_SAVE_INTERVAL);
    
    // Event Listeners
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            saveDraft();
        });
    }
    
    if (marksForm) {
        marksForm.addEventListener('submit', function(e) {
            // Clear draft on successful submission
            clearDraftsFromServer();
            clearLocalStorage();
            clearInterval(autoSaveTimer);
        });
    }
    
    // Listen for input changes to update localStorage
    document.querySelectorAll('.marks-input').forEach(input => {
        input.addEventListener('input', function() {
            updateLocalStorage();
            updateDraftStatus('unsaved');
        });
    });
    
    /**
     * Save draft to server
     */
    function saveDraft() {
        if (!subjectId) {
            console.warn('No subject assigned to this exam');
            return;
        }
        
        updateDraftStatus('saving');
        
        const marksData = collectMarksData();
        
        fetch('{{ route("examinations.save-draft") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                examination_id: examId,
                subject_id: parseInt(subjectId),
                marks: marksData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateDraftStatus('saved');
                // Also save to localStorage as backup
                saveToLocalStorage(marksData);
            } else {
                updateDraftStatus('error');
                // Fallback to localStorage
                updateLocalStorage();
            }
        })
        .catch(error => {
            console.error('Auto-save failed:', error);
            updateDraftStatus('error');
            // Fallback to localStorage
            updateLocalStorage();
        });
    }
    
    /**
     * Load drafts from server
     */
    function loadDraftsFromServer() {
        if (!subjectId) {
            console.warn('No subject assigned to this exam');
            return;
        }
        
        fetch('{{ route("examinations.load-drafts") }}?examination_id=' + examId + '&subject_id=' + subjectId, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.drafts) {
                populateMarksFromDrafts(data.drafts);
            }
        })
        .catch(error => {
            console.error('Failed to load drafts from server:', error);
            // Try loading from localStorage
            loadFromLocalStorage();
        });
    }
    
    /**
     * Clear drafts from server
     */
    function clearDraftsFromServer() {
        if (!subjectId) {
            console.warn('No subject assigned to this exam');
            return;
        }
        
        fetch('{{ route("examinations.clear-drafts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                examination_id: examId,
                subject_id: parseInt(subjectId)
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Drafts cleared from server');
        })
        .catch(error => {
            console.error('Failed to clear drafts:', error);
        });
    }
    
    /**
     * Collect all marks data from the form
     */
    function collectMarksData() {
        const marksData = {};
        document.querySelectorAll('.marks-input').forEach(input => {
            const studentId = input.dataset.studentId;
            const value = input.value;
            if (value !== '') {
                marksData[studentId] = {
                    marks: parseFloat(value),
                    remarks: ''
                };
            }
        });
        return marksData;
    }
    
    /**
     * Populate marks fields from drafts
     */
    function populateMarksFromDrafts(drafts) {
        Object.keys(drafts).forEach(studentId => {
            const draft = drafts[studentId];
            const input = document.querySelector('.marks-input[data-student-id="' + studentId + '"]');
            if (input && draft.marks !== null && draft.marks !== undefined) {
                input.value = draft.marks;
            }
        });
        updateDraftStatus('loaded');
    }
    
    /**
     * Save to localStorage as backup
     */
    function saveToLocalStorage(marksData) {
        try {
            const data = {
                examId: examId,
                subjectId: parseInt(subjectId),
                marks: marksData,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem(storageKey, JSON.stringify(data));
        } catch (e) {
            console.error('Failed to save to localStorage:', e);
        }
    }
    
    /**
     * Update localStorage with current form values
     */
    function updateLocalStorage() {
        const marksData = collectMarksData();
        saveToLocalStorage(marksData);
    }
    
    /**
     * Load from localStorage backup
     */
    function loadFromLocalStorage() {
        try {
            const data = localStorage.getItem(storageKey);
            if (data) {
                const parsed = JSON.parse(data);
                if (parsed.marks) {
                    populateMarksFromDrafts(parsed.marks);
                    updateDraftStatus('restored');
                }
            }
        } catch (e) {
            console.error('Failed to load from localStorage:', e);
        }
    }
    
    /**
     * Clear localStorage
     */
    function clearLocalStorage() {
        try {
            localStorage.removeItem(storageKey);
        } catch (e) {
            console.error('Failed to clear localStorage:', e);
        }
    }
    
    /**
     * Update draft status indicator
     */
    function updateDraftStatus(status) {
        switch(status) {
            case 'saving':
                draftIndicator.className = 'spinner-border spinner-border-sm me-1';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Saving...';
                draftStatus.className = 'badge bg-warning';
                break;
            case 'saved':
                draftIndicator.className = '';
                draftIndicator.innerHTML = '✔';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Draft Saved';
                draftStatus.className = 'badge bg-success';
                break;
            case 'error':
                draftIndicator.className = '';
                draftIndicator.innerHTML = '⚠';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Save Failed';
                draftStatus.className = 'badge bg-danger';
                break;
            case 'loaded':
                draftIndicator.className = '';
                draftIndicator.innerHTML = '✔';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Draft Loaded';
                draftStatus.className = 'badge bg-info';
                break;
            case 'restored':
                draftIndicator.className = '';
                draftIndicator.innerHTML = '↺';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Restored from backup';
                draftStatus.className = 'badge bg-warning';
                break;
            case 'unsaved':
                draftIndicator.className = '';
                draftIndicator.innerHTML = '●';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Unsaved changes';
                draftStatus.className = 'badge bg-secondary';
                break;
            default:
                draftIndicator.className = '';
                draftIndicator.innerHTML = '●';
                draftIndicator.style.display = 'inline-block';
                draftText.textContent = 'Ready';
                draftStatus.className = 'badge bg-light text-dark';
        }
    }
});
</script>
<style>
#draftStatus {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}
#draftIndicator {
    margin-right: 5px;
}
.spinner-border-sm {
    width: 0.8rem;
    height: 0.8rem;
    border-width: 0.15em;
}
</style>
@endpush
@endsection
