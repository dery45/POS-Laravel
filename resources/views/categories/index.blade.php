@extends('layouts.admin')

@section('title', 'List Kategori')
@section('content-header', 'List Kategori')
@section('content-actions')
@hasanyrole('superadmin|inventory')
<a href="{{route('categories.create')}}" class="btn btn-primary">Tambah Kategori</a>
@endhasanyrole
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<div class="card category-list">
    <div class="card-body">
        <table class="table">
            <!-- Import Modal -->

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Kategori</th>
                    <th>Tgl Dibuat</th>
                    <th>Tgl Diubah</th>
                    @hasanyrole('superadmin|inventory')
                    <th>Aksi</th>
                    @endhasanyrole
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->created_at }}</td>
                    <td>{{ $category->updated_at }}</td>
                    @hasanyrole('superadmin|inventory')
                    <td>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        <button class="btn btn-danger btn-delete"
                            data-url="{{ route('categories.destroy', $category) }}"><i class="fas fa-trash"></i>
                        </button>
                    </td>
                    @endhasanyrole
                </tr>
                @endforeach
            </tbody>
        </table>
         <!-- Add pagination links -->
        {{ $categories->links() }}
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $(document).on('click', '.btn-delete', function () {
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Apakah Anda yakin?',
                text: "Kategori akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.post($this.data('url'), { _method: 'DELETE', _token: '{{ csrf_token() }}' }, function (res) {
                        $this.closest('tr').fadeOut(500, function () {
                            $(this).remove();
                        })
                    })
                }
            })
        })
    })
</script>
@endsection
