<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\RentalDetailInterface;
use App\Contracts\Interfaces\RentalInterface;
use App\Enums\StatusEnum;
use App\Events\RentalStatusUpdated;
use App\Helpers\PaginationHelper;
use App\Helpers\Response;
use App\Http\Requests\RentalRequest;
use App\Http\Requests\StatusRentalRequest;
use App\Http\Resources\RentalResource;
use App\Models\rental;
use App\Services\RentalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    private $rentalInterface, $rentalService, $rentDetailInterface;
    public function __construct(RentalInterface $rentalInterface, RentalService $rentalService, RentalDetailInterface $rentalDetailInterface)
    {
        $this->rentalInterface = $rentalInterface;
        $this->rentalService = $rentalService;
        $this->rentDetailInterface = $rentalDetailInterface;
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
            $data = $this->rentalInterface->customPaginate($per_page, $page, $payload);
            $resource = RentalResource::collection($data);
            $helper = PaginationHelper::meta($data);

            return Response::Paginate('Berhasil menampilkan data Rental', $resource, $helper);
        } catch (\Throwable $th) {
            return Response::Error('Gagal menampilkan data Rental', $th->getMessage());
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
    public function store(RentalRequest $request)
    {
        $validate = $request->validated();

        DB::beginTransaction();
        try {
            $totalDays = $this->rentalService->calculateRentalDays(
                $request->rent_date,
                $request->return_date
            );

            $details = $this->rentalService->mapRentalDetails(
                $request->details,
                $totalDays
            );
            $totalPrice = collect($details)->sum('subtotal');

            $map = $this->rentalService->rentalStore($validate, $totalPrice);
            $rental = $this->rentalInterface->store($map);



            foreach ($details as &$detail) {
                $detail['rental_id'] = $rental->id;
                $this->rentDetailInterface->store($detail);
            }

            DB::commit();
            $rental->load(['details', 'customer']);
            return Response::Ok('Berhasil menambahkan data rental', $rental);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Terjadi kesalahan saat menambahkan data Rental', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->rentalInterface->show($id);

            if (!$data) return Response::NotFound('Data rental tidak ditemukan');

            return Response::Ok('Berhasil mengambil data rental', new RentalResource($data));
        } catch (\Throwable $th) {
            return Response::Error('Gagal mengambil data rental', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rental $rental)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RentalRequest $request, string $id)
    {
        $rental = $this->rentalInterface->show($id);
        if (!$rental) {
            return Response::NotFound('Rental tidak ditemukan');
        }

        if ($rental->status !== StatusEnum::PENDING->value) {
            return Response::Error('Rental hanya bisa diubah jika status pending', null);
        }

        $validate = $request->validated();

        DB::beginTransaction();
        try {
            // hitung ulang durasi (SAMA seperti store)
            $totalDays = $this->rentalService->calculateRentalDays(
                $validate['rent_date'],
                $validate['return_date']
            );

            $details = $this->rentalService->mapRentalDetails(
                $validate['details'],
                $totalDays
            );

            $totalPrice = collect($details)->sum('subtotal');

            $updatedRental = $this->rentalInterface->update($id, [
                'rent_date'   => $validate['rent_date'],
                'return_date' => $validate['return_date'],
                'total_price' => $totalPrice,
            ]);

            $this->rentDetailInterface->deleteByRentalId($id);

            foreach ($details as $detail) {
                $detail['rental_id'] = $id;
                $this->rentDetailInterface->store($detail);
            }

            DB::commit();

            $updatedRental->load(['details', 'customer']);

            return Response::Ok('Berhasil mengubah data rental', $updatedRental);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Gagal mengubah data rental', $th->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->rentalInterface->show($id);
        if (!$data) return Response::NotFound('Rental tidak ditemukan');

        if ($data->status !== 'pending') {
            return Response::Error('Rental hanya bisa dihapus jika status pending', null);
        }

        DB::beginTransaction();
        try {
            $rent = $this->rentalInterface->delete($id);

            DB::commit();
            return Response::Ok('Berhasil menhapus data rental', $rent);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error('Terjadi kesalahan saat menghapus data rental', $th->getMessage());
        }
    }

    public function noPaginate(Request $requeset)
    {
        $payload = [];

        try {
            $data = $this->rentalInterface->noPaginate($payload);

            return Response::Ok('Berhasil mendapatkan data rental', RentalResource::collection($data));
        } catch (\Throwable $th) {
            return Response::Error('Gagal mendapatkan data rental', $th->getMessage());
        }
    }

    public function statusRental(StatusRentalRequest $request, string $id)
    {
        $rental = $this->rentalInterface->show($id);
        if (!$rental) {
            return Response::NotFound('Rental tidak ditemukan');
        }

        $validate  = $request->validated();
        $newStatus = $validate['status'];
        $oldStatus = $rental->status;

        $allowedTransitions = [
            StatusEnum::PENDING->value => [
                StatusEnum::APPROVED->value,
                StatusEnum::CANCELLED->value,
            ],
            StatusEnum::APPROVED->value => [
                StatusEnum::ONGOING->value,
            ],
            StatusEnum::ONGOING->value => [
                StatusEnum::RETURNED->value,
            ],
        ];

        if (
            !isset($allowedTransitions[$oldStatus]) ||
            !in_array($newStatus, $allowedTransitions[$oldStatus])
        ) {
            return Response::Error(
                "Status {$oldStatus} tidak bisa diubah menjadi {$newStatus}",
                null
            );
        }

        DB::beginTransaction();
        try {
            // update status rental
            $updatedRental = $this->rentalInterface->update($id, [
                'status' => $newStatus
            ]);

            DB::commit();

            // trigger event SETELAH commit
            event(new RentalStatusUpdated(
                $updatedRental->load('details.instrument'),
                $oldStatus,
                $newStatus
            ));

            return Response::Ok('Berhasil mengubah status rental', $updatedRental);
        } catch (\Throwable $th) {
            DB::rollBack();
            return Response::Error(
                'Terjadi kesalahan saat mengubah status rental',
                $th->getMessage()
            );
        }
    }
}
