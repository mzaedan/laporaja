<?php 

namespace App\repositories;

use App\interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }

    public function logout()
    {
        return Auth::logout();
    }
}


?>