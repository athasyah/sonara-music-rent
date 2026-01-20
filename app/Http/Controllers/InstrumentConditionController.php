<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\InstrumentConditionInterface;
use App\Events\InstrumentConditionCreated;
use App\Helpers\PaginationHelper;
use App\Helpers\Response;
use App\Http\Requests\InstrumentConditionRequest;
use App\Http\Resources\InstrumentConditionResource;
use App\Models\Instrument;
use App\Models\InstrumentCondition;
use App\Services\InstrumentConditionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstrumentConditionController extends Controller
{
    private $conditionInterface, $conditionService;
    public function __construct(InstrumentConditionInterface $conditionInterface, InstrumentConditionService $conditionService)
    {
        $this->conditionInterface = $conditionInterface;
        $this->conditionService = $conditionService;
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
            $data = $this->conditionInterface->customPaginate($per_page, $page, $payload);
            $resource = InstrumentConditionResource::collection($data);
            $helper = PaginationHelper::meta($data);

            return Response::Paginate('Berhasil menampilkan data kondisi instrumen', $resource, $helper);
        } catch (\Throwable $th) {
            return Response::Error('Gagal menampilkan data kondisi instrumen', $th->getMessage());
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
    public function store(InstrumentConditionRequest $request)
    {
        $validate = $request->validated();

        DB::beginTransaction();
        try {
            $service = $this->conditionService->mappingInstrumentCondition($validate);
            $data = $this->conditionInterface->store($service);

            DB::commit();
            event(new InstrumentConditionCreated(
                $data->load('instrument')
            ));
            return Response::Ok('Berhasil menambahkan data kondisi instrumen', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Gagal menambahkan data kondisi instrumen', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->conditionInterface->show($id);
            if (!$data) return Response::NotFound('Gagal mendapatkan data kondisi instrumen');

            return Response::Ok('Berhasil mendapatkan data kondisi instrumen', $data);
        } catch (\Throwable $th) {
            return Response::Error('Gagal mendapatkan data kondisi instrumen', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(instrumentCondition $insrumentCondition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InstrumentConditionRequest $request, string $id)
    {
        $data = $this->conditionInterface->show($id);
        if (!$data) return Response::NotFound('Gagal mendapatkan data kondisi instrumen');

        $validate = $request->validated();
        DB::beginTransaction();
        try {
            $service = $this->conditionService->mappingInstrumentCondition($validate);
            $update = $this->conditionInterface->update($id, $service);

            DB::commit();
            event(new InstrumentConditionCreated(
                $update->load('instrument')
            ));
            return Response::Ok('Berhasil mengubah data kondisi instrumen', $update);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Gagal mengubah data kondisi instrumen', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->conditionInterface->show($id);
        if (!$data) return Response::NotFound('Gagal menghapus data kondisi instrumen');

        DB::beginTransaction();
        try {
            $data = $this->conditionInterface->delete($id);

            DB::commit();
            return Response::Ok('Berhasil menghapus data kondisi instrumen', $data);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Gagal menghapus data kondisi instrumen', $th->getMessage());
        }
    }

    public function noPaginate(Request $requeset)
    {
        $payload = [];

        try {
            $data = $this->conditionInterface->noPaginate($payload);

            return Response::Ok('Berhasil mendapatkan data kategori', InstrumentConditionResource::collection($data));
        } catch (\Throwable $th) {
            return Response::Error('Gagal mendapatkan data kategori', $th->getMessage());
        }
    }
}
