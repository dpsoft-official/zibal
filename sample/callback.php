<?php
include __DIR__."/../vendor/autoload.php";
session_start();

    try {
        $zibal = new \Dpsoft\Zibal\Zibal($_SESSION['merchant']??'zibal');
        $result = $zibal->verify($_SESSION['amount'],$_SESSION['token']);

    }catch (Throwable $exception){

    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/milligram/1.3.0/milligram.css">

    <title>Zibal Callback Sample</title>
</head>
<body class="container">

<h1>Zibal Callback Sample</h1>
<blockquote>
    <p><em><?=isset($exception)?'Exception':'' ?></em></p>
    <p><em><?=isset($exception)?$exception->getMessage():null ?></em></p>
    <?php if(!empty($result)) {?>
        <h5 style="color: green">Success Transaction</h5>
        <p><em>Track Id = <?= $_SESSION['token'] ?></em></p>
        <p><em>Card Number = <?= $result['cardNumber'] ?></em></p>
        <p><em>Transaction Id = <?= $result['transaction_id'] ?></em></p>
        <p><em>Amount = <?= $_SESSION['amount'] ?></em></p>

    <?php } ?>
</blockquote>

</body>
</html>