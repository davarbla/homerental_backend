<?php

    $activeIndex = "";
    if ($menu['activeIndex'] == '1') {
        $activeIndex = "class=active";
    }

    $activeInstall = "";
    if ($menu['activeInstall'] == '1') {
        $activeInstall = "class=active";
    }

    $activeFeedback = "";
    if ($menu['activeFeedback'] == '1') {
        $activeFeedback = "class=active";
    }

    $activeReview = "";
    if ($menu['activeReview'] == '1') {
        $activeReview = "class=active";
    }

    $activeAdmin = "";
    if ($menu['activeAdmin'] == '1') {
        $activeAdmin = "class=active";
    }

    $activeRent = "";
    if ($menu['activeRent'] == '1') {
        $activeRent = "class=active";
    }

    $activeUser = "";
    if ($menu['activeUser'] == '1') {
        $activeUser = "class=active";
    }

    $activeTrans = "";
    if ($menu['activeTrans'] == '1') {
        $activeTrans = "class=active";
    }

    $activeCategory = 'class="menu-item-has-children dropdown"';
    if ($menu['activeCategory'] == '1') {
        $activeCategory = 'class="menu-item-has-children active dropdown"';
    }
?>

<!-- Left Panel -->
<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li <?php echo $activeIndex;?>>
                    <a href="/home"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>
                </li>

                <li <?php echo $activeInstall;?>>
                    <a href="/install"><i class="menu-icon fa fa-rocket"></i>Install </a>
                </li>

                <li <?php echo $activeCategory;?>>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false"> <i class="menu-icon fa fa-table"></i>Category</a>
                    <ul class="sub-menu children dropdown-menu">
                        <li><i class="fa fa-table"></i><a href="/category">List</a></li>
                        <li><i class="fa fa-table"></i><a href="/category?ac=add">Add New</a></li>
                    </ul>
                </li>

                <li <?php echo $activeUser;?>>
                    <a href="/user"><i class="menu-icon fa fa-user"></i>User </a>
                </li>

                <li <?php echo $activeRent;?>>
                    <a href="/rent"><i class="menu-icon fa fa-home"></i>Rent </a>
                </li>

                <li <?php echo $activeTrans;?>>
                    <a href="/trans"><i class="menu-icon fa fa-money"></i>Transaction </a>
                </li>

                <li <?php echo $activeFeedback;?>>
                    <a href="/feedback"><i class="menu-icon fa fa-inbox"></i>Feedback </a>
                </li>

                <li <?php echo $activeReview;?>>
                    <a href="/comment"><i class="menu-icon fa fa-comment"></i>Review </a>
                </li>

                <li <?php echo $activeAdmin;?>>
                    <a href="/userlogin"><i class="menu-icon fa fa-users"></i>Admin Login </a>
                </li>

            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>
<!-- /#left-panel -->