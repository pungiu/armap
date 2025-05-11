<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'ar_user');
define('DB_PASS', 'FfMzqhGgvWsaQXENL5jDm.,12!.,@,.5kPe637CS92cJAVBrKTfMwUYbdF4n8xptzaV9D');
define('DB_NAME', 'ar_location_db');

header('Content-Type: application/json');

$default_radius_km = 5.0;
$user_lat = isset($_GET['lat']) ? filter_var($_GET['lat'], FILTER_VALIDATE_FLOAT) : null;
$user_lon = isset($_GET['lon']) ? filter_var($_GET['lon'], FILTER_VALIDATE_FLOAT) : null;
$radius_km = isset($_GET['radius']) ? filter_var($_GET['radius'], FILTER_VALIDATE_FLOAT) : $default_radius_km;

$response = ['success' => false, 'message' => '', 'models' => []];

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    $response['message'] = 'User not authenticated.';
    echo json_encode($response);
    exit;
}
$loggedInUserId = (int)$_SESSION['id'];

if ($user_lat === null || $user_lat === false || $user_lon === null || $user_lon === false) {
    http_response_code(400);
    $response['message'] = 'Missing or invalid latitude/longitude parameters.';
    echo json_encode($response);
    exit;
}
if ($radius_km === false || $radius_km <= 0) {
    $radius_km = $default_radius_km;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error);
    http_response_code(500);
    $response['message'] = 'Database connection failed.';
} else {
    $sql = "
        SELECT
            m.id, m.name, m.latitude, m.longitude, m.model_url,
            m.base_scale, m.min_scale, m.max_scale, m.reference_distance, m.visibility_threshold,
            m.target_altitude,
            (
                6371 * acos(
                    cos(radians(?)) * cos(radians(m.latitude)) *
                    cos(radians(m.longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(m.latitude))
                )
            ) AS distance_km
        FROM models m
        WHERE
            m.created_by_user_id IS NOT NULL AND
            (
                m.created_by_user_id = ?
                OR EXISTS (
                    SELECT 1
                    FROM group_members gm1
                    JOIN group_members gm2 ON gm1.group_id = gm2.group_id
                    WHERE gm1.user_id = ?
                      AND gm2.user_id = m.created_by_user_id
                )
            )
        HAVING distance_km < ?
        ORDER BY distance_km ASC
        LIMIT 50
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("dddiid", $user_lat, $user_lon, $user_lat, $loggedInUserId, $loggedInUserId, $radius_km);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $models_data = [];
            while ($row = $result->fetch_assoc()) {
                $row['latitude'] = (float)$row['latitude'];
                $row['longitude'] = (float)$row['longitude'];
                $row['base_scale'] = (float)$row['base_scale'];
                $row['min_scale'] = (float)$row['min_scale'];
                $row['max_scale'] = (float)$row['max_scale'];
                $row['reference_distance'] = (float)$row['reference_distance'];
                $row['visibility_threshold'] = (float)$row['visibility_threshold'];
                $row['target_altitude'] = (float)$row['target_altitude'];
                $row['distance_km'] = (float)$row['distance_km'];
                $models_data[] = $row;
            }
            $response['success'] = true;
            $response['models'] = $models_data;
            $response['query_params'] = ['lat' => $user_lat, 'lon' => $user_lon, 'radius_km' => $radius_km, 'user_id' => $loggedInUserId];
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

echo json_encode($response);
?>
