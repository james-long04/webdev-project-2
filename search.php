<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
//connection
$conn = new mysqli("localhost", "root", "", "book");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// fetching the categories
$categories = [];
$categoryQuery = "SELECT category_id, category_description FROM category";
$categoryResult = $conn->query($categoryQuery);
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[$row['category_id']] = $row['category_description'];
    }
}
$searchResults = [];
$title = $author = $category = "";
// search form submitting for search
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $author = $conn->real_escape_string($_POST['author'] ?? '');
    $category = $conn->real_escape_string($_POST['category'] ?? '');
    $query = "SELECT * FROM Books WHERE 1=1";
    if (!empty($title)) {
        $query .= " AND booktitle LIKE '%$title%'";
    }
    if (!empty($author)) {
        $query .= " AND author LIKE '%$author%'";
    }
    if (!empty($category)) {
        $query .= " AND category = '$category'";
    }

    $result = $conn->query($query);
    if ($result) {
        $searchResults = $result->fetch_all(MYSQLI_ASSOC);
    }
}
//this is for reservation logic
if (isset($_POST['reserve'])) {
    $isbn = $_POST['isbn'];
    $user = $_SESSION['username']; // get the logged in user of session

    //check if book already reserved
    $checkQuery = "SELECT * FROM reserved_books WHERE ISBN = '$isbn' AND username = '$user'";
    $checkResult = $conn->query($checkQuery);
    if ($checkResult && $checkResult->num_rows == 0) {
        //if not reserved insert reservation data
        $reservationDate = date("Y-m-d"); // getting todays date
        $insertQuery = "INSERT INTO reserved_books (ISBN, username, reserved_date) 
                        VALUES ('$isbn', '$user', '$reservationDate')";
        $conn->query($insertQuery);

        echo "<script>alert('Book reserved successfully!'); window.location.href='search.php';</script>";
    } else {
        echo "<script>alert('You have already reserved this book or it is already reserved.'); window.location.href='search.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>James's Big Book Library</h1>
</header>
<div class="clearfix"></div>

<div class="container">
    <h2>Search for Books</h2>
    <!-- search form -->
    <form method="POST">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($title) ?>">
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" value="<?= htmlspecialchars($author) ?>">
        <label for="category">Category:</label>
        <select name="category" id="category">
            <option value="">All</option>
            <?php foreach ($categories as $id => $description): ?>
                <option value="<?= $id ?>" <?= ($category == $id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($description) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>
	<div class="results">
		<?php if (!empty($searchResults)): ?>
			<ul>
				<?php foreach ($searchResults as $book): ?>
					<li>
						<strong>Title:</strong> <?= htmlspecialchars($book['booktitle']) ?><br>
						<strong>Author:</strong> <?= htmlspecialchars($book['author']) ?><br>
						<strong>Edition:</strong> <?= htmlspecialchars($book['edition']) ?><br>
						<strong>Year:</strong> <?= htmlspecialchars($book['year']) ?><br>
						<strong>Category:</strong> <?= htmlspecialchars($categories[$book['category']] ?? 'Unknown') ?><br>
						<strong>Status:</strong> <?= $book['reserved'] ? 'Reserved' : 'Available' ?><br>

						<?php if ($book['reserved'] == 0): ?>
							<form method="POST">
								<input type="hidden" name="isbn" value="<?= htmlspecialchars($book['ISBN']) ?>">
								<button type="submit" name="reserve">Reserve</button>
							</form>
						<?php else: ?>
							<button disabled>Reserved</button>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else: ?>
			<p>No books found. Please change your search criteria.</p>
		<?php endif; ?>
	</div>
</div>
<footer class="footer">
	<p><a href="view_reservations.php" class="button">View Your Reserved Books</a></p>
    <form action="logout.php" method="POST" style="display:inline;">
        <button type="submit" class="button">Logout</button>
    </form>
</footer>
</body>








</html>






