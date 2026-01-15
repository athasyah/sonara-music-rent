<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\CategoryInterface;
use App\Helpers\PaginationHelper;
use App\Helpers\Response;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryInterface;
    public function __construct(CategoryInterface $categoryInterface)
    {
        $this->categoryInterface = $categoryInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = $request->per_page ?? 8;
        $page = $request->page ?? 1;
        $payload = [];
        try {
            $data = $this->categoryInterface->customPaginate($per_page, $page, $payload);
            $resource = CategoryResource::collection($data);
            $helper = PaginationHelper::meta($data);

            return Response::Paginate('Berhasil menampilkan data kategori', $resource, $helper);
        } catch (\Throwable $th) {
            return Response::Error('Gagal menampilkan data kategori', $th->getMessage());
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
    public function store(CategoryRequest $request)
    {

        $validate = $request->validated();

        try {
            $category = $this->categoryInterface->store($validate);

            return Response::Ok('Berhasil menambahkan data kategori', $category);
        } catch (\Throwable $th) {
            return Response::Error('Terjadi kesalahan saat menambahkan data kategori', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->categoryInterface->show($id);

            if (!$data) return Response::NotFound('Pengguna tidak ditemukan');

            return Response::Ok('Berhasil mengambil data pengguna', $data);
        } catch (\Throwable $th) {
            return Response::Error('Gagal mengambil data pengguna', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoryRequest $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $data = $this->categoryInterface->show($id);
        if (!$data) return Response::NotFound('Pengguna tidak ditemukan');

        $validate = $request->validated();

        try {
            $update = $this->categoryInterface->update($id, $validate);

            return Response::Ok('Berhasil mengubah data kategori', $update);
        } catch (\Throwable $th) {
            return Response::Error('Gagal mengubah data kategori', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->categoryInterface->show($id);
        if (!$data) return Response::NotFound('Pengguna tidak ditemukan');

        try {
            $delete = $this->categoryInterface->delete($id);

            return Response::Ok('berhasil menghapus data kategori', $delete);
        } catch (\Throwable $th) {
            return Response::Error('gagal menghapus data kategori', $th->getMessage());
        }
    }
}
