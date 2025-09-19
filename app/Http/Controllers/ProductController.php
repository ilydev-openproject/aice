<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'hpp' => 'required|numeric|min:0',
            'het' => 'required|numeric|min:0',
            'isi_per_box' => 'required|integer|min:1',
            'margin' => 'required|numeric|min:0', // wajib diisi — karena ini data utama
        ]);

        Product::create($request->only([
            'nama_produk',
            'hpp',
            'het',
            'isi_per_box',
            'margin', // langsung simpan
            'foto',
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // update
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'hpp' => 'required|numeric|min:0',
            'het' => 'required|numeric|min:0',
            'isi_per_box' => 'required|integer|min:1',
            'margin' => 'required|numeric|min:0',
        ]);

        $product->update($request->only([
            'nama_produk',
            'hpp',
            'het',
            'isi_per_box',
            'margin',
            'foto',
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate!');
    }
}
