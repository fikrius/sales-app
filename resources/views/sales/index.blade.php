@extends('layouts.adminlte')
@section('title','Sales')
@section('content')

<div class="row">
    <!-- Recent Orders History -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Riwayat Pesanan Terbaru</h3>
            </div>
            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                <ul class="list-group list-group-flush">
                    @forelse($recentSales as $sale)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <a href="{{ route('sales.show', $sale->id) }}">{{ $sale->code }}</a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ \App\Helpers\Helper::formatDate($sale->date, 'd/m/Y H:i') }}
                                </small>
                                <div class="mt-1">
                                    @if($sale->status == 'Lunas' || $sale->status == 'Sudah Dibayar')
                                        <span class="badge badge-success">{{ $sale->status }}</span>
                                    @elseif($sale->status == 'Belum Dibayar Sepenuhnya')
                                        <span class="badge badge-info">{{ $sale->status }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ $sale->status }}</span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <strong>Items:</strong> {{ $sale->items->count() }} item(s)
                                    </small>
                                </div>
                                <div class="mt-2">
                                    <strong class="text-primary d-block">{{ \App\Helpers\Helper::formatRupiah($sale->total_price) }}</strong>
                                </div>
                            </div>
                            <div class="ml-2">
                                @if($sale->status == 'Belum Dibayar')
                                    @can('sale-update')
                                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('sale-delete')
                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus penjualan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted">
                        <i class="fas fa-inbox"></i> Belum ada pesanan
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="col-md-8">
        <div class='card'>
            <div class='card-header'>
                <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Daftar Penjualan</h3>
                <div class="card-tools">
                    @can('sale-create')
                    <a href='{{ route("sales.create") }}' class='btn btn-sm btn-primary'>
                        <i class="fas fa-plus"></i> Tambah Sale
                    </a>
                    @endcan
                </div>
            </div>
            <div class='card-body'>
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
                <label>Status</label>
                <select id="status_filter" class="form-control form-control-sm">
                    <option value="">Semua</option>
                    <option value="Belum Dibayar">Belum Dibayar</option>
                    <option value="Belum Dibayar Sepenuhnya">Belum Dibayar Sepenuhnya</option>
                    <option value="Sudah Dibayar">Sudah Dibayar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <div>
                    <button id="btn-filter" class="btn btn-sm btn-info">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button id="btn-reset" class="btn btn-sm btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div>
        <table id='sales-table' class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
$(function(){
    var table = $('#sales-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/api/datatables/sales',
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
            {data: 4}
        ]
    });

    $('#btn-filter').click(function() {
        table.draw();
    });

    $('#btn-reset').click(function() {
        $('#start_date').val('');
        $('#end_date').val('');
        $('#status_filter').val('');
        table.draw();
    });
});
</script>
@endpush
