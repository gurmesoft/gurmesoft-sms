<?php

namespace GurmesoftSms\Providers;

class Netgsm extends \GurmesoftSms\Providers\BaseProvider
{
    public function __construct(array $options)
    {
        $this->prepare($options);
    }

    private function prepare($options)
    {
        $this->url = 'https://api.netgsm.com.tr';

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
           CURLOPT_SSL_VERIFYHOST   => 2,
           CURLOPT_SSL_VERIFYPEER   => 0,
           CURLOPT_RETURNTRANSFER   => 1,
           CURLOPT_TIMEOUT          => 30,
           CURLOPT_HTTPHEADER       => array('Content-Type: text/xml')
        );
    }

    public function send($message, $number)
    {
        $request    = array(
            'header'    => array(
                'company'   => 'Netgsm',
                'usercode'  => $this->apiKey,
                'password'  => $this->apiPass,
                'type'      =>'1:n',
                'msgheader' => $this->title,
            ),
            'body'      => array(
                'msg'       => $message,
                'no'        => $number,
            )

        );

        $this->arrayToXml($request, 'mainbody');
        $this->options[CURLOPT_URL]         = "{$this->url}/sms/send/xml";
        $this->options[CURLOPT_POSTFIELDS]  = $request;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();


        if ($response) {
            $response   = explode(' ', $response);
        }

        if (!empty($response) && $response[0] === '00') {
            $this->result->setIsSuccess(true)
            ->setOperationId($response[1])
            ->setOperationMessage($this->responses($response[0]))
            ->setOperationCode($response[0]);
        } else {
            $this->result->setErrorMessage($this->responses($response[0]))
            ->setErrorCode($response[0]);
        }

        return $this->result;
    }

    public function info($id)
    {
        $request = http_build_query(
            array(
                'usercode'  => $this->apiKey,
                'password'  => $this->apiPass,
                'bulkid'    => $id,
                'type'      => 0,
                'status'    => 100,
                'version'   => 3,
            )
        );

        $request = "https://api.netgsm.com.tr/sms/report/?{$request}";
        $this->options[CURLOPT_URL] = $request;
        $this->result               = new \GurmesoftSms\Result;
        $response                   = $this->request();

        if ($response) {
            $this->result->setIsSuccess(true)
            ->setOperationMessage($response)
            ->setOperationCode('00');
        }

        return $this->result;
    }

    public function checkCredit()
    {
        $request    = array(
            'header'    => array(
                'company'   => 'Netgsm',
                'usercode'  => $this->apiKey,
                'password'  => $this->apiPass,
                'stip'      => 2
            ),
        );

        $this->arrayToXml($request, 'mainbody');
        $this->options[CURLOPT_URL]         = "{$this->url}/balance/list/xml";
        $this->options[CURLOPT_POSTFIELDS]  = $request;
        $this->result                       = new \GurmesoftSms\Result;
        $response                           = $this->request();

        if ($response) {
            $response   = explode(' ', $response);
        }

        if (!empty($response) && $response[0] === '00') {
            $this->result->setIsSuccess(true)
            ->setCredit($response[1])
            ->setOperationMessage($this->responses($response[0]))
            ->setOperationCode($response[0]);
        } else {
            $this->result->setErrorMessage($this->responses($response[0]))
            ->setErrorCode($response[0]);
        }

        return $this->result;
    }

    public function responses($code)
    {
        $codes = array(
            '00'    => 'Başarılı',
            '20'    => 'Mesaj metninde ki problemden dolayı gönderilemedi veya standart maksimum mesaj karakter sayısı.',
            '30'    => 'Geçersiz kullanıcı adı ,şifre veya kullanıcınızın API erişim izni yok. API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız da bu hata oluşabilir. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.',
            '40'    => 'Mesaj başlığınızın (title) sistemde tanımlı değil.',
            '50'    => 'Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.',
            '51'    => 'Aboneliğinize tanımlı İYS Marka bilgisi bulunamadı.',
            '70'    => 'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik.',
            '80'    => 'Gönderim sınır aşımı.',
            '85'    => 'Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20\'den fazla görev oluşturulamaz.',
        );

        return $codes[$code];
    }
}
