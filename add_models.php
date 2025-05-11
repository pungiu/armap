<?php
include 'header.php';
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'db.php';

    $model_name = trim($_POST['model_name'] ?? '');
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);
    $target_altitude_input = $_POST['target_altitude'] ?? '0.0';
    $target_altitude = is_numeric($target_altitude_input) ? (float)$target_altitude_input : 0.0;

    $glb_file_info = $_FILES['glb_file'] ?? null;
    $upload_dir = 'assets/models/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0775, true) && !is_dir($upload_dir)) {
            $message = 'Failed to create upload directory.';
            $message_type = 'error';
        }
    }
    $model_url = '';

    if (empty($message)) {
        if (empty($model_name)) {
            $message = 'Model name is required.';
            $message_type = 'error';
        } elseif ($latitude === false || $longitude === false || $latitude === null || $longitude === null) {
            $message = 'Invalid latitude or longitude.';
            $message_type = 'error';
        } elseif (!$glb_file_info || $glb_file_info['error'] !== UPLOAD_ERR_OK) {
            $message = 'File upload failed.';
            $message_type = 'error';
        } else {
            $file_name = $glb_file_info['name'];
            $file_tmp_name = $glb_file_info['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($file_ext !== 'glb') {
                $message = 'Only .glb files are allowed.';
                $message_type = 'error';
            } else {
                $unique_file_name = uniqid('model_', true) . '_' . preg_replace("/[^a-zA-Z0-9_.-]/", "_", $file_name);
                $target_file_path = $upload_dir . $unique_file_name;

                if (move_uploaded_file($file_tmp_name, $target_file_path)) {
                    $model_url = $target_file_path;
                    try {
                        $pdo = new PDO("mysql:host=localhost;dbname=ar_location_db", 'ar_user2', 'FfMzqhGgvWsaQXENL5jDm.,12!.,@,.5kPe637CS92cJAVBrKTfMwUYbdF4n8xptzaV9D');
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $stmt = $pdo->prepare("INSERT INTO models (name, latitude, longitude, model_url, target_altitude) VALUES (?, ?, ?, ?, ?)");
                        if ($stmt->execute([$model_name, $latitude, $longitude, $model_url, $target_altitude])) {
                            $message = 'Model uploaded successfully!';
                            $message_type = 'success';
                        } else {
                            $message = 'Failed to insert into database.';
                            $message_type = 'error';
                        }
                    } catch (PDOException $e) {
                        $message = 'Database error.';
                        $message_type = 'error';
                    }
                } else {
                    $message = 'Could not move uploaded file.';
                    $message_type = 'error';
                }
            }
        }
    }
}
?>

<!-- Main Form (Tailwind Styled) -->
<form class="w-full max-w-md mx-auto space-y-6 mt-8" method="POST" enctype="multipart/form-data">
    <h2 class="text-2xl font-bold text-center">Upload 3D Model</h2>

    <?php if ($message): ?>
        <div class="px-4 py-3 rounded-lg border text-sm
            <?= $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Map -->
    <div>
        <label class="block mb-2 font-medium text-gray-700">Choose Location</label>
        <div id="map" class="w-full h-64 rounded border"></div>
    </div>

    <!-- Coordinates -->
<div class="flex space-x-2">
    <input type="text" name="latitude" id="latitude" readonly
           class="w-1/2 border rounded px-3 py-2 text-sm bg-white text-black" placeholder="Latitude">
    <input type="text" name="longitude" id="longitude" readonly
           class="w-1/2 border rounded px-3 py-2 text-sm bg-white text-black" placeholder="Longitude">
</div>

<!-- Altitude -->
<input type="number" step="any" name="target_altitude"
       class="w-full border rounded px-3 py-2 text-sm bg-white text-black" placeholder="Target Altitude (e.g., 0.0)">

<!-- File -->
<input type="file" name="glb_file" accept=".glb" required
       class="w-full border rounded px-3 py-2 text-sm bg-white text-black file:bg-gray-100 file:border-0 file:px-3 file:py-2 file:rounded">

<!-- Model Name -->
<input type="text" name="model_name" required
       class="w-full border rounded px-3 py-2 text-sm bg-white text-black" placeholder="Model Name">

    <!-- Submit -->
    <button type="submit"
            class="w-full flex items-center justify-center bg-red-600 text-white py-3 rounded-full">
        Upload Model
    </button>
</form>

<!-- Leaflet Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('map').setView([46.0569, 14.5058], 6); // default: Slovenia

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        let marker;
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');

        map.on('click', function (e) {
            const lat = parseFloat(e.latlng.lat.toFixed(6));
            const lon = parseFloat(e.latlng.lng.toFixed(6));
            latInput.value = lat;
            lonInput.value = lon;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { draggable: true }).addTo(map);
                marker.on('dragend', function (event) {
                    const pos = event.target.getLatLng();
                    latInput.value = pos.lat.toFixed(6);
                    lonInput.value = pos.lng.toFixed(6);
                });
            }
        });
    });
</script>

<?php include 'footer.php'; ?>
