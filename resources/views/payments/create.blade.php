@extends('layouts.adminlte')
@section('title','Tambah Payment')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-money-bill"></i> Tambah Payment</h3>
    </div>
    <form method='post' action='{{ route("payments.store") }}'>
        @csrf
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class='form-group'>
                <label>Sale <span class="text-danger">*</span></label>
                <select name='sale_id' id='sale_id' class='form-control select2' required style="width: 100%;">
                    <option value="">-- Pilih Sale --</option>
                    @foreach($sales as $s)
                    @php
                        $paid = $s->payments->sum('amount');
                        $remaining = $s->total_price - $paid;
                    @endphp
                    <option value='{{ $s->id }}' 
                            data-total="{{ $s->total_price }}"
                            data-paid="{{ $paid }}"
                            data-remaining="{{ $remaining }}">
                        {{ $s->code }} - Total: {{ \App\Helpers\Helper::formatRupiah($s->total_price) }} | Sisa: {{ \App\Helpers\Helper::formatRupiah($remaining) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div id="payment-info" class="alert alert-info" style="display:none;">
                <strong>Informasi Tagihan:</strong><br>
                Total Tagihan: <strong><span id="info-total">Rp 0</span></strong><br>
                Sudah Dibayar: <span id="info-paid">Rp 0</span><br>
                Sisa Tagihan: <strong class="text-danger"><span id="info-remaining">Rp 0</span></strong>
            </div>

            <div class='form-group'>
                <label>Jumlah Bayar <span class="text-danger">*</span></label>
                <input type="number" name='amount' id='amount' class='form-control' required min="1" step="1"/>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Anda dapat melakukan pembayaran sebagian (split payment). Masukkan jumlah yang dibayarkan.
                </small>
            </div>

            <div class="form-group">
                <button type="button" id="btn-pay-full" class="btn btn-success btn-sm" style="display:none;">
                    <i class="fas fa-money-bill-wave"></i> Bayar Penuh (Sisa)
                </button>
                <button type="button" id="btn-pay-half" class="btn btn-info btn-sm" style="display:none;">
                    <i class="fas fa-divide"></i> Bayar 50%
                </button>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class='btn btn-primary'>
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function(){
    // Initialize Select2 with search
    $('#sale_id').select2({
        theme: 'bootstrap4',
        placeholder: '-- Pilih Sale --',
        allowClear: true
    });

    // When sale is selected
    $('#sale_id').change(function(){
        var option = $(this).find(':selected');
        var total = option.data('total');
        var paid = option.data('paid');
        var remaining = option.data('remaining');
        
        if(remaining && remaining > 0) {
            $('#payment-info').show();
            $('#btn-pay-full').show();
            $('#btn-pay-half').show();
            
            // Update info display
            $('#info-total').text('Rp ' + total.toLocaleString('id-ID'));
            $('#info-paid').text('Rp ' + paid.toLocaleString('id-ID'));
            $('#info-remaining').text('Rp ' + remaining.toLocaleString('id-ID'));
            
            // Set max attribute
            $('#amount').attr('max', remaining);
            $('#amount').val('');
            
            // Store remaining in data attribute
            $('#amount').data('remaining', remaining);
        } else {
            $('#payment-info').hide();
            $('#btn-pay-full').hide();
            $('#btn-pay-half').hide();
            $('#amount').val('');
        }
    });

    // Pay full remaining amount button
    $('#btn-pay-full').click(function(){
        var remaining = $('#amount').data('remaining');
        $('#amount').val(remaining);
    });

    // Pay 50% of remaining amount button
    $('#btn-pay-half').click(function(){
        var remaining = $('#amount').data('remaining');
        var half = Math.floor(remaining / 2);
        $('#amount').val(half);
    });

    // Validate amount on input
    $('#amount').on('input', function(){
        var remaining = $(this).data('remaining');
        var value = parseInt($(this).val()) || 0;
        
        if(value > remaining) {
            $(this).val(remaining);
            alert('Jumlah pembayaran tidak boleh melebihi sisa tagihan!');
        }
        
        if(value < 0) {
            $(this).val(1);
        }
    });
});
</script>
@endpush
