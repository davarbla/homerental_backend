<?php require_once 'header_view.php';?>
<link rel="stylesheet" href="public/assets/css/lib/datatable/dataTables.bootstrap.min.css">

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
                            <h1>Dashboard</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="#">Dashboard</a></li>
                                <li><a href="#">User</a></li>
                                <li class="active">Data User</li>
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
                        <div class="card-header">
                            <strong class="card-title">Data User</strong>
                        </div>
                        <div class="card-body">
                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Fullname</th>
                                        <th>Image</th>
                                        <th>Email</th>
                                        <th>Total Like</th>
                                        <th>Total Rent</th>
                                        <th>Total Review</th>
                                        <th>Status</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($result as $row) { 
                                        $status = 'Active';
                                        $bgStatus = 'badge-primary';
                                        if ($row['status'] == '0') {
                                            $status = 'Inactive';
                                            $bgStatus = 'badge-danger';
                                        }
                                        ?>
                                    <tr>
                                        <td><?php echo $row['id_user'];?></td>
                                        <td><?php echo $row['fullname'];?></td>
                                        <td class="avatar">
                                            <div class="round-img">
                                                <a href="#"><img class="rounded-circle"
                                                        src="<?php echo $row['image'];?>"
                                                        style="width: 30px; height: 30px;"></a>
                                            </div>
                                        </td>
                                        <td><?php echo $row['email'];?><br /><?php echo $row['location'];?>
                                        </td>
                                        <td><?php echo $row['total_like'];?></td>
                                        <td><?php echo $row['total_rent'];?></td>
                                        <td><?php echo $row['total_comment'];?></td>
                                        <td>
                                            <span class="badge <?php echo $bgStatus;?>"><?php echo $status;?></span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
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

        /*$('#deleteConfirm').on('click', function() {
            console.log('enter this confirm');
        });*/

    });

    confirmModal = function(id) {
        console.log('confirmModal id: ' + id);
        let dataSession = JSON.parse('<?php echo json_encode($dataSess['user']); ?>')
        //console.log(dataSession);
        console.log(dataSession['flag']);

        if (dataSession['flag'] == '1') {
            alert('Action not allowed, only SuperAdmin level');
            return false;
        }

        var r = confirm("Are you sure to delete this data id " + id + "?");
        if (r == true) {
            window.location.href = 'category/delete?id=' + id;
        }

        return false;
    };
    </script>


</body>

</html>