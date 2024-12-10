<?php

namespace App\Http\Controllers\Api;

use App\Models\ProdukKeluar; // Import model ProdukKeluar
use App\Models\Produk; // Import model Produk
use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukKeluarResource; // Import resource ProdukKeluarResource
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukKeluarController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        // Get all ProdukKeluar with pagination and eager load relations
        $produkKeluars = ProdukKeluar::with(['produk'])->latest()->paginate(5);

        // Return collection of ProdukKeluar as a resource
        return new ProdukKeluarResource(true, 'List Data Produk Keluar', $produkKeluars);
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
            'jumlah'         => 'required|integer',
            'tanggal_keluar' => 'required|date',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Create new ProdukKeluar
        $produkKeluar = ProdukKeluar::create($request->all());
    
        // Update stok produk setelah produk keluar
        $produk = Produk::find($produkKeluar->produk_id);
        if ($produk) {
            // Kurangi jumlah produk yang keluar dari stok gudang
            $produk->stok_gudang -= $produkKeluar->jumlah;
            // Update stok produk
            $produk->save();
        }
    
        // Return response
        return new ProdukKeluarResource(true, 'Data Produk Keluar Berhasil Ditambahkan!', $produkKeluar);
    }
    
    /**
     * Show
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        // Find ProdukKeluar by ID
        $produkKeluar = ProdukKeluar::with(['produk', 'supplier'])->find($id);

        // Check if ProdukKeluar exists
        if (!$produkKeluar) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Keluar Not Found',
            ], 404);
        }

        // Return single ProdukKeluar as a resource
        return new ProdukKeluarResource(true, 'Detail Data Produk Keluar!', $produkKeluar);
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
            'jumlah'         => 'required|integer',
            'tanggal_keluar' => 'required|date',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Find ProdukKeluar by ID
        $produkKeluar = ProdukKeluar::find($id);
    
        // Check if ProdukKeluar exists
        if (!$produkKeluar) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Keluar Not Found',
            ], 404);
        }
    
        // Save current quantity (old quantity) for later adjustment
        $oldQuantity = $produkKeluar->jumlah;
    
        // Update ProdukKeluar data with new request
        $produkKeluar->update($request->all());
    
        // Find the related product
        $produk = Produk::find($produkKeluar->produk_id);
        if ($produk) {
            // Calculate the difference in quantity
            $quantityDifference = $produkKeluar->jumlah - $oldQuantity;
    
            // Adjust stock based on the quantity difference
            $produk->stok_gudang -= $quantityDifference;
            
            // Save updated product stock
            $produk->save();
        }
    
        // Return response
        return new ProdukKeluarResource(true, 'Data Produk Keluar Berhasil Diubah!', $produkKeluar);
    }
    
    /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
{
    // Temukan ProdukKeluar berdasarkan ID
    $produkKeluar = ProdukKeluar::find($id);

    // Periksa apakah ProdukKeluar ada
    if (!$produkKeluar) {
        return response()->json([
            'success' => false,
            'message' => 'Produk Keluar Tidak Ditemukan',
        ], 404);
    }

    // Temukan Produk terkait
    $produk = Produk::find($produkKeluar->produk_id);
    
    // Periksa apakah Produk ada
    if ($produk) {
        // Tambahkan kembali jumlah yang keluar ke stok produk
        $produk->stok_gudang += $produkKeluar->jumlah;

        // Simpan perubahan stok produk
        $produk->save();
    }

    // Hapus data ProdukKeluar
    $produkKeluar->delete();

    // Kembalikan response
    return new ProdukKeluarResource(true, 'Data Produk Keluar Berhasil Dihapus dan Stok Diperbarui!', null);
}
}