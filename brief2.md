# Personal Trainer Session Schedule Feature

## Feature Overview

This feature allows gym members to book and manage personal training session schedules using a timetable system.

Members can select available dates, time slots, and preferred personal trainers for one-on-one training sessions. Admin can manage trainer availability, approve bookings, and monitor all scheduled sessions.

This feature improves gym service quality by organizing trainer appointments efficiently and avoiding scheduling conflicts.

---

## Purpose of This Feature

The purpose of this feature is to:

* Allow members to schedule personal training sessions online
* Help admins manage trainer availability
* Prevent double booking of trainers
* Improve appointment tracking
* Enhance user experience and gym professionalism

---

## User Roles Involved

## 1. Member

Members can:

* View available trainers
* View available time slots
* Book personal training sessions
* View upcoming scheduled sessions
* Cancel or reschedule sessions
* View session history

---

## 2. Admin

Admins can:

* Add personal trainers
* Manage trainer schedules
* Approve or reject booking requests
* Update trainer availability
* View all member bookings
* Generate trainer session reports

---

## Main Functions

## 1. Trainer List Display

### Functions

Display available personal trainers with:

* Trainer Name
* Specialization
* Available Days
* Available Time Slots
* Contact Information
* Status (Available / Busy)

---

## 2. Session Booking Form

### Functions

Members can submit a booking form using:

* Member Name (auto-filled if logged in)
* Trainer Selection (dropdown)
* Session Date (date picker)
* Time Slot (dropdown)
* Session Type (Strength / Cardio / Weight Loss / Rehab)
* Additional Notes (optional)
* Submit Booking Button

This fulfills the HTML form input requirement.

---

## 3. Booking Timetable View

### Functions

Members can view:

* Upcoming sessions
* Approved bookings
* Pending bookings
* Cancelled sessions

Displayed in timetable or calendar format.

---

## 4. Booking Management (CRUD)

### Functions

### Create

Book new training session

### Read

View all session bookings

### Update

Reschedule session date or time

### Delete

Cancel session booking

This fulfills the CRUD requirement.

---

## 5. Search Functionality

### Admin Search Options

Search by:

* Member Name
* Trainer Name
* Booking Date
* Session Status

This helps quickly find appointments.

---

## 6. Report Generation

### Reports Included

### Monthly Session Report

Total personal training sessions booked per month

### Trainer Performance Report

Total sessions handled by each trainer

### Revenue Report

Total income from personal training sessions

### Example Calculation

```text
Total PT Revenue = Number of Sessions × Session Fee
```

This fulfills report generation with basic calculations.

---

## Suggested New Database Tables

## 1. trainers

Stores personal trainer information.

### Fields

* trainer_id (Primary Key)
* trainer_name
* specialization
* available_days
* available_time
* contact_number
* status

---

## 2. session_bookings

Stores member booking records.

### Fields

* booking_id (Primary Key)
* member_id (Foreign Key)
* trainer_id (Foreign Key)
* session_date
* session_time
* session_type
* booking_status
* notes
* created_at

---

## Foreign Key Relationships

```text
session_bookings.member_id
→ members.member_id

session_bookings.trainer_id
→ trainers.trainer_id
```

---

## Admin Dashboard Additions

Add new dashboard cards:

* Total Personal Training Sessions
* Active Trainer Bookings
* Monthly PT Revenue
* Available Trainers Today

---

## Updated Sitemap

```text
Admin Dashboard
│
├── Member Management
├── Membership Package Management
├── Payment Management
├── Personal Trainer Management
│   ├── View Trainers
│   ├── Add Trainer
│   ├── Edit Trainer
│   └── Delete Trainer
│
├── Session Booking Management
│   ├── View All Bookings
│   ├── Approve Booking
│   ├── Reject Booking
│   └── Update Schedule
│
├── Reports
└── Logout

Member Dashboard
│
├── My Profile
├── Membership Details
├── Payment History
├── Personal Trainer Schedule
│   ├── View Trainers
│   ├── Book Session
│   ├── My Timetable
│   └── Cancel / Reschedule Session
│
└── Logout
```

---

## System Flow

```text
Login
↓
Member Dashboard
↓
View Available Trainers
↓
Select Date and Time
↓
Book Personal Training Session
↓
Admin Reviews Booking
↓
Approve / Reject
↓
Session Added to Timetable
↓
Member Attends Session
```

---

## Project Value

Adding this feature makes the Gym Membership Management System more advanced and professional.

It shows stronger system design, better real-world functionality, and improves project presentation quality for academic submission.

This feature is highly recommended because it demonstrates:

* Advanced CRUD implementation
* Additional report generation
* Better database relationships
* Real business workflow
* Improved user experience

This can help increase lecturer impression and project marks.
