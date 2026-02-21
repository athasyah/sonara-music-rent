<?php

namespace App\Http\Controllers;

use App\Helpers\Response;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function availability(Request $request)
    {
        $instrumentIds = $request->input('instrument_ids', []);

        if (empty($instrumentIds)) {
            return Response::Error('No instrument selected', null);
        }

        $blockedDates = $this->cartService
            ->getBlockedDates($instrumentIds);

        return response()->json([
            'data' => $blockedDates
        ]);
    }
}
