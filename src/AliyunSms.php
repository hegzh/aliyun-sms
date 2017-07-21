<?php

namespace hegzh\AliyunSms;

/**
 * 短信发送
 * Created by PhpStorm.
 * User: Gilbert.Ho
 * Date: 2017/7/18
 * Time: 15:34
 */
require_once VENDOR_PATH . "/hegzh/aliyun-core/src/Config.php";

use hegzh\AliyunCore\Profile\DefaultProfile;
use hegzh\AliyunCore\DefaultAcsClient;
use hegzh\AliyunSms\Request\V20170525\SendSmsRequest;
use hegzh\AliyunSms\Request\V20170525\QuerySendDetailsRequest;
use hegzh\AliyunCore\Exception\ClientException;
use hegzh\AliyunCore\Exception\ServerException;

class AliyunSms
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
     * @param array $templateParms 短信模板 ['customer' => '阿里大于']
     * @param int $bizId 外部流水id
     */
    public function sendSms($mobile, $templateCode, $templateParms = [], $bizId = '')
    {
        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);
        $acsClient = new DefaultAcsClient($profile);

        $request = new SendSmsRequest;
        $request->setPhoneNumbers($mobile); //必填-短信接收号码
        $request->setSignName($this->accessSignName);//必填-短信签名
        $request->setTemplateCode($templateCode);//必填-短信模板Code
        $request->setTemplateParam(json_encode($templateParms));//选填-假如模板中存在变量需要替换则为必填(JSON格式)
        if (!empty($bizId)) {
            $request->setOutId($bizId);//选填-发送短信流水号
        }

        $return = ['state' => true];
        try {
            //发起访问请求
            $acsResponse = $acsClient->getAcsResponse($request);
            if ($acsResponse->Code != 'OK') {
                $return['state'] = false;
                $return['code'] = $acsResponse->Code;
                $return['error'] = $acsResponse->Message;
            }
        } catch (ClientException  $e) {
            $return['state'] = false;
            $return['code'] = $e->getErrorCode();
            $return['error'] = $e->getErrorMessage();
        } catch (ServerException  $e) {
            $return['state'] = false;
            $return['code'] = $e->getErrorCode();
            $return['error'] = $e->getErrorMessage();
        }

        return $return;
    }

    /**
     * 获取短信发送记录
     * @param string $mobile 手机号码
     * @param string $date 格式yyyyMMdd 20170720
     * @param int $page 当前页码
     * @param int $pageSize 每页长度
     * @param string $bizId 外部流水id
     */
    public function getSmsDetails($mobile, $date, $page = 1, $pageSize = 10, $bizId = '')
    {
        //初始化访问的acsCleint
        $profile = DefaultProfile::getProfile($this->region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($this->region, $this->region, $this->product, $this->domain);
        $acsClient = new DefaultAcsClient($profile);
        $request = new QuerySendDetailsRequest();

        $request->setPhoneNumber($mobile);//必填-短信接收号码
        if (!empty($bizId)) {
            $request->setBizId($bizId);//选填-短信发送流水号
        }
        $request->setSendDate($date);//必填-短信发送日期，支持近30天记录查询，格式yyyyMMdd
        $request->setPageSize($pageSize);//必填-分页大小
        $request->setCurrentPage($page);//必填-当前页码

        $return = ['state' => true];
        try {
            //发起访问请求
            $acsResponse = $acsClient->getAcsResponse($request);
            if ($acsResponse->Code != 'OK') {
                $return['state'] = false;
                $return['code'] = $acsResponse->Code;
                $return['error'] = $acsResponse->Message;
            }
            $return['total'] = $acsResponse->TotalCount; //总数
            if (!empty($acsResponse->SmsSendDetailDTOs->SmsSendDetailDTO)) {
                $tmp = $acsResponse->SmsSendDetailDTOs->SmsSendDetailDTO;
                foreach ($tmp as $v) {
                    $return['data'][] = [
                        'OutId' => $v->OutId, //流水id
                        'SendDate' => $v->SendDate, //发送日期
                        'SendStatus' => $v->SendStatus, //发送状态
                        'ReceiveDate' => $v->ReceiveDate, //接收状态
                        'ErrCode' => $v->ErrCode, //错误代码
                        'TemplateCode' => $v->TemplateCode, //短信编号
                        'Content' => $v->Content,
                        'PhoneNum' => $v->PhoneNum,
                    ];
                }
            }
        } catch (ClientException  $e) {
            $return['state'] = false;
            $return['code'] = $e->getErrorCode();
            $return['error'] = $e->getErrorMessage();
        } catch (ServerException  $e) {
            $return['state'] = false;
            $return['code'] = $e->getErrorCode();
            $return['error'] = $e->getErrorMessage();
        }

        return $return;
    }

}