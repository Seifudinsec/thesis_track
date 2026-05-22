# Project Presentation: ThesisTrack
**Presented by: [Your Name]**

## 1. Introduction
ThesisTrack is a web-based solution built to solve the lack of organization in academic thesis management. My goal was to create a system that replaces messy email chains with a structured, transparent, and secure workflow.

## 2. Problem Statement
Many universities still use manual methods for thesis submission, leading to lost files, delayed feedback, and a lack of data for administrators. ThesisTrack centralizes this entire lifecycle.

## 3. System Architecture
I developed the system using a modular **LAMP stack** architecture:
- **Presentation Layer**: Custom CSS with a "Mobile-First" approach, ensuring accessibility on smartphones for busy students and supervisors.
- **Logic Layer**: PHP scripts that handle Role-Based Access Control (RBAC). No user can access another role's dashboard.
- **Data Layer**: A relational MySQL database using PDO (PHP Data Objects) for secure communication.

## 4. Technical Highlights
As a developer, I prioritized three core pillars:
1. **Security**: I used `password_hash` with BCRYPT for authentication and forced all database interactions through **Prepared Statements** to eliminate SQL Injection risks.
2. **Data Integrity**: In the feedback module, I implemented **Database Transactions**. This ensures that updating a thesis status and saving feedback happen as a single atomic unit—if one fails, both are rolled back.
3. **UX/UI**: I focused heavily on responsiveness. I implemented custom media queries and flexbox utilities to ensure that tables and dashboards adapt gracefully to small screens (480px and below).

## 5. Demonstration (Walkthrough)
- **Admin**: Shows the system oversight and user management CRUD.
- **Student**: Shows the secure PDF upload process and the status tracking dashboard.
- **Supervisor**: Shows the review interface where academic guidance is provided.

## 6. Conclusion
ThesisTrack is a scalable foundation for academic management. In the future, I plan to add email notifications and a plagiarism check integration.

---
**Thank you. I am now open for any technical questions.**
