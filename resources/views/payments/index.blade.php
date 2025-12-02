@extends('layouts.adminlte')
@section('title','Payments')
@section('content')
<div class='card'>
    <div class='card-header'>
        <h3 class="card-title"><i class="fas fa-money-bill"></i> Daftar Pembayaran</h3>
        <div class="card-tools">
            @can('payment-create')
            <a href='{{ route("payments.create") }}' class='btn btn-sm btn-primary'>
                <i class="fas fa-plus"></i> Tambah Payment
            </a>
            @endcan
        </div>
    </div>
    <div class='card-body'>
        <!-- Date Filter -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Tanggal Mulai</label>
                <input type="date" id="start_date" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label>Tanggal Akhir</label>
                <input type="date" id="end_date" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label>Status Sale</label>
                <select id="status_filter" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="Belum Dibayar">Belum Dibayar</option>
                    <option value="Belum Dibayar Sepenuhnya">Belum Dibayar Sepenuhnya</option>
                    <option value="Sudah Dibayar">Sudah Dibayar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label><br>
                <button id="filter-btn" class="btn btn-sm btn-info">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button id="reset-btn" class="btn btn-sm btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>
        
        <table id='payments-table' class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Sale</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status Sale</th>
                    <th width="150">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(function(){ 
    var table = $('#payments-table').DataTable({ 
        processing: true, 
        serverSide: true, 
        ajax: { 
            url: '/api/datatables/payments', 
            type: 'POST', 
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.status = $('#status_filter').val();
            }
        }, 
        columns: [
            {data: 0},
            {data: 1},
            {data: 2},
            {data: 3},
            {data: 4},
            {data: 5, orderable: false, searchable: false}
        ]
    });
    
    $('#filter-btn').click(function(){
        table.draw();
    });
    
    $('#reset-btn').click(function(){
        $('#start_date').val('');
        $('#end_date').val('');
        $('#status_filter').val('');
        table.draw();
    });
});
</script>
@endpush
