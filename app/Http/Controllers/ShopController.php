<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Validate and sanitize query parameters
        $request->validate([
            'size' => 'integer|min:1|max:100',
            'order' => 'integer|in:-1,1,2,3,4',
            'brands' => 'nullable|string',
            'categories' => 'nullable|string',
            'min' => 'numeric|min:0',
            'max' => 'numeric|min:0',
        ]);

        $size = $request->query('size', 12);
        $order = $request->query('order', -1);
        $f_brands = $request->query('brands', '');
        $f_categories = $request->query('categories', '');
        $min_price = $request->query('min', 1);
        $max_price = $request->query('max', 500);

        // Sorting options
        $sortOptions = [
            1 => ['created_at', 'DESC'],
            2 => ['created_at', 'ASC'],
            3 => ['regular_price', 'ASC'],
            4 => ['regular_price', 'DESC'],
            -1 => ['id', 'DESC'],
        ];
        [$o_column, $o_order] = $sortOptions[$order] ?? ['id', 'DESC'];

        // Fetch brands and categories
        $brands = Brand::orderBy('name', 'ASC')->get();
        $categories = Category::orderBy('name', 'ASC')->get();

        // Fetch products with filters
        $products = Product::with(['brand', 'category']) // Eager load relations
            ->when($f_brands, function ($query) use ($f_brands) {
                $brandIds = array_filter(explode(',', $f_brands));
                if (!empty($brandIds)) {
                    $query->whereIn('brand_id', $brandIds);
                }
            })
            ->when($f_categories, function ($query) use ($f_categories) {
                $categoryIds = array_filter(explode(',', $f_categories));
                if (!empty($categoryIds)) {
                    $query->whereIn('category_id', $categoryIds);
                }
            })
            ->whereBetween('regular_price', [$min_price, $max_price])
            ->orWhereBetween('sale_price', [$min_price, $max_price])
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop', compact('products', 'size', 'order', 'brands', 'categories', 'f_brands', 'f_categories', 'min_price', 'max_price'));
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
