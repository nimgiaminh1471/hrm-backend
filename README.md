# HRM Backend

A comprehensive Human Resource Management System built with Laravel.

## Database Schema

```dbml
// Enums
Enum candidate_status {
  new
  reviewing
  shortlisted
  interview_scheduled
  interviewed
  offered
  hired
  rejected
  withdrawn
}

Enum offer_status {
  pending
  accepted
  rejected
  expired
}

Enum interview_status {
  scheduled
  completed
  cancelled
  rescheduled
}

Enum interview_type {
  phone
  video
  "in-person"
  technical
  hr
  final
}

Enum payroll_status {
  draft
  processing
  completed
  cancelled
}

Enum payroll_period_type {
  monthly
  "bi-weekly"
  weekly
}

Enum attendance_status {
  present
  absent
  late
  half_day
  on_leave
}

Enum leave_request_status {
  pending
  approved
  rejected
  cancelled
}

Enum employment_type {
  "full-time"
  "part-time"
  contract
  internship
}

Enum experience_level {
  entry
  mid
  senior
  lead
  manager
}

// Core Tables
Table companies {
  id bigint [pk]
  name varchar
  domain varchar [unique]
  email varchar
  phone varchar [null]
  address text [null]
  logo varchar [null]
  settings json [null]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table departments {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  name varchar
  code varchar [null]
  description text [null]
  manager_id bigint [ref: > users.id, null]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table positions {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  department_id bigint [ref: > departments.id]
  title varchar
  code varchar [null]
  description text [null]
  requirements json [null]
  base_salary decimal(10,2) [null]
  benefits json [null]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table employees {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  user_id bigint [ref: > users.id]
  department_id bigint [ref: > departments.id]
  position_id bigint [ref: > positions.id]
  employee_id varchar [unique]
  first_name varchar
  last_name varchar
  email varchar [unique]
  phone varchar [null]
  date_of_birth date [null]
  gender enum('male', 'female', 'other') [null]
  address text [null]
  emergency_contact_name varchar [null]
  emergency_contact_phone varchar [null]
  joining_date date
  contract_start_date date [null]
  contract_end_date date [null]
  bank_name varchar [null]
  bank_account_number varchar [null]
  tax_id varchar [null]
  social_security_number varchar [null]
  documents json [null]
  additional_info json [null]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

// Hiring Module
Table job_postings {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  department_id bigint [ref: > departments.id]
  position_id bigint [ref: > positions.id]
  title varchar
  description text
  requirements text
  responsibilities text
  qualifications json
  salary_min decimal(10,2) [null]
  salary_max decimal(10,2) [null]
  location varchar
  employment_type employment_type
  experience_level experience_level
  application_deadline date
  benefits json [null]
  is_active boolean [default: true]
  is_featured boolean [default: false]
  posting_channels json [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table candidates {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  job_posting_id bigint [ref: > job_postings.id]
  first_name varchar
  last_name varchar
  email varchar [unique]
  phone varchar [null]
  address text [null]
  resume_path varchar
  cover_letter_path varchar [null]
  skills json [null]
  experience json [null]
  education json [null]
  status candidate_status [default: 'new']
  notes text [null]
  interview_feedback json [null]
  interview_score decimal(5,2) [null]
  interview_date date [null]
  offer_details json [null]
  offer_date date [null]
  offer_deadline date [null]
  offer_status offer_status [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table interviews {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  candidate_id bigint [ref: > candidates.id]
  job_posting_id bigint [ref: > job_postings.id]
  interviewer_id bigint [ref: > users.id]
  title varchar
  description text [null]
  type interview_type
  scheduled_at datetime
  duration_minutes integer
  location varchar [null]
  meeting_link varchar [null]
  status interview_status [default: 'scheduled']
  interview_questions json [null]
  feedback json [null]
  rating decimal(5,2) [null]
  notes text [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

// Payroll Module
Table payroll_periods {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  name varchar
  start_date date
  end_date date
  type payroll_period_type
  status payroll_status [default: 'draft']
  payment_date date
  settings json [null]
  notes text [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table payrolls {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  payroll_period_id bigint [ref: > payroll_periods.id]
  employee_id bigint [ref: > employees.id]
  basic_salary decimal(10,2)
  gross_salary decimal(10,2)
  net_salary decimal(10,2)
  allowances json [null]
  deductions json [null]
  overtime_amount decimal(10,2) [default: 0]
  bonus_amount decimal(10,2) [default: 0]
  commission_amount decimal(10,2) [default: 0]
  tax_amount decimal(10,2)
  insurance_amount decimal(10,2)
  loan_amount decimal(10,2) [default: 0]
  leave_deduction decimal(10,2) [default: 0]
  other_earnings json [null]
  other_deductions json [null]
  notes text [null]
  status payroll_status [default: 'draft']
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

// Attendance Module
Table shifts {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  name varchar
  start_time time
  end_time time
  break_minutes integer [default: 0]
  working_days json [null]
  is_active boolean [default: true]
  description text [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table employee_shifts {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  employee_id bigint [ref: > employees.id]
  shift_id bigint [ref: > shifts.id]
  start_date date
  end_date date [null]
  is_active boolean [default: true]
  notes text [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table attendance_records {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  employee_id bigint [ref: > employees.id]
  shift_id bigint [ref: > shifts.id]
  date date
  clock_in datetime [null]
  clock_out datetime [null]
  late_minutes integer [default: 0]
  early_leave_minutes integer [default: 0]
  overtime_minutes integer [default: 0]
  break_minutes integer [default: 0]
  status attendance_status [default: 'absent']
  clock_in_location varchar [null]
  clock_out_location varchar [null]
  notes text [null]
  is_approved boolean [default: false]
  approved_by bigint [ref: > users.id, null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

// Leave Management Module
Table leave_types {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  name varchar
  code varchar [unique]
  description text [null]
  default_days integer [default: 0]
  is_paid boolean [default: true]
  requires_approval boolean [default: true]
  accrual_rules json [null]
  carry_forward_rules json [null]
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table leave_balances {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  employee_id bigint [ref: > employees.id]
  leave_type_id bigint [ref: > leave_types.id]
  year integer
  total_days decimal(5,2)
  used_days decimal(5,2) [default: 0]
  carried_forward_days decimal(5,2) [default: 0]
  remaining_days decimal(5,2)
  accrual_history json [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table leave_requests {
  id bigint [pk]
  company_id bigint [ref: > companies.id]
  employee_id bigint [ref: > employees.id]
  leave_type_id bigint [ref: > leave_types.id]
  start_date date
  end_date date
  days decimal(5,2)
  reason text
  attachments json [null]
  status leave_request_status [default: 'pending']
  approved_by bigint [ref: > users.id, null]
  rejection_reason text [null]
  handover_notes json [null]
  approval_chain json [null]
  created_at timestamp
  updated_at timestamp
  deleted_at timestamp [null]
}

Table users {
    id bigint [pk]
    name varchar
    email varchar
    email_verified_at timestamp [null]
    password varchar
    remember_token varchar
    created_at timestamp
    updated_at timestamp
    deleted_at timestamp [null]
}
```

## Features

1. Hiring
   - Job Posting Management
   - Applicant Tracking
   - Interview Scheduling
   - Candidate Evaluation
   - Offer Management

2. HRM (Human Resource Management)
   - Employee Database
   - Onboarding/Offboarding
   - Employee Self-Service Portal
   - HR Analytics & Reporting

3. Payroll
   - Salary Management
   - Payslips & Direct Deposits
   - Tax Compliance
   - Bonus & Incentive Management
   - Leave Accrual

4. Check-in (Time & Attendance)
   - Clock-In/Clock-Out
   - Real-Time Attendance Tracking
   - Shift Scheduling
   - Overtime Management

5. Time-Off (Leave Management)
   - Leave Requests
   - Approval Workflow
   - Leave Balance Tracking
   - Public Holidays
   - Integration with Payroll

## Getting Started

[Add installation and setup instructions here]

## License

[Add license information here]