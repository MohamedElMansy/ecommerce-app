<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemCollection;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $userID = auth('api')->user()->id;
        }
        $cart = Cart::create([
            'key' => md5(uniqid(rand(), true)),
            'user_id' => isset($userID) ? $userID : null,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'New cart created',
            'cart_id' => $cart->id,
            'cart_key' => $cart->key,
        ], 201);
    }

    public function show(Cart $cart, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $cartKey = $request->input('cart_key');
        if ($cart->key == $cartKey) {
            $cart_items = empty($cart->cartItems) ? array() : $cart->cartItems;
            $cart_calc = $this->get_cart_calculations($cart_items);
            return response()->json([
                'success' => true,
                'cart_id' => $cart->id,
                'cart_items' => new CartItemCollection($cart_items),
                "total_products_prices" => $cart_calc["total_products_prices"],
                "total_vat" => $cart_calc["total_vat"],
                "total_shipping_cost" => $cart_calc["total_shipping_cost"],
                "total_to_pay" => $cart_calc["total_to_pay"]
            ], 200);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Cart keys mismatched.',
            ], 400);
        }
    }

    /**
     * Remove the specified Cart from storage.
     *
     * @param  \App\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $cartKey = $request->input('cart_key');

        if ($cart->key == $cartKey) {
            $cart->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cart deleted successfully.',
            ], 200);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Cart keys mismatched.',
            ], 400);
        }
    }

    /**
     * Adds Products to the Cart;
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cart  $cart
     * @return void
     */
    public function addProductToCart(Cart $cart, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart_key' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        $cart_key = $request->input('cart_key');
        $product_id = $request->input('product_id');
        $quantity = $request->input('quantity');
        //Check if the cart key is Valid
        if ($cart->key == $cart_key) {
            //Check if the proudct exist or not.
            try {
                $product = Product::findOrFail($product_id);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'The Product not exist.',
                ], 404);
            }

            //check if the the same product is already in the Cart, if true update the quantity, if not create a new one.
            $cartItem = CartItem::where(['cart_id' => $cart->id, 'product_id' => $product_id])->first();
            if ($cartItem) {
                $cartItem->quantity = $quantity;
                CartItem::where(['cart_id' => $cart->id, 'product_id' => $product_id])->update(['quantity' => $quantity]);
            } else { //add new item to cart
                CartItem::create(['cart_id' => $cart->id, 'product_id' => $product_id, 'quantity' => $quantity]);
            }

            return response()->json(['success' => true, 'message' => 'Products added successfully to cart.'], 200);
        } else {

            return response()->json([
                'success' => false,
                'message' => 'Cart keys mismatched.',
            ], 400);
        }
    }

    /**
     * Get Cart Calculations
     *
     * @param CartItem $cart_items
     * @return object
     */
    public function get_cart_calculations($cart_items)
    {
        $total_products_prices = 0;
        $total_vat = 0;
        $total_shipping_cost = 0;
        $store_shipped = array();

        foreach ($cart_items as $item) {
            //to make code readable
            $store = $item->product->store;
            // get the total prices of each product (price * quantity) then + the total prices of all products
            $total_products_prices = $total_products_prices + ($item->product->price * $item->quantity);

            //vat calulations
            if (!$store->is_vat_included) {
                if (!empty($store->vat_price)) {
                    $vat_amount_all_products = $store->vat_price * $item->quantity;
                    $total_vat = $total_vat + $vat_amount_all_products;
                } elseif (!empty($store->vat_percentage)) {
                    $total_vat = $total_vat + (($store->vat_percentage * $item->product->price * $item->quantity) / 100);
                }
            }

            //shipping cost calculation (add the shipping cost of the stores which their products exist in cart)
            if (!in_array($store->id, $store_shipped)) {
                $store_shipped[] = $store->id;
                $total_shipping_cost = $total_shipping_cost + $store->shipping_cost;
            }
        }
        $total_to_pay = $total_products_prices + $total_vat + $total_shipping_cost;
        return  [
            "total_products_prices" => $total_products_prices,
            "total_vat" => $total_vat,
            "total_shipping_cost" => $total_shipping_cost,
            "total_to_pay" => $total_to_pay
        ];
    }
}
