<?php
//include 'Config.php';
//include_once 'Request/V20170525/SendSmsRequest.php';
//include_once 'Request/V20170525/QuerySendDetailsRequest.php';

require_once "../../aliyun-core/src/Config.php";

use hegzh\AliyunCore\Profile\DefaultProfile;
use hegzh\AliyunCore\DefaultAcsClient;
use hegzh\AliyunSms\Sms\Request\V20170525\SendSmsRequest;
use hegzh\AliyunSms\Sms\Request\V20170525\QuerySendDetailsRequest;
use hegzh\AliyunCore\Exception\ClientException;
use hegzh\AliyunCore\Exception\ServerException;

function sendSms()
{
    //此处需要替换成自己的AK信息
    $accessKeyId = "LTAIl1OH2ZRhcdpD";
    $accessKeySecret = "jcWBCp808MEfFU8GKtctwr0hXmuZp8";
    //短信API产品名
    $product = "Dysmsapi";
    //短信API产品域名
    $domain = "dysmsapi.aliyuncs.com";
    //暂时不支持多Region
    $region = "cn-hangzhou";

    //初始化访问的acsCleint


    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    $acsClient = new DefaultAcsClient($profile);

    $request = new SendSmsRequest;
    //必填-短信接收号码
    $request->setPhoneNumbers("13612846510");
    //必填-短信签名
    $request->setSignName("思贝克");

    $tplCode = 'SMS_75990470';
    $tplParms = ['customer' => '何光忠'];

    //必填-短信模板Code
    $request->setTemplateCode("SMS_75990470");
    //选填-假如模板中存在变量需要替换则为必填(JSON格式)
    $request->setTemplateParam(json_encode(["customer" => "何光忠"]));
    //选填-发送短信流水号
    $request->setOutId(mt_rand(100000,999999));

    //发起访问请求
    try {
        $acsResponse = $acsClient->getAcsResponse($request);
        var_dump($acsResponse);
    } catch (ClientException  $e) {
        var_dump($e->getErrorCode(), $e->getErrorMessage());
    } catch (ServerException  $e) {
        var_dump($e->getErrorCode(), $e->getErrorMessage());
    }

}

function querySendDetails()
{

    //此处需要替换成自己的AK信息
    $accessKeyId = "yourAccessKeyId";
    $accessKeySecret = "yourAccessKeySecret";
    //短信API产品名
    $product = "Dysmsapi";
    //短信API产品域名
    $domain = "dysmsapi.aliyuncs.com";
    //暂时不支持多Region
    $region = "cn-hangzhou";

    //初始化访问的acsCleint
    $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
    DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
    $acsClient = new DefaultAcsClient($profile);

    $request = new QuerySendDetailsRequest();
    //必填-短信接收号码
    $request->setPhoneNumber("15000000000");
    //选填-短信发送流水号
    $request->setBizId("abcdefgh");
    //必填-短信发送日期，支持近30天记录查询，格式yyyyMMdd
    $request->setSendDate("20170525");
    //必填-分页大小
    $request->setPageSize(10);
    //必填-当前页码
    $request->setContent(1);

    //发起访问请求
    $acsResponse = $acsClient->getAcsResponse($request);
    var_dump($acsResponse);
}

sendSms();
