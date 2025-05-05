<?php include 'header.php'; ?>

  <div class="w-full max-w-sm mx-auto space-y-6 mt-8">
    <div class="space-y-4">
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/user.svg" alt="model" class="w-6 h-6">
        <input type="text" placeholder="Modelname"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
      <div class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3">
        <img src="assets/images/mail.svg" alt="desc" class="w-6 h-6">
        <input type="text" placeholder="Description"
               class="ml-3 flex-1 bg-transparent focus:outline-none placeholder-gray-500"/>
      </div>
      <label class="flex items-center bg-white border border-gray-200 rounded-lg px-4 py-3 cursor-pointer">
        <img src="assets/images/user.svg" alt="file" class="w-6 h-6">
        <span class="ml-3 flex-1 text-gray-500">Choose .glb file</span>
        <input type="file" accept=".glb" class="hidden"/>
      </label>
    </div>

    <button class="w-full bg-red-600 text-white py-3 rounded-full">
      Add model
    </button>
  </div>

<?php include 'footer.php'; ?>
