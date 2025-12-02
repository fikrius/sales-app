@extends('layouts.adminlte')
@section('title','Tambah Sale')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tambah Penjualan Baru</h3>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Error!</h5>
            {{ session('error') }}
        </div>
        @endif
        
        <form method='post' action='{{ route("sales.store") }}' id="sale-form">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Item</label>
                </div>
                <div class="col-md-2">
                    <label>Qty</label>
                </div>
                <div class="col-md-3">
                    <label>Harga</label>
                </div>
                <div class="col-md-3">
                    <label>Subtotal</label>
                </div>
            </div>
            
            <div id='items-area'>
                <div class='row mb-2 sale-row'>
                    <div class='col-md-4'>
                        <select name='items[0][item_id]' class='form-control item-select' required>
                            <option value="">-- Ketik untuk mencari item --</option>
                        </select>
                    </div>
                    <div class='col-md-2'>
                        <input type="number" name='items[0][qty]' class='form-control qty-input' value='1' min="1" required/>
                    </div>
                    <div class='col-md-3'>
                        <input type="number" name='items[0][price]' class='form-control price-input' required readonly/>
                    </div>
                    <div class='col-md-2'>
                        <input type="text" class='form-control subtotal-display' readonly/>
                    </div>
                    <div class='col-md-1'>
                        <button type='button' class='btn btn-danger btn-sm remove-row' disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <button type='button' id='add-row' class='btn btn-secondary btn-sm'>
                        <i class="fas fa-plus"></i> Tambah Item
                    </button>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-9 text-right">
                    <h5><strong>Total:</strong></h5>
                </div>
                <div class="col-md-3">
                    <input type="text" id="grand-total" class="form-control" readonly/>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <a href="{{ route('sales.index') }}" class='btn btn-secondary'>
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class='btn btn-primary'>
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let idx = 1;

function initSelect2(element) {
    $(element).select2({
        theme: 'bootstrap',
        placeholder: '-- Ketik untuk mencari item --',
        allowClear: true,
        ajax: {
            url: '/api/search/items',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        minimumInputLength: 0,
        language: {
            inputTooShort: function() {
                return 'Ketik untuk mencari item...';
            },
            searching: function() {
                return 'Mencari...';
            },
            noResults: function() {
                return 'Item tidak ditemukan';
            }
        }
    });
}

function calculateSubtotal(row) {
    let qty = parseInt(row.find('.qty-input').val()) || 0;
    let price = parseInt(row.find('.price-input').val()) || 0;
    let subtotal = qty * price;
    row.find('.subtotal-display').val(subtotal.toLocaleString('id-ID'));
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let total = 0;
    $('.sale-row').each(function() {
        let qty = parseInt($(this).find('.qty-input').val()) || 0;
        let price = parseInt($(this).find('.price-input').val()) || 0;
        total += qty * price;
    });
    $('#grand-total').val(total.toLocaleString('id-ID'));
}

$(document).on('select2:select', '.item-select', function(e) {
    let row = $(this).closest('.sale-row');
    let data = e.params.data;
    let price = data.price;
    row.find('.price-input').val(price);
    calculateSubtotal(row);
});

$(document).on('input', '.qty-input', function() {
    let row = $(this).closest('.sale-row');
    calculateSubtotal(row);
});

$(document).on('click', '.remove-row', function() {
    $(this).closest('.sale-row').remove();
    calculateGrandTotal();
    updateRemoveButtons();
});

function updateRemoveButtons() {
    let rowCount = $('.sale-row').length;
    if(rowCount === 1) {
        $('.remove-row').prop('disabled', true);
    } else {
        $('.remove-row').prop('disabled', false);
    }
}

$('#add-row').on('click', function() {
    // Create new row from scratch instead of cloning
    let newRow = `<div class='row mb-2 sale-row'>
        <div class='col-md-4'>
            <select name='items[${idx}][item_id]' class='form-control item-select-new' required>
                <option value="">-- Ketik untuk mencari item --</option>
            </select>
        </div>
        <div class='col-md-2'>
            <input type="number" name='items[${idx}][qty]' class='form-control qty-input' value='1' min="1" required/>
        </div>
        <div class='col-md-3'>
            <input type="number" name='items[${idx}][price]' class='form-control price-input' required readonly/>
        </div>
        <div class='col-md-2'>
            <input type="text" class='form-control subtotal-display' readonly/>
        </div>
        <div class='col-md-1'>
            <button type='button' class='btn btn-danger btn-sm remove-row'>
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>`;
    
    $('#items-area').append(newRow);
    
    // Initialize Select2 on the newly added select
    let newSelect = $('.item-select-new').last();
    initSelect2(newSelect);
    newSelect.removeClass('item-select-new').addClass('item-select');
    
    idx++;
    updateRemoveButtons();
});

// Form validation before submit
$('#sale-form').on('submit', function(e) {
    let hasError = false;
    let errorMsg = '';
    
    // Check if at least one item is selected
    let itemCount = 0;
    $('.sale-row').each(function() {
        let itemId = $(this).find('.item-select').val();
        let price = $(this).find('.price-input').val();
        
        if (itemId && price) {
            itemCount++;
        }
    });
    
    if (itemCount === 0) {
        hasError = true;
        errorMsg = 'Minimal 1 item harus dipilih dan memiliki harga!';
    }
    
    // Check if all rows have item selected and price filled
    $('.sale-row').each(function() {
        let itemId = $(this).find('.item-select').val();
        let price = $(this).find('.price-input').val();
        
        if (!itemId || !price || price == 0) {
            hasError = true;
            errorMsg = 'Semua item harus dipilih dan memiliki harga yang valid!';
            return false;
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert(errorMsg);
        return false;
    }
    
    return true;
});

// Initialize
$(document).ready(function() {
    initSelect2('.item-select');
    updateRemoveButtons();
});
</script>
@endpush
@endsection
