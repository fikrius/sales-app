@extends('layouts.adminlte')
@section('title', 'Detail Payment')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-money-bill"></i> Detail Payment</h3>
        <div class="card-tools">
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Kode Payment</th>
                        <td>: {{ $payment->code }}</td>
                    </tr>
                    <tr>
                        <th>Kode Sale</th>
                        <td>: <a href="{{ route('sales.show', $payment->sale_id) }}">{{ $payment->sale->code }}</a></td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>: {{ \App\Helpers\Helper::formatDate($payment->date) }}</td>
                    </tr>
                    <tr>
                        <th>Jumlah Bayar</th>
                        <td>: <strong class="text-success">{{ \App\Helpers\Helper::formatRupiah($payment->amount) }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle"></i> Informasi Sale</h5>
                    <p><strong>Total Harga:</strong> {{ \App\Helpers\Helper::formatRupiah($payment->sale->total_price) }}</p>
                    <p><strong>Total Dibayar:</strong> {{ \App\Helpers\Helper::formatRupiah($payment->sale->payments->sum('amount')) }}</p>
                    <p><strong>Status:</strong> 
                        @if($payment->sale->status == 'Sudah Dibayar' || $payment->sale->status == 'Lunas')
                            <span class="badge badge-success">{{ $payment->sale->status }}</span>
                        @elseif($payment->sale->status == 'Belum Dibayar Sepenuhnya')
                            <span class="badge badge-info">{{ $payment->sale->status }}</span>
                        @else
                            <span class="badge badge-warning">{{ $payment->sale->status }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        @can('payment-update')
        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        @endcan
        @can('payment-delete')
        @if($payment->sale->status == 'Belum Dibayar')
        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus payment ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </form>
        @endif
        @endcan
    </div>
</div>
@endsection
