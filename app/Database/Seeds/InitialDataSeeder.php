<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $adminData = [
            'email' => 'admin@example.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'administrator',
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $collaborators = [
            [
                'email' => 'collaborator@example.com',
                'password' => password_hash('collaborator123', PASSWORD_DEFAULT),
                'role' => 'collaborator',
                'first_name' => 'Collaborator',
                'last_name' => 'User',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'email' => 'fran6602@gmail.com',
                'password' => password_hash('francisco123', PASSWORD_DEFAULT),
                'role' => 'collaborator',
                'first_name' => 'Francisco',
                'last_name' => 'Fernandez',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('users')->insert($adminData);

        foreach ($collaborators as $collaborator) {
            $this->db->table('users')->insert($collaborator);
        }

    }
}
