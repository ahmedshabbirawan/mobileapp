@extends('Layout.master')

@section('title')
 Create Logo
@endsection

@section('content')

<div class="page-content">
<div class="page-header"><h1>Logos</h1></div>

<div class="row ">
    <div class="col-lg-12 " style="align-content: center;">
        @include('Layout.alerts')
        <div class="card radius-10 border-top border-0 border-4 border-danger">
            <form method="post" id="product_form" action="{{ isset($product) ? route('post.logo.update', $product->id) : route('post.logo.store') }}" novalidate class="form-horizontal product_form">
                @csrf
                <input type="hidden" name="id" value="">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-lg-12">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="widget-title">{{ isset($product) ? 'Update' : 'Create' }}</h4>
                            </div>
                            <div class="widget-body">

                                <div class="widget-main">


                                    <div style="clear:both;"></div>
                                    <div class="col-lg-4">
                                        <label style="font-size:11px;">File</label>
                                        <input type="file" name="product_image" id="file_2" />
                                    </div>
                                    




                                    <div style="clear:both;"></div>
                                </div>
                            </div>
                            <div class="widget-footer">
                                <div class="clearfix form-actions" style="margin-bottom:0px;">
                                    <div class="col-md-offset-3 col-md-9">
                                        
                                        <button class="btn btn-info" onclick="checkValidation('view');" type="button">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Verify
                                        </button>
                                        &nbsp; &nbsp; &nbsp;
                                        <button class="btn btn-info" onclick="checkValidation('insert');" type="button">
                                            <i class="ace-icon fa fa-check bigger-110"></i>
                                            Upload
                                        </button>



                                       
                                        <!-- <button class="btn" type="reset">
                                            <i class="ace-icon fa fa-undo bigger-110"></i>
                                            Reset
                                        </button> -->
                                    </div>
                                </div>
                            </div>

                            <div class="widget-body" id="req_response"></div>

                        </div>
                    </div><!-- /.span -->
                </div>
            </form>




        </div>
    </div>
</div>

</div>
@section('script')
<script>
    let validator;




    function checkValidation(action) {


        // const formElement = document.querySelector("#product_form");
        let myform = document.getElementById("product_form");
        let fdata = new FormData(myform);


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
                url: "{{ route('post.logo.data_import_post') }}?action="+action,
                success: function(res, textStatus, jqXHR) {
                     console.log('=======>>>>> ',res);
                     $('#req_response').html(res.view);
                    // if (jqXHR.status == 200) {
                    //     if (typeof res.data.post !== 'undefined') {
                    //         $.confirm({
                    //             title: 'Success',
                    //             content: res.message,
                    //             buttons: {
                    //                 yes: {
                    //                     text: 'OK',
                    //                     action: function() {
                    //                         window.location = "{{ route('post.logo.create') }}";
                    //                     }
                    //                 }
                    //             }
                    //         });
                    //     }
                    // }
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
@endsection

