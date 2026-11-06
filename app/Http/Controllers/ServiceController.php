<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Booking;
use App\Models\Worker;
use App\Models\Request as RequestModel; // gunakan alias agar tidak bentrok dengan Illuminate\\Http\\Request
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all(); // ambil semua data
        $admin = auth()->guard('admin')->user();
        $totalBooking = RequestModel::count();
        $activeWorkers = Worker::where('status', 'aktif')->count();
        return view('dashboard_admin', compact('services', 'admin', 'totalBooking', 'activeWorkers'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'deskripsi' => 'nullable',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'harga_min' => 'required|numeric',
            'harga_max' => 'required|numeric',
            'status' => 'required|in:aktif,nonaktif'
        ]);

        // handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('uploads/services');
            if (!is_dir($destPath)) {
                @mkdir($destPath, 0777, true);
            }
            $file->move($destPath, $filename);
            $data['gambar'] = $filename;
        }

        Service::create($data);
        return redirect()->route('services.index')->with('success', 'Layanan berhasil ditambah!');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'harga_min' => 'required|numeric|min:0',
            'harga_max' => 'required|numeric|min:0|gte:harga_min',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('uploads/services');
            if (!is_dir($destPath)) {
                @mkdir($destPath, 0777, true);
            }
            $file->move($destPath, $filename);
            $data['gambar'] = $filename;
            // optional: hapus file lama
            if (!empty($service->gambar)) {
                $old = public_path('uploads/services/' . $service->gambar);
                if (is_file($old)) { @unlink($old); }
            }
        }

        $service->update($data);
        return response()->json(['success' => true, 'service' => $service]);
    }

    public function destroy(Service $service)
    {
        // hapus file gambar jika ada
        if (!empty($service->gambar)) {
            $path = public_path('uploads/services/' . $service->gambar);
            if (is_file($path)) { @unlink($path); }
        }
        $service->delete();
        return response()->json(['success' => true]);
    }
}
