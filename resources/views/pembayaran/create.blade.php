<head>
    <link rel="stylesheet" href="{{ asset('css/create.css') }}">
</head>

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Form Pembayaran</h1>
    
    <form action="{{ route('pembayaran.store') }}" method="POST">
        @csrf
        @php
            $hasBooking = isset($requestData) && isset($requestData['kode_booking']);
            $metode = strtolower($requestData['metode_pembayaran'] ?? '');
            // Generate Virtual Account simple & deterministik untuk demo
            $uid = auth()->id() ?? 0;
            $suffix = str_pad((string)$uid, 6, '0', STR_PAD_LEFT);
            $bookDigits = isset($requestData['kode_booking']) ? preg_replace('/\D/','', $requestData['kode_booking']) : '';
            $bookDigits = substr(str_pad($bookDigits, 4, '0', STR_PAD_LEFT), -4);
            $vaBCA = '3901' . $suffix . $bookDigits;     // BCA VA (dummy)
            $vaBRI = '8881' . $suffix . $bookDigits;     // BRI VA (dummy)
            $vaMandiri = '8967' . $suffix . $bookDigits; // Mandiri VA (dummy)
        @endphp

        @if($hasBooking)
            <input type="hidden" name="booking_kode" value="{{ $requestData['kode_booking'] }}">
            <div class="mb-3">
                <label class="form-label">Kode Booking</label>
                <input type="text" class="form-control" value="{{ $requestData['kode_booking'] }}" readonly>
            </div>
        @else
            <!-- fallback lama: form menyertakan data request -->
            <input type="hidden" name="layanan" value="{{ $requestData['layanan'] ?? '' }}">
            <input type="hidden" name="lokasi" value="{{ $requestData['lokasi'] ?? '' }}">
            <input type="hidden" name="urgensi" value="{{ $requestData['urgensi'] ?? '' }}">
            <input type="hidden" name="detail" value="{{ $requestData['detail'] ?? '' }}">
            <input type="hidden" name="jumlah_pembayaran" value="{{ $requestData['jumlah_pembayaran'] ?? '' }}">
            <input type="hidden" name="bayar_sekarang" value="{{ $requestData['bayar_sekarang'] ?? '' }}">
            @if(!empty($fotoPaths))
                @foreach($fotoPaths as $foto)
                    <input type="hidden" name="fotoPaths[]" value="{{ $foto }}">
                @endforeach
            @endif
        @endif

        <div class="mb-3">
            <label for="jumlah" class="form-label">Jumlah Pembayaran</label>
            <input type="text" name="jumlah" value="{{ $jumlah ?? '' }}" readonly>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Metode Pembayaran</label>
            <div id="opsi-metode">
                @if($metode === 'e-wallet' || $metode === 'qris')
                    <div style="text-align:center; margin:24px 0;">
                        <img src="{{ asset('images/qr.jpg') }}" alt="QRIS" style="max-width:220px; width:100%;"/>
                        <div style="margin-top:12px; font-weight:600; color:#1856a7;">Scan QRIS di atas untuk membayar</div>
                    </div>
                    <input type="hidden" name="metode_pembayaran" value="QRIS">
                @elseif($metode === 'bank' || $metode === 'transfer')
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metode_pembayaran" id="bca" value="BCA" checked>
                        <label class="form-check-label" for="bca">Transfer Bank BCA</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metode_pembayaran" id="bri" value="BRI">
                        <label class="form-check-label" for="bri">Transfer Bank BRI</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="metode_pembayaran" id="mandiri" value="Mandiri">
                        <label class="form-check-label" for="mandiri">Transfer Bank Mandiri</label>
                    </div>

                    <div id="vaContainer" class="mb-3" style="margin-top:12px;">
                        <label class="form-label">Virtual Account</label>
                        <div style="display:flex; align-items:center; gap:10px; background:#f8fafc; border:1px solid #e5e7eb; padding:10px 12px; border-radius:8px;">
                            <span id="vaBankLabel" style="font-weight:700; color:#1856a7;">BCA</span>
                            <span id="vaNumber" style="font-family:monospace; font-size:1.05rem;">{{ $vaBCA }}</span>
                            <button type="button" id="copyVA" style="margin-left:auto; padding:6px 10px; border:1px solid #cbd5e1; background:#fff; border-radius:6px; cursor:pointer;">Salin</button>
                        </div>
                        <small style="color:#64748b;">Gunakan nomor VA sesuai bank yang dipilih pada ATM / Mobile Banking.</small>
                    </div>
                @endif
            </div>
        </div>
        
        @if(!($metode === 'e-wallet' || $metode === 'qris'))
            <div class="mb-3">
                <label for="nama_pembayar" class="form-label">Nama Pembayar</label>
                <input type="text" class="form-control" id="nama_pembayar" name="nama_pembayar" required>
            </div>
            <div class="mb-3">
                <label for="email_pembayar" class="form-label">Email (Opsional)</label>
                <input type="email" class="form-control" id="email_pembayar" name="email_pembayar">
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>
        @endif
        
        <button type="submit" class="btn btn-primary">Konfirmasi & Bayar</button>
    </form>
<script>
// Update VA number on bank selection and copy to clipboard
(function(){
    const vaLabel = document.getElementById('vaBankLabel');
    const vaNumber = document.getElementById('vaNumber');
    const copyBtn = document.getElementById('copyVA');
    const radios = document.querySelectorAll('input[name="metode_pembayaran"]');
    if(!vaLabel || !vaNumber || radios.length===0) return;
    const map = {
        'BCA': '{{ $vaBCA }}',
        'BRI': '{{ $vaBRI }}',
        'Mandiri': '{{ $vaMandiri }}'
    };
    function updateVA(){
        const sel = document.querySelector('input[name="metode_pembayaran"]:checked');
        if(!sel) return;
        const bank = sel.value;
        vaLabel.textContent = bank;
        vaNumber.textContent = map[bank] || '';
    }
    radios.forEach(r=> r.addEventListener('change', updateVA));
    updateVA();
    if(copyBtn){
        copyBtn.addEventListener('click', function(){
            const text = (vaNumber.textContent || '').trim();
            if(!text) return;
            navigator.clipboard.writeText(text).then(()=>{
                const prev = copyBtn.textContent;
                copyBtn.textContent = 'Disalin';
                setTimeout(()=> copyBtn.textContent = prev, 1200);
            });
        });
    }
})();
</script>
</div>
@endsection