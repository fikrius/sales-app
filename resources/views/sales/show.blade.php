@extends('layouts.adminlte')
@section('title', 'Detail Penjualan')

@push('styles')
<style>
.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Penjualan</h3>
        <div class="card-tools">
            @if($sale->status == 'Belum Dibayar')
                @can('sale-update')
                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endcan
            @endif
            <a href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Kode Penjualan</th>
                        <td>: {{ $sale->code }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: {{ \App\Helpers\Helper::formatDate($sale->date) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            @if($sale->status == 'Lunas' || $sale->status == 'Sudah Dibayar')
                                <span class="badge badge-success badge-lg">{{ $sale->status }}</span>
                            @elseif($sale->status == 'Belum Dibayar Sepenuhnya')
                                <span class="badge badge-info badge-lg">{{ $sale->status }}</span>
                            @else
                                <span class="badge badge-warning badge-lg">{{ $sale->status }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <h5 class="mb-3">Daftar Item</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Kode Item</th>
                        <th>Nama Item</th>
                        <th class="text-right">Harga</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $index => $saleItem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $saleItem->item->code }}</td>
                        <td>{{ $saleItem->item->name }}</td>
                        <td class="text-right">{{ \App\Helpers\Helper::formatRupiah($saleItem->price) }}</td>
                        <td class="text-center">{{ $saleItem->qty }}</td>
                        <td class="text-right">{{ \App\Helpers\Helper::formatRupiah($saleItem->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">Total</th>
                        <th class="text-right">{{ \App\Helpers\Helper::formatRupiah($sale->total_price) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <h5 class="mb-3 mt-4">Riwayat Pembayaran</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Kode Pembayaran</th>
                        <th>Tanggal</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sale->payments as $index => $payment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $payment->code }}</td>
                        <td>{{ \App\Helpers\Helper::formatDate($payment->date) }}</td>
                        <td class="text-right">{{ \App\Helpers\Helper::formatRupiah($payment->amount) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Belum ada pembayaran</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($sale->payments->count() > 0)
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Dibayar</th>
                        <th class="text-right">{{ \App\Helpers\Helper::formatRupiah($sale->payments->sum('amount')) }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Sisa</th>
                        <th class="text-right">{{ \App\Helpers\Helper::formatRupiah($sale->total_price - $sale->payments->sum('amount')) }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($sale->status != 'Lunas')
        <div class="mt-3">
            <a href="{{ route('payments.create', ['sale_id' => $sale->id]) }}" class="btn btn-primary">
                <i class="fas fa-money-bill"></i> Tambah Pembayaran
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
