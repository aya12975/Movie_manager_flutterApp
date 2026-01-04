<?php
/**
 * Add Movie API Endpoint (Admin Only)
 * POST /api/movies/add_movie.php
 * 
 * Request Body:
 * {
 *   "title": "Movie Title",
 *   "description": "Movie description",
 *   "genre": "Action",
 *   "release_year": 2024,
 *   "rating": 8.5
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

// Validate required fields
$requiredFields = ['title', 'description', 'genre', 'release_year', 'rating'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => "Field '$field' is required"
        ]);
        exit();
    }
}

// Sanitize and validate input
$title = trim($input['title']);
$description = trim($input['description']);
$genre = trim($input['genre']);
$releaseYear = filter_var($input['release_year'], FILTER_VALIDATE_INT);
$rating = filter_var($input['rating'], FILTER_VALIDATE_FLOAT);

// Validate release year
if ($releaseYear === false || $releaseYear < 1900 || $releaseYear > 2100) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid release year'
    ]);
    exit();
}

// Validate rating
if ($rating === false || $rating < 0 || $rating > 10) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Rating must be between 0.0 and 10.0'
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
    // Insert new movie
    $stmt = $pdo->prepare("
        INSERT INTO movies (title, description, genre, release_year, rating, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([$title, $description, $genre, $releaseYear, $rating]);
    
    $movieId = $pdo->lastInsertId();

    // Fetch the created movie
    $stmt = $pdo->prepare("
        SELECT id, title, description, genre, release_year, rating, created_at
        FROM movies
        WHERE id = ?
    ");
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Movie added successfully',
        'movie' => $movie
    ]);

} catch (PDOException $e) {
    error_log("Add movie error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add movie'
    ]);
}
?>

