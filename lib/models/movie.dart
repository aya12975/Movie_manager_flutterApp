class Movie {
  final int id;
  final String title;
  final String description;
  final String genre;
  final int releaseYear;
  final double rating;
  final String? createdAt;

  Movie({
    required this.id,
    required this.title,
    required this.description,
    required this.genre,
    required this.releaseYear,
    required this.rating,
    this.createdAt,
  });

  factory Movie.fromJson(Map<String, dynamic> json) {
    return Movie(
      id: int.parse(json['id'].toString()),
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      genre: json['genre'] ?? '',
      releaseYear: int.parse(json['release_year'].toString()),
      rating: double.parse(json['rating'].toString()),
      createdAt: json['created_at'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,
      'description': description,
      'genre': genre,
      'release_year': releaseYear,
      'rating': rating,
    };
  }
}

