<?php

namespace App\Http\Controllers;

use App\Database;

class AuthController {
    
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['email']) || !isset($input['password'])) {
            $this->response(['error' => 'Missing credentials'], 400);
            return;
        }

        $email = Database::escape($input['email']);
        $password = $input['password'];
        
        // Query from your users table (adjust column names as needed)
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = Database::query($query);
        
        if ($user = Database::fetchAssoc($result)) {
            if (password_verify($password, $user['password'])) {
                $token = $this->generateToken($user);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                
                $this->response([
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => ['id' => $user['id'], 'email' => $user['email']]
                ]);
                return;
            }
        }

        $this->response(['error' => 'Invalid credentials'], 401);
    }

    public function logout() {
        session_destroy();
        $this->response(['message' => 'Logged out successfully']);
    }

    public function me() {
        if (!$this->authenticate()) {
            $this->response(['error' => 'Unauthorized'], 401);
            return;
        }

        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            $this->response(['error' => 'User not found'], 404);
            return;
        }

        $query = "SELECT id, email FROM users WHERE id = '$user_id' LIMIT 1";
        $result = Database::query($query);
        $user = Database::fetchAssoc($result);

        $this->response(['user' => $user]);
    }

    private function generateToken($user) {
        $issued_at = time();
        $expire = $issued_at + (24 * 60 * 60); // 24 hours
        $payload = [
            'iat' => $issued_at,
            'exp' => $expire,
            'user_id' => $user['id'],
            'email' => $user['email']
        ];

        $secret = getenv('JWT_SECRET') ?: 'your_super_secret_jwt_key_change_this';
        return $this->base64url_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ])) . '.' . 
        $this->base64url_encode(json_encode($payload)) . '.' .
        $this->base64url_encode(hash_hmac('sha256', 
            $this->base64url_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256'])) . '.' .
            $this->base64url_encode(json_encode($payload)), 
            $secret, true));
    }

    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function authenticate() {
        $headers = apache_request_headers();
        $auth_header = $headers['Authorization'] ?? '';
        
        if (empty($auth_header)) {
            return false;
        }

        $parts = explode(' ', $auth_header);
        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            return false;
        }

        // Basic JWT validation (simplified)
        $token = $parts[1];
        // In production, validate signature and expiration
        
        return true;
    }

    protected function response($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
