<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use View;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render("Products/Index", [
            'products' => Product::with('images')->get()
        ]);
    }
    public function create(){
        return Inertia::render("Products/Create");
    }

    public function store(Request $request)
    {
        //$product = Product::create($request->all());
        return redirect()->route('Products/Index')
            ->with('success', 'Product created successfully.');
    }


public function sendImage(Request $request)
{
    $request->validate([
        'image' => 'required|file|image|max:2048',
        'product_id' => 'required|exists:products,id',
    ]);

    $file = $request->file('image');
    
    // Get the MIME type
    $mimeType = $file->getMimeType();
    
    // Read file content and encode to base64
    $fileContent = file_get_contents($file->path());
    $base64 = base64_encode($fileContent);
    
    // Create proper data URI
    $dataUri = "data:{$mimeType};base64,{$base64}";

    ProductImage::create([
        'product_id' => $request->product_id,
        'image' => $dataUri  // Store the complete data URI
    ]);

    return response()->json(['success' => true, 'message' => 'Image uploaded successfully']);
}


public function show($id)
{
    $product = Product::with(['images', 'brand', 'category', 'stock', 'stock.stock_movement'])->findOrFail($id);
    
    return Inertia::render("Products/Show", [
        'product' => $product
    ]);
}
   
}
