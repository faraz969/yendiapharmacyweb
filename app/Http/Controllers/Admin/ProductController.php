<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('is_expired', true);
            }
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,avi,mov|max:10240',
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'purchase_unit' => 'required|string|in:box,pack,bottle,piece',
            'selling_unit' => 'required|string|in:tablet,capsule,ml,piece',
            'conversion_factor' => 'required|integer|min:1',
            'prescription_notes' => 'nullable|string',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        // Handle video
        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        // Auto-generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = Product::generateUniqueSku();
        }

        $validated['requires_prescription'] = $request->has('requires_prescription');
        $validated['track_expiry'] = $request->has('track_expiry');
        $validated['track_batch'] = $request->has('track_batch');
        $validated['is_active'] = $request->has('is_active');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'batches']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,avi,mov|max:10240',
            'selling_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'purchase_unit' => 'required|string|in:box,pack,bottle,piece',
            'selling_unit' => 'required|string|in:tablet,capsule,ml,piece',
            'conversion_factor' => 'required|integer|min:1',
            'prescription_notes' => 'nullable|string',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
            $validated['images'] = $imagePaths;
        } elseif ($request->has('remove_images')) {
            // Remove specific images
            $imagesToRemove = $request->remove_images;
            $currentImages = $product->images ?? [];
            $remainingImages = array_diff($currentImages, $imagesToRemove);
            foreach ($imagesToRemove as $imageToRemove) {
                Storage::disk('public')->delete($imageToRemove);
            }
            $validated['images'] = array_values($remainingImages);
        }

        // Handle video
        if ($request->hasFile('video')) {
            if ($product->video) {
                Storage::disk('public')->delete($product->video);
            }
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        $validated['requires_prescription'] = $request->has('requires_prescription');
        $validated['track_expiry'] = $request->has('track_expiry');
        $validated['track_batch'] = $request->has('track_batch');
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete video
        if ($product->video) {
            Storage::disk('public')->delete($product->video);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Show CSV import form
     */
    public function showImportForm()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.import', compact('categories'));
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_import_template.csv"',
        ];

        $columns = [
            'category_name',
            'name',
            'description',
            'sku',
            'barcode',
            'selling_price',
            'cost_price',
            'discount',
            'purchase_unit',
            'selling_unit',
            'conversion_factor',
            'requires_prescription',
            'prescription_notes',
            'min_stock_level',
            'max_stock_level',
            'track_expiry',
            'track_batch',
            'is_active',
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header row
            fputcsv($file, $columns);
            
            // Sample row
            fputcsv($file, [
                'Medicines',
                'Sample Product Name',
                'Product description',
                'SKU001',
                'BARCODE001',
                '10.00',
                '5.00',
                '0.00',
                'box',
                'tablet',
                '10',
                'false',
                '',
                '10',
                '100',
                'true',
                'true',
                'true',
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import products from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        
        $errors = [];
        $successCount = 0;
        $skipCount = 0;
        $rowNumber = 1; // Start from 1 (header row)

        try {
            // Read CSV file
            $handle = fopen($path, 'r');
            
            if ($handle === false) {
                return back()->withErrors(['csv_file' => 'Unable to read CSV file.']);
            }

            // Skip BOM if present
            $firstLine = fgets($handle);
            if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
                $firstLine = substr($firstLine, 3);
            }
            rewind($handle);
            if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
                fseek($handle, 3);
            }

            // Read header row
            $headers = fgetcsv($handle);
            if (!$headers) {
                fclose($handle);
                return back()->withErrors(['csv_file' => 'CSV file is empty or invalid.']);
            }

            // Normalize headers (trim and lowercase)
            $headers = array_map(function($header) {
                return trim(strtolower($header));
            }, $headers);

            // Expected columns mapping
            $columnMap = [
                'category_name' => 'category_name',
                'name' => 'name',
                'description' => 'description',
                'sku' => 'sku',
                'barcode' => 'barcode',
                'selling_price' => 'selling_price',
                'cost_price' => 'cost_price',
                'discount' => 'discount',
                'purchase_unit' => 'purchase_unit',
                'selling_unit' => 'selling_unit',
                'conversion_factor' => 'conversion_factor',
                'requires_prescription' => 'requires_prescription',
                'prescription_notes' => 'prescription_notes',
                'min_stock_level' => 'min_stock_level',
                'max_stock_level' => 'max_stock_level',
                'track_expiry' => 'track_expiry',
                'track_batch' => 'track_batch',
                'is_active' => 'is_active',
            ];

            // Process rows
            DB::beginTransaction();
            
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map row data to associative array
                $data = [];
                foreach ($headers as $index => $header) {
                    if (isset($row[$index])) {
                        $data[$header] = trim($row[$index]);
                    }
                }

                // Validate required fields
                if (empty($data['name'])) {
                    $errors[] = "Row {$rowNumber}: Product name is required.";
                    $skipCount++;
                    continue;
                }

                // Get or create category
                $categoryName = $data['category_name'] ?? '';
                if (empty($categoryName)) {
                    $errors[] = "Row {$rowNumber}: Category name is required.";
                    $skipCount++;
                    continue;
                }

                $category = Category::where('name', $categoryName)->first();
                if (!$category) {
                    $errors[] = "Row {$rowNumber}: Category '{$categoryName}' not found. Please create it first.";
                    $skipCount++;
                    continue;
                }

                // Prepare product data
                $productData = [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'sku' => !empty($data['sku']) ? $data['sku'] : Product::generateUniqueSku(),
                    'barcode' => $data['barcode'] ?? null,
                    'selling_price' => floatval($data['selling_price'] ?? 0),
                    'cost_price' => floatval($data['cost_price'] ?? 0),
                    'discount' => floatval($data['discount'] ?? 0),
                    'purchase_unit' => $data['purchase_unit'] ?? 'box',
                    'selling_unit' => $data['selling_unit'] ?? 'tablet',
                    'conversion_factor' => intval($data['conversion_factor'] ?? 1),
                    'prescription_notes' => $data['prescription_notes'] ?? null,
                    'min_stock_level' => !empty($data['min_stock_level']) ? intval($data['min_stock_level']) : 0,
                    'max_stock_level' => !empty($data['max_stock_level']) ? intval($data['max_stock_level']) : null,
                    'requires_prescription' => $this->parseBoolean($data['requires_prescription'] ?? 'false'),
                    'track_expiry' => $this->parseBoolean($data['track_expiry'] ?? 'true'),
                    'track_batch' => $this->parseBoolean($data['track_batch'] ?? 'true'),
                    'is_active' => $this->parseBoolean($data['is_active'] ?? 'true'),
                ];

                // Validate SKU uniqueness
                if (!empty($data['sku'])) {
                    $existingProduct = Product::withTrashed()->where('sku', $productData['sku'])->first();
                    if ($existingProduct) {
                        $errors[] = "Row {$rowNumber}: SKU '{$productData['sku']}' already exists.";
                        $skipCount++;
                        continue;
                    }
                }

                // Validate barcode uniqueness if provided
                if (!empty($productData['barcode'])) {
                    $existingProduct = Product::withTrashed()->where('barcode', $productData['barcode'])->first();
                    if ($existingProduct) {
                        $errors[] = "Row {$rowNumber}: Barcode '{$productData['barcode']}' already exists.";
                        $skipCount++;
                        continue;
                    }
                }

                // Validate numeric fields
                if ($productData['selling_price'] <= 0) {
                    $errors[] = "Row {$rowNumber}: Selling price must be greater than 0.";
                    $skipCount++;
                    continue;
                }

                if ($productData['conversion_factor'] < 1) {
                    $errors[] = "Row {$rowNumber}: Conversion factor must be at least 1.";
                    $skipCount++;
                    continue;
                }

                // Create product
                try {
                    Product::create($productData);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: Error creating product - " . $e->getMessage();
                    $skipCount++;
                    Log::error('Product CSV import error', [
                        'row' => $rowNumber,
                        'data' => $productData,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            fclose($handle);

            if ($successCount > 0) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            // Prepare response message
            $message = "Import completed. Success: {$successCount}, Skipped: {$skipCount}";
            if (!empty($errors)) {
                $message .= ". Errors: " . count($errors);
            }

            if ($successCount > 0) {
                return redirect()->route('admin.products.index')
                    ->with('success', $message)
                    ->with('import_errors', $errors);
            } else {
                return back()
                    ->withErrors(['csv_file' => $message])
                    ->with('import_errors', $errors);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV import exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors(['csv_file' => 'An error occurred during import: ' . $e->getMessage()]);
        }
    }

    /**
     * Parse boolean value from CSV
     */
    private function parseBoolean($value)
    {
        $value = strtolower(trim($value));
        return in_array($value, ['true', '1', 'yes', 'y', 'on']);
    }
}
