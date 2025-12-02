@extends('layouts.adminlte')
@section('title','Items')
@section('content')
<div class='card'>
    <div class='card-header'>
        <h3 class="card-title"><i class="fas fa-box"></i> Daftar Item</h3>
        <div class="card-tools">
            @can('item-create')
            <a href='{{ route("items.create") }}' class='btn btn-sm btn-primary'>
                <i class="fas fa-plus"></i> Tambah Item
            </a>
            @endcan
        </div>
    </div>
    <div class='card-body'>
        <table id='items-table' class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th width="80">Gambar</th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Price</th>
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
    $('#items-table').DataTable({ 
        processing: true, 
        serverSide: true, 
        ajax: { 
            url: '/api/datatables/items', 
            type: 'POST', 
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} 
        }, 
        columns: [
            {data: 0, orderable: false, searchable: false},
            {data: 1},
            {data: 2},
            {data: 3},
            {data: 4, orderable: false, searchable: false}
        ]
    }); 
});
</script>
@endpush
