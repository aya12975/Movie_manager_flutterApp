<?php
/**
 * Get Movies API Endpoint
 * GET /api/movies/get_movies.php
 * 
 * Response:
 * {
 *   "success": true,
 *   "movies": [
 *     {
 *       "id": 1,
 *       "title": "Movie Title",
 *       "description": "Movie description",
 *       "genre": "Action",
 *       "release_year": 2024,
 *       "rating": 8.5,
 *       "created_at": "2024-01-01 12:00:00"
 *     }
 *   ]
 * }
 */

require_once '../../config/cors.php';
require_once '../../config/database.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
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
    // Fetch all movies ordered by created_at DESC
    $stmt = $pdo->query("
        SELECT 
            id,
            title,
            description,
            genre,
            release_year,
            rating,
            created_at
        FROM movies
        ORDER BY created_at DESC
    ");
    
    $movies = $stmt->fetchAll();

    // Format the response
    echo json_encode([
        'success' => true,
        'movies' => $movies,
        'count' => count($movies)
    ]);

} catch (PDOException $e) {
    error_log("Get movies error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve movies'
    ]);
}
?>

