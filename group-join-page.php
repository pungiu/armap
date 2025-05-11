<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id'])) {
    header("Location: login-page.php");
    exit();
}

$user_id = $_SESSION['id'];
$error_message = '';
$success_message = '';
$group_id_value = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_id_input = trim($_POST['group_id'] ?? '');
    $group_password_input = $_POST['password'] ?? '';
    $group_id_value = $group_id_input;

    if (empty($group_id_input)) {
        $error_message = "Group ID is required.";
    } elseif (!is_numeric($group_id_input)) {
        $error_message = "Group ID must be a number.";
    } elseif (empty($group_password_input)) {
        $error_message = "Password is required.";
    } else {
        $group_id = intval($group_id_input);
        $stmt = mysqli_prepare($conn, "SELECT id, password_hash FROM groups WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $group_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $group = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$group) {
            $error_message = "Group not found.";
        } elseif (!password_verify($group_password_input, $group['password_hash'])) {
            $error_message = "Incorrect password.";
        } else {
            $check = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM group_members WHERE user_id = ? AND group_id = ?");
            mysqli_stmt_bind_param($check, "ii", $user_id, $group_id);
            mysqli_stmt_execute($check);
            $res = mysqli_stmt_get_result($check);
            $count = mysqli_fetch_assoc($res)['count'];
            mysqli_stmt_close($check);

            if ($count > 0) {
                $error_message = "You are already a member of this group.";
            } else {
                $insert = mysqli_prepare($conn, "INSERT INTO group_members (user_id, group_id) VALUES (?, ?)");
                mysqli_stmt_bind_param($insert, "ii", $user_id, $group_id);
                if (mysqli_stmt_execute($insert)) {
                    $success_message = "Successfully joined the group!";
                    $group_id_value = '';
                } else {
                    $error_message = "Error: " . mysqli_stmt_error($insert);
                }
                mysqli_stmt_close($insert);
            }
        }
    }
}

include 'header.php';
?>

<!-- Join Group Form Styled like Login -->
<form class="w-full max-w-sm mx-auto space-y-6 mt-8" method="POST">
    <!-- Messages -->
    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Success:</strong>
            <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
        </div>
    <?php endif; ?>

    <!-- Inputs -->
    <div class="space-y-4">
        <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
            <img src="assets/images/user.svg" alt="group" class="w-6 h-6">
            <input name="group_id" type="text" placeholder="Group ID"
                   value="<?= htmlspecialchars($group_id_value); ?>"
                   class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500" required>
        </div>
        <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
            <img src="assets/images/lock.svg" alt="password" class="w-6 h-6">
            <input name="password" type="password" placeholder="Password"
                   class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500" required>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit"
            class="w-full flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
        <img src="assets/images/check.svg" alt="" class="w-5 h-5 mr-2">
        Join Group
    </button>
</form>

<?php include 'footer.php'; ?>
