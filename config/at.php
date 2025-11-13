<?php
require __DIR__ . '/../vendor/autoload.php';

$AT_USERNAME = getenv('AT_USERNAME') ?: 'sandbox';
$AT_API_KEY  = getenv('AT_API_KEY')  ?: 'atsk_af87692c068a2055af0b0898f9cca21d19e8adf28379e68c250f8744379c6df6ccdaf7f2';

$AT = new AfricasTalking\SDK\AfricasTalking($AT_USERNAME, $AT_API_KEY);
$sms = $AT->sms();
