<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\UserInterface;
use App\Enums\RoleEnum;
use App\Helpers\PaginationHelper;
use App\Helpers\Response;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $userInterface, $userService;
    public function __construct(UserInterface $userInterface, UserService $userService)
    {
        $this->userInterface = $userInterface;
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ?? 8;
        $page = $request->page ?? 1;
        $payload = $request->only(['role']);
        try {
            $data = $this->userInterface->customPaginate($per_page, $page, $payload);
            $resource = UserResource::collection($data);
            $helper = PaginationHelper::meta($data);

            return Response::Paginate('Berhasil menampilkan data User', $resource, $helper);
        } catch (\Throwable $th) {
            return Response::Error('Gagal menampilkan data User', $th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validate = $request->validated();

        DB::beginTransaction();
        try {
            $map = $this->userService->createUser($validate);
            $store = $this->userInterface->store($map);
            $store->assignRole(RoleEnum::STAFF->value);

            DB::commit();
            return Response::Ok('Berhasil menambahkan data User', $store);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Terjadi kesalahan saat menambahkan data User', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userInterface->show($id);
            if (!$user) return Response::NotFound('User tidak ditemukan');

            return Response::Ok('Berhasil mendapatkan data user', $user);
        } catch (\Throwable $th) {
            return Response::Error('Terjadi kesalahan saat mendapatkan data user', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = $this->userInterface->show($id);
        if (!$user) return Response::NotFound('User tidak ditemukan');

        $validate = $request->validated();
        DB::beginTransaction();

        try {
            $map = $this->userService->editUser($validate);
            $update = $this->userInterface->update($id, $map);

            DB::commit();
            return Response::Ok('Berhasil mengubah data user', $update);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Terjadi kesalahan saat mengubah data user', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = $this->userInterface->show($id);
        if (!$user) return Response::NotFound('User tidak ditemukan');

        DB::beginTransaction();
        try {
            $delete = $this->userInterface->delete($id);

            DB::commit();
            return Response::Ok('Berhasil menghaapus user', $delete);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Gagal menghapus user', $th->getMessage());
        }
    }
}
