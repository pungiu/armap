<?php
include 'header.php';
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        empty($_POST) &&
        empty($_FILES) &&
        isset($_SERVER['CONTENT_LENGTH']) &&
        $_SERVER['CONTENT_LENGTH'] > 0
    ) {
        $post_max_size_setting = ini_get('post_max_size');
        $message =
            "The uploaded data might have exceeded the server's post limit ({$post_max_size_setting}). Please try a smaller file.";
        $message_type = 'error';
    } else {
        $model_name = isset($_POST['model_name'])
            ? trim($_POST['model_name'])
            : '';
        $latitude = filter_input(
            INPUT_POST,
            'latitude',
            FILTER_VALIDATE_FLOAT
        );
        $longitude = filter_input(
            INPUT_POST,
            'longitude',
            FILTER_VALIDATE_FLOAT
        );

        $target_altitude_input = $_POST['target_altitude'] ?? '0.0';
        $target_altitude = is_numeric($target_altitude_input)
            ? (float) $target_altitude_input
            : 0.0;

        $glb_file_info = $_FILES['glb_file'] ?? null;
        $upload_dir = 'assets/';
        $model_url_to_db = '';

        if (empty($model_name)) {
            $message = 'Model name is required.';
            $message_type = 'error';
        } elseif (
            $latitude === false ||
            $longitude === false ||
            $latitude === null ||
            $longitude === null
        ) {
            $message =
                'Invalid or missing latitude or longitude. Please select a location on the map.';
            $message_type = 'error';
        } elseif (
            !$glb_file_info ||
            $glb_file_info['error'] !== UPLOAD_ERR_OK
        ) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE => 'File too large (server limit).',
                UPLOAD_ERR_FORM_SIZE => 'File too large (form limit).',
                UPLOAD_ERR_PARTIAL => 'File upload incomplete.',
                UPLOAD_ERR_NO_FILE => 'No file selected for upload.',
                UPLOAD_ERR_NO_TMP_DIR =>
                    'Server configuration error (no tmp dir).',
                UPLOAD_ERR_CANT_WRITE => 'Server error (cannot write file).',
                UPLOAD_ERR_EXTENSION =>
                    'Server error (PHP extension stopped upload).',
            ];
            $error_code = $glb_file_info['error'] ?? UPLOAD_ERR_NO_FILE;
            $message =
                $upload_errors[$error_code] ?? 'Unknown file upload error.';
            $message_type = 'error';
        } else {
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0775, true) && !is_dir($upload_dir)) {
                    $message =
                        'Failed to create upload directory: ' .
                        htmlspecialchars($upload_dir);
                    $message_type = 'error';
                }
            }

            if (empty($message) && is_dir($upload_dir)) {
                $original_file_name = $glb_file_info['name'];
                $file_tmp_name = $glb_file_info['tmp_name'];
                $file_ext = strtolower(
                    pathinfo($original_file_name, PATHINFO_EXTENSION)
                );

                if ($file_ext !== 'glb') {
                    $message = 'Only .glb files are allowed.';
                    $message_type = 'error';
                } else {
                    $safe_base_name = preg_replace(
                        "/[^a-zA-Z0-9_.-]/",
                        "_",
                        basename($original_file_name)
                    );
                    $target_file_path_on_server =
                        rtrim($upload_dir, '/') . '/' . $safe_base_name;
                    $model_url_to_db = $target_file_path_on_server;

                    if (
                        move_uploaded_file(
                            $file_tmp_name,
                            $target_file_path_on_server
                        )
                    ) {
                        try {
                            $db_host = 'localhost';
                            $db_name = 'ar_location_db';
                            $db_user = 'ar_user';
                            $db_pass =
                                'FfMzqhGgvWsaQXENL5jDm.,12!.,@,.5kPe637CS92cJAVBrKTfMwUYbdF4n8xptzaV9D';

                            $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
                            $options = [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE =>
                                    PDO::FETCH_ASSOC,
                                PDO::ATTR_EMULATE_PREPARES => false,
                            ];
                            $pdo = new PDO($dsn, $db_user, $db_pass, $options);

                            $sql = "INSERT INTO models (name, latitude, longitude, model_url, target_altitude, created_by_user_id)
                                    VALUES (:name, :latitude, :longitude, :model_url, :target_altitude, :user_id)";
                            $stmt = $pdo->prepare($sql);

                            $userid = $_SESSION['id'];

                            $stmt->bindParam(':name', $model_name);
                            $stmt->bindParam(':latitude', $latitude);
                            $stmt->bindParam(':longitude', $longitude);
                            $stmt->bindParam(':model_url', $model_url_to_db);
                            $stmt->bindParam(
                                ':target_altitude',
                                $target_altitude
                            );
                            $stmt->bindParam(':user_id', $userid);

                            if ($stmt->execute()) {
                                $message = 'Model uploaded successfully!';
                                $message_type = 'success';
                            } else {
                                $message =
                                    'Failed to insert model data into database. Check server logs.';
                                $message_type = 'error';
                            }
                        } catch (PDOException $e) {
                            error_log("Database Error: " . $e->getMessage());
                            $message =
                                'A database error occurred. Please try again later. (Check PHP error log for details)';
                            $message_type = 'error';
                        }
                    } else {
                        $message =
                            'Could not move uploaded file. Check permissions for ' .
                            htmlspecialchars($upload_dir) .
                            ' and ensure the path is writable.';
                        $message_type = 'error';
                    }
                }
            } elseif (empty($message) && !is_dir($upload_dir)) {
                $message =
                    'Upload directory does not exist and could not be created.';
                $message_type = 'error';
            }
        }
    }
}
?>

<form class="w-full max-w-md mx-auto space-y-6 mt-8" method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <h2 class="text-2xl font-bold text-center">Upload 3D Model</h2>

    <?php if ($message): ?>
        <div class="px-4 py-3 rounded-lg border text-sm
            <?= $message_type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div>
        <label class="block mb-2 font-medium text-gray-700">Choose Location</label>
        <div id="map" class="w-full h-64 rounded border"></div>
    </div>

    <div class="flex space-x-2">
        <input type="text" name="latitude" id="latitude" readonly
               class="w-1/2 border rounded px-3 py-2 text-sm bg-gray-100 text-gray-700" placeholder="Latitude">
        <input type="text" name="longitude" id="longitude" readonly
               class="w-1/2 border rounded px-3 py-2 text-sm bg-gray-100 text-gray-700" placeholder="Longitude">
    </div>

    <input type="number" step="any" name="target_altitude" value="0.0"
           class="w-full border rounded px-3 py-2 text-sm bg-white text-black" placeholder="Target Altitude (e.g., 0.0)">

    <input type="file" name="glb_file" accept=".glb" required
           class="w-full border rounded px-3 py-2 text-sm bg-white text-black file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">

    <input type="text" name="model_name" required
           class="w-full border rounded px-3 py-2 text-sm bg-white text-black" placeholder="Model Name">

    <button type="submit"
            class="w-full flex items-center justify-center bg-red-600 text-white py-3 rounded-full hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition duration-150">
        Upload Model
    </button>
</form>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('map').setView([46.0569, 14.5058], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker;
        const latInput = document.getElementById('latitude');
        const lonInput = document.getElementById('longitude');

        map.on('click', function (e) {
            const lat = parseFloat(e.latlng.lat.toFixed(6));
            const lon = parseFloat(e.latlng.lng.toFixed(6));

            if (latInput) latInput.value = lat;
            if (lonInput) lonInput.value = lon;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { draggable: true }).addTo(map);
                marker.on('dragend', function (event) {
                    const pos = event.target.getLatLng();
                    if (latInput) latInput.value = pos.lat.toFixed(6);
                    if (lonInput) lonInput.value = pos.lng.toFixed(6);
                });
            }
        });

        const initialLat = parseFloat(latInput.value);
        const initialLon = parseFloat(lonInput.value);

        if (!isNaN(initialLat) && !isNaN(initialLon)) {
            const initialLatLng = L.latLng(initialLat, initialLon);
            marker = L.marker(initialLatLng, { draggable: true }).addTo(map);
            marker.on('dragend', function (event) {
                const pos = event.target.getLatLng();
                if (latInput) latInput.value = pos.lat.toFixed(6);
                if (lonInput) lonInput.value = pos.lng.toFixed(6);
            });
            map.setView(initialLatLng, 13);
        }
    });
</script>

<?php include 'footer.php'; ?>
