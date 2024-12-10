<?php

namespace App\Http\Controllers\Api;

use App\Models\LogStok;
use App\Models\Produk;
use App\Http\Controllers\Controller;
use App\Http\Resources\LogStokResource; // Import resource untuk response standar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LogStokController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        $logStoks = LogStok::with(['produk'])->latest()->paginate(5);

        return new LogStokResource(true, 'List Data Produk', $logStoks);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id'        => 'required|exists:produks,id',
            'user_id'          => 'required|exists:users,id',
            'jumlah_perubahan' => 'required|integer',
            'jenis_perubahan'  => 'required|in:masuk,keluar',
            'keterangan'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $produk = Produk::find($request->produk_id);
        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $stokTokoSebelum = $produk->stok_toko;
        $stokGudangSebelum = $produk->stok_gudang;

        if ($request->jenis_perubahan === 'masuk') {
            $produk->stok_toko += $request->jumlah_perubahan;
            $produk->stok_gudang -= $request->jumlah_perubahan;
        } elseif ($request->jenis_perubahan === 'keluar') {
            $produk->stok_toko -= $request->jumlah_perubahan;
            $produk->stok_gudang += $request->jumlah_perubahan;
        }

        $produk->save();

        LogStok::create([
            'produk_id'        => $request->produk_id,
            'user_id'          => $request->user_id,
            'jumlah_perubahan' => $request->jumlah_perubahan,
            'stok_sebelum'     => $stokTokoSebelum,
            'stok_setelah'     => $produk->stok_toko,
            'jenis_perubahan'  => $request->jenis_perubahan,
            'keterangan'       => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Log Stok Berhasil Ditambahkan!',
        ]);
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'produk_id'        => 'required|exists:produks,id',
            'user_id'          => 'required|exists:users,id',
            'jumlah_perubahan' => 'required|integer',
            'jenis_perubahan'  => 'required|in:masuk,keluar',
            'keterangan'       => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $logStok = LogStok::find($id);
        if (!$logStok) {
            return response()->json([
                'success' => false,
                'message' => 'Log Stok Tidak Ditemukan',
            ], 404);
        }

        $produk = Produk::find($logStok->produk_id);
        if ($produk) {
            // Revert perubahan sebelumnya
            if ($logStok->jenis_perubahan === 'masuk') {
                $produk->stok_toko -= $logStok->jumlah_perubahan;
                $produk->stok_gudang += $logStok->jumlah_perubahan;
            } elseif ($logStok->jenis_perubahan === 'keluar') {
                $produk->stok_toko += $logStok->jumlah_perubahan;
                $produk->stok_gudang -= $logStok->jumlah_perubahan;
            }

            // Terapkan perubahan baru
            if ($request->jenis_perubahan === 'masuk') {
                $produk->stok_toko += $request->jumlah_perubahan;
                $produk->stok_gudang -= $request->jumlah_perubahan;
            } elseif ($request->jenis_perubahan === 'keluar') {
                $produk->stok_toko -= $request->jumlah_perubahan;
                $produk->stok_gudang += $request->jumlah_perubahan;
            }

            $produk->save();
        }

        $logStok->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data Log Stok Berhasil Diubah!',
            'data'    => $logStok
        ]);
    }

    /**
     * Destroy
     */
    public function destroy($id)
    {
        $logStok = LogStok::find($id);
        if (!$logStok) {
            return response()->json([
                'success' => false,
                'message' => 'Log Stok Tidak Ditemukan',
            ], 404);
        }

        $produk = Produk::find($logStok->produk_id);
        if ($produk) {
            if ($logStok->jenis_perubahan === 'masuk') {
                $produk->stok_toko -= $logStok->jumlah_perubahan;
                $produk->stok_gudang += $logStok->jumlah_perubahan;
            } elseif ($logStok->jenis_perubahan === 'keluar') {
                $produk->stok_toko += $logStok->jumlah_perubahan;
                $produk->stok_gudang -= $logStok->jumlah_perubahan;
            }

            $produk->save();
        }

        $logStok->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Log Stok Berhasil Dihapus!',
        ]);
    }
}
