<?php
include 'header.php';
include 'db.php';

$user_id = $_SESSION['id'];

// Pridobimo grupe, v katere je uporabnik vpisan
$sql_groups = "
    SELECT g.id, g.group_name 
    FROM groups g
    JOIN group_members gm ON g.id = gm.group_id
    WHERE gm.user_id = '$user_id'
";
$result_groups = mysqli_query($conn, $sql_groups);
?>

<!-- User Info -->
<div class="space-y-4 mt-6">
  <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
    <img src="assets/images/user.svg" alt="user" class="w-6 h-6">
    <p class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500">
      <?= htmlspecialchars($_SESSION['username']); ?>
    </p>
  </div>
  <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
    <img src="assets/images/mail.svg" alt="mail" class="w-6 h-6">
    <p class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500">
      <?= htmlspecialchars($row['mail']); ?>
    </p>
  </div>
</div>

<!-- Groups Section -->
<section class="mt-8">
  <h2 class="text-lg font-medium mb-3">Groups</h2>
  
  <!-- Join/Create buttons -->
  <div class="space-y-2 mb-4">
    <a href="group-join-page.php">
      <button class="w-full flex items-center justify-center bg-white border border-gray-200 rounded-lg px-4 py-3 hover:bg-gray-50 mb-3">
        <img src="assets/images/external-link.svg" alt="join" class="w-5 h-5">
        <span class="ml-2">Join group</span>
      </button>
    </a>
    <a href="group-add-page.php">
      <button class="w-full flex items-center justify-center bg-white border border-gray-200 rounded-lg px-4 py-3 hover:bg-gray-50 mb-3">
        <img src="assets/images/plus.svg" alt="create" class="w-5 h-5">
        <span class="ml-2">Create group</span>
      </button>
    </a>
  </div>

  <!-- Group list -->
  <div class="space-y-2">
    <?php if ($result_groups && mysqli_num_rows($result_groups) > 0): ?>
      <?php while ($group = mysqli_fetch_assoc($result_groups)): ?>
        <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3 mb-3">
          <div class="flex items-center">
            <img src="assets/images/gridblack.svg" alt="group" class="w-5 h-5">
            <span class="ml-2"><?= htmlspecialchars($group['group_name']); ?></span>
          </div>
          <img src="assets/images/square.svg" alt="delete" class="w-5 h-5 text-red-500">
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-gray-500 text-sm">You are not a member of any groups yet.</p>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>
