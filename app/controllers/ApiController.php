<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->call->database();
        $this->call->library('api');
    }

    public function create()
    {
        $this->api->require_method('POST');
        $input = $this->api->body();

        if (
            empty($input['username']) ||
            empty($input['email']) ||
            empty($input['password'])
        ) {
            $this->api->respond_error(
                'Username, email, and password are required.',
                400
            );
        }

        $this->db->raw(
            "INSERT INTO users (username, email, password, role, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [
                $input['username'],
                $input['email'],
                password_hash($input['password'], PASSWORD_BCRYPT),
                $input['role'] ?? 'user'
            ]
        );

        $this->api->respond([
            'message' => 'User created'
        ], 201);
    }

    public function login()
    {
        $this->api->require_method('POST');
        $input = $this->api->body();

        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        $stmt = $this->db->raw(
            'SELECT * FROM users WHERE username = ?',
            [$username]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->api->respond_error('Invalid credentials', 401);
        }

        $tokens = $this->api->issue_tokens([
            'id'   => $user['id'],
            'role' => $user['role']
        ]);

        $this->api->respond($tokens);
    }

    public function logout()
    {
        $this->api->require_method('POST');

        $input = $this->api->body();

        $this->api->revoke_refresh_token(
            $input['refresh_token'] ?? ''
        );

        $this->api->respond([
            'message' => 'Logged out'
        ]);
    }

    public function refresh()
    {
        $this->api->require_method('POST');

        $input = $this->api->body();

        $this->api->refresh_access_token(
            $input['refresh_token'] ?? ''
        );
    }
    public function debug_users_table()
    {
        $result = $this->db->raw("SHOW COLUMNS FROM users");

        $columns = $result->fetchAll(PDO::FETCH_ASSOC);

        $this->api->respond([
            'status' => true,
            'columns' => $columns
        ], 200);
    }
}