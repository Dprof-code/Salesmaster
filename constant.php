<?php
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "salesmaster");

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (isset($_GET['logout'])) {
    session_destroy();
    header('location: login.php');
    exit;
}

if (!isset($_SESSION["salesid"])) {
    $_SESSION["salesid"] = rand();
}

$user = $_SESSION['user'] ?? '';
$salesid = $_SESSION["salesid"];

if ($user) {
    $sql = $db->query("SELECT * FROM user WHERE sn = '$user' ");
    $row = mysqli_fetch_assoc($sql);
    $name = $row['name'];
    $status = $row['status'];
    $bid = $row['bid'];
    $admin = $row['admin'];

    if (!isset($_COOKIE['biztitle'])) {
        $sql = $db->query("SELECT * FROM business WHERE sn = '$bid' ");
        $row = mysqli_fetch_assoc($sql);
        $biztitle = $row['name'];

        setcookie('biztitle', $biztitle, time() + (86400 * 730), "/");
    }
}

function User($user, $opt = 'name')
{
    global $db;
    $sql = $db->query("SELECT * FROM user WHERE sn = '$user' ");
    $row = mysqli_fetch_assoc($sql);
    return mysqli_num_rows($sql) == 1 ? $row[$opt] : '';
}

function Alert($note, $x = 1)
{
    echo $x == 1 ? '<div class="alert alert-success" role="alert">
  ' . $note . '!
</div>' : '<div class="alert alert-danger" role="alert">
  ' . $note . '!
</div>';
    return;
}

class Salesmaster
{
    function __construct()
    {
        if (array_key_exists("AddItem", $_POST)) {$this->AddItem();}
        if (array_key_exists("Checkout", $_POST)) {$this->Checkout();}
        if (isset($_POST['clearAll'])){$this->clearAll();}
        if (isset($_GET["delete"])){$this->delete();}
        if (isset($_GET['edit'])){$this->edit();}
        if(array_key_exists("RegisterUser", $_POST)){$this->RegisterUser();}
        if (array_key_exists('UserLogin', $_POST)){$this-> UserLogin();}
    }


// AddItem Method

    function AddItem()
    {
        global $db, $salesid, $user, $bid;

        extract($_POST);

        $sql =  $db->query("INSERT INTO item (item,price,qty,amount,salesid,user,bid) VALUES ('$item','$price','$qty','$amount','$salesid','$user','$bid') ");
        if ($sql) {
            Alert('Successfully Added to cart');
        } else {
            Alert('Error Submitting data', 0);
        }
        return;
    }

// Checkout Method

    function Checkout()
    {
        global $db, $salesid, $user, $bid;

        extract($_POST);
        if ($total == 0) {
            header('location: ?');
        }
        $sql =  $db->query("INSERT INTO sales (customer,phone,total,salesid,user,mode,bid) VALUES ('$customer','$phone','$total','$salesid','$user','$mode','$bid') ");
        $db->query("UPDATE item SET status=2 WHERE salesid='$salesid'");
        $sq = $db->query("SELECT * FROM customer WHERE bid='$bid' AND phone = '$phone' ");
        if (mysqli_num_rows($sq) == 0) {
            $sql2 =  $db->query("INSERT INTO customer (name,phone,bid) VALUES ('$customer','$phone','$bid') ");
            //echo 'Customer added successfully<br>';
        }

        if ($sql) {
            Alert('Successfully Submitted');;
            unset($_SESSION['salesid']);
            $salesid = '';
        } else {
            Alert('Error Submitting data', 0);
        }
        return;
    }

// clearAll Method

    function clearAll(){
        global $db, $salesid;
        $db->query("DELETE FROM item WHERE salesid ='$salesid' AND SET status = 0");
        return;
    }

// Edit method

    function edit(){
        global $db,$sn;

        $sn = $_GET['edit'];
        $_SESSION["salesid"] = $_GET['salesid'];
        $sql = $db->query("DELETE FROM sales WHERE sn='$sn' ");
        header('location: ?');

        return;
    }

// Delete Method

    function delete(){
        global $db,$sn;

        $sn =  $_GET["delete"];
        $sql = $db->query("UPDATE item SET status=0 WHERE sn = '$sn'");
        if ($sql) { //Alert('Successfully deleted');
        } else { //Alert('Error deleting data',0);
        };

        return;
    }

// Register new user

    function RegisterUser(){
        global $db,$bid;
        extract($_POST);
        $password = md5($password);
         $sql =  $db->query("INSERT INTO user (name,phone,email,password,bid) VALUES ('$name','$phone','$email','$password','$bid') ");
          if($sql){Alert('Successfully Registered');
         }else{Alert('Error Submitting data',0); };

         return;
    }

    // User Login Method

    function UserLogin(){
        global $db;
          //extract($_POST);
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    // $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $sql = $db->query("SELECT * FROM user WHERE email='$email' AND password='$password' ");
    if (mysqli_num_rows($sql) == 1) {
        $row = mysqli_fetch_assoc($sql);
        $_SESSION['user'] = $row['sn'];
        header('location: sales.php');
        exit;
    } else {
        echo 'Error';
    }
    }

}

$sales = new Salesmaster;
