<!-- resources/views/categories/create.blade.php -->

@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('content-header', 'Tambah Kategori')

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="category_name">Nama Kategori</label>
                    <input type="text" name="category_name" class="form-control @error('category_name') is-invalid @enderror" id="category_name"
                        placeholder="Nama Kategori" value="{{ old('category_name') }}">
                    @error('category_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button class="btn btn-primary" type="submit">Simpan</button>
            </form>
        </div>
    </div>

@endsection

@section('js')
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
    $(document).ready(function () {
        bsCustomFileInput.init();
    });
</script>
@endsection
