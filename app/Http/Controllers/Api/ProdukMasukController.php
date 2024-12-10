<?php

namespace App\Http\Controllers\Api;

use App\Models\ProdukMasuk; // Import model ProdukMasuk
use App\Models\Produk; // Import model Produk
use App\Models\Supplier; // Import model Supplier
use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukMasukResource; // Import resource ProdukMasukResource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukMasukController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        // Get all ProdukMasuk with pagination and eager load relations
        $produkMasuks = ProdukMasuk::with(['produk', 'supplier'])->latest()->paginate(5);

        // Return collection of ProdukMasuk as a resource
        return new ProdukMasukResource(true, 'List Data Produk Masuk', $produkMasuks);
    }

    /**
     * Store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'produk_id'      => 'required|exists:produks,id',
            'user_id'        => 'required|exists:users,id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'jumlah'         => 'required|integer',
            'tanggal_masuk'  => 'required|date',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Create new ProdukMasuk
        $produkMasuk = ProdukMasuk::create($request->all());
    
        // Update stok produk setelah produk masuk
        $produk = Produk::find($produkMasuk->produk_id);
        if ($produk) {
            // Tambahkan jumlah produk yang masuk ke stok gudang
            $produk->stok_gudang += $produkMasuk->jumlah;
            // Update stok produk
            $produk->save();
        }
    
        // Return response
        return new ProdukMasukResource(true, 'Data Produk Masuk Berhasil Ditambahkan!', $produkMasuk);
    }
    
    /**
     * Show
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        // Find ProdukMasuk by ID
        $produkMasuk = ProdukMasuk::with(['produk', 'supplier'])->find($id);

        // Check if ProdukMasuk exists
        if (!$produkMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Masuk Not Found',
            ], 404);
        }

        // Return single ProdukMasuk as a resource
        return new ProdukMasukResource(true, 'Detail Data Produk Masuk!', $produkMasuk);
    }

    /**
     * Update
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'produk_id'      => 'required|exists:produks,id',
            'user_id'        => 'required|exists:users,id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'jumlah'         => 'required|integer',
            'tanggal_masuk'  => 'required|date',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Find ProdukMasuk by ID
        $produkMasuk = ProdukMasuk::find($id);
    
        // Check if ProdukMasuk exists
        if (!$produkMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Masuk Not Found',
            ], 404);
        }
    
        // Save current quantity (old quantity) for later adjustment
        $oldQuantity = $produkMasuk->jumlah;
    
        // Update ProdukMasuk data with new request
        $produkMasuk->update($request->all());
    
        // Find the related product
        $produk = Produk::find($produkMasuk->produk_id);
        if ($produk) {
            // Calculate the difference in quantity
            $quantityDifference = $produkMasuk->jumlah - $oldQuantity;
    
            // Adjust stock based on the quantity difference
            $produk->stok_gudang += $quantityDifference;
            
            // Save updated product stock
            $produk->save();
        }
    
        // Return response
        return new ProdukMasukResource(true, 'Data Produk Masuk Berhasil Diubah!', $produkMasuk);
    }
    
    /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Find ProdukMasuk by ID
        $produkMasuk = ProdukMasuk::find($id);

        // Check if ProdukMasuk exists
        if (!$produkMasuk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Masuk Not Found',
            ], 404);
        }

        // Delete ProdukMasuk
        $produkMasuk->delete();

        // Return response
        return new ProdukMasukResource(true, 'Data Produk Masuk Berhasil Dihapus!', null);
    }
}
