<?php $__env->startSection('pagecss'); ?>
    <link href="<?php echo e(asset('assets/css/plugins/chosen/chosen.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/dropzone/basic.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('assets/css/plugins/dropzone/dropzone.css')); ?>" rel="stylesheet">
    <style>
        .dropzone, .dropzone *, .dropzone-previews, .dropzone-previews *{
            z-index: 999;
        }
        .dropzone .dz-default.dz-message{
            z-index: 1;
        }
        .dz-preview img{
            text-indent: -9999px;
            display: none;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contentPages'); ?>
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Manage Warranty</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="<?php echo e(URL::to('/customer/dashboard')); ?>">Home</a>
                </li>
                <li class="active">
                    <strong>Warranty</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Add Warranty</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <form id="warranty_form" name="warranty_form" method="POST" action="<?php echo e(action('customer\WarrantyCustomerController@warrantySaveData')); ?>" class="form-horizontal" enctype="multipart/form-data" style="min-height: 100vh;">

                            <input name="_token" type="hidden" id="hidden_token" value="<?php echo e(csrf_token()); ?>"/>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's name</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="name" name="name" placeholder="john" value="<?php echo e($customer_data->first_name); ?> <?php echo e($customer_data->last_name); ?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's Address</label>
                                <div class="col-sm-10">
                                    <textarea required class="form-control" id="address" name="address"></textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's postcode</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="postcode" name="postcode" placeholder="">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's EmailID</label>
                                <div class="col-sm-10">
                                    <input type="email" required class="form-control" id="emailID" name="emailID" placeholder="john@mail.com" value="<?php echo e($customer_data->emailID); ?>">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Customer's Telephone</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="phone" name="phone" placeholder="1234">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Brand</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Hot tub"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Model</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="product_model" name="product_model" placeholder="Hot tub"/>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group" id="date_purchase">
                                <label class="col-sm-2 control-label">Date of Delivery</label>
                                <div class="col-sm-10 input-group date" style="padding-left:15px; padding-right:15px; ">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" class="form-control" name="purchase_date" id="purchase_date" value="">
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Serial Number</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="product_serial_number" name="product_serial_number" placeholder="1234">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Fault</label>
                                <div class="col-sm-10">
                                    <textarea required class="form-control" id="product_fault" name="product_fault"></textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Part Required</label>
                                <div class="col-sm-10">
                                    <input type="text" required class="form-control" id="part_require" name="part_require" placeholder="">
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Additional Comments</label>
                                <div class="col-sm-10">
                                    <textarea  class="form-control" id="note" name="note"></textarea>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Upload Images</label>
                                <div class="col-sm-10 dropzone" id="file_note">
                                    <div class="dropzone-previews"></div>
                                </div>
                                <input type="hidden" name="hidden_file_images" id="hidden_file_images" />
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">Send</button>
                                    <button class="btn btn-white" type="button" id="btn_reset">Reset</button>
                                    <input type="hidden" name="warranty_uniqueID" id="warranty_uniqueID" value="<?php echo e($warranty_uniqueID); ?>"/>
                                    <input type="hidden" name="method_process" id="method_process" value="add" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input name="_token" type="hidden" id="hidden_token_file" value="<?php echo e(csrf_token()); ?>"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('pagescript'); ?>
    <?php echo $__env->make('customer.includes.commonscript', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <script src="<?php echo e(asset('assets/js/plugins/chosen/chosen.jquery.js')); ?>"></script>

    <!-- DROPZONE -->
    <script src="<?php echo e(asset('assets/js/plugins/dropzone/dropzone.js')); ?>"></script>

    <!-- Data picker -->
    <script src="<?php echo e(asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>

    <script type="text/javascript">
        $(function ()
        {
            $('#btn_reset').click(function ()
            {
                $('input[type="text"]').each(function(){
                    $(this).val('');
                });
                window.location.reload();
            });

            $('#date_purchase .date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true,
                format: 'd-m-yyyy'
            });

            $('#warranty_form').find('[name="purchase_date"]')
            .change(function(e) {
                $('#warranty_form').formValidation('revalidateField', 'purchase_date');
            })
            .end()
            .formValidation({
                framework: 'bootstrap',
                excluded: ':disabled',
                message: 'This value is not valid',
                icon: {
                    valid: 'glyphicon glyphicon-ok',
                    invalid: 'glyphicon glyphicon-remove',
                    validating: 'glyphicon glyphicon-refresh'
                },
                fields: {
                    name: {
                        validators: {
                            notEmpty: {
                                message: 'Please Enter Name'
                            },
                            stringLength: {
                                min: 3,
                                max: 30,
                                message: 'The text must be more than 3 characters long'
                            }
                        }
                    },
                    address: {
                        validators: {
                            notEmpty: {
                                message: 'Enter Address !'
                            },
                            stringLength: {
                                min: 3,
                                max: 1000,
                                message: 'The Field must be more than 3 characters long'
                            }
                        }
                    },
                    postcode: {
                        row: '.col-xs-3',
                        validators: {
                            notEmpty: {
                                message: 'The Postcode number is required'
                            }
                        }
                    },
                    emailID: {
                        validators: {
                            notEmpty: {
                                message: 'Enter Email Address !'
                            },
                            emailAddress: {
                                message: 'Enter Valid Email Address !'
                            }
                        }
                    },
                    phone: {
                        row: '.col-xs-3',
                        validators: {
                            notEmpty: {
                                message: 'The Phone number is required'
                            }
                        }
                    },
                    product_model: {
                        validators: {
                            notEmpty: {
                                message: 'Enter Product Model'
                            },
                            stringLength: {
                                min: 3,
                                max: 30,
                                message: 'The Field must be more than 3 characters long'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9\s]+$/i,
                                message: 'This Field can consist of alphabetical characters and spaces only'
                            }
                        }
                    },
                    purchase_date: {
                        validators: {
                            notEmpty: {
                                message: 'The Purchase Date is required'
                            },
                            date: {
                                format: 'DD-MM-YYYY',
                                message: 'The date is not a valid'
                            }
                        }
                    },
                    product_fault: {
                        validators: {
                            notEmpty: {
                                message: 'Enter Product Fault !'
                            },
                            stringLength: {
                                min: 3,
                                max: 10000,
                                message: 'The Field must be more than 3 characters long'
                            }
                        }
                    },
                    part_require: {
                        validators: {
                            notEmpty: {
                                message: 'Enter Part Require'
                            },
                            stringLength: {
                                min: 3,
                                max: 30,
                                message: 'The Field must be more than 3 characters long'
                            },
                            regexp: {
                                regexp: /^[a-zA-Z0-9\s]+$/i,
                                message: 'This Field can consist of alphabetical characters and spaces only'
                            }
                        }
                    }
                }
            });

        });
    </script>

    <script>
        $(function()
        {
            $('#hidden_file_images').val('');
            Dropzone.autoDiscover = false;
            var file_note_names = [];
            var hidden_file_note = [];
            var uniqueNames = [];
            var i = 0;
            var n = 0;
            var jk = 0;

            var myDropzone = new Dropzone("div#file_note",
            {
                url: "<?php echo e(URL::to('/customer/warrantyfile/')); ?>",
                method : "post",
                paramName: "note_file",
                params :{ _token: $('#hidden_token_file').val() },
                previewsContainer: ".dropzone-previews",
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                dictMaxFilesExceeded: "You can only upload upto 10 images",
                addRemoveLinks: true,
                dictRemoveFile: "Delete",
                acceptedFiles: ".jpg, .jpeg, .png, .pdf",
                init: function()
                {
                    // Hack: Add the dropzone class to the element
                    $(this.element).addClass("dropzone");
                    this.on("success", function (file, serverFileName)
                    {
                        var json_obj_file = jQuery.parseJSON(serverFileName);
                        var no = 0;
                        for (var i in json_obj_file)
                        {
                            hidden_file_note.push(json_obj_file[i].newname);
                            file_note_names[n] = {"serverFileName": json_obj_file[i].newname, "fileName": json_obj_file[i].oldname, "fileId": n};
                            n++;

                            no++;
                            if(i == jk) {
                                var downloadurl = json_obj_file[i].downloadurl;
                                var id_a = "id=bt-down_" + no;
                                file._downloadLink = Dropzone.createElement("<a download class=\"btn\" " + id_a + " style=\"width:100%; cursor:pointer;\" href=\"" + downloadurl + "\" title=\"Download\" data-dz-download><i class=\"fa fa-download\"></i></a>");
                                file.previewElement.appendChild(file._downloadLink);
                                file.previewElement.classList.add("dz-success");
                            }
                        }
                        jk++;

                        var uniqueNames = [];
                        $.each(hidden_file_note, function(i, el){
                            if($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
                        });
                        hidden_file_note = uniqueNames;

                        var file_name_string = hidden_file_note.join(",");
                        $('#hidden_file_images').val(file_name_string);
                    });

                    this.on("removedfile", function(file)
                    {
                        var rmvFile = "";
                        for(var f=0; f<file_note_names.length; f++)
                        {

                            if(file_note_names[f].fileName == file.name)
                            {
                                rmvFile = file_note_names[f].serverFileName;
                            }
                        }
                        if (rmvFile)
                        {
                            hidden_file_note = jQuery.grep(hidden_file_note, function( a ) {
                                return a !== rmvFile;
                            });
                            var file_name_string = hidden_file_note.join(",");
                            $('#hidden_file_images').val(file_name_string);
                            $.post("<?php echo e(URL::to('/customer/warrantyremove/')); ?>", {
                                file: rmvFile, _token:$('#hidden_token_file').val()
                            });
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('customer/layouts/mastercustomer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>