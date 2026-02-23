Prompt 1 — Remove .env exposure from Git
Context:
Laravel 12 School ERP project

Task:
Remove .env file from Git tracking and secure environment configuration

Requirements:
- remove .env from repository tracking
- ensure .env is in .gitignore
- create .env.example with safe placeholders
- ensure no secrets are committed

Output:
git commands + .env.example file template


🔐 Prompt 2 — Add Login Rate Limiting
Context:
Laravel 12 API using Sanctum authentication

Task:
Add rate limiting to login endpoint

Requirements:
- limit login attempts to 5 per minute per IP
- use Laravel throttle middleware
- apply to /api/login route
- return proper JSON error response

Output:
updated route + middleware config

Prompt 3 — Fix Authentication Fallback Bug
Context:
AttendanceController currently uses auth()->id() ?? 1 fallback

Task:
Fix authentication handling

Requirements:
- remove fallback user id
- throw authentication error if not logged in
- ensure all attendance actions require authenticated user

Output:
updated AttendanceController method


🔐 Prompt 4 — Add Secure File Upload Validation
Context:
DocumentController handles student document uploads

Task:
Secure file upload system

Requirements:
- allow only jpeg, png, pdf
- max size 2MB
- store in private storage
- generate unique filename
- validate file before saving

Output:
updated controller method with validation


🔐 Prompt 5 — Add Global Form Request Validation System
Context:
Laravel project with partial FormRequest usage

Task:
Create base validation structure

Requirements:
- create BaseFormRequest class
- extend all requests from it
- include common validation rules
- include authorize() method

Output:
BaseFormRequest class template


🔐 Prompt 6 — Create StudentRequest Validation
Context:
StudentController currently uses inline validation

Task:
Create StudentRequest FormRequest

Requirements:
- validate name, email, mobile
- validate program_id, division_id
- validate academic_session_id
- validate guardian details
- proper error messages

Output:
StudentRequest class


🔐 Prompt 7 — Create FeePaymentRequest Validation
Context:
FeeController handles payments without structured validation

Task:
Create FeePaymentRequest

Requirements:
- validate amount numeric
- validate payment date
- validate payment mode (cash, UPI, bank)
- validate student_fee_id exists

Output:
FeePaymentRequest class


🔐 Prompt 8 — Enforce Validation in Controllers
Context:
Controllers still using inline validation

Task:
Replace inline validation with FormRequest

Requirements:
- use StudentRequest in StudentController
- use FeePaymentRequest in FeeController
- remove inline validation
- ensure consistent validation response

Output:
updated controller methods


🔐 Prompt 9 — Add Security Headers Middleware
Context:
Laravel app missing security headers

Task:
Create SecurityHeaders middleware

Requirements:
- add CSP header
- add X-Frame-Options
- add X-Content-Type-Options
- add X-XSS-Protection
- register middleware globally

Output:
middleware class + kernel registration


🔐 Prompt 10 — Sanctum Token Expiry
Context:
Sanctum tokens currently never expire

Task:
Add token expiration

Requirements:
- set expiration to 7 days
- update sanctum config
- ensure logout invalidates tokens

Output:
config changes + logout method


🔐 Prompt 11 — API Response Standardization
Context:
Controllers return inconsistent JSON responses

Task:
Create API response helper

Requirements:
- standard success response format
- standard error response format
- include message, data, status
- reusable helper class

Output:
ApiResponse helper class + example usage


🔐 Prompt 12 — Add Pagination to All List APIs
Context:
Some APIs return all records

Task:
Add pagination system

Requirements:
- default page size 25
- allow page parameter
- include meta (total, current page)
- apply to students, fees, attendance

Output:
updated index methods


🔐 Prompt 13 — Add Eager Loading Fix
Context:
Multiple controllers have N+1 query issues

Task:
Add eager loading

Requirements:
- StudentController load guardians, user
- FeeController load feeStructure
- AttendanceController load student relation

Output:
updated query examples


🔐 Prompt 14 — Move Hardcoded Values to Config
Context:
Project has hardcoded values like password, receipt format

Task:
Create config file

Requirements:
- create config/schoolerp.php
- move default password
- move receipt prefix
- move roll number format

Output:
config file + usage examples


🔐 Prompt 15 — Add Exception Handling + Logging
Context:
Controllers lack try-catch handling

Task:
Add global exception handling

Requirements:
- wrap DB transactions with try-catch
- log errors using Log::error
- return safe JSON error message
- no sensitive data leak

Output:
updated controller example