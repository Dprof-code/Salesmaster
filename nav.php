<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
  <a class="navbar-brand" href="#"> <strong><?php if (isset($_COOKIE['biztitle'])) {
                                                            echo strtoupper($_COOKIE['biztitle']);
                                                        } ?></strong></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
         
    <div class="collapse navbar-collapse" id="navbarNav">
    
    <ul class="navbar-nav ms-auto">
    <a href="#" class="nav-link nav-item fw-bold" style="float:right;">
                        <?= $name ?></a>
                    <?php if ($admin == 1) { ?>
                <li class="nav-item">
                    <a href="sales.php" class="nav-link">POS</a>
                </li>
                <li class="nav-item">
                    <a href="transactions.php" class="nav-link">Transactions</a>
                </li>
                <li class="nav-item">
                    <a href="admin.php" class="nav-link">Admin</a>
                </li>
                <li class="nav-item">
                    <a href="user.php" class="nav-link">Users</a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="?logout=true" class="nav-link">Logout</a>
                </li> 
            </ul>

    </div>            
  </div>
</nav>
</body>

</html>