<?php
session_start();

include('connection.php');


if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Simple validation
    if (empty($username) || empty($password)) {
        header("Location: index.php?error=User Name Password Required.");
            exit();
    } else {
        // Validate against SQL injection
        $username = mysqli_real_escape_string($con, $username);
        $password = mysqli_real_escape_string($con, $password);

        // Check the user credentials
        $sql = "SELECT * FROM invoiceemployees WHERE username='$username' AND password='$password'";
        $result = $con->query($sql);

        if ($result->num_rows == 1) {
            $_SESSION['username'] = $username;
            header("Location: what-you-want.php");
            exit();
        } else {
            header("Location: index.php?error=Invalid username or password.");
            exit();
        }
    }
}

$con->close();
