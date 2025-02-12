<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // Add item to the cart
        Cart::instance('cart')->add(
            $request->id,
            $request->name,
            $request->quantity,
            $request->price
        )->associate('App\Models\Product');

        return redirect()->back()->with('success', 'Item added to cart.');
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back()->with('success', 'Item quantity increased.');
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = max($product->qty - 1, 1); // Ensure quantity does not go below 1
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back()->with('success', 'Item quantity decreased.');
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->route('cart.index')->with('success', 'Cart has been cleared.');
    }

    public function moveToCart($id)
    {
        $product = Product::find($id);

        if ($product) {
            // Add product to cart logic
            Cart::create([
                'product_id' => $product->id,
                'quantity' => 1, // Default quantity
                // ...other cart fields...
            ]);

            return redirect()->route('cart.index')->with('success', 'Product moved to cart successfully!');
        }

        return redirect()->back()->with('error', 'Product not found.');
    }
}
