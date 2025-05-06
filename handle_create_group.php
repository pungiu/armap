<?php
session_start(); // Start the session to store messages

require_once 'db.php';
// -----------------------------------------------------------------------


// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Input Validation and Sanitization ---
    $groupname = trim($_POST['groupname'] ?? ''); // Use null coalescing operator
    $password = trim($_POST['password'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $errors = [];

    // Basic validation (add more complex rules as needed)
    if (empty($groupname)) {
        $errors[] = "Group name is required.";
    } elseif (strlen($groupname) > 100) { // Example length limit
        $errors[] = "Group name cannot exceed 100 characters.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) { // Example minimum length
         $errors[] = "Password must be at least 6 characters long.";
    }
    // Add more password complexity rules if desired (uppercase, number, symbol etc.)

    if (strlen($description) > 500) { // Example length limit
        $errors[] = "Description cannot exceed 500 characters.";
    }

    // Sanitize description for storage (prevents basic XSS if displayed later without escaping)
    $sanitized_description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $sanitized_groupname = htmlspecialchars($groupname, ENT_QUOTES, 'UTF-8');


    // --- Process Data if No Errors ---
    if (empty($errors)) {
        // **IMPORTANT: Hash the password before storing!**
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($hashed_password === false) {
             // Handle password hashing failure
             // error_log("Password hashing failed for group: " . $sanitized_groupname);
             $_SESSION['error_message'] = "Could not process password. Please try again.";
             header('Location: create_group_form.php');
             exit;
        }

        // --- Database Insertion (Using Prepared Statements) ---
        // Assume you have a table named 'groups' with columns:
        // id (INT, PK, AI), group_name (VARCHAR), password_hash (VARCHAR), description (TEXT), created_at (TIMESTAMP)

        $sql = "INSERT INTO groups (group_name, password_hash, description, created_at) VALUES (?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Handle prepare statement error
            // error_log("Prepare statement failed: " . $conn->error);
            $_SESSION['error_message'] = "Database error (prepare). Please try again later.";
            header('Location: create_group_form.php');
            exit;
        }

        // Bind parameters (s = string)
        // Use the sanitized groupname and description here
        $stmt->bind_param("sss", $sanitized_groupname, $hashed_password, $sanitized_description);

        // Execute the statement
        if ($stmt->execute()) {
            // Success! Set success message and redirect
            $_SESSION['success_message'] = "Group '" . $sanitized_groupname . "' created successfully!";
            header('Location: create_group_form.php'); // Redirect back to form (or a success page)
            exit;
        } else {
            // Handle execution error (e.g., duplicate group name if you have a UNIQUE constraint)
            // error_log("Execute statement failed: " . $stmt->error);
             if ($conn->errno == 1062) { // Error code for duplicate entry
                 $_SESSION['error_message'] = "Group name '" . $sanitized_groupname . "' already exists.";
             } else {
                 $_SESSION['error_message'] = "Could not create group. Database error.";
             }
            header('Location: create_group_form.php');
            exit;
        }

        $stmt->close();
        // -------------------------------------------------------

    } else {
        // --- Store Errors and Redirect Back ---
        $_SESSION['error_message'] = implode("<br>", $errors); // Combine errors
        // Optional: Store old input to re-fill the form
        // $_SESSION['old_input'] = ['groupname' => $groupname, 'description' => $description];
        header('Location: create_group_form.php');
        exit;
    }

} else {
    // Not a POST request, redirect to the form or show an error
    $_SESSION['error_message'] = "Invalid request method.";
    header('Location: create_group_form.php');
    exit;
}

$conn->close(); // Close the database connection
?>
