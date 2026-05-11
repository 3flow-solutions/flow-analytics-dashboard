<?php

namespace App\Http\Controllers;

abstract class BaseController {

    protected function authenticate() {
        $headers = getallheaders();
        $auth_header = $headers['Authorization'] ?? '';
        
        if (empty($auth_header)) {
            http_response_code(401);
            echo json_encode(['error' => 'Missing authorization header']);
            exit;
        }

        $parts = explode(' ', $auth_header);
        if (count($parts) !== 2 || $parts[0] !== 'Bearer') {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid authorization header']);
            exit;
        }

        // Basic JWT validation (in production, validate signature and expiration)
        $token = $parts[1];
        return true;
    }

    protected function response($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
