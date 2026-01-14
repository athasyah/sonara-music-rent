<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\UserInterface;
use App\Helpers\Response;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private $userInterface, $userService;
    public function __construct(UserInterface $userInterface, UserService $userService)
    {
        $this->userInterface = $userInterface;
        $this->userService = $userService;
    }

    public function login(LoginRequest $request)
    {
        try {
            $validate = $request->validated();

            if (!auth()->attempt($validate)) {
                return Response::Custom(false, "Login gagal, periksa kembali email dan password anda", null, 401);
            }

            $user = auth()->user();
            $roles = $user->getRoleNames();

            $token = $user->createToken('authToken')->plainTextToken;

            $user->token = $token;
            $user->role = $roles;

            return Response::Ok('Login berhasil', $user);
        } catch (\Throwable $th) {
            return Response::Error('Terjadi kesalahan saat login', $th->getMessage());
        }
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $validate = $request->validated();

            if ($request->hasFile('image')) {
                $validate['image'] = $request->file('image');
            }

            $service = $this->userService->mappingDataUser($validate);
            $user = $this->userInterface->store($service);
            $user->assignRole('customer');

            DB::commit();
            return Response::Ok('Pendaftaran berhasil', $user);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Terjadi kesalahan saat pendaftaran', $th->getMessage());
        }
    }
}
