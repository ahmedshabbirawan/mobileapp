<meta name="csrf-token" content="{{ csrf_token() }}" />


<?php
$subCategoryID      = '';
$name = '';
$productID = '';

$actionURL = isset($row) ? route('post.logo.update', $row->id) : route('post.logo.store');
$sub_category = [];

if (isset($product)) {
    $subCategoryID = [];
    $name = $product->post_title;
    $actionURL = route('post.logo.update', $product->id);
    $productID = $product->id;
}
?>


<div class="row ">
    <div class="col-lg-12 " style="align-content: center;">
        @include('Layout.alerts')
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <form method="post" id="product_form" action="{{ isset($row) ? route('post.logo.update', $row->id) : route('post.logo.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="id" value="{{ $productID }}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-lg-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">Meta</h4>
                            </div>
                            <div class="widget-body">

                                <div class="widget-main">

                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Title: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($row))? $row->post_title : '' )}}" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div>
                                    <div class="space"></div>

                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Category : </label>
                                        <div>
                                            <select name="category[]" id="sub_category_id" class="chosen-select form-control select21" multiple required style="width:100%">
                                                <?php foreach ($category as $key => $val) { ?> 
                                                    <option value="<?= $key ?>" <?= (isset($post_category) && in_array($key,$post_category)) ? 'selected="selected"' : '' ?>><?= $val ?></option> 
                                                <?php } ?>    
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-4">
                                        <label for="file_2">Product Image</label>
                                        <input type="file" class="form-control" name="product_image" id="file_2" />
                                    </div>

                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Status </label>
                                        <div class="">
                                            {{ \App\Util\Form::statusSelect(old('status', (isset($row))?$row->status:'' )) }}
                                        </div>
                                    </div>


                                    <div class="space-4"></div>
                                    <div style="clear:both;"></div>






                                </div>
                            </div>



                            <!----------------------------------------------------------------------------------------------------------->
                            <div class="widget-header">
                                <h4 class="widget-title">Sub Views</h4>

                                <div class="widget-toolbar"><button class="btn btn-primary btn-xs" type="button" onclick="openAddSubViewPop();">
                                        <i class="fa fa-plus"></i>
                                        Add Subview
                                    </button></div>

                            </div>
                            <div class="widget-body">
                                <div class="widget-main">
                                    <div class="space-4"></div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <table id="subview-table" class="table  table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="center">
                                                         Type
                                                        </th>
                                                        <th>Frame</th>
                                                        <th>Text</th>
                                                        <th>Font Name</th>
                                                        <th>Font Size</th>
                                                        <th>Text Color</th>
                                                        <th>Image Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php 
                                                    $index = 0;
                                                    ?>
                                                    @if(isset($subview))
                                                    @foreach($subview as $view)
                                                    <tr id="tr_{{$index}}" >
        <td class=""><select name="type[]" style="pointer-events: none;"><option value="Image" `+imageView+` >Image</option><option value="Label" `+labelView+` >Label</option></select></td>
        <td>
            <input type="hidden" value="{{$view->id}}" name="sub_view_id[]" >
            <input type="text" value="{{$view->frame}}" name="frame[]" class="subview_input" >
        </td>
        <td><input type="text" value="{{$view->text}}" name="text[]" class="subview_input" ></td>
        <td><input type="text" value="{{$view->font_name}}" name="font_name[]" class="subview_input" ></td>
        <td><input type="text" value="{{$view->font_size}}" name="font_size[]" class="subview_input" ></td>
        <td><input type="text" value="{{$view->text_color}}" name="text_color[]" class="subview_input" ></td>
        <td><input type="text" value="{{$view->image_name}}" name="image_name[]" class="subview_input" ></td>
        <td><div class="hidden-sm hidden-xs btn-group">
            <button class="btn btn-xs btn-info" type="button" onclick="openUpdateSubViewPopUp({{$index}});" ><i class="ace-icon fa fa-pencil bigger-120"></i></button>
            <button class="btn btn-xs btn-danger" type="button" onclick="openDeleteSubViewPopUp({{$index}});" ><i class="ace-icon fa fa-trash-o bigger-120"></i></button>
        </div></td></tr>

        <?php 
                                                    $index++;
                                                    ?>
                                                    @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!----------------------------------------------------------------------------------------------------------->




                            <div class="widget-footer">
                                <div class="clearfix form-actions" style="margin-bottom:0px;">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button class="btn btn-info" onclick="checkValidation();" type="button">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Submit
                                        </button>

                                        &nbsp; &nbsp; &nbsp;
                                        <!-- <button class="btn" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                            Reset
                                        </button> -->
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div><!-- /.span -->
                </div>
            </form>




        </div>
    </div>
</div>

<!-- ADD MODEL -->
<div class="modal" tabindex="-1" role="dialog" id="add_subview">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


            <form action="#" method="post" id="subview_form">
                <input type="hidden" id="save_action">
                <input type="text" name="subview_id">
                <div class="modal-header">
                    <h5 class="modal-title">SubView</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">








                    <div class="col-lg-6">
                        <label for="subview_title"> Type: </label>
                        <div class="">
                        <select name="type" class="form-control" >
                            <option value="Image">Image</option>
                            <option value="Label">Label</option>
                        </select>
                                                </div></div>

                                                <div class="col-lg-6 col-sm-6">
                        <label for="subview_title"> Frame: </label>
                        <div class="">
                        <input type="text" class="form-control" name="frame">
                        </div></div>

                        <div class="col-lg-6">
                        <label for="subview_title"> Text: </label>
                        <div class="">
                        <input type="text" class="form-control" name="text">
                        </div></div>
                    
                    <div class="col-lg-6 col-sm-6">
                        <label for="subview_title"> Front Name: </label>
                        <div class="">
                        <input type="text" class="form-control" name="font_name">
                    </div></div>

                    <div class="col-lg-6 col-sm-6">
                        <label for="subview_title"> Font Size: </label>
                        <div class="">
                        <input type="text" class="form-control" name="font_size">
                    </div></div>
                    <div class="col-lg-6 col-sm-6">
                        <label for="subview_title"> Text Color: </label>
                        <div class="">
                        <input type="text" class="form-control" name="text_color">
                    </div></div>
                    <div class="col-lg-6 col-sm-6">
                        <label for="subview_title"> Image Name: </label>
                        <div class="">
                        <input type="text" class="form-control" name="image_name">
                    </div></div>






                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveSubView();" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- ADD MODEL END -->
@section('script')
<script>
    let validator;
    var subviewIndex = {{ $index }};



    function openAddSubViewPop(){
        $('#add_subview').modal('show');
        $('#save_action').val('save',subviewIndex);
    }

    function openUpdateSubViewPopUp(index){
        $('#save_action').val('update');
        $('#add_subview').modal('show', index);
        let form = $('#subview_form');
        var row = $('#tr_'+index);

        // $("#gate option[value='Gateway 2']").prop('selected', true);

        var typeValue = row.find('[name="type[]"]').val();
        console.log(typeValue);
        form.find(`[name="type"] option[value='`+typeValue+`']`).prop('selected', true);

        form.find('[name="subview_id"]').val(row.find('[name="subview_id[]"]').val());
        form.find('[name="frame"]').val(row.find('[name="frame[]"]').val());
        form.find('[name="text"]').val(row.find('[name="text[]"]').val());
        form.find('[name="font_name"]').val(row.find('[name="font_name[]"]').val());
        form.find('[name="font_size"]').val(row.find('[name="font_size[]"]').val());
        form.find('[name="text_color"]').val(row.find('[name="text_color[]"]').val());
        form.find('[name="image_name"]').val(row.find('[name="image_name[]"]').val());
    }


    function openDeleteSubViewPopUp(index){
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to Remove ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        $('#tr_'+index).remove();
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


    function saveSubView(action, index){
        let form = document.querySelector("#subview_form");
        let formdata = new FormData(form);

        let type = formdata.get("type");
        let frame = formdata.get("frame");
        let text = formdata.get("text");
        let font_name = formdata.get("font_name");
        let font_size = formdata.get("font_size");
        let text_color = formdata.get("text_color");
        let image_name = formdata.get("image_name");

        var imageView = '';
        var labelView = '';

        if(type == 'Image'){
            imageView = 'selected="selected"';
        }else{
            labelView = 'selected="selected"';
        }
        var rowString = `<tr id="tr_`+index+`" >
        <td class=""><select name="type[]" style="pointer-events: none;"><option value="Image" `+imageView+` >Image</option><option value="Label" `+labelView+` >Label</option></select></td>
        <td><input type="hidden" id="" name="sub_view_id[]" ><input type="text" value="`+frame+`" name="frame[]" class="subview_input" ></td>
        <td><input type="text" value="`+text+`" name="text[]" class="subview_input" ></td>
        <td><input type="text" value="`+font_name+`" name="font_name[]" class="subview_input" ></td>
        <td><input type="text" value="`+font_size+`" name="font_size[]" class="subview_input" ></td>
        <td><input type="text" value="`+text_color+`" name="text_color[]" class="subview_input" ></td>
        <td><input type="text" value="`+image_name+`" name="image_name[]" class="subview_input" ></td>
        <td><div class="hidden-sm hidden-xs btn-group">
            <button class="btn btn-xs btn-info" onclick="openUpdateSubViewPopUp(`+index+`);" ><i class="ace-icon fa fa-pencil bigger-120"></i></button>
            <button class="btn btn-xs btn-danger"><i class="ace-icon fa fa-trash-o bigger-120"></i></button>
        </div></td></tr>`;
        var action = $('#save_action').val();
        if(action == 'save'){
            $('#subview-table tbody').append(rowString);
            subviewIndex++;
        }else{
            console.log(index, rowString);
            $('#tr_'+index).replaceWith(rowString);
        }
        $('#add_subview').modal('hide');
    }

     



    function checkValidation() {


        // const formElement = document.querySelector("#product_form");
        let myform = document.getElementById("product_form");
        let fdata = new FormData(myform);

        var errorCount = validator.checkAll();
        if (errorCount == 0) {
            $.ajax({
                data: fdata,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: "JSON",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ $actionURL }}",
                success: function(res, textStatus, jqXHR) {
                    // console.log('=======>>>>> ',res);
                    if (jqXHR.status == 200) {
                        if (typeof res.data.post !== 'undefined') {
                            $.confirm({
                                title: 'Success',
                                content: res.message,
                                buttons: {
                                    yes: {
                                        text: 'OK',
                                        action: function() {
                                            window.location = "{{ route('post.logo.create') }}";
                                        }
                                    }
                                }
                            });
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // console.log(textStatus,jqXHR, errorThrown);
                    if (jqXHR.status != 200) {
                        if (typeof jqXHR.responseJSON !== 'undefined') {
                            $.confirm({
                                title: 'Error',
                                content: jqXHR.responseJSON.message
                            });
                        }
                    }
                }
            });
        }

        // console.log(validator.checkAll());
    }




    $(document).ready(function() {

        $('#file_2').ace_file_input({
            no_file: 'No File ...',
            btn_choose: 'Choose',
            btn_change: 'Change',
            droppable: false,
            onchange: null,
            thumbnail: false //| true | large
            //whitelist:'gif|png|jpg|jpeg'
            //blacklist:'exe|php'
            //onchange:''
            //
        });


        validator = $('form.product_form').jbvalidator({
            allow_single_deselect: true,
            errorMessage: true,
            successClass: true,
            html5BrowserDefault: false,
            validFeedBackClass: 'valid-feedback',
            invalidFeedBackClass: 'invalid-feedback',
            validClass: 'is-valid',
            invalidClass: 'is-invalid'
        });



        $(window)
            .off('resize.chosen')
            .on('resize.chosen', function() {
                $('.chosen-select').each(function() {
                    var $this = $(this);
                    $this.next().css({
                        'width': $this.parent().width()
                    });
                })
            }).trigger('resize.chosen');
        //resize chosen on sidebar collapse/expand
        $(document).on('settings.ace.chosen', function(e, event_name, event_val) {
            if (event_name != 'sidebar_collapsed') return;
            $('.chosen-select').each(function() {
                var $this = $(this);
                $this.next().css({
                    'width': $this.parent().width()
                });
            })
        });





        $('.select21').chosen({
            allow_single_deselect: false
        });



    });
</script>
@endsection