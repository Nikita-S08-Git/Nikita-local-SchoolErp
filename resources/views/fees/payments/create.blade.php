@extends('layouts.app')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Record Fee Payment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fees.payments.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Select Student <span class="text-danger">*</span></label>
                                    <select id="studentSelect" class="form-select @error('student_id') is-invalid @enderror" required>
                                        <option value="">Choose Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}">
                                                {{ $student->first_name }} {{ $student->last_name }} 
                                                ({{ $student->roll_number }}) - {{ $student->program->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Outstanding Fees</label>
                                    <div id="outstandingFees" class="border rounded p-3 bg-light">
                                        <p class="text-muted mb-0">Select a student to view outstanding fees</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="paymentForm" style="display: none;">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                                        <select name="student_fee_id" id="studentFeeSelect" class="form-select @error('student_fee_id') is-invalid @enderror" required>
                                            <option value="">Select Fee</option>
                                        </select>
                                        @error('student_fee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" id="paymentAmount" class="form-control @error('amount') is-invalid @enderror" 
                                               step="0.01" min="0.01" placeholder="0.00" required>
                                        <small class="text-muted">Maximum: <span id="maxAmount">₹0.00</span></small>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                        <select name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror" required>
                                            <option value="">Select Mode</option>
                                            <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="online" {{ old('payment_mode') == 'online' ? 'selected' : '' }}>Online</option>
                                            <option value="cheque" {{ old('payment_mode') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                            <option value="dd" {{ old('payment_mode') == 'dd' ? 'selected' : '' }}>Demand Draft</option>
                                        </select>
                                        @error('payment_mode')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                        <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                        @error('payment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Transaction ID</label>
                                        <input type="text" name="transaction_id" class="form-control @error('transaction_id') is-invalid @enderror" 
                                               value="{{ old('transaction_id') }}" placeholder="Optional">
                                        @error('transaction_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Remarks</label>
                                        <input type="text" name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                               value="{{ old('remarks') }}" placeholder="Optional notes">
                                        @error('remarks')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-2"></i>Record Payment
                                </button>
                                <a href="{{ route('fees.payments.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('studentSelect').addEventListener('change', function() {
    const studentId = this.value;
    const outstandingDiv = document.getElementById('outstandingFees');
    const paymentForm = document.getElementById('paymentForm');
    const studentFeeSelect = document.getElementById('studentFeeSelect');
    
    if (!studentId) {
        outstandingDiv.innerHTML = '<p class="text-muted mb-0">Select a student to view outstanding fees</p>';
        paymentForm.style.display = 'none';
        return;
    }
    
    // Fetch outstanding fees for selected student
    fetch(`/api/students/${studentId}/outstanding-fees`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                outstandingDiv.innerHTML = '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>No outstanding fees</p>';
                paymentForm.style.display = 'none';
                return;
            }
            
            let html = '<div class="row">';
            let options = '<option value="">Select Fee</option>';
            
            data.forEach(fee => {
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="d-flex justify-content-between">
                            <span>${fee.fee_head_name}:</span>
                            <strong class="text-danger">₹${fee.outstanding_amount}</strong>
                        </div>
                    </div>
                `;
                options += `<option value="${fee.id}" data-outstanding="${fee.outstanding_amount}">${fee.fee_head_name} - ₹${fee.outstanding_amount}</option>`;
            });
            
            html += '</div>';
            outstandingDiv.innerHTML = html;
            studentFeeSelect.innerHTML = options;
            paymentForm.style.display = 'block';
        })
        .catch(() => {
            outstandingDiv.innerHTML = '<p class="text-danger mb-0">Error loading fees</p>';
            paymentForm.style.display = 'none';
        });
});

document.getElementById('studentFeeSelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const outstanding = selectedOption.getAttribute('data-outstanding');
    const maxAmountSpan = document.getElementById('maxAmount');
    const paymentAmountInput = document.getElementById('paymentAmount');
    
    if (outstanding) {
        maxAmountSpan.textContent = `₹${outstanding}`;
        paymentAmountInput.max = outstanding;
        paymentAmountInput.value = outstanding; // Auto-fill with full amount
    } else {
        maxAmountSpan.textContent = '₹0.00';
        paymentAmountInput.max = '';
        paymentAmountInput.value = '';
    }
});
</script>
@endsection