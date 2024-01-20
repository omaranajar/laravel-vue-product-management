<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductsResource;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductsResource::collection(
            Product::where('user_id', Auth::user()->id)->paginate(3)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'size' => $request->size,
            'type' => $request->type,
            'user_id' => Auth::user()->id,
        ]);

        // Call the uploadImage method to handle image upload
        $imagePath = $this->uploadImage($request, $product);

        // If an image was uploaded, update the product with the image path
        if ($imagePath) {
            $product->update(['image' => $imagePath]);
        }

        return new ProductsResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->isNotAuthorized($product) ? $this->isNotAuthorized($product) : new ProductsResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        if ($this->isNotAuthorized($product)) {
            return $this->isNotAuthorized($product);
        }

        $product->update($request->all());

        // Call the uploadImage method to handle image upload
        $imagePath = $this->uploadImage($request, $product);

        // If an image was uploaded, update the product with the image path
        if ($imagePath) {
            $product->update(['image' => $imagePath]);
        }

        return new ProductsResource($product);
    }

    public function uploadImage(Request $request, $product = null)
    {
        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('product_images', 'public');

            if ($product) {
                // Delete the old picture from storage
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
            }

            return $path;
        }

        return null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return $this->isNotAuthorized($product) ? $this->isNotAuthorized($product) : $product->delete();
    }

    public function filterProducts(Request $request)
    {
        // Retrieve input parameters
        $name = $request->input('name');
        $type = $request->input('type');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        // Build the base query
        $query = Product::where('user_id', Auth::user()->id);

        // Apply filters based on provided parameters
        if ($name) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        return ProductsResource::collection(
            $query->paginate(3)
        );
    }

    private function isNotAuthorized(Product $product)
    {
        if (Auth::user()->id !== $product->user_id) {
            return $this->error([], "You're not authorized to make this request.", 403);
        }
    }
}
