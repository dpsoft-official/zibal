<?php
include __DIR__."/../vendor/autoload.php";
session_start();

if (isset($_POST['merchant'])){
    try {
        $zibal = new \Dpsoft\Zibal\Zibal($_POST['merchant']);
        $result = $zibal->request($_POST['callback_url'],$_POST['amount']);
        $_SESSION['amount']=$_POST['amount'];
        $_SESSION['token']=$result['token'];
        $_SESSION['merchant']=$_POST['merchant'];

        $zibal->redirectToBank();
        exit();
    }catch (Throwable $exception){

    }
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

    <title>Zibal Request Sample</title>
</head>
<body class="container">

<h1>Zibal Sample</h1>
<blockquote>
    <p><em><?=isset($exception)?'Exception':'' ?></em></p>
    <p><em><?=isset($exception)?$exception->getMessage():null ?></em></p>
</blockquote>
<form action="" method="post">
    <label for="merchant">Merchant</label><input type="text" name="merchant" id="merchant" value="<?= $_POST['merchant']??'zibal' ?>" placeholder="For testing environment enter 'zibal'">
    <label for="amount">Amounts In Rial</label><input type="number" name="amount" id="amount" value="<?= $_POST['amount']??null ?>">
    <label for="callbackUrl">Callback URL</label><input type="url" name="callback_url" id="callbackUrl" value="<?= $_POST['callback_url']??"http://{$_SERVER['HTTP_HOST']}/callback.php" ?>">
    <input type="submit" value="submit">
</form>

</body>
</html>