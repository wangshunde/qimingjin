<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//echo $_GET['body'];
// echo  urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING']);
// echo "<br>";
// echo $_SERVER['HTTP_HOST'];
// echo "<br>";
// echo $_SERVER['PHP_SELF'];
// echo "<br>";
// echo $_SERVER['QUERY_STRING'];
// echo "<br>";
session_start();
$tem_pp = $_SESSION["test"];
//获取实际价格
$tem_pp_num = $tem_pp['num'];
//获取订单号
$tem_pp_dingdanhao=$tem_pp['dingdanhao'];
//echo $tem_pp_taocanname."<br>".$tem_pp_taocanprice."<br>".$tem_pp_num."<br>".$tem_pp_dingdanhao;
//exit();

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
    	//打印出 用户及商家的 支付用 信息
        //echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
}
//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("test");
$input->SetAttach("test");
$input->SetOut_trade_no(WxPayConfig::MCHID."-".$tem_pp_dingdanhao);
$input->SetTotal_fee("1");
$input->SetTotal_fee($tem_pp_num);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
//$input->SetNotify_url("http://www.yftong.biz/mndjk/weixin.php/Wxpay/wxpayget");
$input->SetNotify_url("http://www.yftong.biz/mndjk/Wxpay2/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);


echo '<font color="#f00"><b>&nbsp;&nbsp;页面跳转中......</b></font><br/>';
printf_info($order);


// 注销 传递的 session变量
unset($_SESSION["test"]);

$jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>都健康</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code+res.err_desc+res.err_msg);

                if (res.err_msg == "get_brand_wcpay_request:ok") {
                	//支付成功返回跳转到订单详情页面
                    window.location.href="http://www.yftong.biz/mndjk/weixin.php/Hdchongzhiok/index/dingdanhao/<?php echo $tem_pp_dingdanhao?>/chongzhiprice/<?php echo $tem_pp_num?>";
                }else{ 
				    // message: "已取消微信支付!"
                    window.location.href="http://www.yftong.biz/mndjk/weixin.php/Hdchongzhiok/index1/dingdanhao/<?php echo $tem_pp_dingdanhao?>/chongzhiprice/<?php echo $tem_pp_num?>";
				}
			}
		);
	}
	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	
</head>
<body>
<!--     <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px"><?php echo $tem_pp_num/100?>元</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div> -->
<script type="text/javascript">
	callpay();
</script>
</body>
</html>