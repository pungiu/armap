<?php
include 'header.php';
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<?php if ($success_message): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Success:</strong>
      <span class="block sm:inline"><?= htmlspecialchars($success_message); ?></span>
  </div>
<?php endif; ?>

<?php if ($error_message): ?>
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
      <strong class="font-bold">Error:</strong>
      <span class="block sm:inline"><?= htmlspecialchars($error_message); ?></span>
  </div>
<?php endif; ?>
  <form class="w-full max-w-sm mx-auto space-y-6 mt-8" action="login.php" method="post">
    <!-- Inputs -->
    <div class="space-y-4">
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/user.svg" alt="user" class="w-6 h-6">
        <input name="username" type="text" placeholder="Username"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/lock.svg" alt="mail" class="w-6 h-6">
        <input name="geslo" type="password" placeholder="Geslo"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
    </div>

    <!-- Buttons -->
<div class="flex space-x-4">
  <!-- Sign up redirects to register page -->
  <button type="button" onclick="window.location.href='register-page.php'"
          class="flex-1 flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
    <img src="assets/images/plus-square.svg" alt="" class="w-5 h-5 mr-2">
    Sign up
  </button>

  <!-- Login submits form -->
  <button type="submit"
          class="flex-1 flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
    <img src="assets/images/check.svg" alt="" class="w-5 h-5 mr-2">
    Login
  </button>
</div>
  </form>

<?php include 'footer.php'; ?>
