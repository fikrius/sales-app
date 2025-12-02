@extends('layouts.adminlte')
@section('title', 'Edit Payment')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> Edit Payment</h3>
    </div>
    <form action="{{ route('payments.update', $payment->id) }}" method="POST">
        @csrf
        @method('PUT')
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

            <div class="form-group">
                <label>Kode Payment</label>
                <input type="text" class="form-control" value="{{ $payment->code }}" disabled>
            </div>

            <div class="form-group">
                <label>Sale</label>
                <select name="sale_id" class="form-control" required disabled>
                    <option value="{{ $payment->sale_id }}">{{ $payment->sale->code }} - {{ \App\Helpers\Helper::formatRupiah($payment->sale->total_price) }}</option>
                </select>
                <small class="text-muted">Sale tidak dapat diubah</small>
            </div>

            <div class="form-group">
                <label>Tanggal <span class="text-danger">*</span></label>
                <input type="datetime-local" name="date" class="form-control" value="{{ date('Y-m-d\TH:i', strtotime($payment->date)) }}" required>
            </div>

            <div class="form-group">
                <label>Jumlah Bayar <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" value="{{ $payment->amount }}" required min="1">
                <small class="text-muted">
                    Total Sale: {{ \App\Helpers\Helper::formatRupiah($payment->sale->total_price) }} | 
                    Sudah Dibayar: {{ \App\Helpers\Helper::formatRupiah($payment->sale->payments->where('id', '!=', $payment->id)->sum('amount')) }}
                </small>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection
