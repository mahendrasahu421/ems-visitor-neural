<?php
// Include your database connection file or establish a connection here

include 'connection.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Assuming you have a database connection, replace 'your_connection' with your actual connection variable
    // Example: $conn = mysqli_connect("localhost", "username", "password", "database_name");

    $expense_categories_id = $_POST["expense_categories_id"];
    $categories = $_POST["categories"];
    $status = $_POST["status"];

    // Validate the data if needed

    // Perform the SQL query to insert data into the table
    $sql = "INSERT INTO expense_categories (expense_categories_id, categories, status) VALUES ('$expense_categories_id', '$categories', '$status')";

    // Assuming $conn is your database connection variable
    if (mysqli_query($con, $sql)) {
        header("Location: expense-categories-list.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }

    // Close the database connection if needed
    // mysqli_close($conn);
}
