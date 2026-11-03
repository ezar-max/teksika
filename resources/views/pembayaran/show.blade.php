@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Pembayaran</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Kode Pembayaran: {{ $pembayaran->kode_pembayaran }}</h5>
            
            <table class="table">
                <tr>
                    <th>Jumlah</th>
                    <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Metode</th>
                    <td>{{ ucfirst($pembayaran->metode_pembayaran) }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $pembayaran->status == 'success' ? 'success' : ($pembayaran->status == 'failed' ? 'danger' : 'warning') }}">
                            {{ strtoupper($pembayaran->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Nama Pembayar</th>
                    <td>{{ $pembayaran->nama_pembayar }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $pembayaran->email_pembayar ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td>{{ $pembayaran->keterangan ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $pembayaran->created_at->format('d-m-Y H:i') }}</td>
                </tr>
            </table>
            
            @if($pembayaran->status == 'pending')
                <form action="{{ route('pembayaran.update', $pembayaran->kode_pembayaran) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Update Status</label>
                        <select name="status" class="form-select" required>
                            <option value="">Pilih Status</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection