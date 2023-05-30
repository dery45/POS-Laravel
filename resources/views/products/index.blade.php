@extends('layouts.admin')

@section('title', 'List Produk')
@section('content-header', 'List Produk')
@section('content-actions')
<button class="btn btn-success" data-toggle="modal" data-target="#importModal">Import CSV</button>
<a href="{{route('products.create')}}" class="btn btn-primary">Tambah Data</a>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
<!-- Add the search bar and button -->
<div class="mb-3">
    <form id="searchForm" action="{{ route('products.index') }}" method="GET" class="form-inline">
        <div class="input-group">
            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>
</div>
<div class="card product-list">
    <div class="card-body">
        <table class="table">
            <!-- Import Modal -->
            <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Produk</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="csvFile">CSV File:</label>
                                    <a href="{{ asset('csv/template-product.csv') }}">Download CSV Template</a>
                                    <input type="file" class="form-control-file" id="csvFile" name="csvFile"
                                        accept=".csv">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Gambar</th>
                    <th>Barcode</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Tgl Dibuat</th>
                    <th>Tgl Diubah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->name}}</td>
                    <td><img class="product-img" src="{{ Storage::url($product->image) }}" alt=""></td>
                    <td>{{$product->barcode}}</td>
                    <td>{{$product->price}}</td>
                    <td>{{$product->quantity}}</td>
                    <td>
                        <span class="right badge badge-{{ $product->status ? 'success' : 'danger' }}">
                            {{$product->status ? 'Active' : 'Inactive'}}
                        </span>
                    </td>
                    <td>{{$product->created_at}}</td>
                    <td>{{$product->updated_at}}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-delete" data-url="{{route('products.destroy', $product)}}" data-token="{{csrf_token()}}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $products->render() }}
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
         // Function to fetch search results and update the autocomplete dropdown
         function fetchSearchResults(query, callback) {
            $.ajax({
                url: '{{ route("autocomplete.search") }}',
                dataType: 'json',
                data: {
                    term: query
                },
                success: function(data) {
                    callback(data);
                }
            });
        }

        // Initialize autocomplete
        $('#searchInput').autocomplete({
            source: function(request, response) {
                fetchSearchResults(request.term, function(data) {
                    response(data);
                });
            },
            minLength: 2, // Minimum characters before autocomplete starts
            select: function(event, ui) {
                // Redirect to the selected product page
                window.location.href = '{{ route("products.show", ["product" => ":id"]) }}'.replace(':id', ui.item.id);
            }
        });

        // Update search results on keyup
        $('#searchInput').on('keyup', function() {
            var query = $(this).val();
            fetchSearchResults(query, function(data) {
                // Update autocomplete dropdown
                $('#searchInput').autocomplete('option', 'source', data);
            });
        });

        $(document).on('click', '.btn-delete', function () {
            $this = $(this);
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: 'Hapus?',
                text: "Apakah anda yakin ingin menhpaus data?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus data!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    var url = $this.data('url');
                    var token = $this.data('token');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: token
                        },
                        success: function (res) {
                            $this.closest('tr').fadeOut(500, function () {
                                $(this).remove();
                            });
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
