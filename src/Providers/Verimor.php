<?php

namespace GurmesoftSms\Providers;

class Verimor extends \GurmesoftSms\Providers\BaseProvider
{
    public function __construct(array $options)
    {
        $this->prepare($options);
    }

    private function prepare($options)
    {
        $this->url = 'https://sms.verimor.com.tr/v2';

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
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTPHEADER      => array('Content-Type: text/json'),
        );
    }

    public function send($message, $numbers)
    {
        $request    = array(
            'username'      => $this->apiKey,
            'password'      => $this->apiPass,
            'custom_id'     => uniqid(),
            'source_addr'   => $this->title,
            'messages'      => array(
                'dest'  =>  implode(',', $numbers),
                'msg'   =>  $message
            )
        );

        $this->options[CURLOPT_URL]         = "{$this->url}/send.json";
        $this->options[CURLOPT_POSTFIELDS]  = json_encode($request);
        $this->options[CURLOPT_POST ]       = 1;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();
        $code                               = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if (!empty($response) && $code == '200') {
            $this->result->setIsSuccess(true)
            ->setOperationId($response)
            ->setOperationMessage('Başarılı')
            ->setOperationCode($code);
        } else {
            $this->result->setErrorMessage($response)
            ->setErrorCode($code);
        }

        return $this->result;
    }

    public function info($id)
    {
        $query = http_build_query(
            array(
                'username'  => $this->apiKey,
                'password'  => $this->apiPass,
                'id'        => $id,
            )
        );

        $this->options[CURLOPT_URL]         = "{$this->url}/balance?{$query}";
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();
        $code                               = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if (!empty($response) && $code == '200') {
            $this->result->setIsSuccess(true)
            ->setOperationId($response)
            ->setOperationMessage('Başarılı')
            ->setOperationCode($code);
        } else {
            $this->result->setErrorMessage($response)
            ->setErrorCode($code);
        }

        return $this->result;
    }

    public function checkCredit()
    {
        $query = http_build_query(
            array(
                'username'  => $this->apiKey,
                'password'  => $this->apiPass,
            )
        );

        $this->options[CURLOPT_URL]         = "{$this->url}/balance?{$query}";
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();
        $code                               = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if (!empty($response) && $code == '200') {
            $this->result->setIsSuccess(true)
            ->setCredit($response)
            ->setOperationMessage('Başarılı')
            ->setOperationCode($code);
        } else {
            $this->result->setErrorMessage($response)
            ->setErrorCode($code);
        }

        return $this->result;
    }
}
