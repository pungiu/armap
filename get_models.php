<?php

// --- Database Configuration ---
define('DB_HOST', 'localhost');
define('DB_USER', 'ar_user'); // The user you created
define('DB_PASS', 'FfMzqhGgvWsaQXENL5jDm.,12!.,@,.5kPe637CS92cJAVBrKTfMwUYbdF4n8xptzaV9D'); // <<< SET YOUR ACTUAL PASSWORD HERE
define('DB_NAME', 'ar_location_db'); // The database you created
// -----------------------------

header('Content-Type: application/json');

// --- Parameters ---
$default_radius_km = 5.0;
$user_lat = isset($_GET['lat']) ? filter_var($_GET['lat'], FILTER_VALIDATE_FLOAT) : null;
$user_lon = isset($_GET['lon']) ? filter_var($_GET['lon'], FILTER_VALIDATE_FLOAT) : null;
$radius_km = isset($_GET['radius']) ? filter_var($_GET['radius'], FILTER_VALIDATE_FLOAT) : $default_radius_km;

// --- Response Setup ---
$response = ['success' => false, 'message' => '', 'models' => []];

if ($user_lat === null || $user_lat === false || $user_lon === null || $user_lon === false) {
    http_response_code(400); // Bad Request
    $response['message'] = 'Missing or invalid latitude/longitude parameters.';
    echo json_encode($response);
    exit;
}
if ($radius_km === false || $radius_km <= 0) {
    $radius_km = $default_radius_km;
}

// --- Database Interaction ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error);
    http_response_code(500);
    $response['message'] = 'Database connection failed.';
} else {
    // --- SQL Query (includes target_altitude) ---
    $sql = "
        SELECT
            id, name, latitude, longitude, model_url,
            base_scale, min_scale, max_scale, reference_distance, visibility_threshold,
            target_altitude, -- Select the altitude field
            (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance_km
        FROM models
        HAVING distance_km < ?
        ORDER BY distance_km ASC
        LIMIT 50 -- Limit results for performance
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind parameters (d = double/float)
        $stmt->bind_param("dddd", $user_lat, $user_lon, $user_lat, $radius_km);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $models_data = [];
            while ($row = $result->fetch_assoc()) {
                // Cast types for JSON consistency
                $row['latitude'] = (float)$row['latitude'];
                $row['longitude'] = (float)$row['longitude'];
                $row['base_scale'] = (float)$row['base_scale'];
                $row['min_scale'] = (float)$row['min_scale'];
                $row['max_scale'] = (float)$row['max_scale'];
                $row['reference_distance'] = (float)$row['reference_distance'];
                $row['visibility_threshold'] = (float)$row['visibility_threshold'];
                $row['target_altitude'] = (float)$row['target_altitude']; // Cast altitude
                $row['distance_km'] = (float)$row['distance_km'];
                $models_data[] = $row;
            }
            $response['success'] = true;
            $response['models'] = $models_data;
            $response['query_params'] = ['lat' => $user_lat, 'lon' => $user_lon, 'radius_km' => $radius_km];
            $result->free();
        } else {
            error_log("Database Execute Error: " . $stmt->error);
            http_response_code(500);
            $response['message'] = 'Failed to execute query.';
        }
        $stmt->close();
    } else {
        error_log("Database Prepare Error: " . $conn->error);
        http_response_code(500);
        $response['message'] = 'Failed to prepare query.';
    }
    $conn->close();
}

// Output the JSON
echo json_encode($response);
?>

