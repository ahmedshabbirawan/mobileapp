@extends('Layout.master')

@section('title')
Post
@endsection

@section('content')
<div class="page-content">
<div class="page-header"><h1>Post</h1></div>
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