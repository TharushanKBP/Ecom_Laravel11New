<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;


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
        return redirect()->route('admin.Brand.brands')->with('status', 'Brand has been updated successfully!');
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            // Optional: Add logic to handle related products or images if necessary
            $brand->delete();
            return redirect()->route('admin.Brand.brands')->with('status', 'Brand deleted successfully!');
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
}
