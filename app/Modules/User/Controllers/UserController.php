<?php

namespace App\Modules\User\Controllers; 

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\DTOs\LoginDTO; 
use Illuminate\Http\Request; 

class UserController extends Controller
{
    public function __construct(private UserService $userService) {} 

    public function register(RegisterRequest $request) 
    {
    $validated = $request->validated(); 
    $dto = RegisterDTO::fromRequest((object) $validated); 
    return $this->userService->register($dto); 
    }


}