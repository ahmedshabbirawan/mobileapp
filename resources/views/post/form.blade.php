<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>
    .ui-menu {
        z-index: 5455;
    }

    input[type="file"] {
  display: none;
}

.custom-file-upload {
  border: 1px solid #ccc;
  display: inline-block;
  padding: 6px 12px;
  cursor: pointer;
  width: 100%;
}
</style>
<?php


$imagePlaceholder = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22274%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20274%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1923d49c7bd%20text%20%7B%20fill%3A%23AAAAAA%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A14pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1923d49c7bd%22%3E%3Crect%20width%3D%22274%22%20height%3D%22200%22%20fill%3D%22%23EEEEEE%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22100.6796875%22%20y%3D%22106.3%22%3E274x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';

$templateThumb = $imagePlaceholder;

if(isset($thumbnail)){
    $templateThumb = $thumbnail;
}

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
                <input type="hidden" name="id" value="{{ $postId }}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-lg-12">
<div class="widget-box">
    <div class="widget-header">
        <h4 class="widget-title">Meta</h4>
    </div>
    <div class="widget-body">

        <div class="widget-main">


        <div class="col-lg-10 col-sm-12" style="padding-left: 0px;" >


        <div class="col-lg-6 col-sm-12">
                <label for="form-field-1"> Title </label>
                <div class="">
                    <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($row))? $row->post_title : '' )}}" name="name" id="name" placeholder="Name">
                </div>
            </div>

            <div class="col-lg-6 col-sm-12">
                <label for="form-field-1"> Template Bounds </label>
                <div class="">
                    <input type="text" required class="form-control @error('template_bounds') is-invalid @enderror" value="{{old('template_bounds', (isset($row))? $row->template_bounds : '' )}}" name="template_bounds" id="template_bounds" placeholder="e.g 2500,3600">
                </div>
            </div>

            <div style="clear:both;"></div>
            <div class="space"></div>

            <div class="col-lg-6 col-sm-6">
                <label class="" for="form-field-1"> Category</label>
                <div>
                    <select name="category[]" id="category" class="form-control select21" multiple required style="width:100%">
                        <?php foreach ($category as $key => $val) { ?>
                            <option value="<?= $key ?>" <?= (isset($post_category) && in_array($key, $post_category)) ? 'selected="selected"' : '' ?>><?= $val ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6">
                <label class="" for="form-field-1"> Status </label>
                <div class="">
                    {{ \App\Util\Form::statusSelect(old('status', (isset($row))?$row->status:'' )) }}
                </div>
            </div>
        </div>         
                <!------- File Upload ------->                                
                <div class="col-lg-2 col-sm-12">
                    <div class="col-lg-12">
                    <label for="file_2">Template Thumbnail</label>
                    <div class="">
                        <span class="profile-picture">
                            <img id="img_template_thumbnail_input" class="editable img-responsive editable-click editable-empty" style="max-height: 180px;" width="180" height="180" alt="Alex's Avatar" src="{{ $templateThumb }}">
                        </span>
                        <label for="template_thumbnail_input" class="custom-file-upload" style="text-align: center;">Custom Upload</label>
                        <input id="template_thumbnail_input" name="template_thumbnail_input" type="file"  onchange="imageFileChange(this)" />
                    </div>
                    </div>
                </div>
                <!--------------------------------------------------------------->
            <div class="space-4"></div>
            <div style="clear:both;"></div>
        </div>
    </div>



    <!----------------------------------------------------------------------------------------------------------->
    <div class="widget-header">
        <h4 class="widget-title">Sub Views</h4>

        <div class="widget-toolbar"><button class="btn btn-primary btn-xs" type="button" onclick="addNewRow();">
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
                                <th>Image ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>




                        <tbody>
                            <?php
                            $index = 0;
                            ?>
                            @if(isset($subview))
                            @foreach($subview as $view)
                            <tr id="tr_{{$index}}">
                                <td class=""><select name="type[]" onchange="selectType(this);" class="subviews_type" job_index="<?=$index?>"  >
                                        <option value="Image" <?=($view->type == 'Image')?'selected="selected"':'' ?> >Image</option>
                                        <option value="Label" <?=($view->type == 'Label')?'selected="selected"':'' ?> >Label</option>
                                    </select></td>
                                <td>
                                    <input type="hidden" value="{{$view->id}}" name="sub_view_id[]">
                                    <input type="text" value="{{$view->frame}}" name="frame[]" placeholder="x,y,width,height" class="subview_input" >
                                </td>
                                <td><input type="text" value="{{$view->text}}" name="text[]" class="subview_input label_input" <?=($view->type == 'Image')?'readonly"':'' ?> ></td>
                                <td><input type="text" value="{{$view->font_name}}" name="font_name[]" class="subview_input label_input" <?=($view->type == 'Image')?'readonly"':'' ?> ></td>
                                <td><input type="text" value="{{$view->font_size}}" name="font_size[]" class="subview_input label_input" <?=($view->type == 'Image')?'readonly"':'' ?> ></td>
                                <td><input type="text" value="{{$view->text_color}}" name="text_color[]" class="subview_input label_input" <?=($view->type == 'Image')?'readonly"':'' ?> ></td>
                                <td>
                                
                                    <div class="col-lg-6 col-sm-6 image_div" id="image_div_<?=$index?>" >
                                        <label for="file-upload_{{ $index }}" class="custom-file-upload btn-xs btn-info image_input"  style="text-align: center;">Change</label>
                                        <input name="subview_image_file[]" id="file-upload_{{ $index }}" class="subview_file_input image_input"   type="file" onchange="imageFileChange(this);" accept="image/*" />
                                    </div>
                                        <div class="col-lg-6 col-sm-6"><img src="{{ ($view->image_name)? App\Util\Util::imageUrl($view->image_name) :  $imagePlaceholder}}" width="50" height="50" ></div>
                            
                                </td>
                                <td>
                                    <div class="hidden-sm hidden-xs btn-group">
                                        <button class="btn btn-xs btn-danger" type="button" onclick="openDeleteSubViewPopUp({{$index}});"><i class="ace-icon fa fa-trash-o bigger-120"></i></button>
                                    </div>
                                </td>
                            </tr>

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


@section('script')
<script>
    let validator;
    var subviewIndex = <?=$index?>


    



    function selectType(ele) {
       var jobId = $(ele).attr('job_index');
        var type = $(ele).val();
        if (type == 'Image') {
            $('#tr_'+jobId).find('.label_input').val('');
            $('#tr_'+jobId).find('.label_input').attr('readonly', true);
            $('#tr_'+jobId).find('.image_input').removeAttr('readonly');

            $('#tr_'+jobId).find('.image_div').removeAttr('disabled');
            

        } else {
            $('#tr_'+jobId).find('.image_input').val('');
            $('#tr_'+jobId).find('.image_input').attr('readonly', true);
            $('#tr_'+jobId).find('.label_input').removeAttr('readonly');

            $('#tr_'+jobId).find('.image_div').attr('disabled',true);
        }
    }



    function openAddSubViewPop() {
        $('#add_subview').modal('show');
        $('#save_action').val('save', subviewIndex);
    }




    function openDeleteSubViewPopUp(index) {
        $.confirm({
            title: 'Confirmation',
            content: 'Do you really want to Remove ?',
            buttons: {
                yes: {
                    text: 'Yes',
                    action: function() {
                        $('#tr_' + index).remove();
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


    function addNewRow() {
        let title = $('#name').val();
        let bounds = $('#template_bounds').val();
        let category = $('#category').val();

        // if(title == '' || bounds == '' || category == ''){
        // $.confirm({
        //     title: 'Error',
        //     content: 'Please complate meta info'
        // });

        // return false;
        // }


      

        // let form = document.querySelector("#subview_form");
        // let formdata = new FormData(form);

        let type = 'Image'; //formdata.get("type");
        let frame = ''; // formdata.get("frame");
        let text = ''; // formdata.get("text");
        let font_name = ''; //formdata.get("font_name");
        let font_size = ''; //formdata.get("font_size");
        let text_color = ''; // formdata.get("text_color");
        let image_name = ''; //formdata.get("image_name");

        var imageView = '';
        var labelView = '';

        if (type == 'Image') {
            imageView = 'selected="selected"';
        } else {
            labelView = 'selected="selected"';
        }
        var rowString = `<tr id="tr_` + subviewIndex + `" >
        <td class=""><select name="type[]" onchange="selectType(this);" id="select_` + subviewIndex + `"  job_index="` + subviewIndex + `" ><option value="Image" ` + imageView + ` >Image</option><option value="Label" ` + labelView + ` >Label</option></select></td>
        <td><input type="hidden" id="" name="sub_view_id[]" ><input type="text" value="` + frame + `" name="frame[]" class="subview_input" placeholder="x,y,width,height" ></td>
        <td><input type="text" value="` + text + `" name="text[]" class="subview_input label_input"  ></td>
        <td><input type="text" value="` + font_name + `" name="font_name[]" class="subview_input label_input"  ></td>
        <td><input type="text" value="` + font_size + `" name="font_size[]" class="subview_input label_input"  ></td>
        <td><input type="text" value="` + text_color + `" name="text_color[]" class="subview_input label_input"  ></td>
        <td>
                <div class="col-lg-6 col-sm-6 image_div" id="image_div_` + subviewIndex + `" ><label for="file-upload_`+subviewIndex+`" class="custom-file-upload btn-xs btn-info image_input" style="text-align: center;">Change</label>
                <input name="subview_image_file[]" id="file-upload_`+subviewIndex+`" class="subview_file_input image_input" type="file" onchange="imageFileChange(this);" accept="image/*" /></div>
                <div class="col-lg-6 col-sm-6"><img src="{{$imagePlaceholder}}" id="img_file-upload_`+subviewIndex+`" width="50" height="50" ></div>
        </td>
        <td><div class="hidden-sm hidden-xs btn-group">
            <button class="btn btn-xs btn-danger" type="button" onclick="openDeleteSubViewPopUp(`+subviewIndex+`);" ><i class="ace-icon fa fa-trash-o bigger-120"></i></button>
        </div></td></tr>`;
            $('#subview-table tbody').append(rowString);
        
            setTimeout(function(){
                console.log('#select_' + subviewIndex);
                selectType($('#select_' + subviewIndex));
                subviewIndex++;
            },1000);


            $('input[type=file]').click(function(e){
            var attr = $(this).attr('readonly');
            if (typeof attr !== 'undefined' && attr !== false) {
                e.preventDefault();
            }
        });
         

    }


  





    function checkValidation() {


        // subview_image_file[]

        // var dd = $("[name='subview_image_file[]']").val();
        // $().each()
        // console.log(dd);
        // return true;



        // const formElement = document.querySelector("#product_form");
        let myform = document.getElementById("product_form");
        let fdata = new FormData(myform);



        $('.widget-box').loading();


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
                $('.widget-box').loading('stop');
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
                                      //   window.location = "{{ route('post.logo.create') }}";
                                    }
                                }
                            }
                        });
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('.widget-box').loading('stop');
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

    // document.querySelector("input[type=file]").addEventListener('click', function(e){
    //         var attr = $(this).attr('readonly');
    //         // For some browsers, `attr` is undefined; for others,
    //         // `attr` is false.  Check for both.
    //         if (typeof attr !== 'undefined' && attr !== false) {
    //             e.preventDefault();
    //         }
    //     }, false);


    $(document).ready(function() {

        $('input[type=file]').click(function(e){
            var attr = $(this).attr('readonly');
            if (typeof attr !== 'undefined' && attr !== false) {
                e.preventDefault();
            }
        });

        @if(isset($subview)) 
            $('.subviews_type').each(function(index,ele){
                selectType(ele);
            });
        @endif


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


        ///////
        // $('#ajax_select').chosen({allow_single_deselect:true, width:"200px", search_contains: true});
        //     chosen_ajaxify('ajax_select', '{{route("post.search_image")}}?query=');

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


        $.ui.autocomplete.prototype._renderItem = function(ul, item) {
            if (item.value === '')
                t = "None found";
            else {
                var searchMask = this.term;
                var regEx = new RegExp(searchMask, "ig");
                t = '<div><img class=""  width="50px" src="' + item.profile_image + '">';
                t += '<b class="user" >' + item.title + '</b></div>';
            }

            return $("<li></li>")
                .data("item.autocomplete", item)
                .append(t)
                .appendTo(ul);
        };

        $('.search-query').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '{{route("post.search_image")}}',
                    dataType: "json",
                    data: {
                        query: request.term
                        //filter: request.term,
                        //pagesize: 5
                    },
                    success: function(data) {
                        //response( data );
                        response($.map(data, function(el, index) {
                            return {
                                value: el.id,
                                profile_image: el.img,
                                title: el.title
                                // location: el.value
                            };
                        }));
                    }
                });
            },
            select: function(event, ui) {
                //   $("#result").html("entered="+this.value + "<br/> selected text="+ui.item.label + "<br/>selected value=" + ui.item.value);             
                // submitSearch(this.id,ui.item.value);          
            },
        });


        $("#search-query").keyup(function(e) {
            if (e.keyCode == 13) {
                console.dir(e);
                submitSearch(e.target.id, $("#search-query").val());
            }
        });


        ///////






    }); // ready end

    function imageFileChange(ele){
        var id = $(ele).attr('id');
        const imagePreviewDiv = document.getElementById('imagePreview');
        const file = event.target.files[0];
        // Check if a file was selected
        if (file) {
            const reader = new FileReader();
            // When the file is loaded, set the image inside the div
            reader.onload = function (e) {
                $('#img_'+id).attr('src', e.target.result);
                // imagePreviewDiv.innerHTML = '<img src="' + e.target.result + '" alt="Image Preview">';
            };
            // Read the image file as a data URL
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, show the default message
            imagePreviewDiv.innerHTML = 'No Image Selected';
        }
    }
</script>
@endsection