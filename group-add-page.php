<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login-page.php");
    exit();
}

$error_message = '';
$success_message = '';
$group_name_value = '';
$description_value = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST['group_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $creator_id = $_SESSION['id'];

    $group_name_value = $group_name;
    $description_value = $description;

    if (empty($group_name)) {
        $error_message = "Group name is required.";
    } elseif (strlen($group_name) > 100) {
        $error_message = "Group name cannot exceed 100 characters.";
    } elseif (empty($password)) {
        $error_message = "Password is required.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif (strlen($description) > 500) {
        $error_message = "Description cannot exceed 500 characters.";
    } else {
        $sql_check = "SELECT id FROM groups WHERE group_name = ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);

        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "s", $group_name);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);

            if (mysqli_stmt_num_rows($stmt_check) > 0) {
                $error_message = "A group with this name already exists.";
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $sql_insert = "INSERT INTO groups (group_name, password_hash, description, created_by_user_id) VALUES (?, ?, ?, ?)";
                $stmt_insert = mysqli_prepare($conn, $sql_insert);

                if ($stmt_insert) {
                    mysqli_stmt_bind_param($stmt_insert, "sssi", $group_name, $password_hash, $description, $creator_id);

                    if (mysqli_stmt_execute($stmt_insert)) {
                        $success_message = "Group '" . htmlspecialchars($group_name) . "' created successfully!";
                        $group_name_value = '';
                        $description_value = '';
                    } else {
                        $error_message = "Failed to create group: " . mysqli_stmt_error($stmt_insert);
                    }
                    mysqli_stmt_close($stmt_insert);
                } else {
                    $error_message = "Insert prepare error: " . mysqli_error($conn);
                }
            }
            mysqli_stmt_close($stmt_check);
        } else {
            $error_message = "Check prepare error: " . mysqli_error($conn);
        }
    }
}

include 'header.php';
?>

<!-- Styled like login -->
<form class="w-full max-w-sm mx-auto space-y-6 mt-8" method="POST">
    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <strong class="font-bold">Success:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($success_message); ?></span>
        </div>
    <?php endif; ?>

    <!-- Inputs -->
    <div class="space-y-4">
        <!-- Group Name -->
        <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
            <img src="assets/images/user.svg" alt="group" class="w-6 h-6">
            <input name="group_name" type="text" placeholder="Group name"
                   value="<?= htmlspecialchars($group_name_value); ?>"
                   class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500" required>
        </div>

        <!-- Password -->
        <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
            <img src="assets/images/lock.svg" alt="password" class="w-6 h-6">
            <input name="password" type="password" placeholder="Password"
                   class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500" required>
        </div>

        <!-- Description -->
        <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
            <img src="assets/images/mail.svg" alt="description" class="w-6 h-6">
            <input name="description" type="text" placeholder="Description (optional)"
                   value="<?= htmlspecialchars($description_value); ?>"
                   class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500">
        </div>
    </div>

    <!-- Submit button -->
    <button type="submit"
            class="w-full flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
        <img src="assets/images/plus.svg" alt="create" class="w-5 h-5 mr-2">
        Create Group
    </button>
</form>

<?php include 'footer.php'; ?>
