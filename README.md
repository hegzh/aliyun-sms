# aliyun-sms

阿里云短信服务 PHP SDK 的 composer 封装

TP5

[帮助文档](https://help.aliyun.com/document_detail/55451.html?spm=5176.sms-account.109.2.56907c16xG4lWM)


## Thinkphp 5 config 配置信息
```
$config['alidayu'] = [
        'app_key' => 'LTAIRdKEkYqxxxxx',
        'app_secret' => '6Vfl3lBuHexxxxxxxxxxxxxxxx',
        'signature' => '签名',
        ];
```

## Examples
```
<?php
use hegzh\AliyunSms\AliyunSms;

$mobile = "13800138000"; //国内手机号码
$templateCode = "SMS_75990000"; //短信模板 阿里云上申请
$templateParms = ['customer' => 'hegzh'];//短信模板变量参数
$bizId = 1111; //短信业务流水id

$sms = new AliyunSms();
//发送短信
$res = $sms->sendSms($mobile, $templateCode, $templateParms, $bizId);
Response:
{
    "state": false, //发送状态
    "code": "InvalidAccessKeyId.NotFound", //错误编码
    "error": "Specified access key is not found.", //错误消息
}

//查询短信记录
$date = "20170720";
$res = $sms->getSmsDetails($mobile, $date);
Response:
{
    "state": true, //返回状态
    "total": 2, //总记录数
    'code' => 'InvalidAccessKeyId.NotFound', //错误编码
    'error' => 'Specified access key is not found.', //错误消息
    "data": [
        {
            "OutId": "277770", //外部流水id
            "SendDate": "2017-07-21 11:11:25", //发送情趣时间
            "SendStatus": 3, //发送状态
            "ReceiveDate": "2017-07-21 11:11:25", //接收时间
            "ErrCode": "DELIVRD", //错误代码
            "TemplateCode": "SMS_75990400", // 短信模板编码
            "Content": "【签名】尊敬的hegzh，欢迎您使用阿里大鱼短信服务，阿里大鱼将为您提供便捷的通信服务！",
            "PhoneNum": "13800138000", //手机号码
        }
    ]
}
?>
```
