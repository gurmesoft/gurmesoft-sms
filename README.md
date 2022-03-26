# gurmesoft/sms
Gurmesoft için üretilmiş sms entegrasyon pakedi.Netgsm, İletimerkezi, VatanSms, Verimor desteği mevcuttur.

## Adım 1 
`composer.json` dosyası oluşturulur yada var olan dosyadaki uygun objelere ekleme yapılır.
```json
{
    "require": {
        "gurmesoft/sms": "dev-master"
    },
    "repositories": [
        {
            "type" : "github",
            "url" : "https://github.com/gurmesoft/gurmesoft-sms"
        }
    ]    
}
```

## Adım 2
`composer` kullanılarak paket yüklenir.
```bash
composer require gurmesoft/sms:dev-master
```

## Adım 3 
`vendor/autoload.php` dosyası dahil edilir ve firma türetilerek hazır hale getirilir.
```php
<?php 

require 'vendor/autoload.php';

$options = array(
    'title'     => 'XXXXXXXX',              // Sms sağlayıcınızda tanımlı gönderim isminiz, başlığınız vb. 
    'apiKey'    => 'XXXXXXXX',              // Sms sağlayıcınız tarafından verilen anahtar, kullanıcı vb.
    'apiPass'   => 'XXXXXXXX',              // Sms sağlayıcınız tarafından verilen şifre, gizli anahtar vb.  
);

$netgsm = new \GurmesoftSms\Client('Netgsm', $options);
```

### Sms gönderme 
```php
<?php 

$message = 'Hello World';                   
$numbers = array(
    '5XXXXXXXX0',
    '5XXXXXXXX1',
    '5XXXXXXXX2',
)

$result = $netgsm->send($message,$numbers) 

$result->getResponse();                     // Sms sağlayıcınız gelen tüm cevabı incelemek için kullanılır.

if ($result->isSuccess()) {
    echo $result->getOperationId();         // Eşsiz işlem numaranız. (İşlem durumu sorgulamak için kullanılacaktır.)
    echo $result->getOperationCode();       // Başarılı sonuç kodu döndürür.
    echo $result->getOperationMessage();    // Başarılı sonuç mesajı döndürür.
} else {
    echo $result->getErrorCode();           // Hatalı sonuç kodunu döndürür.
    echo $result->getErrorMessage();        // Hatalı sonuç mesajını döndürür.
}
```

### İşlem sorgulama
```php
<?php 

$operationId     = 'XXXXXXXX';
$result = $netgsm->info($id);               // Dönen cevabı gönderi oluşturmadaki methodlar ile inceleyebilirsiniz.
```

### Bakiye durumunu sorgulama

```php
<?php 

$result = $netgsm->checkCredit();           // Dönen cevabı sms gönderim methodlar ile inceleyebilirsiniz.
echo $result->getCredit();                  // (Extra Method) Kalan sms gönderim kredinizi döndürür.
```





