<?php
session_start();

require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [];
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'book';

    if ($action === 'book') {
        $name = clean_input($_POST['name'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $subject = clean_input($_POST['subject'] ?? '');
        $date = clean_input($_POST['date'] ?? '');
        $time = clean_input($_POST['time'] ?? '');
        $notes = clean_input($_POST['notes'] ?? '');

        $errors = validate_booking($name, $email, $subject, $date, $time);

        if (empty($errors)) {
            $booking = [
                'id' => uniqid('bk_'),
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'date' => $date,
                'time' => $time,
                'notes' => $notes,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $_SESSION['bookings'][] = $booking;

            // Cookie example: remember the student's name for 7 days.
            setcookie('student_name', $name, time() + (7 * 24 * 60 * 60), '/');

            header('Location: index.php?status=booked');
            exit;
        }
    }

    if ($action === 'cancel') {
        $id = $_POST['id'] ?? '';
        $_SESSION['bookings'] = array_values(array_filter(
            $_SESSION['bookings'],
            function ($booking) use ($id) {
                return $booking['id'] !== $id;
            }
        ));

        header('Location: index.php?status=cancelled');
        exit;
    }

    if ($action === 'clear') {
        $_SESSION['bookings'] = [];
        header('Location: index.php?status=cleared');
        exit;
    }
}

$status = $_GET['status'] ?? '';

if ($status === 'booked') {
    $success = 'Appointment booked successfully!';
} elseif ($status === 'cancelled') {
    $success = 'Appointment cancelled.';
} elseif ($status === 'cleared') {
    $success = 'All appointments were cleared.';
}

$filter = clean_input($_GET['subject_filter'] ?? 'All');

$bookings = $_SESSION['bookings'];

if ($filter !== 'All') {
    $bookings = array_values(array_filter(
        $bookings,
        function ($booking) use ($filter) {
            return $booking['subject'] === $filter;
        }
    ));
}

$subjects = get_subjects();
$totalBookings = count($_SESSION['bookings']);
$todayBookings = count_today_bookings($_SESSION['bookings']);
$rememberedName = $_COOKIE['student_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <div>
            <h1>BlueTutor</h1>
            <p>Tutor Appointment Booking System</p>
        </div>
        <span class="badge">Book now</span>
    </div>
</header>

<main class="container">
    <section class="hero card">
        <div>
            <p class="hero-p">SHARE YOUR CONCERNS HERE!</p>
            <h2>Got a Problem, Book an Appointment in Seconds</h2>
            <p>
                This application allows students to book tutoring appointments,
                validate their information, view saved appointments, filter them by subject,
                and cancel appointments.
            </p>
        </div>
        <div class="stats">
            <div class="stat">
                <strong><?php echo $totalBookings; ?></strong>
                <span>Total Bookings</span>
            </div>
            <div class="stat">
                <strong><?php echo $todayBookings; ?></strong>
                <span>Today's Bookings</span>
            </div>
        </div>
    </section>

    <?php if ($success): ?>
        <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <strong>Please fix the following:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid">
        <section class="card">
            <h2>Book an Appointment</h2>
            <form method="POST" action="index.php" novalidate>
                <input type="hidden" name="action" value="book">

                <label for="name">Student Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($_POST['name'] ?? $rememberedName); ?>"
                    placeholder="e.g. Alex Johnson"
                >

                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    placeholder="alex@example.com"
                >

                <label for="subject">Subject</label>
                <select id="subject" name="subject">
                    <option value="">Choose a subject</option>
                    <?php foreach ($subjects as $subjectOption): ?>
                        <option
                            value="<?php echo htmlspecialchars($subjectOption); ?>"
                            <?php echo (($_POST['subject'] ?? '') === $subjectOption) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($subjectOption); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="two-columns">
                    <div>
                        <label for="date">Date</label>
                        <input
                            type="date"
                            id="date"
                            name="date"
                            min="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>"
                        >
                    </div>
                    <div>
                        <label for="time">Time</label>
                        <select id="time" name="time">
                            <option value="">Choose a time</option>
                            <?php
                            $times = ['09:00 AM', '10:30 AM', '01:00 PM', '02:30 PM', '04:00 PM', '09:00 PM'];
                            foreach ($times as $timeOption):
                            ?>
                                <option
                                    value="<?php echo $timeOption; ?>"
                                    <?php echo (($_POST['time'] ?? '') === $timeOption) ? 'selected' : ''; ?>
                                >
                                    <?php echo $timeOption; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <label for="notes">Notes (optional)</label>
                <textarea id="notes" name="notes" rows="4" placeholder="What would you like help with?"><?php
                    echo htmlspecialchars($_POST['notes'] ?? '');
                ?></textarea>

                <button class="btn primary" type="submit">Book Appointment</button>
            </form>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <h2>Saved Appointments</h2>
                    <p>Appointments are stored in a PHP session.</p>
                </div>

                <form method="GET" action="index.php" class="filter-form">
                    <label for="subject_filter">Filter</label>
                    <select id="subject_filter" name="subject_filter" onchange="this.form.submit()">
                        <option value="All">All</option>
                        <?php foreach ($subjects as $subjectOption): ?>
                            <option
                                value="<?php echo htmlspecialchars($subjectOption); ?>"
                                <?php echo ($filter === $subjectOption) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($subjectOption); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <p>No appointments found.</p>
                    <span>Submit the form to create your first booking.</span>
                </div>
            <?php else: ?>
                <div class="booking-list">
                    <?php foreach ($bookings as $booking): ?>
                        <article class="booking-item">
                            <div class="booking-top">
                                <div>
                                    <h3><?php echo htmlspecialchars($booking['subject']); ?></h3>
                                    <p class="muted">
                                        <?php echo htmlspecialchars($booking['date']); ?>
                                        at
                                        <?php echo htmlspecialchars($booking['time']); ?>
                                    </p>
                                </div>
                                <span class="status">Confirmed</span>
                            </div>

                            <p><strong>Student:</strong> <?php echo htmlspecialchars($booking['name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>

                            <?php if (!empty($booking['notes'])): ?>
                                <p><strong>Notes:</strong> <?php echo htmlspecialchars($booking['notes']); ?></p>
                            <?php endif; ?>

                            <form method="POST" action="index.php" onsubmit="return confirm('Cancel this appointment?');">
                                <input type="hidden" name="action" value="cancel">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                <button class="btn danger" type="submit">Cancel</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($totalBookings > 0): ?>
                <form method="POST" action="index.php" class="clear-form" onsubmit="return confirm('Clear all appointments?');">
                    <input type="hidden" name="action" value="clear">
                    <button class="btn secondary" type="submit">Clear All</button>
                </form>
            <?php endif; ?>
        </section>
    </div>
</main>

<footer>
    <div class="container">
        Design by Muneera Aman - 2026
    </div>
</footer>
</body>
</html>
