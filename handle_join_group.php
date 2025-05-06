<?php
session_start(); // Start the session to access user ID and store messages

// --- IMPORTANT: Check if user is logged in using $_SESSION['id'] ---
if (!isset($_SESSION['id'])) { // <--- Changed from 'user_id' to 'id'
    // Should not happen if form page checks, but good practice
    $_SESSION['error_message'] = "Authentication required.";
    header('Location: login.php'); // Adjust as needed
    exit;
}
// -----------------------------------------


// --- Database Connection (Replace with your actual connection details) ---
require_once 'db.php';
// -----------------------------------------------------------------------


// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Input Validation and Retrieval ---
    $group_id_input = trim($_POST['group_id'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user_id = $_SESSION['id']; // <--- Changed from 'user_id' to 'id' (Get logged-in user's ID)

    $errors = [];

    // Validate Group ID
    if (empty($group_id_input)) {
        $errors[] = "Group ID is required.";
    } elseif (!ctype_digit($group_id_input)) {
        $errors[] = "Invalid Group ID format.";
    } else {
        $group_id = (int)$group_id_input;
    }

    // Validate Password
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // --- Process if Initial Validation Passes ---
    if (empty($errors)) {
        // --- Fetch Group Details (ID and Password Hash) ---
        $sql_fetch_group = "SELECT id, group_name, password_hash FROM groups WHERE id = ?";
        $stmt_fetch = $conn->prepare($sql_fetch_group);

        if ($stmt_fetch === false) {
            // error_log("Prepare statement failed (fetch group): " . $conn->error);
            $_SESSION['error_message'] = "Database error (prepare fetch). Please try again later.";
            header('Location: join_group_form.php');
            exit;
        }

        $stmt_fetch->bind_param("i", $group_id);
        $stmt_fetch->execute();
        $result = $stmt_fetch->get_result();
        $group = $result->fetch_assoc();

        $stmt_fetch->close();

        // --- Verify Group Existence and Password ---
        if ($group) {
            if (password_verify($password, $group['password_hash'])) {
                // Password is correct!

                // --- Check if user is already a member ---
                $sql_check_member = "SELECT 1 FROM group_members WHERE user_id = ? AND group_id = ?";
                $stmt_check = $conn->prepare($sql_check_member);
                 if ($stmt_check === false) {
                    // error_log("Prepare statement failed (check member): " . $conn->error);
                    $_SESSION['error_message'] = "Database error (prepare check). Please try again later.";
                    header('Location: join_group_form.php');
                    exit;
                }
                // Use the $user_id variable which now holds $_SESSION['id']
                $stmt_check->bind_param("ii", $user_id, $group_id);
                $stmt_check->execute();
                $stmt_check->store_result();

                if ($stmt_check->num_rows > 0) {
                    $_SESSION['error_message'] = "You are already a member of group '" . htmlspecialchars($group['group_name']) . "'.";
                    $stmt_check->close();
                    header('Location: join_group_form.php');
                    exit;
                }
                $stmt_check->close();


                // --- Add user to the group_members table ---
                $sql_insert_member = "INSERT INTO group_members (user_id, group_id) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert_member);

                 if ($stmt_insert === false) {
                    // error_log("Prepare statement failed (insert member): " . $conn->error);
                    $_SESSION['error_message'] = "Database error (prepare insert). Please try again later.";
                    header('Location: join_group_form.php');
                    exit;
                }

                // Use the $user_id variable which now holds $_SESSION['id']
                $stmt_insert->bind_param("ii", $user_id, $group_id);

                if ($stmt_insert->execute()) {
                    $_SESSION['success_message'] = "Successfully joined group '" . htmlspecialchars($group['group_name']) . "'!";
                    header('Location: join_group_form.php');
                    exit;
                } else {
                    // error_log("Execute statement failed (insert member): " . $stmt_insert->error);
                    $_SESSION['error_message'] = "Could not join group due to a database error.";
                    header('Location: join_group_form.php');
                    exit;
                }
                $stmt_insert->close();

            } else {
                $errors[] = "Incorrect password for the specified Group ID.";
            }
        } else {
            $errors[] = "Group with the specified ID does not exist.";
        }
    }

    // --- Handle Errors (Validation or Logic) ---
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header('Location: join_group_form.php');
        exit;
    }

} else {
    // Not a POST request
    $_SESSION['error_message'] = "Invalid request method.";
    header('Location: join_group_form.php');
    exit;
}

$conn->close();
?>
