@extends('layouts.app')

@php
    $active = 'penyewaan';
@endphp

@section('title','Penyewaan')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/upload_pembayaran.css') }}">
@endpush 

@section('content')

    <!-- INFO PEMBAYARAN -->

    <!-- UPLOAD AREA -->
    <form action="{{ route('penyewaan_pusat.upload_bukti', $penyewaan->idpenyewaan) }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    <div class="upload-wrapper">
        <h2 class="upload-title">Upload Bukti Bayar</h2>
        <span class="timer" data-time="{{ $sisaDetik }}"><i class="fa-solid fa-clock"></i>
        <strong class="countdown">-- : -- : --</strong></span>
        
        <div class="info-bayar">
            <div class="info-left">
                <p>Metode Pembayaran : <strong>{{ ucfirst($penyewaan->metode_bayar) }}</strong></p>
                <p class="total">
                    Total : <span>Rp. {{ number_format($penyewaan->total,  0, ',', '.') }}</span>
                </p>
            </div>

            <div class="info-right">
            <span class="badge bank">
                Bank Negara Indonesia (BNI)
            </span>

            <span class="badge rekening">
                No. rekening : 489755489287
            </span>
        </div>
        </div>

        <label class="upload-box">
    <input type="file" name="bukti_bayar" id="uploadInput" hidden accept="image/*">

    <div class="upload-content">
        <i class="fa-solid fa-cloud-arrow-up"></i>
        <p id="fileName">Upload bukti bayar</p>
    </div>
</label>

<div id="uploadSuccess" class="upload-success" style="display:none;">
    ✓ Gambar berhasil dipilih
</div>

        <div class="action-button">
            <button type="submit" class="btn-konfirmasi">Konfirmasi</button>
            <a href="{{ route('item_penyewaan_pusat') }}" class="btn-batal">Batal</a>
        </div>
    </div>
</form>


</div>
<script>
const input = document.getElementById('uploadInput');
const fileText = document.getElementById('fileName');
const successBox = document.getElementById('uploadSuccess');

input.addEventListener('change', function () {
    const file = this.files[0];

    if (file) {
        fileText.textContent = file.name;
        fileText.classList.add('active');

        successBox.style.display = 'block';
    } else {
        fileText.textContent = 'Upload bukti bayar';
        successBox.style.display = 'none';
    }
});
document.querySelectorAll('.timer').forEach(timer => {
    let sisa = parseInt(timer.dataset.time);
    const el = timer.querySelector('.countdown');

    function tick() {
        if (sisa <= 0) {
            el.innerText = '00 : 00 : 00';
            return;
        }

        let h = Math.floor(sisa / 3600);
        let m = Math.floor((sisa % 3600) / 60);
        let s = sisa % 60;

        el.innerText =
            String(h).padStart(2,'0') + ' : ' +
            String(m).padStart(2,'0') + ' : ' +
            String(s).padStart(2,'0');

        sisa--;
    }

    tick();
    setInterval(tick, 1000);
});
</script>
@endsection