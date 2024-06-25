<?php
session_start(); ob_start();
include('constant.php');
if(!isset($_SESSION['user'])){
    header('location: login.php'); exit;
}
if($admin!=1){
    header('location: login.php'); exit;
}




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>point of Sale</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" >
<style>
   /*h4{color:red; font-family:'Courier New', Courier, monospace; text-decoration:underline;}*/
</style>
</head>
<body>

<div class="container mt-4">
<?php include('nav.php') ?>
<div class="row">
        <div class="col-md-12">
            <h2>Transaction History</h2>
        </div>
    </div>
   <form method="post">  
    <div class="row pt-5">
      
<div class="col-md-12">
    <div class="card">
        <div class="card-header"><h6>Transaction Filter</h6></div>
        <div class="card-body">
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
            <label for="">Mode of Payment</label>
<select class="form-control" name="mode">
<option value="">All</option>
<option value="cash">Cash</option>
<option value="transfer">Transfer</option>
<option value="pos">POS</option>

</select>
            </div>
            <div class="col-md-2">
            <label for="">Agent</label>
<select class="form-control" name="user">
<option value="">All</option>

<?php $sql = $db->query("SELECT * FROM user WHERE bid='$bid' ORDER BY name");
while($row = mysqli_fetch_assoc($sql)){ ?>
<option value="<?=$row['sn']?>"><?=$row['name']?></option>
<?php } ?>
</select>
            </div>
            <div class="col-md-2"><br>
                <button type="submit" class="btn btn-primary" name="SearchTransaction">Search Transaction</button>
            </div>
            </div>
        </div>
    </div>

</div>
</div>
</form>

<div class="row pb-5">
    <div class="col-md-12 pt-4">
        <div class="card">
            <div class="card-header">
                <h6>Sales Summary</h6></div>
            <div class="card-body table-responsive">
                <table class="table">
                    <tr>
                        <th>SN</th>
                        <th>Customer</th>
                        <th>Customer phone</th>
                        <th>Total Amount</th>
                        <th>Payment Mode</th>
                        <th>Date</th>
                        <th>Served By</th>
                        <th>Action</th>
                    </tr>
<?php $i=1;
$sql = $db->query("SELECT * FROM sales WHERE bid='$bid' ORDER BY sn DESC LIMIT 20");  
if(isset($_POST['SearchTransaction'])){
    extract($_POST);
if($user=='' AND $mode==''){
    $sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND created BETWEEN '$from' AND '$to' ORDER BY sn DESC");    
}
elseif($user=='' AND $mode!=''){
    $sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND mode='$mode' AND created BETWEEN '$from' AND '$to' ORDER BY sn DESC");    
}
elseif($user!='' AND $mode==''){
    $sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND user='$user' AND created BETWEEN '$from' AND '$to' ORDER BY sn DESC");    
}
else{
    $sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND user='$user' AND mode='$mode' AND created BETWEEN '$from' AND '$to' ORDER BY sn DESC");    
}
}    


elseif(isset($_GET['date'])){
    $date = $_GET['date'];
    $sql = $db->query("SELECT * FROM sales WHERE bid='$bid' AND created LIKE '%$date%' ");
}
$total =0;
while($row = mysqli_fetch_assoc($sql)){ $e = $i++; $total += $row['total'] ?> 
                    <tr>
                        <td><?= $e ?></td>
                        <td><?= $row['customer'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><strike>N</strike><?= number_format($row['total']) ?></td>
                        <td><?= $row['mode'] ?></td>
                        <td><?= substr($row['created'],0,10) ?></td>
                        <td><?= User($row['user']) ?></td>
                        <td><a class="btn btn-sm btn-info" href="receipt.php?salesid=<?= $row['salesid'] ?>">Receipt</a></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th colspan="3"></th><th><strike>N</strike><?= number_format($total) ?></th><th colspan="4"></th>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <?php $i = -7;
                while($i<1){$e=$i++; 
                $time = time() + 60*60*24*$e; 
                    $date = date('jS M, Y',$time);
                    $ymd = date('ymd',$time);
                    $d = date('Y-m-d',$time);
                    $sql = $db->query("SELECT SUM(total) AS amt FROM sales WHERE bid='$bid' AND created LIKE '%$d%' ");
                    $row = mysqli_fetch_assoc($sql);

                ?>
                <a class="btn btn-primary" href="?date=<?= date('Y-m-d',$time) ?>"  data-bs-toggle="tooltip" data-bs-placement="top"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="NGN<?=number_format($row['amt'])?>"><?= $date ?></a>
                
                <?php } ?>
            </div>
            <div class="card-footer">
                <?php $i = -9;
                while($i<1){$e=$i++; 
                $time = time() + 60*60*24*30.5*$e; 
                    $date = date('M, Y',$time);
                     $d = date('Y-m',$time);
                    $sql = $db->query("SELECT SUM(total) AS amt FROM sales WHERE bid='$bid' AND created LIKE '%$d%' ");
                    $row = mysqli_fetch_assoc($sql);
                ?>
                <a class="btn btn-success" href="?date=<?= date('Y-m',$time) ?>" data-bs-toggle="tooltip" data-bs-placement="top"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="NGN<?=number_format($row['amt'])?>"><?= $date ?></a>
                <?php } ?>
            </div>
         
        </div>
    </div>
</div>
    
 <script src="js/bootstrap.bundle.min.js"></script>  
 <script type="text/javascript">
     const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
 </script> 
 <!-- <script src="js/popper.min.js"></script>    -->
</body>
</html>