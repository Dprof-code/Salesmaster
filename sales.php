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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        a {
            text-decoration: none !important;
        }
    </style>
</head>

<body>

    <?php include('nav.php') ?>

    <div class="container mt-4">

        <div class="row">
            <div class="col-md-12">
                <h2>Point of Sale</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6>Add Items to Cart</h6>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="">Item</label>
                                <input type="text" class="form-control" name="item" placeholder="Item name">
                            </div>
                            <div class="form-group">
                                <label for="">price</label>
                                <input type="number" class="form-control" id="price" name="price" onkeyup="document.getElementById('amount').value = this.value * document.getElementById('qty').value;" placeholder="Enter price" required>
                            </div>
                            <div class="form-group">
                                <label for="">Quantity</label>
                                <input type="number" class="form-control" id="qty" name="qty" onkeyup="document.getElementById('amount').value = this.value * document.getElementById('price').value;" placeholder="Enter Qty" required>
                            </div>
                            <div class="form-group">
                                <label for="">Amount</label>
                                <input type="tel" class="form-control" id="amount" onkeyup="this.value = document.getElementById('qty').value * document.getElementById('price').value;" name="amount" placeholder="Enter Amount">
                            </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group">
                            <div style="float:right">
                                <button type="submit" class="btn btn-primary btn-block" name="AddItem">Add Item to Cart</button>
                            </div>
                        </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <form method="post">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <div>
                                        <h3>Cart</h3>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $sales->sqL1('item', 'salesid', $salesid); ?>
                                            <span class="visually-hidden">New alerts</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button name="clearAll" class="btn btn-danger" style="float:right;">Clear All</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="cart-items" class="table">
                                <tr>
                                    <th>Qty</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>

                                <?php $total  = 0;
                                $sql = $db->query("SELECT * FROM item WHERE bid='$bid' AND salesid='$salesid' AND status=1");
                                while ($row = $sql->fetch_assoc()) {
                                    $total += $row["amount"];
                                    $delete  = 'href="?delete=' . $row["sn"] . '"';
                                    echo "<tr><td>" . $row["qty"] . "</td><td>" . $row["item"] . "</td><td>" . number_format($row["price"]) . "</td><td>" . number_format($row["amount"]) . "</td><td><a " . $delete . ">Remove</a></td></tr>";
                                }
                                echo '<tr><th colspan="3">Grand Total</th><th>' . number_format($total) . '</th><th></th></tr>';
                                ?>
                            </table>
                            <?php $sql = $db->query("SELECT * FROM item WHERE bid='$bid' AND salesid='$salesid' AND status=0");
                            while ($row = $sql->fetch_assoc()) {

                                echo ' <a href="?restore=' . $row['sn'] .  '">' . $row['item'] .  '</a> | ';
                            }
                            ?>
                            <input type="hidden" name="total" value="<?= $total ?>">
                            <br>
                            <label for="">Mode of Payment</label>
                            <select class="form-control" name="mode">
                                <option value="">Select Option...</option>
                                <option value="cash">Cash</option>
                                <option value="Paystack"> PayStack</option>
                                <option value="Flutterwave"> FlutterWave</option>
                                <option value="pos">POS</option>
                            </select><br>
                            <label for="">Customer Name</label>
                            <input type="text" id="customer-name" class="form-control" name="customer"><br>
                            <label for="">Customer Phone Number</label>
                            <input type="number" id="customer-number" class="form-control" name="phone"><br>
                        </div>
                        <div class="card-footer">
                            <div>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Pay with Paystack
                                </button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#flutterWavePayModal" onclick="getCartInput()">
                                    Pay with FlutterWave
                                </button>
                                <button style="float:right" type="submit" class="btn btn-primary btn-block" name="Checkout">Complete Checkout</button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- PayStack Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Pay with Paystack</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="paymentForm">
                                    <div class="form-group">
                                        <label class="mt-1" for="email">Email Address</label>
                                        <input class="form-control" type="email" id="email-address" required />
                                    </div>
                                    <div class="form-group">
                                        <label class="mt-2" for="amount">Amount</label>
                                        <input class="form-control" type="tel" id="amoun" value="<?= $total ?>" readonly />
                                    </div>
                                    <div class="form-group">
                                        <label class="mt-2" for="first-name">Customer Name</label>
                                        <input class="form-control" type="text" id="first-name" onkeyup="this.value = document.getElementById('customer-name').value" readonly />
                                    </div>
                                    <div class="form-submit">
                                        <button type="Submit" class="btn btn-primary mt-2" onclick="payWithPaystack()">Pay</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FlutterWave Modal -->

                <div class="modal fade" id="flutterWavePayModal" tabindex="-1" aria-labelledby="flutterWavePayModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="flutterWavePayModalLabel">Pay with FlutterWave</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="https://checkout.flutterwave.com/v3/hosted/pay">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Payment</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="public_key" value="FLWPUBK_TEST-60431fb4391bdf6bf0b9f0f234adcc04-X" />
                                            <label for="">Customer Email</label>
                                            <input type="email" class="form-control" name="customer[email]"><br>
                                            <label for="">Customer Name</label>
                                            <input type="text" id="pcustomer-name" class="form-control" name="customer[name]" value=""><br>
                                            <label for="">Customer Phone Number</label>
                                            <input type=" tel" id="pcustomer-number" class="form-control" name="phone" value=""><br>
                                            <input type="hidden" name="tx_ref" value="txref-81123" />
                                            <label for="">Total Amount</label>
                                            <input type="" class="form-control" name="amount" value="<?= $total ?>" readonly /><br>
                                            <input type="hidden" name="currency" value="NGN" />
                                            <input type="hidden" name="redirect_url" value="localhost/salesmaster/payment_complete.php/" />
                                            <input type="hidden" name="meta[source]" value="docs-html-test" />
                                        </div>
                                        <div class="card-footer">
                                            <div style="float:right">
                                                <button type="submit" class="btn btn-primary btn-block" name="PayNow" id="start-payment-button">Pay Now</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>

        <div class="row">
            <div class="col-md-12 pt-4 pb-5">
                <div class="card">
                    <div class="card-header">
                        <h6>Recent Transactions</h6>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <tr>
                                <th>SN</th>
                                <th>Customer</th>
                                <th>Customer phone</th>
                                <th>Total Amount</th>
                                <th>Payment Mode</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            <?php $i = 1;
                            $sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND user='$user' ORDER BY sn DESC LIMIT 20");
                            while ($row = mysqli_fetch_assoc($sql)) {
                                $e = $i++ ?>
                                <tr>
                                    <td><?= $e ?></td>
                                    <td><?= $row['customer'] ?></td>
                                    <td><?= $row['phone'] ?></td>
                                    <td><strike>N</strike><?= number_format($row['total']) ?></td>
                                    <td><?= $row['mode'] ?></td>
                                    <td><?= substr($row['created'], 0, 10) ?></td>
                                    <td><a class="btn btn-sm btn-info" href="receipt.php?salesid=<?= $row['salesid'] ?>">Receipt</a>

                                        <?php if ($e == 1 && substr($row['created'], 0, 10) == date('Y-m-d')) { ?><a style="margin-left: 10px" href="?edit=<?= $row['sn'] ?>&salesid=<?= $row['salesid'] ?>" class="btn btn-sm btn-primary">Edit</a><?php } ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>



        <script src="js/bootstrap.bundle.min.js"></script>

        <!-- Paystack script link -->

        <script src="https://js.paystack.co/v1/inline.js"></script>


        <script>
            let emptyCart = document.getElementById("empty-cart");
            let cartItems = document.getElementById("cart-items");

            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            let emptyAction = () => {
                // Assuming you might want to confirm before emptying the cart
                if (confirm('Are you sure you want to empty the cart?')) {
                    // You can optionally do more here, such as showing a loading spinner
                    // Send an AJAX request to inform PHP to empty the cart
                    fetch('sales.php', {
                            method: 'POST',
                            body: new URLSearchParams('Empty=1'),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                // Handle success (maybe refresh the page or update UI)
                                location.reload(); // Refresh the page after successful emptying
                            } else {
                                // Handle errors
                                console.error('Failed to empty cart');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            }

            const toastTrigger = document.getElementById('liveToastBtn')
            const toastLiveExample = document.getElementById('liveToast')

            // Paystack Integeration code

            const paymentForm = document.getElementById('paymentForm');
            paymentForm.addEventListener("submit", payWithPaystack, false);

            function payWithPaystack(e) {
                e.preventDefault();

                let handler = PaystackPop.setup({
                    key: 'pk_test_795d779e21245d2bb3c349d193ee41572af57971', // Replace with your public key
                    email: document.getElementById("email-address").value,
                    amount: document.getElementById("amoun").value * 100,
                    // currency: NGN,
                    ref: '' + Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                    // label: "Optional string that replaces customer email"
                    onClose: function() {
                        alert('Window closed.');
                    },
                    callback: function(response) {
                        let message = 'Payment complete! Reference: ' + response.reference;
                        alert(message);
                    }
                });

                handler.openIframe();
            }

            const getCartInput = () => {
                let customerName = document.getElementById("customer-name").value;
                let customerPhone = document.getElementById("customer-number").value;

                document.getElementById("pcustomer-name").value = customerName;
                document.getElementById("pcustomer-number").value = customerPhone;
            }
        </script>
</body>

</html>