@extends('layouts.adminlte')
@section('title', 'Detail Item')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box"></i> Detail Item</h3>
        <div class="card-tools">
            <a href="{{ route('items.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid img-thumbnail" style="max-width: 300px;">
                @else
                    <div class="border p-5 bg-light">
                        <i class="fas fa-image fa-5x text-muted"></i>
                        <p class="mt-3 text-muted">No Image</p>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Kode Item</th>
                        <td>: <strong>{{ $item->code }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nama Item</th>
                        <td>: {{ $item->name }}</td>
                    </tr>
                    <tr>
                        <th>Harga</th>
                        <td>: <strong class="text-success">{{ \App\Helpers\Helper::formatRupiah($item->price) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>: {{ \App\Helpers\Helper::formatDate($item->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>Diupdate</th>
                        <td>: {{ \App\Helpers\Helper::formatDate($item->updated_at) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer">
        @can('item-update')
        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
        </a>
        @endcan
        @can('item-delete')
        <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </form>
        @endcan
    </div>
</div>
@endsection
