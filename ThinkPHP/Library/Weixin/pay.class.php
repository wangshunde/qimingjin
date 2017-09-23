<?php 
namespace Weixin;
class pay{
	private $appId;
  	private $appSecret;

	public function __construct($appId, $appSecret) {
	    $this->appId = $appId;
	    $this->appSecret = $appSecret;
  	}

	public function pay($url,$obj,$PARTNERKEY){
		$obj['nonce_str'] = $this->create_noncestr();
		$stringA = $this->formatQueryParaMap($obj,false);
		$stringSignTemp = $stringA."&key=".$PARTNERKEY;
		$sign = strtoupper(md5($stringSignTemp));
		$obj['sign'] = $sign;
		//var_dump($obj);
		$postXml = $this->arrayToXml($obj);
		//var_dump($postXml);
		$responseXml = $this->curl_post_ssl($url,$postXml);
		//var_dump($responseXml);
		return $responseXml;


	}

	public function create_noncestr($length = 32){
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0;$i<$length;$i++){
			$str .= substr($chars, mt_rand(0,strlen($chars)-1),1);
		}
		return $str;
	}

	public function formatQueryParaMap($paraMap,$urlencode){
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
			if(null != $v && "null" != $v && "sign" != $k){
				if($urlencode){
					$v = urlencode($v);
				}
				$buff .= $k ."=". $v ."&";
			}
		}
		$reqPar;
		if(strlen($buff)>0){
			$reqPar = substr($buff,0,strlen($buff)-1);
		}
		return $reqPar;
	}

	//数组转XML
	public function arrayToXml($arr){
		$xml = "<xml>";
		
		foreach ($arr as $key => $val) {
			if(is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}

	/*function curl_post_ssl($url,$vars,$second=30){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

		curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/cert/apiclient_cert.pem');
		curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/cert/apiclient_key.pem');
		curl_setopt($ch,CURLOPT_CAINFO,getcwd().'/cert/rootca.pem');

		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		}else{
			$error = curl_error($ch);
			echo "call faild,errorCode:$error\n";
			curl_close($ch);
			return false;
		}

	}*/
	public function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
	{
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		
		//以下两种方式需选择一种
		
		//第一种方法，cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		//curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/cert/apiclient_cert.pem');
		//默认格式为PEM，可以注释
		//curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,getcwd().'/cert/apiclient_key.pem');

		curl_setopt($ch,CURLOPT_CAINFO,'cert'.DIRECTORY_SEPARATOR.'rootca.pem');
		//curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).DIRECTORY_SEPARATOR.'cert'.DIRECTORY_SEPARATOR.'rootca.pem');

 		if( count($aHeader) >= 1 ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		}


		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_error($ch);
			echo "call faild, errorCode:$error\n"; 
			curl_close($ch);
			return false;
		}
	}

}






 ?>