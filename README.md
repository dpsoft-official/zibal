# Zibal online payment - درگاه پرداخت Zibal به زبان PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dpsoft/zibal.svg?style=flat-square)](https://packagist.org/packages/dpsoft/zibal)
[![Total Downloads](https://img.shields.io/packagist/dt/dpsoft/zibal.svg?style=flat-square)](https://packagist.org/packages/dpsoft/zibal)

## Installation

You can install the package via composer:

```bash
composer require dpsoft/zibal
```

## Usage

copy `sample` directory to server. Open `request.php` in browser and bala balah ...

### Request
```php
try {
    $zibal = new \Dpsoft\Zibal\Zibal($merchant);
    $result = $zibal->request($callbackUrl,$amount);
    //save amount and invoice id to forther use
    $_SESSION['amount']=$amount;
    $_SESSION['token']=$result['token'];

    $zibal->redirectToBank();
    exit();
}catch (Throwable $exception){
    echo $exception->getMessage();
}
```

### Response
```php
try {
    $zibal = new \Dpsoft\Zibal\Zibal($merchant);
    $result = $zibal->verify($_SESSION['amount'],$_SESSION['token']);
    //save result. The keys are: card_number,transaction_id and token for example $result['token']
    echo "Successfull transaction.";
}catch (Throwable $exception){
    echo "Error in transaction: ";
}
```
### Testing

``` bash
composer test
```

### Security

If you discover any security related issues, please email info@dpsoft.ir instead of using the issue tracker.

## Credits

- [Dpsoft](https://github.com/dpsoft)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
