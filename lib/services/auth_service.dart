import 'package:shared_preferences/shared_preferences.dart';
import '../models/user.dart';
import 'api_service.dart';

class AuthService {
  static const String _userKey = 'logged_in_user';
  static const String _userIdKey = 'user_id';
  static const String _userRoleKey = 'user_role';

  static Future<Map<String, dynamic>> login(
    String email,
    String password,
  ) async {
    final response = await ApiService.post('/api/login.php', {
      'email': email,
      'password': password,
    });

    if (response['success'] == true) {
      await _saveUserData(
        response['user_id'].toString(),
        response['role'].toString(),
        email,
      );
    }

    return response;
  }

  static Future<void> _saveUserData(
    String userId,
    String role,
    String email,
  ) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_userIdKey, userId);
    await prefs.setString(_userRoleKey, role);
    await prefs.setString(_userKey, email);
  }

  static Future<User?> getCurrentUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getString(_userIdKey);
    final role = prefs.getString(_userRoleKey);
    final email = prefs.getString(_userKey);

    if (userId == null || role == null || email == null) {
      return null;
    }

    return User(
      id: int.parse(userId),
      email: email,
      role: role,
    );
  }

  static Future<bool> isLoggedIn() async {
    final user = await getCurrentUser();
    return user != null;
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_userIdKey);
    await prefs.remove(_userRoleKey);
    await prefs.remove(_userKey);
  }

  static Future<String?> getUserRole() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString(_userRoleKey);
  }
}

