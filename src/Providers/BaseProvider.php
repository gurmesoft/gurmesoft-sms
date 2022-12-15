<?php

namespace GurmesoftSms\Providers;

use Exception;
use Spatie\ArrayToXml\ArrayToXml;

class BaseProvider
{
    public function arrayToXml(array &$array, string $root)
    {
        $array = ArrayToXml::convert($array, $root, true, 'UTF-8');
    }

    public function request()
    {
        $response   = false;
        $this->ch   = curl_init();
        curl_setopt_array($this->ch, $this->options);
        try {
            $response = curl_exec($this->ch);
            $this->result->setResponse($response);
        } catch (Exception $e) {
            $this->result->setErrorMessage($e->getMessage);
        }

        return $response;
    }
}
