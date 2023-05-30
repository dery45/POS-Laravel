@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('content-header', 'Tambah Produk')

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('products.store') }}" method="POST" action="/upload" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Nama" value="{{ old('name') }}">
                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                    id="description" placeholder="Deskripsi produk">{{ old('description') }}</textarea>
                @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_product">Kategori</label>
                <select name="category_product" class="form-control @error('category_product') is-invalid @enderror" id="category_product">
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
                @error('category_product')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="image">Gambar</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="image" id="image">
                    <label class="custom-file-label" for="image">Pilih File</label>
                </div>
                <img id="image-preview" src="" alt="Preview" style="max-height: 200px; margin-top: 10px;">
                @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" id="brand"
                    placeholder="Brand" value="{{ old('brand') }}">
                @error('brand')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" name="barcode" class="form-control @error('barcode') is-invalid @enderror"
                    id="barcode" placeholder="Barcode" value="{{ old('barcode') }}">
                @error('barcode')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="price">Harga</label>
                <input type="text" name="price" class="form-control @error('price') is-invalid @enderror" id="price"
                    placeholder="Harga Normal" value="{{ old('price') }}">
                @error('price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>



            <div class="form-group">
                <label for="minimum_low">Jumlah minimum harga grosir</label>
                <input type="text" name="minimum_low" class="form-control @error('minimum_low') is-invalid @enderror"
                    id="minimum_low" placeholder="" value="{{ old('minimum_low') }}">
                @error('minimum_low')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>


            <div class="form-group">
                <label for="low_price">Harga grosir</label>
                <input type="text" name="low_price" class="form-control @error('low_price') is-invalid @enderror"
                    id="low_price" placeholder="" value="{{ old('low_price') }}">
                @error('low_price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="stock_price">Harga modal</label>
                <input type="text" name="stock_price" class="form-control @error('stock_price') is-invalid @enderror"
                    id="stock_price" placeholder="" value="{{ old('stock_price') }}">
                @error('stock_price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="quantity">Stok</label>
                <input type="text" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                    id="quantity" placeholder="Quantity" value="{{ old('quantity', 1) }}">
                @error('quantity')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" id="status">
                    <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <button class="btn btn-primary" type="submit">Simpan</button>
        </form>
    </div>
</div>

@yield('js')
@endsection


@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
        $(document).ready(function () {
        bsCustomFileInput.init();

        // Image preview
        $('#image').on('change', function () {
            var file = $(this).get(0).files[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                $('#image-preview').attr('src', reader.result);
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
                // Add the icon and text before uploading an image
                $('#image-preview').attr('src', '');
                $('#image-preview').addClass('fa fa-file-image-o');
                $('#image-preview').text('Gambar belum ada');
            }
        });
    });
</script>
@endsection
