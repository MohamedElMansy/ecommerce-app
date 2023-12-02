<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    public function create(StoreRequest $request)
    {
        $user = auth()->user();
        $store = new Store();
        $store->name = $request->name;
        $store->is_vat_included = $request->is_vat_included;
        $store->vat_price = $request->vat_price;
        $store->vat_percentage = $request->vat_percentage;
        $store->shipping_cost = $request->shipping_cost;
        $store->user_id = $user->id;
        $store->save();

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully',
        ], Response::HTTP_OK);
    }
}
