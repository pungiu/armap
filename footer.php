<?php
// footer.php
?>
  </main>

  <!-- Bottom Navigation -->
  <nav class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 flex justify-around py-3">
    <!-- Add Button -->
    <a href="<?php echo isset($_SESSION['id']) ? 'add_models.php' : 'login-page.php'; ?>">
      <img src="assets/images/plus-circle.svg" alt="add" class="w-6 h-6">
    </a>

    <!-- Map Button -->
    <a href="index.php">
      <img src="assets/images/map.svg" alt="map" class="w-6 h-6">
    </a>

    <!-- User/Profile Button -->
    <a href="<?php echo isset($_SESSION['id']) ? 'user-page.php' : 'login-page.php'; ?>">
      <img src="assets/images/user.svg" alt="profile" class="w-6 h-6">
    </a>
  </nav>
</body>
</html>
