<meta name="csrf-token" content="{{ csrf_token() }}" />


<?php
$categoryID         = '';
$subCategoryID      = '';
$productCategoryID  = '';
$name = '';
$productID = '';
$uomID = '';
$qty = '';

// print_r($attributeIDs); exit;

$attributeStr = '';

$actionURL =  route('post.logo.store');

$sub_category = [];

// echo $attributeStr; exit;

if (isset($product)) {
    $categoryID         = '';//$categories['category']['id'];
    $subCategoryID      = []; //$categories['sub_category']['id'];
    $productCategoryID  = ''; //$categories['product_category']['id'];
    $name = $product->post_title;
    $actionURL =  route('post.logo.update', $product->id);
    $productID = $product->id;

   

}
?>

<?php

?>

<div class="row ">
    <div class="col-12 col-lg-12" style="margin-top:20px; width:100%">



        @include('Layout.alerts')

        <div class="card radius-10 border-top border-0 border-4 border-danger">



            <form method="post" id="product_form" action="{{ isset($product) ? route('post.logo.update', $product->id) : route('post.logo.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="id" value="{{ $productID }}">
                <div class="row">




                    <div class="col-xs-12 col-sm-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">{{ isset($product) ? 'Update' : 'Create' }}</h4>
                            </div>
                            <div class="widget-body" style="display: block;">

                                <div class="widget-main">

                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Category : </label>
                                        <div>
                                            <select name="sub_cat_id" id="sub_category_id" class="chosen-select form-control select21" required style="width:100%">
                                                <option> -- Select -- </option>
                                                <?php foreach ($sub_category as $cat) : ?><optgroup label="<?= $cat->name ?>">
                                                        <?php foreach ($cat->subCategories as $subCat) { ?> <option value="<?= $subCat->id ?>" <?= ($subCat->id == $subCategoryID)?'selected="selected"':'' ?>   ><?= $subCat->name ?></option> <?php } ?>
                                                    </optgroup> <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>



                                    

                                    

                                    <div style="clear:both;"></div>
                                    <div class="space"></div>

                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Title: </label>
                                        <div class="">
                                            <input type="text" required class="form-control @error('name') is-invalid @enderror" value="{{old('name', (isset($product))? $product->post_title : '' )}}" name="name" id="name" placeholder="Name">
                                        </div>
                                    </div>

                                    <div style="clear:both;"></div>
                                    <div class="space"></div>


                                    <div class="col-lg-12 col-sm-12">
                                        <label for="form-field-1"> Description/Specification: </label>
                                        <div class="">
                                           
                                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" id="form-field-8" placeholder="Description">{{old('description', (isset($product))? $product->post_content : '' ) }}</textarea>
                                        </div>
                                    </div>

                                

                                    <div style="clear:both;"></div>







                               

                                    <div class="col-lg-4">
                                        <label style="font-size:11px;">Product Image</label>
                                        <input type="file" name="product_image" id="file_2" />
                                      </div>

                                




                                    <div class="col-lg-4 col-sm-4">
                                        <label class="" for="form-field-1"> Status </label>
                                        <div class="">
                                            {{ \App\Util\Form::statusSelect(old('status')) }}
                                        </div>
                                    </div>


                                    <div style="clear:both;"></div>


                                </div>
                            </div>
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


@section('script')
<script>
    let validator;

   


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



        $(document).on('change', '#sub_category_id', function() {
            getProductCategories();
        });

        // $(document).on('change', '#product_category_id', function() {
        //     getProductAttribute();
        // });


        



        $('.chosen-select').chosen({
            allow_single_deselect: true
        });


        $(".chosen-select").chosen().change(function() {
            validator.checkAll();
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
            allow_single_deselect: true
        });



    });
</script>
@endsection