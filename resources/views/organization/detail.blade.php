@extends('Layout.master')

@section('title')
Detail
@endsection

@section('content')
<style>
    .profile-info-name{
        width: 190px;
    }
</style>

<div class="page-content">
<div class="page-header"><h1>Organization</h1></div>

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">
        <div class="card-body">
                <a href="{{ route('Settings.org.list') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon "></i> Back </a> 
        </div>

            <div class="row">

                <div class="col-xs-12 col-sm-12">
                    <div class="widget-box">
                        <div class="widget-header">
                            <h4 class="widget-title">Detail</h4>
                        </div>
                        <div class="widget-body" style="display: block;">

                            <div class="profile-user-info profile-user-info-striped">
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> ID </div>

                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $org->id }}</span>
                                    </div>
                                </div>

                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Name </div>

                                    <div class="profile-info-value">
                                        <span class="editable editable-click" id="username">{{ $org->name }}</span>
                                    </div>
                                </div>

                        </div>


                    </div>
                </div><!-- /.span -->
            </div>





        </div>
    </div>
</div>
<!--end row-->

</div>

@endsection

@section('script')
<script>

    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection