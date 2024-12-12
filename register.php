<?php
// connection to the database
$conn = new mysqli("localhost", "root", "", "book");

// check if the form was submitted using POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get the input data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $mobile = $_POST['mobile'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $addressLine1 = $_POST['addressLine1'];
    $addressLine2 = $_POST['addressLine2'];
    $city = $_POST['city'];
    $telephone = $_POST['telephone'];

    // validation for empty fields, password match, and mobile
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($mobile) || empty($firstName) || empty($lastName) || empty($addressLine1) || empty($city) || empty($telephone)) {
        // if any field is empty
        $error = "All fields are required!";
    } elseif ($password !== $confirmPassword) {
        // if password and confirm password don't match
        $error = "Passwords do not match!";
    } elseif (!is_numeric($mobile) || strlen($mobile) != 10) {
        // if mobile number is not numeric or doesn't have 10 digits
        $error = "Invalid mobile number!";
    } else {
        // if validation passes, insert user data into the users table
        $query = "INSERT INTO Users (username, password, mobile, firstname, lastname, addressline1, addressline2, city, telephone) 
                  VALUES ('$username', '$password', '$mobile', '$firstName', '$lastName', '$addressLine1', '$addressLine2', '$city', '$telephone')";       
        // execute the SQL query to insert the new user
        if ($conn->query($query)) {
            // if successful, show success
            $success = "Registration successful! You can now log in.";
        } else {
            // if username already exists, show error
            $error = "Username already exists!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
<h1>James's Big Book Library!</h1>
</header>
<div class="container">
    <h2>Register</h2>
    <!-- display error message if exists -->
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>    
    <!-- display success message if registration successful -->
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
    
    <!-- registration form for user to sign up -->
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword" required>
        
        <label for="mobile">Mobile:</label>
        <input type="text" name="mobile" id="mobile" required>
        
        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" id="firstName" required>
        
        <label for="lastName">Last Name:</label>
        <input type="text" name="lastName" id="lastName" required>
        
        <label for="addressLine1">Address Line 1:</label>
        <input type="text" name="addressLine1" id="addressLine1" required>
		
        <label for="addressLine2">Address Line 2:</label>
        <input type="text" name="addressLine2" id="addressLine2">
		
        <label for="city">City:</label>
        <input type="text" name="city" id="city" required> 
		
        <label for="telephone">Telephone:</label>
        <input type="text" name="telephone" id="telephone" required>  
		
        <button type="submit">Register</button>
    </form>
    <p>Already have a library account? <a href="login.php">Login to your account here!</a></p>
</div>


</body>

</html>




