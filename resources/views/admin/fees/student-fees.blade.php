@extends('layouts.app')

@section('title', 'Student Fees')
@section('page-title', 'Student Fees')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-user-graduate me-2 text-primary"></i>Student Fees</h2>
                    <p class="text-muted mb-0">View and manage student fee records</p>
                </div>
                <a href="{{ route('admin.fees') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Student</label>
                    <select name="student_id" class="form-select">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.fees.student-fees') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Fees Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Fee Records</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Student</th>
                            <th>Fee Head</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Outstanding</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fees as $fee)
                        <tr>
                            <td>
                                <strong>{{ $fee->student->first_name ?? 'N/A' }} {{ $fee->student->last_name ?? '' }}</strong>
                                <br><small class="text-muted">{{ $fee->student->admission_number ?? '' }}</small>
                            </td>
                            <td>{{ $fee->feeStructure->feeHead->name ?? $fee->feeStructure->fee_head ?? 'N/A' }}</td>
                            <td><strong>₹{{ number_format($fee->total_amount ?? $fee->final_amount ?? 0, 2) }}</strong></td>
                            <td class="text-success"><strong>₹{{ number_format($fee->paid_amount ?? 0, 2) }}</strong></td>
                            <td class="text-danger"><strong>₹{{ number_format(($fee->total_amount ?? $fee->final_amount ?? 0) - ($fee->paid_amount ?? 0), 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $fee->status === 'paid' ? 'success' : ($fee->status === 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($fee->status) }}
                                </span>
                                @if($fee->status !== 'paid' && $fee->created_at && \Carbon\Carbon::parse($fee->created_at)->diffInDays() > 30)
                                    <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Overdue</small>
                                @endif
                            </td>
                            <td>
                                {{ $fee->created_at->format('d M Y') }}
                                <br><small class="text-muted">{{ $fee->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="openPaymentModal({{ $fee->id }}, '{{ $fee->student->first_name ?? 'Student' }} {{ $fee->student->last_name ?? '' }}', {{ $fee->total_amount ?? $fee->final_amount ?? 0 }}, {{ $fee->paid_amount ?? 0 }})" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="fas fa-credit-card me-1"></i> Pay Now
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <h5>No fee records found</h5>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($fees->hasPages())
            <div class="card-footer bg-light">
                {{ $fees->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 20px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border: none;">
                <div class="d-flex align-items-center">
                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <i class="fas fa-credit-card text-white" style="font-size: 1.8rem;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0" id="paymentModalLabel" style="font-weight: 700; font-size: 1.4rem;">Make Payment</h5>
                        <p class="text-white mb-0 opacity-75" style="font-size: 0.9rem;">Complete the fee payment</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body" style="padding: 35px; background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);">
                <!-- Student Info Card -->
                <div class="card mb-4" style="border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div class="card-body" style="padding: 20px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0" style="color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fas fa-user-graduate me-2" style="color: #667eea;"></i> Student Details
                            </h6>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-1" style="font-size: 0.8rem; color: #94a3b8;">Student Name</p>
                                <p class="mb-0 fw-semibold" id="modalStudentName" style="font-size: 1.05rem; color: #1e293b; font-weight: 600;">-</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1" style="font-size: 0.8rem; color: #94a3b8;">Fee ID</p>
                                <p class="mb-0 fw-semibold" id="modalFeeId" style="font-size: 1.05rem; color: #1e293b; font-weight: 600;">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fee Summary Card -->
                <div class="card mb-4" style="border-radius: 15px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <div class="card-body" style="padding: 20px;">
                        <h6 class="mb-3" style="color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-calculator me-2" style="color: #667eea;"></i> Fee Summary
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 15px; border-radius: 12px; border: 1px solid #fcd34d;">
                                    <p class="mb-1" style="font-size: 0.75rem; color: #92400e; font-weight: 600; text-transform: uppercase;">Total Amount</p>
                                    <p class="mb-0 fw-bold" id="modalTotalAmount" style="font-size: 1.2rem; color: #92400e; font-weight: 700;">₹0</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 15px; border-radius: 12px; border: 1px solid #93c5fd;">
                                    <p class="mb-1" style="font-size: 0.75rem; color: #1e40af; font-weight: 600; text-transform: uppercase;">Paid Amount</p>
                                    <p class="mb-0 fw-bold" id="modalPaidAmount" style="font-size: 1.2rem; color: #1e40af; font-weight: 700;">₹0</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); padding: 18px; border-radius: 12px; border: 1px solid #fca5a5;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1" style="font-size: 0.75rem; color: #991b1b; font-weight: 600; text-transform: uppercase;">Outstanding Balance</p>
                                            <p class="mb-0 fw-bold" id="modalOutstanding" style="font-size: 1.4rem; color: #991b1b; font-weight: 700;">₹0</p>
                                        </div>
                                        <i class="fas fa-exclamation-circle" style="font-size: 2rem; color: #991b1b; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form id="paymentForm" method="POST" action="{{ route('admin.fees.pay') }}">
                    @csrf
                    <input type="hidden" name="fee_id" id="feeIdInput">
                    
                    <h6 class="mb-3" style="color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fas fa-wallet me-2" style="color: #667eea;"></i> Payment Details
                    </h6>
                    
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #475569; font-size: 0.9rem;">
                            <i class="fas fa-rupee-sign me-2" style="color: #667eea;"></i> Payment Amount
                        </label>
                        <input type="number" class="form-control" name="amount" id="paymentAmount" 
                               placeholder="Enter amount" required min="1" step="0.01"
                               style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 14px 18px; font-size: 1.05rem; font-weight: 600;">
                        <div class="form-text" style="font-size: 0.85rem; color: #64748b;">
                            <i class="fas fa-info-circle me-1"></i> Enter amount to pay (minimum ₹1)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #475569; font-size: 0.9rem;">
                            <i class="fas fa-qrcode me-2" style="color: #667eea;"></i> Payment Method
                        </label>
                        <select class="form-select" name="payment_method" id="paymentMethod" required
                                style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 14px 18px; font-size: 1rem; font-weight: 600;">
                            <option value="">Select payment method</option>
                            <option value="cash">💵 Cash</option>
                            <option value="card">💳 Card/Debit Card</option>
                            <option value="upi">📱 UPI/PhonePe/GPay</option>
                            <option value="net_banking">🏦 Net Banking</option>
                            <option value="cheque">📝 Cheque</option>
                            <option value="bank_transfer">🔄 Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #475569; font-size: 0.9rem;">
                            <i class="fas fa-receipt me-2" style="color: #667eea;"></i> Transaction Reference (Optional)
                        </label>
                        <input type="text" class="form-control" name="transaction_id" 
                               placeholder="Enter transaction ID / Cheque number"
                               style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 14px 18px; font-size: 1rem;">
                        <div class="form-text" style="font-size: 0.85rem; color: #64748b;">
                            <i class="fas fa-info-circle me-1"></i> For card/UPI/cheque payments
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-weight: 600; color: #475569; font-size: 0.9rem;">
                            <i class="fas fa-comment-alt me-2" style="color: #667eea;"></i> Payment Remarks (Optional)
                        </label>
                        <textarea class="form-control" name="remarks" rows="3" 
                                  placeholder="Add any notes or comments about this payment"
                                  style="border-radius: 12px; border: 2px solid #e2e8f0; padding: 14px 18px; font-size: 1rem; resize: none;"></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" 
                                style="border-radius: 14px; padding: 16px; font-weight: 700; font-size: 1.05rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                            <i class="fas fa-check-circle me-2"></i> Confirm Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openPaymentModal(feeId, studentName, totalAmount, paidAmount) {
    // Update modal content
    document.getElementById('modalStudentName').textContent = studentName;
    document.getElementById('modalFeeId').textContent = '#' + feeId;
    document.getElementById('feeIdInput').value = feeId;
    
    // Format amounts
    const outstanding = totalAmount - paidAmount;
    document.getElementById('modalTotalAmount').textContent = '₹' + totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('modalPaidAmount').textContent = '₹' + paidAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('modalOutstanding').textContent = '₹' + outstanding.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Set suggested payment amount
    document.getElementById('paymentAmount').value = outstanding > 0 ? outstanding : totalAmount;
}

// Form validation
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const amount = parseFloat(document.getElementById('paymentAmount').value);
    const outstanding = parseFloat(document.getElementById('modalOutstanding').textContent.replace(/[^0-9.]/g, ''));
    
    if (amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount greater than 0');
        return false;
    }
    
    if (amount > outstanding) {
        if (!confirm('Payment amount exceeds outstanding balance. Continue?')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
@endpush
@endsection
