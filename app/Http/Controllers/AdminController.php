<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Coupon;


class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    // Brand Part

    public function brands()
    {
        $brands = Brand::orderBy('id', 'desc')->paginate(10);
        return view('admin.Brand.brands', compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.Brand.brand_add');
    }

    public function brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been added succesfully!');
    }

    public function brand_edit($id)
    {
        $brands = Brand::find($id);
        return view('admin.Brand.brand_edit', compact('brands'));
    }

    public function brand_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);


        if ($request->hasFile('image')) {

            if ($brand->image && File::exists(public_path('uploads/brands/' . $brand->image))) {
                File::delete(public_path('uploads/brands/' . $brand->image));
            }

            $image = $request->file('image');
            $file_name = Carbon::now()->timestamp . '.' . $image->extension();
            $this->GenerateBrandThumbailsImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand has been updated successfully!');
    }

    public function brand_destroy($id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            // Optional: Add logic to handle related products or images if necessary
            $brand->delete();
            return redirect()->route('admin.brands')->with('status', 'Brand deleted successfully!');
        }

        return redirect()->route('admin.brands')->with('error', 'Brand not found.');
    }


    public function GenerateBrandThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRation();
        })->save($destinationPath . '/' . $imageName);
    }

    // Category Part

    public function categories()
    {
        $categories = Category::orderBy('id', 'desc')->paginate(10);
        return view('admin.Category.categories', compact('categories'));
    }

    public function add_categories()
    {
        return view('admin.Category.categories_add');
    }

    public function categories_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateCategoryThumbailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been added succesfully!');
    }

    public function categories_edit($id)
    {
        $categories = Category::find($id);
        return view('admin.Category.categories_edit', compact('categories'));
    }

    public function categories_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);


        if ($request->hasFile('image')) {

            if ($category->image && File::exists(public_path('uploads/categories/' . $category->image))) {
                File::delete(public_path('uploads/categories/' . $category->image));
            }

            $image = $request->file('image');
            $file_name = Carbon::now()->timestamp . '.' . $image->extension();
            $this->GenerateCategoryThumbailsImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category has been updated successfully!');
    }

    public function categories_destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            // Optional: Add logic to handle related products or images if necessary
            $category->delete();
            return redirect()->route('admin.categories')->with('status', 'Category deleted successfully!');
        }

        return redirect()->route('admin.categories')->with('error', 'Category not found.');
    }

    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRation();
        })->save($destinationPath . '/' . $imageName);
    }

    // Product Part

    public function products()
    {
        $products = Product::orderBy('id', 'desc')->paginate(10);
        return view('admin.Product.products', compact('products'));
    }

    public function add_products()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.Product.products_add', compact('categories', 'brands'));
    }

    public function products_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required|in:instock,outofstock',
            'featured' => 'required|boolean',
            'quantity' => 'required|integer',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'images.*' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        // Handle main image
        if ($request->hasFile('image')) {
            $mainImage = $request->file('image');
            $mainImageName = time() . '.' . $mainImage->extension();
            $mainImage->move(public_path('uploads/products'), $mainImageName);
            $product->image = $mainImageName;
        }

        // Handle gallery images
        if ($request->hasFile('images')) {
            $gallery_images = [];
            foreach ($request->file('images') as $file) {
                $galleryImageName = time() . '-' . uniqid() . '.' . $file->extension();
                $file->move(public_path('uploads/products/newitems'), $galleryImageName);
                $gallery_images[] = $galleryImageName;
            }
            $product->images = implode(',', $gallery_images);
        }

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product added successfully!');
    }


    public function products_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();
        return view('admin.Product.products_edit', compact('product', 'categories', 'brands'));
    }

    public function products_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,' . $id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'SKU' => 'required',
            'stock_status' => 'required|in:instock,outofstock',
            'featured' => 'required|boolean',
            'quantity' => 'required|integer',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'images.*' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Update product details
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        // Handle main image update
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
                unlink(public_path('uploads/products/' . $product->image));
            }

            // Upload the new image
            $mainImage = $request->file('image');
            $mainImageName = time() . '.' . $mainImage->extension();
            $mainImage->move(public_path('uploads/products'), $mainImageName);
            $product->image = $mainImageName;
        }

        // Handle gallery images update
        if ($request->hasFile('images')) {
            // Delete old gallery images if they exist
            if ($product->images) {
                foreach (explode(',', $product->images) as $oldImage) {
                    if (file_exists(public_path('uploads/products/newitems/' . $oldImage))) {
                        unlink(public_path('uploads/products/newitems/' . $oldImage));
                    }
                }
            }

            // Upload new gallery images
            $gallery_images = [];
            foreach ($request->file('images') as $file) {
                $galleryImageName = time() . '-' . uniqid() . '.' . $file->extension();
                $file->move(public_path('uploads/products/newitems'), $galleryImageName);
                $gallery_images[] = $galleryImageName;
            }
            $product->images = implode(',', $gallery_images);
        }

        $product->save();

        return redirect()->route('admin.products')->with('status', 'Product updated successfully!');
    }


    public function products_destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return redirect()->route('admin.products')->with('status', 'Product deleted successfully!');
        }
        return redirect()->route('admin.products')->with('error', 'Product not found.');
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy('expiry_date', 'desc')->paginate(12);
        return view('admin.Coupons.coupons', compact('coupons'));
    }

    public function coupons_add()
    {
        return view('admin.Coupons.coupons_add');
    }

    public function coupons_store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',

            'expiry_date' => 'required|date',
        ]);

        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();

        return redirect()->route('admin.coupons')->with('status', 'Coupon added successfully!');
    }
}
