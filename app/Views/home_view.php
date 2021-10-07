<?php require_once 'header_view.php';?>

<body>
    <!-- Left Panel -->
    <?php require_once 'left_menu.php';?>
    <!-- /#left-panel -->

    <!-- Right Panel -->
    <?php require_once 'right_panel.php';?>

    <!-- Content -->
    <div class="content">
        <!-- Animated -->
        <div class="animated fadeIn">
            <!-- Widgets  -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-1">
                                    <i class="pe-7s-cash"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text">$<span
                                                class="count"><?php echo $trans[0]['total'];?></span></div>
                                        <div class="stat-heading">Trans</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-2">
                                    <i class="pe-7s-date"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text"><span
                                                class="count"><?php echo $trans[0]['count'];?></span></div>
                                        <div class="stat-heading">Book</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-3">
                                    <i class="pe-7s-browser"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text"><span
                                                class="count"><?php echo $rent[0]['total'];?></span></div>
                                        <div class="stat-heading">Rent</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="stat-widget-five">
                                <div class="stat-icon dib flat-color-4">
                                    <i class="pe-7s-users"></i>
                                </div>
                                <div class="stat-content">
                                    <div class="text-left dib">
                                        <div class="stat-text"><span
                                                class="count"><?php echo $user[0]['total'];?></span></div>
                                        <div class="stat-heading">Member</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Widgets -->
            <!-- Orders -->
            <div class="orders">
                <div class="row">
                    <div class="col-lg">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="box-title">Latest Book </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-stats order-table ov-h">
                                    <table class="table ">
                                        <thead>
                                            <tr>
                                                <th class="serial">#</th>
                                                <th class="avatar">Avatar</th>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Rent</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $no = 1;
                                                foreach($latest as $row) { 
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
                                                <td class="serial"><?php echo $no;?></td>
                                                <td class="avatar">
                                                    <div class="round-img">
                                                        <a href="#"><img class="rounded-circle"
                                                                src="<?php echo $row['user']['image'];?>" alt=""></a>
                                                    </div>
                                                </td>
                                                <td> #<?php echo $row['no_trans'];?><br /><?php echo $row['duration'];?>
                                                </td>
                                                <td> <span class="name"><?php echo $row['user']['fullname'];?></span>
                                                </td>
                                                <td> <span class="product"><?php echo $row['rent']['title'];?></span>
                                                </td>
                                                <td><?php echo $row['currency'].'. ';?><span
                                                        class="count"><?php echo $row['total'];?></span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge <?php echo $bgStatus;?>"><?php echo $status;?></span>
                                                </td>
                                            </tr>
                                            <?php 
                                                    $no++;
                                                    } ?>

                                        </tbody>
                                    </table>
                                </div> <!-- /.table-stats -->
                            </div>
                        </div> <!-- /.card -->
                    </div> <!-- /.col-lg-8 -->


                </div>
            </div>
            <!-- /.orders -->

            <!--  feedback -->
            <div class="orders">
                <div class="row">
                    <div class="col-lg">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Top Feedback</strong>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Feedback</th>
                                            <th scope="col">Rating</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($feedback as $row) { ?>
                                        <tr>
                                            <th scope="row"><?php echo $row['id_feedback'];?></th>
                                            <td><?php echo $row['fullname'];?></td>
                                            <td><?php echo $row['desc_feedback'];?></td>
                                            <td><?php echo $row['rating'];?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  feedback -->


        </div>
        <!-- .animated -->
    </div>
    <!-- /.content -->
    <div class="clearfix"></div>
    <!-- Footer -->
    <?php require_once 'footer_view.php'; ?>
    <!-- /.site-footer -->
    </div>
    <!-- /#right-panel -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="public/assets/js/main.js"></script>


    <!--Local Stuff-->
    <script>
    jQuery(document).ready(function($) {
        "use strict";

        $("#confirmLogout").click(function() {
            window.location.href = '/home/logout';
        });


    });
    </script>
</body>

</html>