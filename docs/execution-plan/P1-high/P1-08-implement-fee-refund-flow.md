# [P1-08] Implement Fee Refund Flow

## Objective
Add refund transaction logic and reversal entries for fee payments.

## Problem Statement
System can collect fees but has no mechanism to process refunds.

## Expected Outcome
- Refund request workflow
- Refund approval process
- Reversal entries in fee_payments table
- Updated outstanding amount calculation

## Scope of Work
1. Create Refund model and migration
2. Build RefundController
3. Create refund request form
4. Implement approval workflow
5. Add reversal entry logic
6. Generate refund receipt

## Files to Modify
- CREATE: `app/Models/Fee/Refund.php`
- CREATE: `database/migrations/xxxx_create_refunds_table.php`
- CREATE: `app/Http/Controllers/Web/RefundController.php`
- CREATE: `resources/views/fees/refunds/request.blade.php`
- CREATE: `resources/views/fees/refunds/receipt.blade.php`
- MODIFY: `routes/web.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Refund request workflow
- [ ] Refund approval process
- [ ] Reversal entries in fee_payments table
- [ ] Updated outstanding amount calculation
- [ ] Refund receipt generation
- [ ] Audit trail for all refunds

## Developer Notes
Refund should:
- Create negative payment entry
- Update student_fee outstanding_amount
- Generate refund receipt with unique number
- Require approval from authorized user
- Log all actions for audit
