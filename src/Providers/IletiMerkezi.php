<?php

namespace GurmesoftSms\Providers;

class IletiMerkezi extends \GurmesoftSms\Providers\BaseProvider
{
    public function __construct(array $options)
    {
        $this->prepare($options);
    }

    private function prepare($options)
    {
        $this->url = 'https://api.iletimerkezi.com/v1';

        if (isset($options['title']) && !empty($options['title'])) {
            $this->title = $options['title'];
        }

        if (isset($options['apiKey']) && !empty($options['apiKey'])) {
            $this->apiKey = $options['apiKey'];
        }

        if (isset($options['apiPass']) && !empty($options['apiPass'])) {
            $this->apiPass = $options['apiPass'];
        }

        $this->options = array(
           CURLOPT_RETURNTRANSFER   => 1,
           CURLOPT_TIMEOUT          => 30,
           CURLOPT_HTTPHEADER       => array('Content-Type: text/xml')
        );
    }

    public function send($message, $number)
    {
        $request    = array(
            'authentication'    => array(
                'username'          => $this->apiKey,
                'password'          => $this->apiPass,
            ),
            'order'             => array(
                'iys'               => 0,
                'sender'            => $this->title,
                'sendDateTime'      => '',
            ),
            'message'           => array(
                'text'              => $message,
                'receipents'        => array(
                    'number'            => $number,
                )
            )
        );

        $this->arrayToXml($request, 'request');
        $this->options[CURLOPT_URL]         = "{$this->url}/send-sms";
        $this->options[CURLOPT_POSTFIELDS]  = $request;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if ($response) {
            $response = simplexml_load_string($response);
        }

        if (!empty($response) && $response->status->code == '200') {
            $this->result->setIsSuccess(true)
            ->setOperationId($response->order->id)
            ->setOperationMessage($response->status->message)
            ->setOperationCode($response->status->code);
        } else {
            $this->result->setErrorMessage($response->status->message)
            ->setErrorCode($response->status->code);
        }

        return $this->result;
    }

    public function info($id)
    {
        $request    = array(
            'authentication'    => array(
                'username'          => $this->apiKey,
                'password'          => $this->apiPass,
            ),
            'order'             => array(
                'id'                => $id,
                'page'              => '',
                'rowCount'          => '',
            ),
        );

        $this->arrayToXml($request, 'request');
        $this->options[CURLOPT_URL]         = "{$this->url}/get-report";
        $this->options[CURLOPT_POSTFIELDS]  = $request;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if ($response) {
            $response = simplexml_load_string($response);
        }

        if (!empty($response) && $response->status->code == '200') {
            $this->result->setIsSuccess(true)
            ->setOperationMessage("Undefined message use getResponse method")
            ->setOperationCode($response->status->code);
        } else {
            $this->result->setErrorMessage($response->status->message)
            ->setErrorCode($response->status->code);
        }

        return $this->result;
    }

    public function checkCredit()
    {
        $request    = array(
            'authentication'    => array(
                'username'  => $this->apiKey,
                'password'  => $this->apiPass,
            ),
        );

        $this->arrayToXml($request, 'request');
        $this->options[CURLOPT_URL]         = "{$this->url}/get-balance";
        $this->options[CURLOPT_POSTFIELDS]  = $request;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if ($response) {
            $response = simplexml_load_string($response);
        }

        if (!empty($response) && $response->status->code == '200') {
            $this->result->setIsSuccess(true)
            ->setCredit($response->balance->sms)
            ->setOperationMessage($response->status->message)
            ->setOperationCode($response->status->code);
        } else {
            $this->result->setErrorMessage($response->status->message)
            ->setErrorCode($response->status->code);
        }

        return $this->result;
    }
}
