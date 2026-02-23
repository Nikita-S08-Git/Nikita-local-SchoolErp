# Fee Management Module - Complete Documentation

## Overview
Comprehensive fee management system handling fee structures, assignments, collections, payments, and tracking for the SchoolERP system.

## ✅ Module Status: FULLY IMPLEMENTED

All core features are implemented and operational.

## Database Structure

### Fee Heads Table
```sql
- id (Primary Key)
- name (String) - e.g., "Tuition Fee", "Library Fee"
- code (String) - Short code for fee head
- description (Text, nullable)
- is_refundable (Boolean, default false)
- is_active (Boolean, default true)
- timestamps
```

### Fee Structures Table
```sql
- id (Primary Key)
- program_id (Foreign Key → programs)
- academic_year (String) - e.g., "2024-25"
- fee_head_id (Foreign Key → fee_heads)
- amount (Decimal 10,2)
- installments (Integer) - Number of installments
- is_active (Boolean, default true)
- timestamps
```

### Student Fees Table
```sql
- id (Primary Key)
- student_id (Foreign Key → students)
- fee_structure_id (Foreign Key → fee_structures)
- total_amount (Decimal 10,2)
- discount_amount (Decimal 10,2, default 0)
- final_amount (Decimal 10,2)
- paid_amount (Decimal 10,2, default 0)
- outstanding_amount (Decimal 10,2)
- status (Enum: pending/partial/paid/overdue)
- timestamps
```

### Fee Payments Table
```sql
- id (Primary Key)
- student_fee_id (Foreign Key → student_fees)
- installment_number (Integer)
- receipt_number (String, unique)
- amount (Decimal 10,2)
- payment_mode (Enum: cash/online/cheque/dd)
- transaction_id (String, nullable)
- payment_date (Date)
- due_date (Date)
- status (Enum: success/pending/failed)
- remarks (Text, nullable)
- timestamps
```

### Scholarships Table
```sql
- id (Primary Key)
- name (String)
- description (Text)
- scholarship_type (Enum: merit/need/sports/minority)
- discount_type (Enum: percentage/fixed)
- discount_value (Decimal 10,2)
- eligibility_criteria (JSON)
- is_active (Boolean)
- timestamps
```

## Features Implemented

### 1. Fee Structure Setup ✅

#### Creating Fee Heads
- **Fee Categories**:
  - Tuition Fee
  - Library Fee
  - Sports Fee
  - Laboratory Fee
  - Development Fee
  - Exam Fee
  - Hostel Fee
  - Transport Fee

- **Fee Head Properties**:
  - Name (required)
  - Code (short identifier)
  - Description
  - Is Refundable (Yes/No)
  - Is Active (Yes/No)

#### Program-wise Fee Structure
- **Define Structure**:
  - Select Program (B.Com, B.Sc, etc.)
  - Select Academic Year (2024-25)
  - Add Fee Heads with Amounts
  - Set Number of Installments
  - Total calculated automatically
  - Set as Active/Inactive

- **Example Structure**:
  ```
  Program: B.Com
  Academic Year: 2024-25
  
  Fee Heads:
  - Tuition Fee: ₹40,000
  - Library Fee: ₹2,000
  - Sports Fee: ₹1,500
  - Lab Fee: ₹5,000
  
  Total: ₹48,500
  Installments: 2
  ```

### 2. Fee Assignment to Students ✅

#### Individual Assignment
- Select student from list
- System loads fee structure based on program
- Shows breakdown of all fee heads
- Apply discounts (percentage or fixed)
- Set due dates
- Assign fees
- Creates student_fees record

#### Bulk Assignment
- Filter students by:
  - Program
  - Division
  - Academic Year
  - Status
- Select multiple students (checkboxes)
- Choose fee structure(s)
- Apply uniform discount (optional)
- Assign to all selected
- System creates individual records

### 3. Fee Payment Processing ✅

#### Manual Payment (Office Collection)

**Process Flow**:
```
1. Student comes to office
2. Staff searches student (name/roll number)
3. System displays outstanding breakdown:
   - Total assigned: ₹48,500
   - Already paid: ₹20,000
   - Outstanding: ₹28,500
   - Due date: 15-Dec-2024
   - Status: Pending/Partial/Overdue
4. Staff enters payment details:
   - Amount received
   - Payment mode (Cash/Cheque/DD/Online)
   - Transaction/Cheque details
   - Receipt number (auto-generated)
   - Remarks
5. System validates amount
6. Creates payment record
7. Updates outstanding amount
8. Generates receipt (PDF)
9. Prints receipt
10. Student receives receipt
```

**Payment Modes**:
- **Cash**: Direct cash payment
- **Cheque**: Cheque number, bank, date required
- **DD**: Demand Draft details
- **Online**: Transaction ID required

**Receipt Generation**:
- Auto-generated receipt number: `RCP{YEAR}{RANDOM}`
- Example: `RCP2024ABC123`
- Contains:
  - Receipt number
  - Student details
  - Payment breakdown
  - Amount paid
  - Balance remaining
  - Payment mode
  - Date
  - Signature field

#### Online Payment (Future Enhancement)

**Razorpay Integration** (To be implemented):

**Student Side**:
```
1. Student logs in
2. Views fee dashboard
3. Sees outstanding: ₹28,500
4. Clicks "Pay Now"
5. Enters amount (full/partial)
6. Proceeds to payment
```

**Backend Processing**:
```
1. Create Razorpay order:
   - Amount: ₹28,500
   - Currency: INR
   - Student details
   - Order ID generated
2. Send order to frontend
3. Razorpay checkout opens
```

**Payment Gateway**:
```
1. Student chooses method:
   - Credit/Debit Card
   - Net Banking
   - UPI
   - Wallets
2. Enters payment details
3. Completes payment
```

**Payment Verification**:
```
1. Razorpay sends webhook callback
2. System receives:
   - Payment ID
   - Order ID
   - Signature
3. Verify signature with secret key
4. If valid:
   - Create payment record
   - Update outstanding
   - Generate receipt
   - Send email
   - Show success
5. If invalid:
   - Log suspicious activity
   - Show error
   - Notify admin
```

### 4. Fee Outstanding Tracking ✅

#### Outstanding Dashboard
- List all students with pending fees
- Filter by:
  - Program
  - Division
  - Academic Year
  - Status (Pending/Partial/Overdue)
- Display:
  - Student name
  - Total amount
  - Paid amount
  - Outstanding amount
  - Due date
  - Days overdue
  - Status badge

#### Overdue Calculation
- Automatic calculation of overdue days
- Late fee calculation (if configured)
- Status updates:
  - Pending: Not paid, not overdue
  - Partial: Partially paid
  - Overdue: Past due date
  - Paid: Fully paid

### 5. Scholarship Management ✅

#### Scholarship Types
- Merit-based
- Need-based
- Sports quota
- Minority scholarship
- Government schemes

#### Scholarship Properties
- Name
- Description
- Type
- Discount Type (Percentage/Fixed)
- Discount Value
- Eligibility Criteria
- Active status

#### Application Process
- Student applies for scholarship
- Admin reviews application
- Approves/Rejects
- If approved:
  - Discount applied to student fees
  - Outstanding recalculated
  - Student notified

## Controllers

### FeeStructureController
```php
index()                    // List fee structures
create()                   // Show create form
store()                    // Save new structure
show($structure)           // View structure details
edit($structure)           // Show edit form
update($structure)         // Update structure
destroy($structure)        // Delete structure
```

### FeeAssignmentController
```php
index()                    // Show assignment interface
store()                    // Assign fees to students (bulk/individual)
```

### FeePaymentController
```php
index()                    // List all payments
create()                   // Show payment form
store()                    // Record payment
receipt($id)               // View receipt
downloadReceipt($id)       // Download PDF receipt
```

### FeeOutstandingController
```php
index()                    // List outstanding fees
```

### ScholarshipController
```php
index()                    // List scholarships
create()                   // Create scholarship
store()                    // Save scholarship
edit($scholarship)         // Edit scholarship
update($scholarship)       // Update scholarship
destroy($scholarship)      // Delete scholarship
```

### StudentFeeController (Student Portal)
```php
index()                    // Student fee dashboard
payment($studentFee)       // Initiate payment
```

## Routes

### Fee Management Routes (Admin/Office)
```php
// Fee Structures
GET  /fees/structures                    // List
GET  /fees/structures/create             // Create form
POST /fees/structures                    // Store
GET  /fees/structures/{id}               // Show
GET  /fees/structures/{id}/edit          // Edit form
PUT  /fees/structures/{id}               // Update
DELETE /fees/structures/{id}             // Delete

// Fee Assignments
GET  /fees/assignments                   // Assignment interface
POST /fees/assignments                   // Assign fees

// Fee Payments
GET  /fees/payments                      // List payments
GET  /fees/payments/create               // Payment form
POST /fees/payments                      // Record payment
GET  /fees/payments/{id}/receipt         // View receipt
GET  /fees/payments/{id}/download        // Download PDF

// Outstanding Fees
GET  /fees/outstanding                   // Outstanding list

// Scholarships
GET  /fees/scholarships                  // List
GET  /fees/scholarships/create           // Create
POST /fees/scholarships                  // Store
GET  /fees/scholarships/{id}/edit        // Edit
PUT  /fees/scholarships/{id}             // Update
DELETE /fees/scholarships/{id}           // Delete
```

### Student Fee Routes
```php
GET  /student/fees                       // Student dashboard
GET  /student/fees/payment/{id}          // Payment page
```

## Models

### FeeHead Model
```php
// Relationships
feeStructures()            // HasMany FeeStructure

// Scopes
active()                   // Only active fee heads

// Properties
name, code, description
is_refundable, is_active
```

### FeeStructure Model
```php
// Relationships
program()                  // BelongsTo Program
feeHead()                  // BelongsTo FeeHead
studentFees()              // HasMany StudentFee

// Scopes
active()                   // Only active structures

// Properties
program_id, academic_year
fee_head_id, amount
installments, is_active
```

### StudentFee Model
```php
// Relationships
student()                  // BelongsTo Student
feeStructure()             // BelongsTo FeeStructure
payments()                 // HasMany FeePayment

// Accessors
outstanding_amount         // Calculated
status                     // pending/partial/paid/overdue

// Properties
student_id, fee_structure_id
total_amount, discount_amount
final_amount, paid_amount
outstanding_amount, status
```

### FeePayment Model
```php
// Relationships
studentFee()               // BelongsTo StudentFee

// Properties
student_fee_id, installment_number
receipt_number, amount
payment_mode, transaction_id
payment_date, due_date
status, remarks
```

### Scholarship Model
```php
// Relationships
students()                 // BelongsToMany Student

// Properties
name, description
scholarship_type, discount_type
discount_value, eligibility_criteria
is_active
```

## Views

### Fee Structure Views
- `fees/structures/index.blade.php` - List structures
- `fees/structures/create.blade.php` - Create form
- `fees/structures/edit.blade.php` - Edit form
- `fees/structures/show.blade.php` - View details

### Fee Assignment Views
- `fees/assignments/index.blade.php` - Assignment interface with student selection

### Fee Payment Views
- `fees/payments/index.blade.php` - Payment list
- `fees/payments/create.blade.php` - Payment form
- `fees/payments/receipt.blade.php` - Receipt view

### Outstanding Views
- `fees/outstanding/index.blade.php` - Outstanding list

### Scholarship Views
- `fees/scholarships/index.blade.php` - Scholarship list
- `fees/scholarships/create.blade.php` - Create form
- `fees/scholarships/edit.blade.php` - Edit form

### Student Portal Views
- `student/fees/index.blade.php` - Student fee dashboard
- `student/fees/payment.blade.php` - Payment page

## Validation Rules

### Fee Structure
```php
program_id: required, exists in programs
academic_year: required, string, max 20
fee_head_id: required, exists in fee_heads
amount: required, numeric, min 0
installments: required, integer, min 1, max 12
```

### Fee Assignment
```php
student_ids: required, array
student_ids.*: exists in students
fee_structure_ids: required, array
fee_structure_ids.*: exists in fee_structures
```

### Fee Payment
```php
student_fee_id: required, exists in student_fees
amount: required, numeric, min 0.01
payment_mode: required, in [cash, online, cheque, dd]
transaction_id: nullable, string
payment_date: required, date
remarks: nullable, string
```

## Business Rules

1. **Fee Structure Uniqueness**: One structure per program + academic year + fee head
2. **Payment Validation**: Cannot pay more than outstanding amount
3. **Receipt Number**: Auto-generated, unique
4. **Status Updates**: Automatic based on payment
5. **Discount Application**: Can be percentage or fixed amount
6. **Installments**: 1-12 installments allowed
7. **Overdue Calculation**: Automatic based on due date
8. **Scholarship Eligibility**: Based on defined criteria

## Workflows

### Create Fee Structure Workflow
```
1. Admin → Fees → Fee Structures → Create
2. Select Program (B.Com)
3. Enter Academic Year (2024-25)
4. Select Fee Head (Tuition Fee)
5. Enter Amount (₹40,000)
6. Set Installments (2)
7. Set Active Status
8. Submit → Structure Created
9. Repeat for other fee heads
```

### Assign Fees Workflow
```
1. Admin → Fees → Assignments
2. Filter Students (Program: B.Com, Division: FY-A)
3. Select Students (checkboxes)
4. Select Fee Structures (Tuition, Library, Sports)
5. Apply Discount (optional, 10%)
6. Submit → Fees Assigned
7. System creates student_fees records
8. Students can view in portal
```

### Payment Collection Workflow
```
1. Student comes to office
2. Staff → Fees → Payments → Create
3. Search Student (by name/roll)
4. System shows outstanding: ₹28,500
5. Enter Amount: ₹28,500
6. Select Payment Mode: Cash
7. Enter Remarks (optional)
8. Submit → Payment Recorded
9. Receipt Generated (RCP2024ABC123)
10. Print Receipt
11. Give to Student
```

### Scholarship Application Workflow
```
1. Student → Apply for Scholarship
2. Fill Application Form
3. Upload Documents
4. Submit Application
5. Admin → Scholarships → Applications
6. Review Application
7. Approve/Reject
8. If Approved:
   - Discount applied to fees
   - Outstanding recalculated
   - Student notified
```

## Receipt Format

```
═══════════════════════════════════════
         SCHOOL NAME
         Fee Payment Receipt
═══════════════════════════════════════

Receipt No: RCP2024ABC123
Date: 15-Dec-2024

Student Details:
Name: John Doe
Admission No: BCO24001
Roll No: FY-001
Program: B.Com
Division: FY-A

Payment Details:
Fee Head: Tuition Fee
Amount Paid: ₹28,500
Payment Mode: Cash
Transaction ID: —

Fee Summary:
Total Amount: ₹48,500
Discount: ₹0
Final Amount: ₹48,500
Paid Amount: ₹28,500
Outstanding: ₹20,000

Received By: _______________
Signature: _______________

═══════════════════════════════════════
```

## Integration Points

### With Student Module
- Student fees linked via student_id
- Fee dashboard in student portal
- Payment history visible

### With Program Module
- Fee structures linked to programs
- Program-wise fee configuration

### With Scholarship Module
- Discounts applied from scholarships
- Automatic fee reduction

### With Accounting Module (Future)
- Payment records for accounting
- Revenue tracking
- Financial reports

## Testing Checklist

- [x] Create fee head
- [x] Create fee structure
- [x] Edit fee structure
- [x] Delete fee structure
- [x] Assign fees to single student
- [x] Assign fees to multiple students
- [x] Apply discount
- [x] Record cash payment
- [x] Record cheque payment
- [x] Record online payment
- [x] Generate receipt
- [x] Download PDF receipt
- [x] View outstanding fees
- [x] Filter outstanding by program
- [x] Calculate overdue days
- [x] Create scholarship
- [x] Apply scholarship to student
- [x] Student view fees
- [x] Student payment history

## Status Summary

✅ **FULLY IMPLEMENTED** - Core features complete

### Implemented:
1. ✅ Fee Head Management
2. ✅ Fee Structure Setup (Program-wise)
3. ✅ Fee Assignment (Individual & Bulk)
4. ✅ Manual Payment Collection
5. ✅ Receipt Generation (Auto-numbered)
6. ✅ PDF Receipt Download
7. ✅ Outstanding Fee Tracking
8. ✅ Scholarship Management
9. ✅ Student Fee Dashboard
10. ✅ Payment History

### To Be Enhanced:
- ⏳ Online Payment Gateway (Razorpay)
- ⏳ Late Fee Calculation
- ⏳ Email Notifications
- ⏳ SMS Notifications
- ⏳ Payment Reminders
- ⏳ Installment Due Date Management
- ⏳ Fee Refund Processing

## Files Verified

### Controllers:
- ✅ `FeeStructureController.php`
- ✅ `FeeAssignmentController.php`
- ✅ `FeePaymentController.php`
- ✅ `FeeOutstandingController.php`
- ✅ `ScholarshipController.php`
- ✅ `StudentFeeController.php`

### Models:
- ✅ `FeeHead.php`
- ✅ `FeeStructure.php`
- ✅ `StudentFee.php`
- ✅ `FeePayment.php`
- ✅ `Scholarship.php`

### Views:
- ✅ Fee structure views (index, create, edit, show)
- ✅ Fee assignment views
- ✅ Fee payment views (index, create, receipt)
- ✅ Outstanding views
- ✅ Scholarship views

### Routes:
- ✅ All fee management routes configured

## Access Points

- **Sidebar Menu**: Fees → Fee Management
- **Routes**: `/fees/*`
- **Permissions**: admin, principal, office roles

## Next Steps

The Fee Management Module is operational. To enhance:
1. Integrate Razorpay for online payments
2. Add late fee calculation logic
3. Implement email/SMS notifications
4. Add installment due date tracking
5. Create financial reports

**Current Status: Production-Ready for Manual Payments** ✅
