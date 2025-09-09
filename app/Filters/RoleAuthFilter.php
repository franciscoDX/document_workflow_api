<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuthFilter implements FilterInterface
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
        
        $jwtUser = $request->jwt_user;
        
        
        if (!$jwtUser) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Authentication required'
            ])->setStatusCode(401);
        }

        if (empty($arguments)) {
            return $request;
        }

        $requiredRole = $arguments[0] ?? null;
        $userRole = $jwtUser->role ?? null;

        // Check if user has the required role
        if ($requiredRole && $userRole !== $requiredRole) {
            return $response->setJSON([
                'status' => 'error',
                'message' => 'Insufficient permissions.'
            ])->setStatusCode(403);
        }

        return $request;
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
