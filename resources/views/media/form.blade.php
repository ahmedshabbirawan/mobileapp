<meta name="csrf-token" content="{{ csrf_token() }}" />
<?php
$subCategoryID      = '';
$name = '';
$productID = '';

$actionURL =  route('media.store');
$sub_category = [];

if (isset($product)) {
    $subCategoryID = [];
    $name = $product->post_title;
    $actionURL = route('media.update', $product->id);
    $productID = $product->id;
}
?>


<div class="row ">
    <div class="col-lg-12 " style="align-content: center;">
        @include('Layout.alerts')
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <form method="post" id="product_form" action="{{ isset($product) ? route('post.logo.update', $product->id) : route('post.logo.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="id" value="{{ $productID }}">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-lg-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">{{ isset($product) ? 'Update' : 'Create' }}</h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main">
                                    <p id="msg-div"></p>
                                    <div class="col-lg-4">
                                        <label style="font-size:11px;">Image file(s)</label>
                                        <input type="file" name="post_files[]" multiple id="file_2" />
                                    </div>
                                    <div class="space-4"></div>
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
            $('#msg-div').html('Files uploading......');
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

                        $('#msg-div').html(res.message);

                            $.confirm({
                                title: 'Success',
                                content: res.message,
                                buttons: {
                                    yes: {
                                        text: 'OK',
                                        action: function() {
                                            window.location = "{{ route('media.create') }}";
                                        }
                                    }
                                }
                            });
                        
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // console.log(textStatus,jqXHR, errorThrown);
                    if (jqXHR.status != 200) {
                        if (typeof jqXHR.responseJSON !== 'undefined') {
                            $('#msg-div').html(jqXHR.responseJSON.message);
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
            thumbnail: false, //| true | large
            whitelist:'gif|png|jpg|jpeg'
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