@extends('Layout.master')

@section('title')
Template
@endsection

@section('content')
<div class="page-content">
<div class="page-header"><h1>Template Update</h1></div>
@include('post.form')
</div>
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection