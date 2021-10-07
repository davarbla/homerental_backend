<?php require_once 'header_view.php';?>

<body>
    <!-- Left Panel -->
    <?php require_once 'left_menu.php';?>
    <!-- /#left-panel -->

    <!-- Right Panel -->
    <?php require_once 'right_panel.php';?>

    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <a href="#" onclick="javascript:self.history.back();">
                                <h1>
                                    < Back</h1>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li><a href="#">Category</a></li>
                                <li class="active">Data Category</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="animated fadeIn">
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <form name="formCategory" action="category/add_update" method="post" class="">
                            <input type="hidden" id="id" name="id" value="<?php echo $row['id_category'];?>">

                            <div class="card-header"><strong><?php echo ($row['id_category'] != '') ? 'Edit' : 'Add' ;?>
                                    Category</strong><small> Form</small></div>
                            <div class="card-body card-block">
                                <div class="form-group">
                                    <label for="company" class=" form-control-label">Title</label>
                                    <input type="text" id="title" name="title" value="<?php echo $row['title'];?>"
                                        placeholder="Title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="vat" class=" form-control-label">Description</label>
                                    <input type="text" id="description" name="description"
                                        value="<?php echo $row['description'];?>" placeholder="Description"
                                        class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="street" class=" form-control-label">Image Url (https..)</label>
                                    <input type="text" id="image" name="image" value="<?php echo $row['image'];?>"
                                        placeholder="Enter image url" class="form-control">
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3"><label class=" form-control-label">Status</label></div>
                                    <div class="col col-md-6">
                                        <div class="form-check-inline form-check">
                                            <label for="inline-radio1" class="form-check-label ">
                                                <input type="radio" id="status1" name="status" value="0"
                                                    class="form-check-input"
                                                    <?php echo $row['status'] == 0 ? 'checked' : '';?>>Disabled
                                            </label>
                                            &nbsp;&nbsp; &nbsp;&nbsp;
                                            <label for="inline-radio2" class="form-check-label ">
                                                <input type="radio" id="status2" name="status" value="1"
                                                    class="form-check-input"
                                                    <?php echo $row['status'] == 1 ? 'checked' : '';?>>Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary ">
                                    <i class="fa fa-dot-circle-o"></i> Submit
                                </button>
                                <button type="reset" class="btn btn-danger">
                                    <i class="fa fa-ban"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>
        </div><!-- .animated -->
    </div><!-- .content -->


    <div class="clearfix"></div>

    <!-- Footer -->
    <?php require_once 'footer_view.php'; ?>
    <!-- /.site-footer -->

    </div><!-- /#right-panel -->

    <!-- Right Panel -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="public/assets/js/main.js"></script>


    <script src="public/assets/js/lib/data-table/datatables.min.js"></script>
    <script src="public/assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>
    <script src="public/assets/js/lib/data-table/dataTables.buttons.min.js"></script>
    <script src="public/assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
    <script src="public/assets/js/lib/data-table/jszip.min.js"></script>
    <script src="public/assets/js/lib/data-table/vfs_fonts.js"></script>
    <script src="public/assets/js/lib/data-table/buttons.html5.min.js"></script>
    <script src="public/assets/js/lib/data-table/buttons.print.min.js"></script>
    <script src="public/assets/js/lib/data-table/buttons.colVis.min.js"></script>
    <script src="public/assets/js/init/datatables-init.js"></script>


    <script type="text/javascript">
    $(document).ready(function() {
        $('#bootstrap-data-table-export').DataTable();
    });
    </script>


</body>

</html>