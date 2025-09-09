<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JWTAuthFilter implements FilterInterface
{
    /**
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Authorization header missing'
            ])->setStatusCode(401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid authorization header format'
            ])->setStatusCode(401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET_KEY'), getenv('JWT_ALGORITHM')));
            
            $request->jwt_user = $decoded;
            
            return $request;
            
        } catch (ExpiredException $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Token has expired'
            ])->setStatusCode(401);
            
        } catch (SignatureInvalidException $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid token signature'
            ])->setStatusCode(401);
            
        } catch (\Exception $e) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Invalid token: ' . $e->getMessage()
            ])->setStatusCode(401);
        }
    }

    /**
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
