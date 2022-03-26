<?php

namespace GurmesoftSms\Providers;

class Vatan extends \GurmesoftSms\Providers\BaseProvider
{
    public function __construct(array $options)
    {
        $this->prepare($options);
    }

    private function prepare($options)
    {
        $this->url = 'http://panel.vatansms.com/panel';

        if (isset($options['title']) && !empty($options['title'])) {
            $this->title = $options['title'];
        }

        if (isset($options['userCode']) && !empty($options['userCode'])) {
            $this->userCode = $options['userCode'];
        }

        if (isset($options['apiKey']) && !empty($options['apiKey'])) {
            $this->apiKey = $options['apiKey'];
        }

        if (isset($options['apiPass']) && !empty($options['apiPass'])) {
            $this->apiPass = $options['apiPass'];
        }

        $this->options = array(
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTPHEADER      => array('Content-Type: text/html'),
        );
    }

    public function send($message, $numbers)
    {
        $request = array(
            'kno'       => $this->userCode,
            'kulad'     => $this->apiKey,
            'sifre'     => $this->Apipass,
            'gonderen'  => $this->title,
            'mesaj'     => $message,
            'numaralar' => implode(',', $numbers),
            'tur'       => 'Normal'
        );

        $this->arrayToXml($request, 'sms');
        $this->options[CURLOPT_URL]         = "{$this->url}/smsgonder1Npost.php";
        $this->options[CURLOPT_POSTFIELDS]  = "data={$request}";
        $this->options[CURLOPT_POST ]       = 1;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if ($response) {
            $response = explode(':', $response);
        }

        if (!empty($response) && $response[0] == '1') {
            $this->result->setIsSuccess(true)
            ->setOperationId($response[3])
            ->setOperationMessage($response[2])
            ->setOperationCode($response[0]);
        } else {
            $this->result->setErrorMessage($response[1])
            ->setErrorCode($response[0]);
        }

        return $this->result;
    }

    public function info($id)
    {
        $request = array(
            'kulad'     => $this->apiKey,
            'sifre'     => $this->Apipass,
            'ozelkod'   => $id
        );
        
        $this->arrayToXml($request, 'smsrapor');
        $this->options[CURLOPT_URL]         = "{$this->url}/send.json";
        $this->options[CURLOPT_POSTFIELDS]  = "data={$request}";
        $this->options[CURLOPT_POST ]       = 1;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if ($response) {
            $response = explode(':', $response);
        }

        if (!empty($response) && $response[0] == '1') {
            $this->result->setIsSuccess(true)
            ->setOperationId($response[3])
            ->setOperationMessage($response[2])
            ->setOperationCode($response[0]);
        } else {
            $this->result->setErrorMessage($response[1])
            ->setErrorCode($response[0]);
        }

        return $this->result;
    }

    public function checkCredit()
    {
        $query = http_build_query(
            array(
                'kul_ad'    => $this->apiKey,
                'sifre'     => $this->Apipass,
            )
        );
        
        $this->options[CURLOPT_URL]         = "{$this->url}/kullanicibilgi.php?{$query}";
        $this->options[CURLOPT_POST ]       = 1;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if (!empty($response)) {
            $this->result->setIsSuccess(true)
            ->setOperationMessage($response);
        }

        return $this->result;
    }
}
