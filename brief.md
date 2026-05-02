# Gym Membership Management System

## Project Overview

The Gym Membership Management System is a web-based system developed using **HTML, PHP, and MySQL** to help gym administrators manage members, membership packages, payments, and reports efficiently.

The purpose of this system is to replace manual record keeping (paper forms or spreadsheets) with a digital system that is faster, more accurate, and easier to manage.

This project fulfills the required features of:

- Multiple user authentication (login, registration, cookies, logout)
- Display multiple lists from database
- HTML form inputs (text fields, dropdowns, radio buttons, checkboxes)
- Search functionality
- Report generation with basic calculations
- Full CRUD operations (Create, Read, Update, Delete)

---

## Tech Stack

### Frontend

- HTML
- CSS

### Backend

- PHP

### Database

- MySQL

### Database Management Tool

- phpMyAdmin

### Local Server

- XAMPP

---

## User Roles

The system has two main users:

### 1. Admin

Admin manages the full system.

### 2. Member

Members can register, log in, and manage their own membership details.

---

## Core Features

## 1. Authentication System

### Functions

- User registration
- Login
- Session management
- Cookie support
- Logout

### Users

- Admin
- Member

---

## 2. Admin Dashboard

### Functions

Admin can view:

- Total Members
- Active Memberships
- Expired Memberships
- Monthly Income
- Recent member activity

This provides a quick overview of gym operations.

---

## 3. Member Management (CRUD)

### Functions

- Add new member
- View member list
- Edit member details
- Delete member records
- Search members by name, email, or phone number

### Example Fields

- Full Name
- Email
- Phone Number
- Gender
- Join Date
- Membership Package

---

## 4. Membership Package Management (CRUD)

### Functions

- Add package
- View package list
- Edit package
- Delete package

### Example Packages

| Package  | Duration | Price |
| -------- | -------- | ----- |
| Basic    | 1 Month  | RM100 |
| Standard | 3 Months | RM250 |
| Premium  | 6 Months | RM450 |

---

## 5. Payment Management (CRUD)

### Functions

- Add payment record
- View payment history
- Update payment status
- Delete payment record

### Example Fields

- Member Name
- Payment Date
- Amount
- Payment Status

---

## 6. Search Functionality

### Functions

Admin can search using:

- Member Name
- Email
- Phone Number
- Membership Package

This helps quickly find specific records.

---

## 7. Report Generation

### Reports Included

### Monthly Income Report

Calculate total payments received.

### Active Membership Report

Display currently active members.

### Expired Membership Report

Display members whose memberships have ended.

### Basic Calculation Example

```text
Total Monthly Income = SUM(payment_amount)
```

This fulfills the report requirement with calculations.

---

## 8. Member Dashboard

### Functions

Members can:

- View profile
- Update personal information
- View membership details
- Check membership expiry date
- View payment history
- Renew membership

---

## Suggested Database Tables

## 1. users

Stores login information.

### Fields

- user_id (Primary Key)
- username
- password
- role

---

## 2. members

Stores member personal details.

### Fields

- member_id (Primary Key)
- full_name
- email
- phone
- gender
- join_date
- package_id (Foreign Key)

---

## 3. membership_packages

Stores available gym packages.

### Fields

- package_id (Primary Key)
- package_name
- duration
- price

---

## 4. payments

Stores payment transactions.

### Fields

- payment_id (Primary Key)
- member_id (Foreign Key)
- payment_method
- payment_date
- amount
- payment_status

---

## Foreign Key Relationships

```text
members.package_id
→ membership_packages.package_id

payments.member_id
→ members.member_id
```

---

## Simple Sitemap

```text
Home
│
├── Login
├── Register (Members only)
├── About Gym
└── Contact Us

Dashboard
│
├── Admin Dashboard
│   ├── Member Management
│   ├── Membership Package Management
│   ├── Payment Management
│   ├── Search Member
│   ├── Reports
│   └── Logout
│
└── Member Dashboard
    ├── My Profile
    ├── Membership Details
    ├── Payment History
    └── Logout
```

---

## System Flow

Home Page
↓
Register (for new users)
↓
System saves data into database
↓
Login
↓
Dashboard Access
↓
Use system features
↓
Logout

---

## Project Goal

The main goal of this project is to build a simple but fully functioning Gym Membership Management System that demonstrates the use of:

- HTML forms
- PHP backend processing
- MySQL database operations
- CRUD implementation
- Search functionality
- Report generation
- User authentication

This system should be professional, easy to present, and suitable for academic project submission.
