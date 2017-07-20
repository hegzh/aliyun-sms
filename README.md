# aliyun-sms

阿里云短信服务 PHP SDK 的 composer 封装

TP5 thinkphp5

[帮助文档](https://help.aliyun.com/document_detail/55451.html?spm=5176.sms-account.109.2.56907c16xG4lWM)


## TP5 config

```
$config['alidayu'] = [
        'app_key' => 'LTAIRdKEkYqxxxxx',
        'app_secret' => '6Vfl3lBuHexxxxxxxxxxxxxxxx',
        'signature' => '签名',
        ];
```

## Examples

´´´
<?php
use hegzh\AliyunSms\AliyunSms;


$mobile = "13800138000"; //国内手机号码
$templateCode = "SMS_75990000"; //短信模板 阿里云上申请
$templateParm = ['customer' => '客户名称'];//短信模板变量参数
$smsId = 1111; //短信业务流水id

$sms = new AliyunSms();
//发送短信
$res = $sms->sendSms($mobile, $templateCode, $templateParm, $smsId);


//查询短信记录
$date = "20170720";
$res = $sms->getSendDetails($mobile, $date);
?>

´´´
