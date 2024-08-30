@extends('Layout.master')

@section('title')
Logo Category
@endsection

@section('content')
<div class="page-content">
<div class="page-header"><h1>Logo Category</h1></div>
@include('term.form')
</div>
@endsection

@section('javascript')
<script>


    $(document).ready(function() {
        //   update_all_account_balance();
    });
</script>
@endsection