<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\HaversineService;

class WorkerController extends Controller
{
    private const OFFICE_ADDRESS = 'Kantor Pusat TEKSIKA - SMKN 1 Gunung Putri';

    public function index()
    {
        $workers = Worker::withCount('reviews')->orderBy('name')->get();
        $services = DB::table('services')->orderBy('nama')->get();
        foreach ($workers as $worker) {
            $worker->display_availability = $this->availabilityFor($worker);
        }
        return view('admin.worker', compact('workers', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'role' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'skills' => 'required|array|min:1',
            'skills.*' => ['required', 'string', 'max:100', Rule::in(DB::table('services')->pluck('nama')->all())],
            'tarif' => 'required|numeric|min:0|max:9999999999',
            'pengalaman' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|between:0,5',
            'umur' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,tidak aktif',
            'status_ketersediaan' => 'required|in:siap,tidak_tersedia,libur',
            'foto' => 'nullable|image|max:5120',
        ]);

        if (!isset($validated['rating'])) {
            $validated['rating'] = 5.0;
        }
        $validated['skills'] = implode(', ', array_unique($validated['skills']));
        $validated['alamat'] = self::OFFICE_ADDRESS;
        $validated['latitude'] = HaversineService::OFFICE_LATITUDE;
        $validated['longitude'] = HaversineService::OFFICE_LONGITUDE;

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('workers', 'public');
        }
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
            'phone' => 'required|string|max:50',
            'skills' => 'required|array|min:1',
            'skills.*' => ['required', 'string', 'max:100', Rule::in(DB::table('services')->pluck('nama')->all())],
            'tarif' => 'required|numeric|min:0|max:9999999999',
            'pengalaman' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|between:0,5',
            'umur' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,tidak aktif',
            'status_ketersediaan' => 'required|in:siap,tidak_tersedia,libur',
            'foto' => 'nullable|image|max:5120',
        ]);

        $worker = Worker::findOrFail($id);
        $validated['skills'] = implode(', ', array_unique($validated['skills']));
        $validated['alamat'] = self::OFFICE_ADDRESS;
        $validated['latitude'] = HaversineService::OFFICE_LATITUDE;
        $validated['longitude'] = HaversineService::OFFICE_LONGITUDE;
        if ($request->hasFile('foto')) {
            if ($worker->foto) {
                Storage::disk('public')->delete($worker->foto);
            }
            $validated['foto'] = $request->file('foto')->store('workers', 'public');
        } else {
            unset($validated['foto']);
        }
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

    private function availabilityFor(Worker $worker): string
    {
        if ($worker->status !== 'aktif' || in_array($worker->status_ketersediaan, ['tidak_tersedia', 'libur'], true)) {
            return 'Tidak Tersedia';
        }

        $hasActiveJob = DB::table('request')
            ->where('id_worker', $worker->id)
            ->whereIn('status', ['dikonfirmasi', 'dikerjakan'])
            ->exists();

        return $hasActiveJob || $worker->status_ketersediaan === 'sedang_bekerja'
            ? 'Sedang Bekerja'
            : 'Siap Bekerja';
    }
}
