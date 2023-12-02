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
            return response()->json([
                'success' => true,
                'cart_id' => $cart->id,
                'cart_items' => new CartItemCollection($cart_items),
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
}
