@extends('Layout.master')

@section('title')
Templates
@endsection

@section('content')

<div class="page-content">

<div class="page-header" style="min-height:40px;">
    <div class="" style="float: left;">
        <h1>Templates</h1>
    </div>
    <div class="" style="float: right;">
        <a href="{{ route('post.logo.create') }}" class="btn btn-xs btn-light bigger"><i class="ace-icon fa fa-floppy-o"></i> Add Record </a> 
    </div>
</div>




<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px;">

        <div class="card radius-10 border-top border-0 border-4 border-danger">

        <div class="card-header">
            <div class="row">
            <form action="#" id="search-form">
            <div class="col-lg-6 col-sm-6">
                <label class="" for="form-field-1"> Category</label>
                <div>
                    <select name="category[]" id="category_id" class="form-control select21" onchange="applyFilter();"  style="width:100%">
                            <option value=""> All </option>
                        <?php foreach ($category as $key => $val) { ?>
                            <option value="<?= $key ?>" <?= (isset($post_category) && in_array($key, $post_category)) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            </form>
            </div>
            <div class="space-4"></div>
        </div>

            <div class="card-body">

                @include('Layout.alerts')

                <div class="table-responsive">
                    <table class="table table-striped table-bordered yajratable" id="yajra-table" style="width:100%"></table>
                </div>
            </div>



        </div>
    </div>
</div>
<!--end row-->

</div>

<!--- delete confirmation modal --->
<div class="modal" tabindex="-1" role="dialog" id="deletePopup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="delete_form">
                <input type="hidden" name="_method" value="delete" />
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete ?</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!--- delete confirmation modal end --->
@endsection




@section('script')
<script>
    var table;
    var selectID;


    function doChangeStatus(objID){
        var changeURL = "{{ route('post.logo.status','') }}";
        $.ajax({
            dataType: 'json',
            type:'get',
            url : changeURL + '/' + objID,
            success:function(res){
                console.log('yes');
                if(res.status){
                    table.ajax.reload();
                    $('#statusPopup').modal('hide');
                } 
            }
        });
    }

    function deleteConfirmation(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        var changeURL = "{{ route('post.logo.delete','') }}";
                        window.location = changeURL + '/' + selectID;
                    }
                },
                no: {
                    text: 'No', // With spaces and symbols
                    action: function() {

                    }
                }
            }
        });
    }


    function changeStatus(id) {
        selectID = id;
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to change status ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        doChangeStatus(id)
                    }
                },
                no: {
                    text: 'No', // With spaces and symbols
                    action: function() {

                    }
                }
            }
        });
    }

    $(document).ready(function() {

        // var categoryId = $('#category_id').val();

        table = $('#yajra-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url : "{{ route('post.logo.list') }}",
                data : function(q){
                q.category_id = $('#category_id').val();
            },
            },
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    title: 'ID',
                   
                },
                {
                    data: 'thumbnail_url',
                    name: 'thumbnail_url',
                    title: 'Thumbnail',
                    render: function (data, type, row, meta) {
                        return '<img src="' + data + '" width="75" height="75" onclick="viewImageLargeView(this);" >';
                    }
                },
                {
                    data: 'post_title',
                    name: 'post_title',
                    title: 'Title'
                },
                {
                    data: 'categories',
                    name: 'categories',
                    title: 'Category'
                },
                // {
                //     data: 'user_name',
                //     name: 'user_name',
                //     title: 'Author'
                // },
                {
                    data: 'status_label',
                    name: 'status',
                    title: 'Status'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: 'Created At'
                },
                {
                    data: 'action',
                    name: 'action',
                    title: 'Action'
                }
            ],
            // order: [[1, 'asc']]
        });


        


    });

    function applyFilter(){
            $('#yajra-table').DataTable().ajax.reload();
        }
</script>
@endsection