<?php
namespace Weixin;
//支付宝签名

        

class aliapp{
    $notify_url = 'http://www.alipay.cn/server/notify_url.php'; //回调地址
    $path = BASE_ROOT_PATH.DS.DIR_SHOP.DS.'control/res_pay.txt'; // 私钥地址  根据当前框架的路径变量填写的绝对路径
    // $path = BASE_ROOT_PATH.DS.DIR_SHOP.DS.'control/key/rsa_private_key.pem';
    $privateKey = file_get_contents($path);
    $partner = "208812146xxxxxxx"; // 商家id 2088开头 16位 
    $seller = "www@baidu.com"; //商家名称
    $dataString = array(
        "app_id"        => "2016090801xxxxxx",//appid
        "method"        => "alipay.trade.app.pay",//无需修改
        "notify_url"    =>  $notify_url, //此参数可选  开发平台填写了授权回调地址的 话这里无需填写
        "sign_type"     => "RSA", //无需修改
        "version"       => "1.0", //当前app支付版本 无需修改
        "timestamp"     => date('Y-m-d H:i:s',time()),//yyyy-MM-dd HH:mm:ss
        "biz_content"   => '{"timeout_express":"60m","seller_id":"","product_code":"QUICK_MSECURITY_PAY","total_amount":"'.$total_fee.'","subject":"'.$subject.'","body":"'.$body.'","out_trade_no":"'.$out_trade_no.'"}',
        "charset"       => "utf-8",
        "format"        => "json"
    );

    ksort( $dataString );

    //重新组装参数
    $params = array();
    foreach($dataString as $key => $value){
        //生成加密的签名参数
        $params[] = $key .'='. rawurlencode($value);
        // 生成未加密的签名参数  用此参数去签名
        $signparams[] = $key .'='. $value;
    }

    //2种参数 都用&符合拼接
    $dataString = implode('&', $params);
    $signString = implode('&', $signparams);
        

    $res = openssl_get_privatekey($privateKey);
  
    openssl_sign($signString, $sign, $res,OPENSSL_ALGO_SHA1);
     
    openssl_free_key($res);
        
    $sign = urlencode(base64_encode($sign));


    $dataString.='&sign='.$sign;
        
    // ios 使用 openshare  SDK 返回数据连接
    //$iOSLink= "alipay://alipayclient/?".urlencode(json_encode(array('requestType' => 'SafePay', "fromAppUrlScheme" => /*iOS App的url schema，支付宝回调用*/"openshare","dataString"=>$dataString)));
        //$return_data['ios+'] =$iOSLink;
        
    $return_data['ios'] = $dataString;
    $return_data['android'] =$dataString;
    return $return_data;


public function pay(){
    $partner = "";  //你的pid
    $seller_id = "";  //seller_id
    $subject = "支付宝移动支付测试";  //交易主题
    $body = "支付宝移动支付测试detail";  //交易详细说明
    $total_fee = "0.01";    //支付金额 单位是元
    $out_trade_no = "";  //自己业务系统生成的交易no，可以唯一标识
    $rsa_path = "";  //rsa私钥路径
    $notify_url = "";    //接收支付结果通知url

    $data = array();
    $data['service'] = "mobile.securitypay.pay"; 
    $data['partner'] =$partner;
    $data['_input_charset'] = "utf-8";
    $data['notify_url'] = $notify_url;
    $data['out_trade_no'] = $out_trade_no;    
    $data['subject'] = $subject;
    $data['payment_type'] = "1";
    $data['seller_id'] = $seller_id;
    $data['total_fee'] = $total_fee;
    $data['body'] = $body;

    //签名
    $unsign_str =createLinkString(argSort($data));
    $sign =rsaSign($unsign_str, $rsa_path);
    $sign = urlencode(mb_convert_encoding($sign, "UTF-8"));  //需要进行utf8格式转换

    $pay_params = $unsign_str . "&sign=" . $sign . "&sign_type=RSA";
}
/**
 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
 * @param $para mixed 需要拼接的数组
 * @return string 拼接完成以后的字符串
 */
public static function createLinkString($para) {    
  $arg  = "";    
  while (list ($key, $val) = each ($para)) {        
    if($val == "") {            
      continue;        
    }        
    $arg.=$key."=".$val."&";    
  }    
  //去掉最后一个&字符    
  $arg = substr($arg,0,count($arg)-2);    
  //如果存在转义字符，那么去掉转义    
  if(get_magic_quotes_gpc()){
    $arg = stripslashes($arg);
  }    
  return $arg;
  }

/**
 * 数组排序 按照ASCII字典升序
 * @param $para mixed 排序前数组
 * @return mixed 排序后数组
 */
public static function argSort($para) {    
  ksort($para);    
  reset($para);    
  return $para;
}

/**
 * RSA签名
 * @param $data string 待签名数据
 * @param $private_rsa_path string 用户私钥地址
 * @return mixed
 *      失败:false
 *      成功:签名结果
 */
public static function rsaSign($data, $private_rsa_path) {    
  $private_rsa = file_get_contents($private_rsa_path);    
  $res = openssl_get_privatekey($private_rsa);    
  if(!$res) {        
    return false;    
  }    
  openssl_sign($data, $sign, $res);    
  openssl_free_key($res);    
  //base64编码    
  $sign = base64_encode($sign);    
  return $sign;
}



}



?>