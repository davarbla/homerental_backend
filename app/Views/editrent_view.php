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
                                <li><a href="#">Rent</a></li>
                                <li class="active">Data Rent</li>
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
                        <form name="formCategory" action="rent/add_update" method="post" class="">
                            <input type="hidden" id="id" name="id" value="<?php echo $row['id_rent'];?>">
                            <input type="hidden" id="rating" name="rating" value="<?php echo $row['rating'];?>">

                            <div class="card-header"><strong><?php echo ($row['id_rent'] != '') ? 'Edit' : 'Add' ;?>
                                    Rent</strong><small> Form</small></div>
                            <div class="card-body card-block">
                                <div class="form-group">
                                    <label for="category" class=" form-control-label">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="0">Please select</option>
                                        <?php foreach($category as $categ) { ?>
                                        <option value="<?php echo $categ['id_category'];?>"
                                            <?php ($row['id_category']==$categ['id_category']) ? 'SELECTED' : '' ?>>
                                            <?php echo $categ['title'];?></option>
                                        <? } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="company" class=" form-control-label">Title</label>
                                    <input type="text" id="title" name="title" value="<?php echo $row['title'];?>"
                                        placeholder="Title" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="vat" class=" form-control-label">Description</label>
                                    <textarea name="description" id="description" rows="5" placeholder="Content..."
                                        class="form-control"><?php echo $row['description'];?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="image" class=" form-control-label">(1) Image Url (https..)</label>
                                    <input type="text" id="image" name="image" value="<?php echo $row['image'];?>"
                                        placeholder="Enter image url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="image2" class=" form-control-label">(2) Image Url (https..)</label>
                                    <input type="text" id="image2" name="image2" value="<?php echo $row['image2'];?>"
                                        placeholder="Enter image2 url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="image3" class=" form-control-label">(3) Image Url (https..)</label>
                                    <input type="text" id="image3" name="image3" value="<?php echo $row['image3'];?>"
                                        placeholder="Enter image3 url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="street" class=" form-control-label">(4) Image Url (https..)</label>
                                    <input type="text" id="image4" name="image4" value="<?php echo $row['image4'];?>"
                                        placeholder="Enter image4 url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="street" class=" form-control-label">(5) Image Url (https..)</label>
                                    <input type="text" id="image5" name="image5" value="<?php echo $row['image5'];?>"
                                        placeholder="Enter image5 url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="street" class=" form-control-label">(6) Image Url (https..)</label>
                                    <input type="text" id="image6" name="image6" value="<?php echo $row['image6'];?>"
                                        placeholder="Enter image6 url" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="vat" class=" form-control-label">Address</label>
                                    <textarea name="address" id="address" rows="2" placeholder="Address.."
                                        class="form-control"><?php echo $row['address'];?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="vat" class=" form-control-label">Latitude (Look for Google Map
                                        Latitude)</label>
                                    <textarea name="latitude" id="latitude" rows="2" placeholder="Latitude.."
                                        class="form-control"><?php echo $row['latitude'];?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="price" class=" form-control-label">Price ($USD)/month</label>
                                    <input type="text" id="price" name="price" value="<?php echo $row['price'];?>"
                                        placeholder="Price" class="form-control">
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-2"><label for=beds" class=" form-control-label">Beds</label>
                                    </div>
                                    <div class="col-2 col-md-2"><input type="text" id="beds" name="beds"
                                            placeholder="beds" class="form-control" value="<?php echo $row['beds'];?>">
                                    </div>

                                    <div class="col col-md-2"><label for=baths"
                                            class=" form-control-label">Baths</label>
                                    </div>
                                    <div class="col-2 col-md-2"><input type="text" id="baths" name="baths"
                                            placeholder="baths" class="form-control"
                                            value="<?php echo $row['baths'];?>"></div>

                                    <div class="col col-md-2"><label for=sqft" class=" form-control-label">SQFT</label>
                                    </div>
                                    <div class="col-2 col-md-2"><input type="text" id="sqft" name="sqft"
                                            placeholder="sqft" class="form-control" value="<?php echo $row['sqft'];?>">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col col-md-3"><label class=" form-control-label">Is Recommend</label>
                                    </div>
                                    <div class="col col-md-6">
                                        <div class="form-check-inline form-check">
                                            <label for="recom-radio1" class="form-check-label ">
                                                <input type="radio" id="recomm1" name="recomm" value="0"
                                                    class="form-check-input"
                                                    <?php echo $row['is_recommend'] == 0 ? 'checked' : '';?>>Non-Active
                                            </label>
                                            &nbsp;&nbsp; &nbsp;&nbsp;
                                            <label for="recom-radio2" class="form-check-label ">
                                                <input type="radio" id="recomm2" name="recomm" value="1"
                                                    class="form-check-input"
                                                    <?php echo $row['is_recommend'] == 1 ? 'checked' : '';?>>Active
                                            </label>
                                        </div>
                                    </div>
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