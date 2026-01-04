import '../models/movie.dart';
import 'api_service.dart';

class MovieService {
  static Future<Map<String, dynamic>> getMovies() async {
    final response = await ApiService.get('/api/movies/get_movies.php');

    if (response['success'] == true && response['movies'] != null) {
      final List<dynamic> moviesJson = response['movies'];
      final movies = moviesJson.map((json) => Movie.fromJson(json)).toList();
      return {
        'success': true,
        'movies': movies,
      };
    }

    return response;
  }

  static Future<Map<String, dynamic>> addMovie(Movie movie) async {
    return await ApiService.post('/api/movies/add_movie.php', movie.toJson());
  }

  static Future<Map<String, dynamic>> deleteMovie(int movieId) async {
    return await ApiService.post('/api/movies/delete_movie.php', {
      'movie_id': movieId,
    });
  }
}

