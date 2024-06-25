<?php
session_start(); ob_start();
include('constant.php');

if(!isset($_SESSION['user'])){
    header('location: login.php'); exit;
}

$salesid = $_GET['salesid']??'';


$sql = $db->query("SELECT * FROM business WHERE sn='$bid' ");
$row = mysqli_fetch_assoc($sql);
$business = $row['name'];
$services = $row['services'];
$address = $row['address'];
$phone = $row['phone'];



$sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND salesid = '$salesid' ");
$row = mysqli_fetch_assoc($sql);
$name = $row['customer'];
$phone = $row['phone'];
$date = $row['created'];



//$d = 1718284098;
 //2024-06-13 15:45:17


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer-Receipt-<?=str_replace(' ','-',$name).'-'.$salesid?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet" >
    <style type="text/css">
        tr{border-bottom-color: #000!important; }
    </style>
</head>
<body onload="window.print()">
    <center>
        <h5><?=$business?></h5>
        (<?=$services?>)<br>
       <?=$address?>. <?=$phone?><br><br>
        <h5>Customer Receipt</h5>
    </center>
<!-- <h6>Bill To:</h6> -->
<p>Customer: <?= ucwords($name) ?>
<!-- <br><?= $phone ?> -->
<br>Date: <span style="float: rightx; padding-bottom:10px"><?= date('d/m/y h:iA',strtotime($date)) ?></span><br>Receipt ID: <?= $salesid ?><br>Served by: <?= User($user) ?></p>

    <table class="table table-bordered table-sm">
<tr><th>Qty</th><th>Item</th><th>Price</th><th>Amount</th></tr>

<?php $total  = 0;
$sql = $db->query("SELECT * FROM item WHERE salesid='$salesid'");
while( $row = $sql->fetch_assoc() ){ $total += $row["amount"];
$delete  = 'href="?delete='.$row["sn"].'"';
echo "<tr><td>".$row["qty"]."</td><td>".$row["item"]."</td><td><strike>N</strike>".number_format($row["price"])."</td><td><strike>N</strike>".number_format($row["amount"])."</td></tr>";
}
echo '<tr><th colspan="3">Grand Total</th><th><strike>N</strike>'.number_format($total).'</th></tr>';
?>
</table>
<p>Thanks for patronage. Please, call again</p>

<center><p>Powered By: Livepetal Systems Ltd</p></center>

</body>
</html>


