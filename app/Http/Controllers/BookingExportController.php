<?php

namespace App\Http\Controllers;

use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingExportController extends Controller
{
    public function exportPDF()
    {
        // Ambil semua data dari tabel request beserta user
        $requests = RequestModel::with('user')->get();

        // Load view PDF dengan data
        $pdf = Pdf::loadView('exports.booking_pdf', compact('requests'));

        // Download PDF
        return $pdf->download('data_booking_' . date('Y-m-d') . '.pdf');
    }
}