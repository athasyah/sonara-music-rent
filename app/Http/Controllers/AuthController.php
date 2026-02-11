<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\UserInterface;
use App\Enums\RoleEnum;
use App\Helpers\Response;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailOtpMail;
use App\Mail\ForgotPasswordOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

            if (!$user->email_verified_at) {
                auth()->logout();
                return Response::Error(
                    'Email belum diverifikasi. Silakan verifikasi terlebih dahulu',
                    null
                );
            }

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

            // generate OTP
            $otp = rand(100000, 999999);

            $validate['email_otp'] = $otp;
            $validate['email_verified_at'] = null;
            $validate['otp_expires_at'] = now()->addMinutes(15);

            $user = $this->userInterface->findByEmail($request->email);
            if ($user && $user->email_verified_at === null) {
                // regenerate OTP
                $otp = rand(100000, 999999);

                $user->update([
                    'email_otp' => $otp,
                    'otp_expires_at' => now()->addMinutes(15),
                ]);

                Mail::to($user->email)->send(new EmailOtpMail($otp));

                DB::commit();

                return Response::Ok(
                    'Akun sudah terdaftar tetapi belum diverifikasi. OTP baru telah dikirim.',
                    null
                );
            }

            $service = $this->userService->mappingDataUser($validate);
            $user = $this->userInterface->store($service);
            $user->assignRole(RoleEnum::CUSTOMER->value);

            // kirim email OTP
            Mail::to($user->email)->send(new EmailOtpMail($otp));

            DB::commit();

            return Response::Ok(
                'Registrasi berhasil. Silakan cek email untuk verifikasi OTP',
                null
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Terjadi kesalahan saat pendaftaran', $th->getMessage());
        }
    }

    public function getMe()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return Response::Error('User tidak ditemukan', null);
            }

            $user->role = $user->getRoleNames();
            $user->token = request()->bearerToken();

            return Response::Ok('Berhasil mendapatkan data user', $user);
        } catch (\Throwable $th) {
            return Response::Error('Terjadi kesalahan saat mendapatkan data user', $th->getMessage());
        }
    }

    public function logout()
    {
        try {
            auth()->user()?->tokens()?->delete();
            return Response::Ok('Berhasil logout', null);
        } catch (\Throwable $th) {
            return Response::Error('Terjadi kesalahan saat logout: ' . $th->getMessage(), null);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string'
        ]);

        $user = $this->userInterface->findByEmail($request->email);

        if (!$user) {
            return Response::Error('User tidak ditemukan', null);
        }

        if ($user->email_verified_at) {
            return Response::Error('Email sudah diverifikasi', null);
        }

        if ($user->email_otp !== $request->otp) {
            return Response::Error('OTP tidak valid', null);
        }

        if (now()->gt($user->otp_expires_at)) {
            return Response::Error('OTP sudah kadaluarsa', null);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_otp' => null,
            'otp_expires_at' => null,
        ]);

        return Response::Ok('Email berhasil diverifikasi', null);
    }

    public function changePassword(PasswordRequest $request, string $id)
    {
        $user = $this->userInterface->show($id);
        if (!$user) return Response::NotFound('User tidak ditemukan');

        $validate = $request->validated();
        DB::beginTransaction();
        try {
            if (!Hash::check($validate['old_password'], $user->password)) {
                return Response::Error('Password lama tidak valid', null);
            }

            $hashedPassword = Hash::make($validate['password']);
            $update = $this->userInterface->update($id, ['password' => $hashedPassword]);

            DB::commit();
            return Response::Ok('Berhasil mengubah password', $update);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Gagal mengubah password', $th->getMessage());
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = $this->userInterface->findByEmail($request->email);

        if (!$user) {
            return Response::Error('Email tidak terdaftar', null);
        }

        if ($user->email_verified_at == null) {
            return Response::Error('Email tidak terdaftar atau belum terverifikasi', null);
        }

        // if ($user->reset_password_expires_at && now()->lt($user->reset_password_expires_at->subMinutes(13))) {
        //     return Response::Error('OTP sudah dikirim, silakan tunggu beberapa saat', null);
        // }

        $otp = rand(100000, 999999);

        $user->update([
            'reset_password_otp' => $otp,
            'reset_password_expires_at' => now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new ForgotPasswordOtpMail($otp));

        return Response::Ok(
            'Kode OTP reset password telah dikirim ke email',
            null
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email',
                'otp' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',

                'otp.required' => 'Kode OTP wajib diisi',
                'otp.string' => 'Kode OTP tidak valid',

                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sama',
            ]
        );

        $user = $this->userInterface->findByEmail($request->email);

        if (!$user) {
            return Response::Error('User tidak ditemukan', null);
        }

        if (!$user->reset_password_otp || !$user->reset_password_expires_at) {
            return Response::Error('Tidak ada permintaan reset password', null);
        }

        if (now()->gt($user->reset_password_expires_at)) {
            return Response::Error('OTP sudah kadaluarsa', null);
        }

        if ($user->reset_password_otp !== $request->otp) {
            return Response::Error('OTP tidak valid', null);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'reset_password_otp' => null,
            'reset_password_expires_at' => null,
        ]);

        return Response::Ok('Password berhasil diubah', null);
    }
}
