<?php
/**
 * Chatopera 开发者平台 PHP SDK
 *	@Author: Hai Liang Wang
 *  @Company: 北京华夏春松科技有限公司
 *  All right reserved.
 */

namespace Chatopera\SDK;
use Exception;


/**
 * Class Chatbot 企业聊天机器人
 * @package Chatopera\SDK
 */
class Chatbot {

    private $baseUrl = "https://bot.chatopera.com"; // 服务地址
    private $clientId; // 机器人 ClientId
    private $clientSecret; // 机器人 Secret

    /**
     * Chatbot constructor.
     * @param $clientId 机器人 ClientId
     * @param $clientSecret 机器人 Secret
     */
    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * 认证签名算法
     * @param $clientId
     * @param $secret
     * @param $method
     * @param $path
     * @return string
     */
    private function generate($clientId, $secret, $method, $path)
    {

        if($clientId == null || $secret == null)
            return null;

        $timestamp = time();
        $random = rand(1000000000, 9999999999);
        $signature = hash_hmac('sha1', $clientId.$timestamp.$random.$method.$path, $secret);
        $json =json_encode(array(
            'appId' => $clientId,
            'timestamp' => $timestamp,
            'random' => $random,
            'signature' => $signature
        ));

        return base64_encode($json);
    }

    /**
     * 查看机器人详情
     * @return mixed
     * @throws Exception
     */
    public function detail(){
        $service_path = '/api/v1/chatbot/' . $this->clientId;
        $service_url = $this->baseUrl . $service_path;
        $service_method = "GET";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        return $this->purge(json_decode($curl_response, true));
    }

    /**
     * 检索多轮对话
     * @param $userId 用户唯一标识
     * @param $textMessage 问题
     * @return mixed
     * @throws Exception
     */
    public function conversation($userId, $textMessage){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/conversation/query';
        $service_url = $this->baseUrl . $service_path;
        $service_method = "POST";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $data = json_encode(array(
            "fromUserId" => $userId,
            "textMessage" => $textMessage,
            "isDebug" => false
        ));

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json",
            "Content-Length: ".strlen($data)
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_POSTFIELDS, $data);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        return json_decode($curl_response, true);
    }

    /**
     * 检索机器人知识库
     * @param $userId 用户唯一标识
     * @param $query  问题
     * @return mixed
     * @throws Exception
     */
    public function faq($userId, $query){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/faq/query';
        $service_url = $this->baseUrl . $service_path;
        $service_method = "POST";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $data = json_encode(array(
            "fromUserId" => $userId,
            "query" => $query
        ));

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json",
            "Content-Length: ".strlen($data)
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_POSTFIELDS, $data);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        return json_decode($curl_response, true);
    }


    /**
     * 查询用户列表
     * @param int $limit 每页数据条数
     * @param int $page 页面索引
     * @param string $sortby 排序规则
     * @return mixed
     * @throws Exception
     */
    public function users($limit = 50, $page = 1, $sortby = "-lasttime"){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/users?page='.$page.'&limit='.$limit."&sortby=".$sortby;
        $service_url = $this->baseUrl . $service_path;
        $service_method = "GET";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        return $this->purge(json_decode($curl_response, true));
    }


    /**
     * 查看一个用户的聊天历史
     * @param $userId 用户唯一标识
     * @param int $limit 每页数据条数
     * @param int $page 页面索引
     * @param string $sortby 排序规则[-lasttime: 最后对话时间降序]
     * @return mixed
     * @throws Exception
     */
    public function chats($userId, $limit = 50, $page = 1, $sortby = '-lasttime'){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/users/'.$userId.'/chats?page='.$page.'&limit='.$limit."&sortby=".$sortby;
        $service_url = $this->baseUrl . $service_path;
        $service_method = "GET";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        return $this->purge(json_decode($curl_response, true));
    }

    /**
     * 屏蔽用户
     * @param $userId 用户唯一标识
     * @return bool 执行是否成功
     * @throws Exception
     */
    public function mute($userId){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/users/'.$userId.'/mute';
        $service_url = $this->baseUrl . $service_path;
        $service_method = "POST";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        $result = json_decode($curl_response, true);

        if(isset($result['rc']) && ($result['rc'] == 0)){
            return true;
        }
        return false;
    }


    /**
     * 取消屏蔽用户
     * @param $userId 用户唯一标识
     * @return bool 执行是否成功
     * @throws Exception
     */
    public function unmute($userId){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/users/'.$userId.'/unmute';
        $service_url = $this->baseUrl . $service_path;
        $service_method = "POST";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        $result = json_decode($curl_response, true);

        if(isset($result['rc']) && ($result['rc'] == 0)){
            return true;
        }
        return false;
    }


    /**
     * 检测用户是否被屏蔽
     * @param $userId 用户唯一标识
     * @return bool 用户是否被屏蔽
     * @throws Exception
     */
    public function ismute($userId){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/users/'.$userId.'/ismute';
        $service_url = $this->baseUrl . $service_path;
        $service_method = "POST";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        $result = json_decode($curl_response, true);


        if(isset($result['rc']) && ($result['rc'] == 0)){
            return $result['data']['mute'];
        }
        throw new Exception("Unexpected Chatbot Response.");
    }

    /**
     * 读取用户画像
     * @param $userId 用户唯一标识
     * @return mixed
     * @throws Exception
     */
    public function user($userId){
        $service_path = '/api/v1/chatbot/' . $this->clientId . '/users/'.$userId.'/profile';
        $service_url = $this->baseUrl . $service_path;
        $service_method = "POST";
        $request = curl_init($service_url);
        $token = $this->generate($this->clientId, $this->clientSecret, $service_method, $service_path);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: $token",
            "Accept: application/json"
        );

        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_CUSTOMREQUEST, $service_method);
        curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
        $curl_response = curl_exec($request);

        $http_code = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if($http_code != 200){
            throw new Exception("Wrong Chatbot Response.");
        }

        return json_decode($curl_response, true);
    }


    /**
     * 删除内部ID
     * @param $resp
     * @return mixed
     */
    private function purge($resp){
        if(isset($resp["data"]) && is_array($resp["data"])){
            foreach ($resp["data"] as $key => $value){
                // data: sublist
                if(is_array($resp["data"][$key])){
                    foreach ($resp["data"][$key] as $key2 => $value2){
                        if($key2 == "chatbotID")
                            unset($resp["data"][$key][$key2]);
                    }
                } else { // data: plain object
                    if($key == "chatbotID"){
                        unset($resp["data"][$key]);
                    }
                }
            }
        }
        return $resp;
    }
}
