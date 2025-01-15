<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size', 12);
        $order = $request->query('order', -1);
        $f_brands = $request->query('brands', '');
        $f_categories = $request->query('categories', '');
        $min_price = $request->query('min', 1);
        $max_price = $request->query('max', 500);

        // Determine order column and direction
        switch ($order) {
            case 1:
                $o_column = 'created_at';
                $o_order = 'DESC';
                break;
            case 2:
                $o_column = 'created_at';
                $o_order = 'ASC';
                break;
            case 3:
                $o_column = 'regular_price';
                $o_order = 'ASC';
                break;
            case 4:
                $o_column = 'regular_price';
                $o_order = 'DESC';
                break;
            default:
                $o_column = 'id';
                $o_order = 'DESC';
                break;
        }

        // Fetch brands and categories
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();

        // Fetch products with filters
        $products = Product::when($f_brands, function ($query) use ($f_brands) {
            $query->whereIn('brand_id', explode(',', $f_brands));
        })
            ->when($f_categories, function ($query) use ($f_categories) {
                $query->whereIn('category_id', explode(',', $f_categories));
            })
            ->where(function ($query) use ($min_price, $max_price) {
                $query->whereBetween('regular_price', [$min_price, $max_price])
                    ->orWhereBetween('sale_price', [$min_price, $max_price]);
            })
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop', compact('products', 'size', 'order', 'brands', 'categories', 'f_brands', 'f_categories','min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->firstOrFail();
        $rproduct = Product::where('slug', '<>', $product_slug)
            ->take(8)
            ->get();

        return view('details', compact('product', 'rproduct'));
    }
}
