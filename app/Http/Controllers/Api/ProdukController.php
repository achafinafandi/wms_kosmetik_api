<?php

namespace App\Http\Controllers\Api;

use App\Models\Produk; // Import model Produk
use App\Http\Controllers\Controller;
use App\Http\Resources\ProdukResource; // Import resource untuk response standar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        // Get all produk with pagination
        $produks = Produk::with(['kategori', 'supplier'])->latest()->paginate(5);

        // Return collection of produk as a resource
        return new ProdukResource(true, 'List Data Produk', $produks);
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
            'nama_produk'    => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategoris,id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'harga'          => 'required|integer',
            'stok_gudang'    => 'required|integer',
            'stok_toko'      => 'required|integer',
            'expired'        => 'required|date',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create new produk
        $produk = Produk::create($request->all());

        // Return response
        return new ProdukResource(true, 'Data Produk Berhasil Ditambahkan!', $produk);
    }

    /**
     * Show
     *
     * @param  int $id
     * @return void
     */
    public function show($id)
    {
        // Find produk by ID
        $produk = Produk::with(['kategori', 'supplier'])->find($id);

        // Check if produk exists
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Not Found',
            ], 404);
        }

        // Return single produk as a resource
        return new ProdukResource(true, 'Detail Data Produk!', $produk);
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
            'nama_produk'    => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategoris,id',
            'supplier_id'    => 'required|exists:suppliers,id',
            'harga'          => 'required|integer',
            'stok_gudang'    => 'required|integer',
            'stok_toko'      => 'required|integer',
            'expired'        => 'required|date',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find produk by ID
        $produk = Produk::find($id);

        // Check if produk exists
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Not Found',
            ], 404);
        }

        // Update produk data
        $produk->update($request->all());

        // Return response
        return new ProdukResource(true, 'Data Produk Berhasil Diubah!', $produk);
    }

    /**
     * Destroy
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Find produk by ID
        $produk = Produk::find($id);

        // Check if produk exists
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk Not Found',
            ], 404);
        }

        // Delete produk
        $produk->delete();

        // Return response
        return new ProdukResource(true, 'Data Produk Berhasil Dihapus!', null);
    }
}
