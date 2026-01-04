<?php
/**
 * Delete Movie API Endpoint (Admin Only)
 * POST /api/movies/delete_movie.php
 * 
 * Request Body:
 * {
 *   "movie_id": 1
 * }
 * 
 * Note: In a real application, you would verify the user's role here.
 * For this example, we'll assume role verification is done on the frontend.
 */

require_once '../../config/cors.php';
require_once '../../config/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate movie_id
if (!isset($input['movie_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'movie_id is required'
    ]);
    exit();
}

$movieId = filter_var($input['movie_id'], FILTER_VALIDATE_INT);

if ($movieId === false || $movieId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid movie_id'
    ]);
    exit();
}

// Get database connection
$pdo = getDatabaseConnection();

if (!$pdo) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit();
}

try {
    // Check if movie exists
    $stmt = $pdo->prepare("SELECT id FROM movies WHERE id = ?");
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch();

    if (!$movie) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Movie not found'
        ]);
        exit();
    }

    // Delete the movie
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$movieId]);

    echo json_encode([
        'success' => true,
        'message' => 'Movie deleted successfully'
    ]);

} catch (PDOException $e) {
    error_log("Delete movie error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete movie'
    ]);
}
?>

