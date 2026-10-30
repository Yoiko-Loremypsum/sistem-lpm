<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPengaduan;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori pengaduan
     */
    public function index()
    {
        $kategoris = KategoriPengaduan::withCount('pengaduan')
            ->orderBy('nama_kategori')
            ->get();

        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * Tambah kategori pengaduan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori_pengaduan,nama_kategori'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori ini sudah terdaftar.',
        ]);

        KategoriPengaduan::create($validated);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori pengaduan baru berhasil ditambahkan.');
    }

    /**
     * Ubah kategori pengaduan
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriPengaduan::findOrFail($id);

        $validated = $request->validate([
            'nama_kategori' => ['required', 'string', 'max:255', 'unique:kategori_pengaduan,nama_kategori,' . $id],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ], [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori ini sudah digunakan oleh data lain.',
        ]);

        $kategori->update($validated);

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Data kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori pengaduan
     */
    public function destroy($id)
    {
        $kategori = KategoriPengaduan::withCount('pengaduan')->findOrFail($id);

        if ($kategori->pengaduan_count > 0) {
            return back()->with('error', "Kategori '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh {$kategori->pengaduan_count} laporan pengaduan.");
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori pengaduan berhasil dihapus.');
    }
}
