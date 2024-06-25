<?php
session_start();  ob_start();
include('constant.php');

if(!isset($_SESSION['user'])){
    header('location: login.php'); exit;
}
if($admin!=1){
    header('location: login.php'); exit;
}


  if(isset($_GET['sn'])){
    $sn = $_GET['sn'];
    $status = $_GET['status'];
    if(User($sn,'admin')==1){$status=1; }
    $sql = $db->query("UPDATE user SET status='$status' WHERE sn='$sn' ");
    header('location: ?');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sales Agents</title>
    <link href="css/bootstrap.min.css" rel="stylesheet" >
    <style>
        a{text-decoration: none;}
    </style>
</head>
<body>

<div class="container mt-4">
<?php include('nav.php') ?>
    <div class="row">
        <div class="col-md-12">
            <h2>Manage Sales Agents</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
<div class="card">
    <div class="card-header">
        <h6>Register New Agent</h6>
    </div>
    <div class="card-body">
        <form method="post">
      <div class="form-group">
<label for="">Name:</label>
<input type="text" class="form-control" name="name" placeholder="Enter name">
<label for="">Phone:</label>
<input type="number" class="form-control" name="phone" placeholder="Enter phone">
<label for="">Email:</label>
<input type="email" class="form-control" name="email" placeholder="Enter email">
<label for="">Password:</label>
<input type="password" class="form-control" name="password" placeholder="Enter password">
</div>
</div>
<div class="card-footer">
<div class="form-group">
    <div style="float:right">
    <button type="submit" class="btn btn-primary" name="RegisterUser">Register User</button>
    </div>
</div>
</form>
</div>
</div>
</div>

<div class="col-md-8">

<div class="card">
    <div class="card-header">
        <h6>Users</h6>
    </div>
    <div class="card-body">
       
        <table class="table">
            <tr>
                <th>SN</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Registered</th>
                <th>Action</th>
            </tr>
            <?php $i=1; $sql = $db->query("SELECT * FROM user WHERE bid='$bid' ORDER BY sn DESC LIMIT 20");
while($row = mysqli_fetch_assoc($sql)){ $e = $i++ ;
     //$action = '<a href="transactions.php?agent='.$row['sn'].'" class="btn btn-sm btn-secondary" style="margin-right:10px">Sales</a>';
 if($row['status']==1){
          $action = '<a href="?sn='.$row['sn'].'&status=0" class="btn btn-sm btn-danger">Deactivate</a>';    
    } else{$action = '<a href="?sn='.$row['sn'].'&status=1" class="btn btn-sm btn-success">Activate</a>'; }

//$action = $row['status']==1 ? '<a href="?sn='.$row['sn'].'&status=0" class="btn btn-sm btn-danger">Deactivate</a>' : '<a href="?sn='.$row['sn'].'&status=1" class="btn btn-sm btn-danger">Activate</a>';
?> 
                    <tr>
                        <td><?= $e ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['email'] ?></td>
                    <td><?= substr($row['created'],0,10) ?></td>
                    <td><?= $action ?></td>
                    </tr>
                    <?php } ?>
        </table>
        
    </div>
    </div>
    <div class="card-footer"></div>
    </div>
</div>
    

<script src="js/bootstrap.bundle.min.js"></script> 
</body>
</html>