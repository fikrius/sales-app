@extends('layouts.adminlte')
@section('title','Edit Item')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit"></i> Edit Item - {{ $item->code }}</h3>
    </div>
    <form method='post' action='{{ route("items.update", $item->id) }}' enctype="multipart/form-data" id="item-form">
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

            <div class='form-group'>
                <label>Kode Item</label>
                <input type="text" class='form-control' value="{{ $item->code }}" disabled/>
                <small class="text-muted"><i class="fas fa-info-circle"></i> Kode tidak dapat diubah</small>
            </div>

            <div class='form-group'>
                <label>Nama Item <span class="text-danger">*</span></label>
                <input type="text" name='name' class='form-control' value="{{ old('name', $item->name) }}" required/>
            </div>

            <div class='form-group'>
                <label>Harga <span class="text-danger">*</span></label>
                <input type="number" name='price' class='form-control' value="{{ old('price', $item->price) }}" min="0" required/>
            </div>

            <div class='form-group'>
                <label>Gambar Produk</label>
                
                @if($item->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-thumbnail" style="max-width: 200px;">
                    <p class="text-muted mt-1"><small>Gambar saat ini</small></p>
                </div>
                @endif
                
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                    <label class="custom-file-label" for="image">{{ $item->image ? 'Pilih gambar baru...' : 'Pilih gambar...' }}</label>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> Format: JPG, PNG, JPEG. Gambar akan otomatis di-resize maksimal 100KB. Kosongkan jika tidak ingin mengubah gambar.
                </small>
                <div id="image-preview" class="mt-2" style="display:none;">
                    <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    <p class="text-muted mt-1">Ukuran: <span id="file-size">0 KB</span></p>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class='btn btn-primary'>
                <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    // Update file input label
    $('.custom-file-input').on('change', function(){
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        
        // Show preview
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
                
                // Show file size
                var sizeKB = (input.files[0].size / 1024).toFixed(2);
                $('#file-size').text(sizeKB + ' KB');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
});
</script>
@endpush
