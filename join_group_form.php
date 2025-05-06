<?php
// Start the session to store potential messages (errors/success)
// Also needed to identify the logged-in user
session_start();

// --- IMPORTANT: Check if user is logged in using $_SESSION['id'] ---
if (!isset($_SESSION['id'])) { // <--- Changed from 'user_id' to 'id'
    // Redirect to login page if not logged in
    $_SESSION['error_message'] = "You must be logged in to join a group.";
    header('Location: login.php'); // Adjust 'login.php' to your actual login page
    exit;
}
// -----------------------------------------

include 'header.php';
?>

<div class="w-full max-w-sm mx-auto space-y-6 mt-8">

  <?php
  // Display error messages if they exist
  if (isset($_SESSION['error_message'])) {
      echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
      echo '<strong class="font-bold">Error!</strong>';
      echo '<span class="block sm:inline"> ' . htmlspecialchars($_SESSION['error_message']) . '</span>';
      echo '</div>';
      unset($_SESSION['error_message']); // Clear message after displaying
  }

  // Display success message if it exists
  if (isset($_SESSION['success_message'])) {
      echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">';
      echo '<strong class="font-bold">Success!</strong>';
      echo '<span class="block sm:inline"> ' . htmlspecialchars($_SESSION['success_message']) . '</span>';
      echo '</div>';
      unset($_SESSION['success_message']); // Clear message after displaying
  }
  ?>

  <!-- Add the form tag with method and action -->
  <form action="handle_join_group.php" method="POST" class="space-y-4">
    <div>
      <label for="group_id" class="sr-only">Group ID</label>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/user.svg" alt="group id" class="w-6 h-6">
        <input id="group_id" name="group_id" type="text" placeholder="Group ID" required inputmode="numeric" pattern="[0-9]*"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
    </div>

    <div>
      <label for="password" class="sr-only">Password</label>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/lock.svg" alt="password" class="w-6 h-6">
        <input id="password" name="password" type="password" placeholder="Password" required
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
    </div>

    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-full transition duration-150 ease-in-out">
      Join group
    </button>
  </form>
</div>

<?php include 'footer.php'; ?>
