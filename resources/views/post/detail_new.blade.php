@extends('Layout.master')

@section('title','Product Details')
@section('Title','Products')
@section('URL',route("post.logo.list"))
@section('PageName','Product Details')


@section('content')
<div class="page-content">
    <div class="page-header">
        <h1>
            @yield('PageName')
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i>
                {{ $row->name }}
            </small>
        </h1>
    </div><!-- /.page-header -->


    <div class="row">

    <div class="col-xs-3 col-sm-3">

    <img src="{{ $thumbnail_url }}" width="200" height="200" class="img-thumbnail" onclick="viewImageLargeView(this);" >

    </div>

        <div class="col-xs-9 col-sm-9">
            <h4 class="blue">
                <span class="middle">{{ $row->post_title }}</span>
            </h4>

            <div class="profile-user-info profile-user-info-striped">
                <div class="profile-info-row">
                    <div class="profile-info-name"> Title </div>
                    <div class="profile-info-value"><span>{{ $row->post_title }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Categories </div>
                    <div class="profile-info-value"><span>@foreach($categories as $cat) {{ $cat->name }}  @endforeach</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Created Date & Time </div>
                    <div class="profile-info-value"><span>{{ (isset($row->created_at))?$row->created_at:'' }}</span></div>
                </div>
                <div class="profile-info-row">
                    <div class="profile-info-name"> Description </div>
                    <div class="profile-info-value"><span>{{ $row->description }}</span></div>
                </div>
            </div>
        </div>
    </div>

    





</div>


@endsection



@section('javascript')
<script>
	var table;
	var selectID;
    var historyTable;
</script>

@yield('delivery_script')
@yield('suppliers_script')
@yield('items_script')
@yield('shop_wise_stock_script')
<script>
function historyTableReload(itemID){
    $('#item_id').val(itemID);
    historyTable.ajax.reload();
    $('#item-history-btn').click();
}
</script>
@endsection