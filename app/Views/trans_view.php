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
                                <li><a href="#">Transaction</a></li>
                                <li class="active">Data Transaction</li>
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
                            <strong class="card-title">Data Transaction</strong>
                        </div>
                        <div class="card-body">
                            <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ref. No</th>
                                        <th>Fullname</th>
                                        <th>Rent</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($result as $row) { 
                                         $status = 'Complete';
                                         $bgStatus = 'badge-success';
                                         if ($row['status'] == '1') {
                                             $status = 'New';
                                             $bgStatus = 'badge-primary';
                                         }
                                         else if ($row['status'] == '2') {
                                             $status = 'Checkin';
                                             $bgStatus = 'badge-secondary';
                                         }
                                         else if ($row['status'] == '3') {
                                             $status = 'Done';
                                         }
                                         else if ($row['status'] == '4') {
                                             $status = 'Cancel';
                                             $bgStatus = 'badge-danger';
                                         }
                                        ?>
                                    <tr>
                                        <td><?php echo $row['id_trans'];?></td>
                                        <td><?php echo $row['no_trans'];?></td>
                                        <td><?php echo $row['user']['fullname'];?></td>
                                        <td><?php echo $row['rent']['title'];?></td>
                                        <td><?php echo $row['total'];?> /<?php echo $row['unit_price'];?>
                                        </td>
                                        <td><?php echo $row['duration'];?></td>
                                        <td><?php echo $row['payment'];?></td>
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