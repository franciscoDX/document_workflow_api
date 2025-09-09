<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'email',
        'password', 
        'role',
        'first_name',
        'last_name',
        'is_active'
    ];

    protected bool $allowEmptyInserts = false;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[administrator,collaborator]',
        'first_name' => 'permit_empty|string|max_length[100]',
        'last_name' => 'permit_empty|string|max_length[100]'
    ];
    
    protected $validationMessages = [
        'email' => [
            'required' => 'Email is required',
            'valid_email' => 'Please provide a valid email',
            'is_unique' => 'This email is already registered'
        ],
        'password' => [
            'required' => 'Password is required',
            'min_length' => 'Password must be at least 6 characters'
        ],
        'role' => [
            'required' => 'Role is required',
            'in_list' => 'Role must be administrator or collaborator'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)
                   ->where('is_active', 1)
                   ->findAll();
    }

    /**
     * Get collaborators for task assignment
     */
    public function getCollaborators()
    {
        return $this->select('id, email, first_name, last_name')
                   ->where('role', 'collaborator')
                   ->where('is_active', 1)
                   ->findAll();
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($userId, $role)
    {
        $user = $this->find($userId);
        return $user && $user['role'] === $role;
    }

    /**
     * Activate/deactivate user
     */
    public function toggleStatus($userId)
    {
        $user = $this->find($userId);
        if ($user) {
            return $this->update($userId, ['is_active' => !$user['is_active']]);
        }
        return false;
    }
}