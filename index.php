<?php

include 'util/dbconn.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
}

$sql = "SELECT * FROM user";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $users = [];
}

$username = $_SESSION['username'];

//Getting uid from user table
$useridQuery = "SELECT uid FROM user WHERE username=?";
$stmt = mysqli_prepare($conn, $useridQuery);
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userid = mysqli_fetch_assoc($result)['uid'];

//Getting number of products
$numProductsQuery = "SELECT COUNT(*) AS num_products FROM products WHERE uid=?";
$stmt = mysqli_prepare($conn, $numProductsQuery);
mysqli_stmt_bind_param($stmt, 'i', $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$numProducts = $row['num_products'];

//Getting product name
$productName = "SELECT productName FROM products WHERE uid=?";
$stmt = mysqli_prepare($conn, $productName);
mysqli_stmt_bind_param($stmt, 'i', $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$productName = mysqli_fetch_assoc($result)['productName'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <meta http-equiv="refresh" content="5"> -->
    <title>Dashboard</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap3/bootstrap-switch.min.css"
        rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <style>
        #wrapper {
            display: flex;
            flex-direction: column;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin 0.25s ease-out;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js"></script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index.php?productName=<?php echo $productName ?>" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="addProduct.php" class="nav-link">Add Product</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="home.php" class="nav-link">Switches</a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Navbar Search -->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                        <i class="fas fa-search"></i>
                    </a>
                    <div class="navbar-search-block">
                        <form class="form-inline">
                            <div class="input-group input-group-sm">
                                <input class="form-control form-control-navbar" type="search" placeholder="Search"
                                    aria-label="Search">
                                <div class="input-group-append">
                                    <button class="btn btn-navbar" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                                style="opacity: .8"> -->
                <span class="brand-text font-weight-light">IOT AUTOMATION</span>
            </a>
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">
                            <?php echo $_SESSION['username']; ?>
                        </a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item menu-open">
                            <!-- <a href="#" class="nav-link active">
                                            <i class="nav-icon fas fa-tachometer-alt"></i>
                                            <p>
                                                Dashboard
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a> -->
                            <?php
                            // Assuming 'uid' is the column in the 'products' table that corresponds to the user's ID
                            $productNameQuery = "SELECT productName FROM products WHERE uid=?";
                            $stmt = mysqli_prepare($conn, $productNameQuery);
                            mysqli_stmt_bind_param($stmt, 'i', $userid);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            // Fetch all product names for the user
                            $productNames = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                $productNames[] = $row['productName'];
                            }
                            ?>

                            <!-- Product Dashboard links -->
                            <?php foreach ($productNames as $productName) { ?>
                            <li class="nav-item">
                                <a href="index.php?productName=<?php echo urlencode($productName) ?>" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p><?php echo $productName; ?> Dashboard</p>
                                </a>
                            </li>
                        <?php } ?>

                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>
                                    Logout
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Content Wrapper. Contains page content -->

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Dashboard</h1>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb
                                float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="#">Home</a>
                                </li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- /.content -->
            <!-- /.content-wrapper -->
            <!-- Main content -->

            <?php
            if ($numProducts == 0)
                header('location: addProduct.php');

            $productName = $_GET['productName'];

            // Query to fetch the pid for the given product name
            $serialNumberQuery = "SELECT serialNumber FROM products WHERE productName = ?";
            $stmt = $conn->prepare($serialNumberQuery);
            $stmt->bind_param("s", $productName);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $serialNumber = $row['serialNumber'];
            $_SESSION["serialNumber"] = $serialNumber;

            // Select values from MySQL database table
            $sql = "SELECT * FROM tricksumo_nodemcu WHERE serialNumber = ? ORDER BY id DESC LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $serialNumber);
            $stmt->execute();

            // Execute the query
            $result = $stmt->get_result();

            ?>
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <!-- row -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-primary shadow">
                                <div class="inner">
                                    <h3>
                                        <?php
                                        if ($result === false) {
                                            echo "Error executing the query: " . $conn->error;
                                        } else {
                                            $lastRow = $result->fetch_assoc();
                                            echo isset($lastRow['val']) ? $lastRow['val'] . " °C" : "0 °C";
                                        }
                                        ?>
                                    </h3>

                                    <p>Temperature</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-thermometer-half"></i>
                                </div>
                                <a href="#temperature-graph" class="small-box-footer">More Info <i
                                        class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>
                                        <?php echo isset($lastRow['val2']) ? $lastRow['val2'] . "" : "0"; ?>
                                    </h3>
                                    <p>Smoke Level</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-smog"></i>
                                </div>
                                <a href="#smoke-graph" class="small-box-footer">More Info <i
                                        class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>
                                        <?php echo isset($lastRow['Irms']) ? $lastRow['Irms'] . " A" : "0 A"; ?>
                                    </h3>
                                    <p>Current</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <a href="#current-graph" class="small-box-footer">More Info
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>
                                        <?php echo isset($lastRow['power']) ? $lastRow['power'] . " W" : "0 W"; ?>
                                    </h3>
                                    <p>Power</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-plug"></i>
                                </div>
                                <a href="#power-graph" class="small-box-footer">More Info
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>
                                        <?php echo isset($lastRow['energy']) ? $lastRow['energy'] . " kWh" : "0 kWh"; ?>
                                    </h3>
                                    <p>Energy Consumption</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <a href="#energy-table" class="small-box-footer">More Info
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- row -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header">
                                    <h1 class="card-title">
                                        <i class="fas fa-table"></i>
                                        DataTable
                                    </h1>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse"
                                            title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                    <div class="float-right" id="buttons-example-html5">
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                    <script>
                                        $(document).ready(function () {
                                            $('#filter-select').change(function () {
                                                var selectedValue = $(this).val();
                                                if (selectedValue === 'temperature') {
                                                    $('.temperature').show();
                                                    $('.smoke, .current, .power').hide();
                                                } else if (selectedValue === 'smoke') {
                                                    $('.smoke').show();
                                                    $('.temperature, .current, .power').hide();
                                                } else if (selectedValue === 'current') {
                                                    $('.current').show();
                                                    $('.temperature, .smoke, .power').hide();
                                                } else if (selectedValue === 'power') {
                                                    $('.power').show();
                                                    $('.temperature, .smoke, .current').hide();
                                                } else {
                                                    $('.temperature, .smoke, .current, .power').show();
                                                }
                                            });
                                        });
                                    </script>

                                    <div class="float-right">
                                        <label for="filter-select"><i class="fas fa-filter"></i></label>
                                        <select id="filter-select">
                                            <option value="all">All</option>
                                            <option value="temperature">Temperature</option>
                                            <option value="smoke">Smoke</option>
                                            <option value="current">Current</option>
                                            <option value="power">Power</option>
                                        </select>
                                    </div>

                                    <br><br>
                                    <div class="table-responsive">
                                        <div class="table-content">
                                            <table id="example2"
                                                class="table table-bordered table-hover mb-4 table-striped">
                                                <thead>
                                                    <tr>
                                                        <th class="temperature">Temperature</th>
                                                        <th class="smoke">Smoke</th>
                                                        <th class="current">Current</th>
                                                        <th class="power">Power</th>
                                                        <th>Date & Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Number of records per page
                                                    $recordsPerPage = 10;

                                                    // Get the current page number from the URL, default to 1 if not set
                                                    $page = isset($_GET['page']) ? $_GET['page'] : 1;

                                                    $total_pages_sql = "SELECT COUNT(*) AS total FROM tricksumo_nodemcu WHERE serialNumber = ?";
                                                    $stmt = $conn->prepare($total_pages_sql); // Corrected from $sql
                                                    $stmt->bind_param("s", $serialNumber);
                                                    $stmt->execute();
                                                    $total_pages_result = $stmt->get_result(); // Corrected from $conn->query($total_pages_sql)
                                                    $total_pages_row = $total_pages_result->fetch_assoc();
                                                    $total_pages = ceil($total_pages_row["total"] / $recordsPerPage);


                                                    $start = ($page - 1) * $recordsPerPage; // Default value for $start
                                                    
                                                    $orderLimitSql = "SELECT e.val AS Temperature, e.val2 AS Smoke, e.Irms AS Current, e.power AS Power, e.energy AS Energy, e.date AS Date, e.time AS Time 
                                                    FROM tricksumo_nodemcu e 
                                                    WHERE serialNumber = ? 
                                                    ORDER BY id DESC 
                                                    LIMIT $start, $recordsPerPage";

                                                    $stmt = $conn->prepare($orderLimitSql);
                                                    $stmt->bind_param("s", $serialNumber);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    if ($result->num_rows > 0) {
                                                        // Output data of each row
                                                        while ($row = $result->fetch_assoc()) {
                                                            echo "
                                                            <tr>
                                                                <td class='temperature'>" . $row["Temperature"] . "</td>
                                                                <td class='smoke'>" . $row["Smoke"] . "</td>
                                                                <td class='current'>" . $row["Current"] . "</td>
                                                                <td class='power'>" . $row["Power"] . "</td>
                                                                <td class='date&time'>" . $row["Date"] . " " . $row["Time"] . "</td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "0 results";
                                                    }

                                                    ?>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <?php if ($total_pages > 1):
                                            ?>
                                            <nav>
                                                <ul class="pagination justify-content-end" style="margin-right: 20px;">
                                                    <?php if ($page > 1): ?>
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                                href="index.php?productName=<?php echo "$productName" ?>&page=1"
                                                                aria-label="First">
                                                                <span aria-hidden="true">&laquo;</span>
                                                            </a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                                href="index.php?productName=<?php echo "$productName" ?>&page=<?php echo $page - 1; ?>"
                                                                aria-label="Previous">
                                                                <span aria-hidden="true">&lsaquo;</span>
                                                                <span class="sr-only">Previous</span>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php
                                                    // Calculate the start and end page numbers to display
                                                    $startPage = max(1, $page - 2);
                                                    $endPage = min($startPage + 4, $total_pages);

                                                    // Display the page links
                                                    for ($i = $startPage; $i <= $endPage; $i++):
                                                        $active = ($i == $page) ? "active" : "";
                                                        ?>
                                                        <li class="page-item <?php echo $active; ?>">
                                                            <a class="page-link"
                                                                href="index.php?productName=<?php echo "$productName" ?>&page=<?php echo $i; ?>">
                                                                <?php echo $i; ?>
                                                            </a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <?php if ($page < $total_pages): ?>
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                                href="index.php?productName=<?php echo "$productName" ?>&page=<?php echo $page + 1; ?>"
                                                                aria-label="Next">
                                                                <span aria-hidden="true">&rsaquo;</span>
                                                                <span class="sr-only">Next</span>
                                                            </a>
                                                        </li>
                                                        <li class="page-item">
                                                            <a class="page-link"
                                                                href="index.php?productName=<?php echo "$productName" ?>&page=<?php echo $total_pages; ?>"
                                                                aria-label="Last">
                                                                <span aria-hidden="true">&raquo;</span>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </nav>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="graph-row">
                        <div class="col-md-6" id="temperature-graph-col">
                            <div class="card card-primary card-outline shadow" id="temperature-graph">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i>
                                        Temperature
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse"
                                            title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <canvas id="temperatureChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                </div>
                                <!-- /.card-body-->
                            </div>
                        </div>
                        <div class="col-md-6 float-right">
                            <div class="card card-warning card-outline shadow" id="energy-table">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i>
                                        Energy Consumption
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse"
                                            title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 10px">#</th>
                                                <th>Energy Used</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM tricksumo_nodemcu WHERE serialNumber = ? ORDER BY id DESC LIMIT 5";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param("s", $serialNumber);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($result->num_rows > 0) {
                                                // Output data of each row
                                                $i = 1;
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "
                                                    <tr>
                                                        <td>" . $i . "</td>
                                                        <td>" . $row["energy"] . "</td>
                                                        <td>" . $row["date"] . " " . $row["time"] . "</td>
                                                    </tr>";
                                                    $i++;
                                                }
                                            } else {
                                                echo "0 results";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
                                    <ul class="pagination pagination-sm m-0 float-right">
                                        <?php
                                        $sql = "SELECT * FROM tricksumo_nodemcu WHERE serialNumber = ? ORDER BY id DESC LIMIT 5";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("s", $serialNumber);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $totalRows = mysqli_num_rows($result);

                                        $totalPages = ceil($totalRows / 5);
                                        for ($i = 1; $i <= $totalPages; $i++) {
                                            echo "<li class='page-item'><a class='page-link' href='index.php?productName=$productName&page=$i'>$i</a></li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                    <!-- /.row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-info card-outline shadow" id="smoke-graph">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i>
                                        Smoke
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse"
                                            title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="interactive" style="height: 300px;"></div>
                                </div>
                                <!-- /.card-body-->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-success card-outline shadow" id="current-graph">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="far fa-chart-bar"></i>
                                        Current
                                    </h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse"
                                            title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="interactive" style="height: 300px;"></div>
                                </div>
                                <!-- /.card-body-->
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="switch1">Switch 1:</label>
                                <input type="checkbox" id="switch1" data-toggle="switch" data-on-text="On"
                                    data-on-color="success" data-off-color="danger" data-off-text="Off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="switch2">Switch 2:</label>
                                <input type="checkbox" id="switch2" data-toggle="switch" data-on-text="On"
                                    data-on-color="success" data-off-text="Off" data-off-color="danger">
                            </div>
                        </div>
                    </div> -->
                    <!-- Flow Graph -->
                    <!-- row -->


                </div>
            </section>
        </div>
        <!-- /.content -->
        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>
                Copyright &copy; 2024

                .
            </strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <!-- <b>Version</b>
        3.2.0-rc -->
            </div>
        </footer>
    </div>
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- AdminLTE -->
    <script src="dist/js/adminlte.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard3.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- date-range-picker -->
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- FLOT CHARTS -->
    <script src="plugins/flot/jquery.flot.js"></script>
    <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
    <script src="plugins/flot/plugins/jquery.flot.resize.js"></script>
    <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
    <script src="plugins/flot/plugins/jquery.flot.pie.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.min.js"></script>
    <script>
        $(function () {
            $("#example2").DataTable({
                "info": false,
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "paging": false,
                "searching": false,
                "ordering": true,
                "buttons": [
                    {
                        extend: 'copy',
                        text: '<i class="far fa-copy"></i>',
                        titleAttr: 'Copy',
                        className: 'btn btn-default btn-sm',
                        background: 'none'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i>',
                        titleAttr: 'CSV',
                        className: 'btn btn-default btn-sm',
                        background: 'none'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="far fa-file-excel"></i>',
                        titleAttr: 'Excel',
                        className: 'btn btn-default btn-sm',
                        background: 'none'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i>',
                        titleAttr: 'PDF',
                        className: 'btn btn-default btn-sm',
                        background: 'none'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i>',
                        titleAttr: 'Print',
                        className: 'btn btn-default btn-sm',
                        background: 'none'
                    }
                ]
            }).buttons().container().appendTo('#buttons-example-html5');
        });

        $(document).ready(function () {
            var ctx = document.getElementById('temperature-graph').getContext('2d');
            var graph = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Temperature',
                        data: [],
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            fetchTemperatureData();

            function fetchTemperatureData() {
                $.ajax({
                    url: 'fetch_temperature_data.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        var labels = [];
                        var values = [];
                        data.forEach(function (row) {
                            labels.push(row.time);
                            values.push(row.val);
                        });

                        var ctx = document.getElementById('temperatureChart').getContext('2d');
                        var chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Temperature',
                                    data: values,
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching temperature data: ', textStatus, errorThrown);
                    }
                });
            }

            setInterval(fetchTemperatureData, 1000); // Fetch temperature data every second
            fetchTemperatureData(); // Initial fetch
        });
    </script>

</body>

</html>