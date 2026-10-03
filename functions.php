<?php
function clean_input(string $value): string
{
    return trim($value);
}

function get_subjects(): array
{
    return [
        'PHP Programming',
        'Web Design',
        'Database Basics',
        'JavaScript',
        'Study Skills'
    ];
}

function validate_booking(
    string $name,
    string $email,
    string $subject,
    string $date,
    string $time
): array {
    $errors = [];

    if ($name === '') {
        $errors[] = 'Student name is required.';
    } elseif (strlen($name) < 2) {
        $errors[] = 'Student name must be at least 2 characters.';
    }

    if ($email === '') {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($subject === '' || !in_array($subject, get_subjects(), true)) {
        $errors[] = 'Please choose a valid subject.';
    }

    if ($date === '') {
        $errors[] = 'Appointment date is required.';
    } else {
        $selectedDate = strtotime($date);
        $today = strtotime(date('Y-m-d'));

        if ($selectedDate === false) {
            $errors[] = 'Please choose a valid date.';
        } elseif ($selectedDate < $today) {
            $errors[] = 'Appointment date cannot be in the past.';
        }
    }

    $allowedTimes = ['09:00 AM', '10:30 AM', '01:00 PM', '02:30 PM', '04:00 PM'];

    if ($time === '' || !in_array($time, $allowedTimes, true)) {
        $errors[] = 'Please choose a valid appointment time.';
    }

    return $errors;
}

function count_today_bookings(array $bookings): int
{
    $today = date('Y-m-d');
    $count = 0;

    foreach ($bookings as $booking) {
        if (($booking['date'] ?? '') === $today) {
            $count++;
        }
    }

    return $count;
}
