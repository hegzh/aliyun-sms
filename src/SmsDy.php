<?php

namespace hegzh\AliyunSms;

/**
 * 短信发送
 * Created by PhpStorm.
 * User: Gilbert.Ho
 * Date: 2017/7/18
 * Time: 15:34
 */
use hegzh\AliyunCore\Profile\DefaultProfile;
use hegzh\AliyunCore\DefaultAcsClient;
use hegzh\AliyunSms\Request\V20170525\SendSmsRequest;
use hegzh\AliyunSms\Request\V20170525\QuerySendDetailsRequest;
use hegzh\AliyunCore\Exception\ClientException;
use hegzh\AliyunCore\Exception\ServerException;

class SmsDy
{
    private $product = "Dysmsapi";//短信API产品名
    private $domain = "dysmsapi.aliyuncs.com";//短信API产品域名
    private $region = "cn-hangzhou"; //暂时不支持多Region

    private $accessKeyId = null;
    private $accessKeySecret = null;
    private $accessSignName = null;

    public function __construct()
    {
        $config = config('alidayu');
        $this->accessKeyId = empty($config['app_key']) ? '' : $config['app_key'];
        $this->accessKeySecret = empty($config['app_secret']) ? '' : $config['app_secret'];
        $this->accessSignName = empty($config['signature']) ? '阿里云' : $config['signature'];
    }

    /**
     * 发送短信
     * @param string $mobile 手机号码 136128xxxxx
     * @param string $templateCode 短信
     * @param array $templateParm 短信模板 ['customer' => '阿里大于']
     * @param int $smsId 短信id
     */
    public function sendSms($mobile, $templateCode, $templateParm = [], $smsId = 0)
    {
        $smsId = empty($smsId) ? mt_rand(100000, 999999) : (int)$smsId;

        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);
        $acsClient = new DefaultAcsClient($profile);

        $request = new SendSmsRequest;
        $request->setPhoneNumbers($mobile); //必填-短信接收号码
        $request->setSignName($this->accessSignName);//必填-短信签名
        $request->setTemplateCode($templateCode);//必填-短信模板Code
        $request->setTemplateParam(json_encode($templateParm));//选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setOutId($smsId);//选填-发送短信流水号

        //发起访问请求
        try {
            $acsResponse = $acsClient->getAcsResponse($request);
            return $acsResponse;
        } catch (ClientException  $e) {
            var_dump($e->getErrorCode(), $e->getErrorMessage());
        } catch (ServerException  $e) {
            var_dump($e->getErrorCode(), $e->getErrorMessage());
        }

    }

    /**
     * 获取短信发送记录
     * @param string $mobile 手机号码
     * @param string $date 格式yyyyMMdd 20170720
     * @param int $page 当前页码
     * @param int $pageSize 每页长度
     * @param string $smsId 短信id
     */
    public function getSendDetails($mobile, $date, $page = 1, $pageSize = 10, $smsId = '')
    {
        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);
        $acsClient = new DefaultAcsClient($profile);

        $request = new QuerySendDetailsRequest();

        $request->setPhoneNumber($mobile);//必填-短信接收号码
        if (!empty($smsId)) {
            $request->setBizId($smsId);//选填-短信发送流水号
        }

        $request->setSendDate($date);//必填-短信发送日期，支持近30天记录查询，格式yyyyMMdd
        $request->setPageSize($pageSize);//必填-分页大小
        $request->setContent($page);//必填-当前页码

        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        return $acsResponse;
    }

}