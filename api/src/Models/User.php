<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo
     */
    protected $table = 'users';

    /**
     * Os atributos que são atribuíveis em massa
     */
    protected $fillable = [
        'name',
        'email'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Os atributos que devem ser escondidos para arrays
     */
    protected $hidden = [
        // Adicionar campos sensíveis aqui se necessário
    ];

    /**
     * Regras de validação
     */
    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email'
    ];

    /**
     * Mensagens de validação personalizadas
     */
    public static $messages = [
        'name.required' => 'O nome é obrigatório',
        'name.string' => 'O nome deve ser uma string',
        'name.max' => 'O nome não pode ter mais de 255 caracteres',
        'email.required' => 'O email é obrigatório',
        'email.email' => 'O email deve ser válido',
        'email.unique' => 'Este email já está em uso'
    ];

    /**
     * Escopo para usuários ativos
     */
    public function scopeActive($query)
    {
        return $query->where('deleted_at', null);
    }

    /**
     * Buscar usuário por email
     */
    public static function findByEmail($email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Verificar se usuário existe por email
     */
    public static function existsByEmail($email)
    {
        return static::where('email', $email)->exists();
    }
}
