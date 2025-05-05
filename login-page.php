<?php include 'header.php'; ?>

  <div class="w-full max-w-sm mx-auto space-y-6 mt-8">
    <!-- Inputs -->
    <div class="space-y-4">
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/user.svg" alt="user" class="w-6 h-6">
        <input type="text" placeholder="Username"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/mail.svg" alt="mail" class="w-6 h-6">
        <input type="email" placeholder="Mail"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex space-x-4">
      <button class="flex-1 flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
        <img src="assets/images/plus-square.svg" alt="" class="w-5 h-5 mr-2">
        Sign up
      </button>
      <button class="flex-1 flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
        <img src="assets/images/check.svg" alt="" class="w-5 h-5 mr-2">
        Login
      </button>
    </div>
  </div>

<?php include 'footer.php'; ?>
