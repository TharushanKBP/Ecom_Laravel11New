<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Product;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

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
        )->associate(Product::class);

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
            // Add product to cart
            Cart::instance('cart')->add(
                $product->id,
                $product->name,
                1, // Default quantity
                $product->price
            )->associate(Product::class);

            return redirect()->route('cart.index')->with('success', 'Product moved to cart successfully!');
        }

        return redirect()->back()->with('error', 'Product not found.');
    }

    public function apply_coupon_code(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:255',
        ]);

        $coupon_code = $request->coupon_code;

        $coupon = Coupon::where('code', $coupon_code)
            ->where('expiry_date', '>=', Carbon::today())
            ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
            ->first();

        if (!$coupon) {
            return redirect()->back()->with('error', 'Invalid coupon code!');
        }

        if (Session::has('coupon')) {
            return redirect()->back()->with('error', 'A coupon is already applied.');
        }

        Session::put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'cart_value' => $coupon->cart_value,
        ]);

        $this->calculateDiscount();

        return redirect()->back()->with('success', 'Coupon has been applied!');
    }

    protected function calculateDiscount()
    {
        $discount = 0;

        if (Session::has('coupon')) {
            $coupon = Session::get('coupon');

            if ($coupon['type'] == 'fixed') {
                $discount = $coupon['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * $coupon['value']) / 100;
            }

            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = $subtotalAfterDiscount * config('cart.tax') / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discount', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),
            ]);
        }
    }
}
