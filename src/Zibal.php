<?php

namespace Dpsoft\Zibal;

use WpOrg\Requests\Requests;

class Zibal
{
    private $restEndpoint = 'https://gateway.zibal.ir/v1';
    private $payGatewayUrl='https://gateway.zibal.ir/start/';
    private $merchant;
    private $trackId;
    private $transport;

    /**
     * Pay constructor.
     *
     * @param $merchant string Zibal api key. For test use 'zibal'
     */
    public function __construct($merchant = 'zibal')
    {
        $this->merchant = $merchant;
    }

    /**
     * Create new request, and save the token.
     *
     * @param $callbackUrl string Callback url from payment page
     * @param $amount int Amount in Rial
     * @return array The array contains 2 keys: token & invoice_id , save theme with amount for further use.
     * @throws \Exception
     */
    public function request($callbackUrl, $amount)
    {
        $invoiceId = $this->getInvoiceId();

        $response = $this->httpRequest(
            Requests::POST,
            '/request',
            [
                'merchant' => $this->merchant,
                'amount' => $amount,
                'callbackUrl' => $callbackUrl,
                'orderId' => $invoiceId
            ]
        );
        $result = json_decode($response->body, true);
        if ($response->success and ($result['trackId']??false) and $result['result']==100) {
            $this->trackId = $result['trackId'];
            return [
                'token' => $this->trackId,
                'invoice_id' => $invoiceId,
            ];
        }
        throw new \Exception($this->resultCodes($result['result']), $result['result']);
    }

    /**
     * Verify Transaction And Return The Result
     *
     * @param $amount
     * @param $token
     * @return array The array contain 3 key: card_number, transaction_id,token .
     * @throws \Exception
     */
    public function verify($amount, $token)
    {
        if (
            empty($status = $_GET['status']) or
            empty($trackId = $_GET['trackId']) or
            ($token != $trackId) or
            ($_GET['success']!=1)
        ) {
            throw new \Exception($this->statusCodes($status), $status);
        }

        $response = $this->httpRequest(Requests::POST, '/verify', ['merchant' => $this->merchant, 'trackId' => $trackId]);
        $result = json_decode($response->body,true);
        if (
            $response->success and
            ($result['status'] == 1) and
            ($result['amount'] == $amount)
        ) {
            return [
                'card_number' => $result['cardNumber'],
                'transaction_id' => $result['refNumber'],
                'token'=>$trackId
            ];
        }
        throw new \Exception($result['errorMessage']??'Unknown Error!',$result['errorCode']??-1);
    }

    public function redirectUrl()
    {
        return $this->payGatewayUrl.$this->trackId;
    }

    public function redirectToBank()
    {
        header(sprintf("Location: %s", $this->redirectUrl()));
    }

    /**
     * Generate nearly unique integer ( hopefully! )
     *
     * @return int
     */
    private function getInvoiceId()
    {
        return hexdec(uniqid());
    }


    /**
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return \WpOrg\Requests\Response
     */
    public function httpRequest($method = Requests::GET, $endpoint = '', $data = [])
    {
        $options = !empty($this->transport) ? ['transport' => $this->transport] : [];
        return Requests::request(
            $this->restEndpoint . $endpoint,
            [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            empty($data) ? null : json_encode($data),
            $method,
            $options
        );
    }

    public function setTransport($transport)
    {
        $this->transport = $transport;
    }

    public static function trustLogoScript()
    {
        return '<script src="https://zib.al/trust/scripts/1.js"></script>';
    }

    protected function resultCodes($code)
    {
        switch ($code)
        {
            case 100:
                return "با موفقیت تایید شد";

            case 102:
                return "merchant یافت نشد";

            case 103:
                return "merchant غیرفعال";

            case 104:
                return "merchant نامعتبر";

            case 201:
                return "قبلا تایید شده";

            case 105:
                return "amount بایستی بزرگتر از 1,000 ریال باشد";

            case 106:
                return "callbackUrl نامعتبر می‌باشد. (شروع با http و یا https)";

            case 113:
                return "amount مبلغ تراکنش از سقف میزان تراکنش بیشتر است.";

            case 202:
                return "سفارش پرداخت نشده یا ناموفق بوده است";

            case 203:
                return "trackId نامعتبر می‌باشد";

            default:
                return "وضعیت مشخص شده معتبر نیست";
        }
    }
    protected function statusCodes($code)
    {
        switch ($code)
        {
            case -1:
                return "در انتظار پردخت";

            case -2:
                return "خطای داخلی";

            case 1:
                return "پرداخت شده - تاییدشده";

            case 2:
                return "پرداخت شده - تاییدنشده";

            case 3:
                return "لغوشده توسط کاربر";

            case 4:
                return "‌شماره کارت نامعتبر می‌باشد";

            case 5:
                return "‌موجودی حساب کافی نمی‌باشد";

            case 6:
                return "رمز واردشده اشتباه می‌باشد";

            case 7:
                return "‌تعداد درخواست‌ها بیش از حد مجاز می‌باشد";

            case 8:
                return "‌تعداد پرداخت اینترنتی روزانه بیش از حد مجاز می‌باشد";

            case 9:
                return "مبلغ پرداخت اینترنتی روزانه بیش از حد مجاز می‌باشد";

            case 10:
                return "‌صادرکننده‌ی کارت نامعتبر می‌باشد";

            case 11:
                return "خطای سوییچ";

            case 12:
                return "کارت قابل دسترسی نمی‌باشد";

            default:
                return "وضعیت مشخص شده معتبر نیست";
        }
    }
}
