<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Login endpoint
     * POST /api/auth/login
     */
    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(422);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ])->setStatusCode(401);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ])->setStatusCode(401);
        }

        if (!$user['is_active']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Account is inactive'
            ])->setStatusCode(403);
        }

        $token = $this->generateJWT($user);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name']
                ]
            ]
        ])->setStatusCode(200);
    }

    /**
     * Get current user info
     * GET /api/auth/me
     */
    public function me()
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name']
                ]
            ]
        ])->setStatusCode(200);
    }

    /**
     * Generate JWT token for user
     */
    private function generateJWT($user)
    {
        $payload = [
            'iss' => base_url(), 
            'sub' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(), 
            'exp' => time() + (int)getenv('JWT_EXPIRE_TIME')
        ];

        return JWT::encode($payload, getenv('JWT_SECRET_KEY'), getenv('JWT_ALGORITHM'));
    }

    /**
     * Get current authenticated user from JWT token
     */
    private function getCurrentUser()
    {
        $token = $this->getTokenFromHeader();
        
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), getenv('JWT_ALGORITHM')));
            return $this->userModel->find($decoded->sub);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extract JWT token from Authorization header
     */
    private function getTokenFromHeader()
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
}