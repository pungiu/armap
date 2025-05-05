<?php include 'header.php'; ?>

  <div class="w-full max-w-sm mx-auto space-y-6 mt-8">
    <div class="space-y-4">
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/user.svg" alt="group" class="w-6 h-6">
        <input type="text" placeholder="Groupname"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/lock.svg" alt="password" class="w-6 h-6">
        <input type="password" placeholder="Password"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/mail.svg" alt="desc" class="w-6 h-6">
        <input type="text" placeholder="Description"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
    </div>

    <button class="w-full bg-red-600 text-white py-3 rounded-full">
      Create group
    </button>
  </div>

<?php include 'footer.php'; ?>
