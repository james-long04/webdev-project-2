<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_GET['book_id']) ? $_GET['book_id'] : null;
$message = "";
$message_class = "";

if ($book_id) {
    // Check if the book is available
    $stmt = $conn->prepare("SELECT available FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $stmt->bind_result($available);
    $stmt->fetch();
    $stmt->close();

    if ($available) {
        // Reserve the book for the user
        $reservation_date = date("Y-m-d");
        $stmt = $conn->prepare("INSERT INTO reservations (user_id, book_id, reservation_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $book_id, $reservation_date);

        if ($stmt->execute()) {
            // Update the book's availability to 'unavailable'
            $update_stmt = $conn->prepare("UPDATE books SET available = 0 WHERE book_id = ?");
            $update_stmt->bind_param("i", $book_id);
            $update_stmt->execute();
            $update_stmt->close();

            $message = "Book reserved successfully!";
            $message_class = "success";
        } else {
            $message = "Error reserving book: " . $stmt->error;
            $message_class = "error";
        }
        $stmt->close();
    } else {
        $message = "This book is already reserved by another user.";
        $message_class = "error";
    }
} else {
    $message = "Invalid book selection.";
    $message_class = "error";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserve Book</title>
</head>
<body>
    <div class="container">
        <h2>Reserve Book</h2>

        <?php if (!empty($message)): ?>
            <div class="message <?= $message_class ?>"><?= $message ?></div>
        <?php endif; ?>
        
        <a href="search.php">Back to Search</a>
        <a href="view_reservations.php">View My Reservations</a>
    </div>
</body>
</html>
