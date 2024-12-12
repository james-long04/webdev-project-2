<?php
session_start();
// connection to the database
$conn = new mysqli("localhost", "root", "", "book");
// check if the connection worked
if ($conn->connect_error) {
    // if theres error connecting to database, stop script and display error
    die("Connection failed: " . $conn->connect_error);
}
$error = "";
// check if the form submitted using POST 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // getting the input from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    // SQL query to fetch the data from database based on username
    $query = "SELECT * FROM Users WHERE username = '$username'";    
    // execute and store the result
    $result = $conn->query($query);
    // seeing if any user with the given username exists in the database
    if ($result->num_rows > 0) {
        // fetch the user data if they exist
        $user = $result->fetch_assoc();        
        // check if the entered password matches the stored password in database
       if ($password === $user['password']) {
            // store the username in the session and redirect
            $_SESSION['username'] = $user['username'];
            header("Location: search.php");
            exit();
        } else {
            // if the password is incorrect, display an error
            $error = "Invalid password!";
        }
    } else {
        // if no user is found, display an error
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html> <!--start of html code -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- link to the font im using -->
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500&display=swap" rel="stylesheet">
    <!-- css link to my css file -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
<h1>James's Big Book Library!</h1>
</header>
    <div class="container">
        <h2>Login</h2>
        <!-- display error messages if they exist -->
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <!-- form for user to enter detailss -->
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <!-- i have a button for submitting details -->
            <button type="submit">Login</button>
        </form>
        <!-- footer with link to my registration page -->
        <p class="footer">Don't have a library account? <a href="register.php">Register for one here!</a></p>
    </div>
</body>







</html>














