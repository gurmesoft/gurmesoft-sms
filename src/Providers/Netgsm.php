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
            $response = explode(' ', $response);
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
            '00'    => 'Ba??ar??l??',
            '20'    => 'Mesaj metninde ki problemden dolay?? g??nderilemedi veya standart maksimum mesaj karakter say??s??.',
            '30'    => 'Ge??ersiz kullan??c?? ad?? ,??ifre veya kullan??c??n??z??n API eri??im izni yok. API eri??iminizde IP s??n??rlamas?? yapt??ysan??z ve s??n??rlad??????n??z ip d??????nda g??nderim sa??l??yorsan??z da bu hata olu??abilir. API eri??im izninizi veya IP s??n??rlaman??z?? , web aray??zden; sa?? ??st k????ede bulunan ayarlar> API i??lemleri men??sunden kontrol edebilirsiniz.',
            '40'    => 'Mesaj ba??l??????n??z??n (title) sistemde tan??ml?? de??il.',
            '50'    => 'Abone hesab??n??z ile ??YS kontroll?? g??nderimler yap??lamamaktad??r.',
            '51'    => 'Aboneli??inize tan??ml?? ??YS Marka bilgisi bulunamad??.',
            '70'    => 'Hatal?? sorgulama. G??nderdi??iniz parametrelerden birisi hatal?? veya zorunlu alanlardan birinin eksik.',
            '80'    => 'G??nderim s??n??r a????m??.',
            '85'    => 'M??kerrer G??nderim s??n??r a????m??. Ayn?? numaraya 1 dakika i??erisinde 20\'den fazla g??rev olu??turulamaz.',
        );

        return $codes[$code];
    }
}
