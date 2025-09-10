<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends BaseController
{
    /**
     * Listar todos os usuários
     */
    public function index(Request $request, Response $response): Response
    {
        try {
            $users = User::all();
            return $this->success($response, $users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($response, 'Error retrieving users: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mostrar usuário específico
     */
    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->error($response, 'User ID is required', 400);
            }

            $user = User::find($id);
            if (!$user) {
                return $this->error($response, 'User not found', 404);
            }

            return $this->success($response, $user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($response, 'Error retrieving user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Criar novo usuário
     */
    public function store(Request $request, Response $response): Response
    {
        try {
            $data = $this->getRequestData($request);
            
            // Validação básica
            if (empty($data['name']) || empty($data['email'])) {
                return $this->error($response, 'Name and email are required', 400);
            }

            // Verificar se email já existe
            if (User::where('email', $data['email'])->exists()) {
                return $this->error($response, 'Email already exists', 409);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email']
            ]);

            return $this->success($response, $user, 'User created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($response, 'Error creating user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->error($response, 'User ID is required', 400);
            }

            $user = User::find($id);
            if (!$user) {
                return $this->error($response, 'User not found', 404);
            }

            $data = $this->getRequestData($request);
            
            // Atualizar apenas campos fornecidos
            if (isset($data['name'])) {
                $user->name = $data['name'];
            }
            if (isset($data['email'])) {
                $user->email = $data['email'];
            }

            $user->save();

            return $this->success($response, $user, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->error($response, 'Error updating user: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Deletar usuário
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        try {
            $id = $args['id'] ?? null;
            if (!$id) {
                return $this->error($response, 'User ID is required', 400);
            }

            $user = User::find($id);
            if (!$user) {
                return $this->error($response, 'User not found', 404);
            }

            $user->delete();

            return $this->success($response, null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->error($response, 'Error deleting user: ' . $e->getMessage(), 500);
        }
    }
}
