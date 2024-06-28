<?php

session_start();
ob_start();

include('constant.php');
if ($status == 0) {
    header('location: login.php');
}

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit;
}
if ($admin != 1) {
    header('location: login.php');
    exit;
} {
}

if (isset($_GET['edit'])) {
    $sn = $_GET['edit'];
    $_SESSION["salesid"] = $_GET['salesid'];
    $sql = $db->query("DELETE FROM sales WHERE sn='$sn' ");
    header('location: ?');

    return;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include('nav.php') ?>




    <div class="container mt-4">

        <div class="row">
            <div class="col-md-9">
                <h2>Store</h2>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                    Add product
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticFrontdrop">
                    Search
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Product</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="">Item</label>
                                <input type="text" class="form-control" name="item" placeholder="Item name">
                            </div>
                            <div class="form-group">
                                <label for="" class="mt-2">Quantity</label>
                                <input type="number" class="form-control" id="qty" name="qty" placeholder="Enter quantity" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="mt-2">Cost Price</label>
                                <input type="number" class="form-control" id="cost" name="cost" placeholder="Enter Cost Price" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="mt-2">Selling Price</label>
                                <input type="number" class="form-control" id="sp" name="sp" placeholder="Enter Selling Price">
                            </div>
                            <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-block mt-4" data-bs-dismiss="modal" name="AddProduct">Add Product to Store</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="staticFrontdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Product Filter</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>From:</label>
                                    <input type="date" class="form-control" name="from" placeholder="Select Date">
                                </div>
                                <div class="col-md-3">
                                    <label>To:</label>
                                    <input type="date" class="form-control" name="to" placeholder="Select Date">
                                </div>
                                <div class="col-md-2">
                                    <label for="">Item</label>
                                    <select class="form-control" name="mode">
                                        <option value="">All</option>
                                        <?php $sql = $db->query("SELECT * FROM product");
                                        while ($row = mysqli_fetch_assoc($sql)) { ?>
                                            <option value="<?= $row['sn'] ?>"><?= $row['item'] ?></option>
                                        <?php } ?>

                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="">Status</label>
                                    <select class="form-control" name="user">
                                        <option value="">All</option>

                                        <?php $sql = $db->query("SELECT * FROM product");
                                        while ($row = mysqli_fetch_assoc($sql)) { ?>
                                            <option value="<?= $row['sn'] ?>"><?= $row[''] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-2"><br>
                                    <button type="submit" class="btn btn-primary" name="SearchProduct">Search Product</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 pt-4 pb-5">
                <div class="card">
                    <div class="card-header">
                        <h6>Product List</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <tr>
                                <th>SN</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Cost Price</th>
                                <th>Selling Price</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            <?php $i = 1;
                            $total  = 0;
                            $sql = $db->query("SELECT * FROM product ORDER BY sn DESC LIMIT 100");
                            while ($row = $sql->fetch_assoc()) {
                                $e = $i++; ?>
                                <tr>
                                    <td><?= $e ?></td>
                                    <td><?= $row['item'] ?></td>
                                    <td><?= $row['qty'] ?></td>
                                    <td><?= number_format($row['cost']) ?></td>
                                    <td><?= number_format($row['sp']) ?></td>
                                    <td style=<?php if ($row['qty'] == 0) {
                                        echo "color:red";
                                    } elseif ($row['qty'] <= 20) {
                                        echo "color:orange";
                                    } else {
                                        echo "color:green";
                                    }
                                     ?>><?php stock($row['qty']) ?></td>
                                    <td><?= substr($row['created'], 0, 10) ?></td>
                                    <td><a href="" class="btn btn-sm btn-primary">Edit</a></td>

                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>


    <script src="js/bootstrap.bundle.min.js"></script>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>

</html>