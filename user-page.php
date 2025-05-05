<?php include 'header.php'; ?>

  <!-- User Info -->
  <div class="space-y-4 mt-6">
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

  <!-- Groups Section -->
  <section class="mt-8">
    <h2 class="text-lg font-medium mb-3">Groups</h2>
    <div class="space-y-2">
      <button class="w-full flex items-center justify-center bg-white border border-gray-200 rounded-lg px-4 py-3 hover:bg-gray-50">
        <img src="assets/images/external-link.svg" alt="join" class="w-5 h-5">
        <span class="ml-2">Join group</span>
      </button>
      <button class="w-full flex items-center justify-center bg-white border border-gray-200 rounded-lg px-4 py-3 hover:bg-gray-50">
        <img src="assets/images/plus.svg" alt="create" class="w-5 h-5">
        <span class="ml-2">Create group</span>
      </button>
      <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3">
        <div class="flex items-center">
          <img src="assets/images/gridblack.svg" alt="" class="w-5 h-5">
          <span class="ml-2">Group 1</span>
        </div>
        <img src="assets/images/square.svg" alt="delete" class="w-5 h-5 text-red-500">
      </div>
      <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3">
        <div class="flex items-center">
          <img src="assets/images/gridblack.svg" alt="" class="w-5 h-5">
          <span class="ml-2">Group 2</span>
        </div>
        <img src="assets/images/square.svg" alt="delete" class="w-5 h-5 text-red-500">
      </div>
    </div>
  </section>

  <!-- Pins Section -->
  <section class="mt-8">
    <h2 class="text-lg font-medium mb-3">List of pins</h2>
    <div class="space-y-2">
      <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3">
        <div class="flex items-center">
          <img src="assets/images/map-pin.svg" alt="pin" class="w-5 h-5">
          <span class="ml-2">Location name</span>
        </div>
        <img src="assets/images/square.svg" alt="delete" class="w-5 h-5 text-red-500">
      </div>
      <!-- ... više pinova po potrebi ... -->
    </div>
  </section>

<?php include 'footer.php'; ?>
