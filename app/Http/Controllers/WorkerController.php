<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;

class WorkerController extends Controller
{
    public function index()
    {
        $workers = Worker::all();
        return view('admin.worker', compact('workers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'skills' => 'required|string|max:255',
            'pengalaman' => 'required|integer|min:0',
            'umur' => 'nullable|integer|min:0',
            'status' => 'required|string|max:20',
        ]);

        Worker::create($validated);

        return redirect()->route('worker')->with('success', 'Pekerja berhasil ditambahkan!');
    }

    // 🔎 Detail pekerja
    public function show($id)
    {
        $worker = Worker::findOrFail($id);
        return view('admin.worker_show', compact('worker'));
    }

    // ✏️ Update pekerja
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'skills' => 'required|string|max:255',
            'pengalaman' => 'required|integer|min:0',
            'umur' => 'nullable|integer|min:0',
            'status' => 'required|string|max:20',
        ]);

        $worker = Worker::findOrFail($id);
        $worker->update($validated);

        return redirect()->route('worker')->with('success', 'Pekerja berhasil diperbarui!');
    }

    // ❌ Hapus pekerja
    public function destroy($id)
    {
        $worker = Worker::findOrFail($id);
        $worker->delete();

        return redirect()->route('worker')->with('success', 'Pekerja berhasil dihapus!');
    }
}
