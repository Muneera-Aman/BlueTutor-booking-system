# BlueTutor - PHP Tutor Appointment Booking System

A small working PHP application for a classroom participation project.

## Real-world purpose

BlueTutor allows a student to:
- Book a tutoring appointment
- Enter and validate student information
- Choose a subject, date, and time
- View saved appointments
- Filter appointments by subject
- Cancel an appointment
- Clear all appointments

## PHP concepts used

1. Variables and data types
   - Strings, arrays, integers, booleans

2. Conditions
   - `if`, `elseif`, and validation rules

3. Loops
   - `foreach` to display subjects, errors, and appointments

4. Functions
   - `clean_input()`
   - `get_subjects()`
   - `validate_booking()`
   - `count_today_bookings()`

5. Forms
   - Appointment form
   - Cancel form
   - Clear-all form

6. GET and POST
   - POST is used to create/cancel/clear appointments
   - GET is used for subject filtering and status messages

7. Form validation
   - Required fields
   - Email validation
   - Date cannot be in the past
   - Subject and time must be valid

8. Sessions and cookies
   - PHP session stores bookings
   - Cookie remembers the student's name for 7 days

## How to run with XAMPP

1. Install and open XAMPP.
2. Start **Apache**.
3. Copy the `php_tutor_booking` folder into:
   - Windows: `C:\xampp\htdocs\`
4. Open your browser.
5. Go to:
   - `http://localhost/php_tutor_booking/`

No database is required.

## Suggested demo

1. Open the application.
2. Submit the form empty to show validation.
3. Enter valid data and create an appointment.
4. Show the appointment in the Saved Appointments section.
5. Use the subject filter.
6. Explain that bookings are stored in `$_SESSION`.
7. Explain that the student's name is remembered with a cookie.
8. Cancel an appointment.

## Main files

- `index.php` - main interface and application logic
- `functions.php` - reusable PHP functions
- `style.css` - dark-blue design
