class User {
  final int id;
  final String email;
  final String role;

  User({
    required this.id,
    required this.email,
    required this.role,
  });

  bool get isAdmin => role == 'admin';

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: int.parse(json['user_id'].toString()),
      email: json['email'] ?? '',
      role: json['role'] ?? 'user',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': id,
      'email': email,
      'role': role,
    };
  }
}

