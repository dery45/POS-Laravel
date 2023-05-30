@extends('layouts.admin')

@section('title', 'Kasir')

@section('css')
<style>
    .infocart{
        margin-bottom: 2%; 
        margin-right: 1%; 
        width: 10%
    }
</style>
@endsection

@section('content')
    <div id="info" class="container-fluid">
        <div class="row mb-4">
            <div class="col-2"><input id="daily" class="form-control" type="text" name="" value="Modal Harian : {{config('settings.currency_symbol')}} {{number_format($capitalValue, 2)}}" readonly></div>
            <div class="col-2"><input id="cash" class="form-control" type="text" name="" value="Cash Income : {{config('settings.currency_symbol')}} {{number_format($cashIn, 2)}}" readonly></div>
            <div class="col-2"><input id="cashless" class="form-control" type="text" name="" value="Cahsless Income : {{config('settings.currency_symbol')}} {{number_format($cashlessIn, 2)}}" readonly></div>
            <div class="col-2"><input id="total" class="form-control" type="text" name="" value="Total Income : {{config('settings.currency_symbol')}} {{number_format($pendapatan, 2)}}" readonly></div>
        </div>
    </div>
    <button id="btnmodal" class="btn btn-primary" style="width: 30%;">Modal Harian</button>
    <div class="modal fade" id="ModalHarian" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Input Modal Harian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cart.capital') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="csvFile">Modal Harian (Rp) :</label>
                        <input type="text" class="form-control" id="capital" name="capital">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <div id="cart"></div>
@endsection
@section('js')
<script>
    $( document ).ready(function() {
        console.log( "document loaded" );
        if({{$capitalValue}}<1){
            $("#ModalHarian").modal('show');
            $("#info").hide();
            $("#cart").hide();
            $("#btnmodal").show();
        }
        else{
            $("#ModalHarian").modal('hide');
            $("#info").show();
            $("#cart").show();
            $("#btnmodal").hide();
        }
        $("#btnmodal").on("click", function(){
            $("#ModalHarian").modal('show');
        });
    });
</script>
@endsection