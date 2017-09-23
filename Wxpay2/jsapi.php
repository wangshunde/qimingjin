<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
//session_start();
//$tem_pp = $_SESSION["test"];
//$reload = $tem_pp['success_url'];
//获取实际价格
//$tem_pp_num = $tem_pp['num'];
//获取名称
//$tem_pp_name=$tem_pp['body'];

$reload = $_POST['success_url'];
//获取实际价格
$tem_pp_num = $_POST['num'];
//获取名称
$tem_pp_name = $_POST['body'];
//echo $tem_pp_name;

//初始化日志
$logHandler= new CLogFileHandler("logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

//打印输出数组信息
// function printf_info($data)
// {
//     foreach($data as $key=>$value){
     	//打印出 用户及商家的 支付用 信息
        //echo "<font color='#00ff55;'>$key</font> : $value <br/>";
//     }
// }
//①、获取用户openid
$tools = new JsApiPay();
//$openId = $tools->GetOpenid();
$openId = $_POST['openid'];
//②、统一下单
$input = new WxPayUnifiedOrder();

$input->SetBody($tem_pp_name);
$input->SetAttach($tem_pp_name);
$input->SetOut_trade_no(WxPayConfig::MCHID.date('YmdHis')) ;
//$input->SetOut_trade_no(WxPayConfig::MCHID."-".$tem_pp_dingdanhao);
$input->SetTotal_fee($tem_pp_num*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url("http://zzj00300003.com/dlteach/Wxpay2/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
//var_dump($input);
$order = WxPayApi::unifiedOrder($input);
echo '<font color="#f00"><b>&nbsp;&nbsp;页面跳转中......</b></font><br/>';
//printf_info($order);

// 注销 传递的 session变量
//unset($_SESSION["test"]);

$jsApiParameters = $tools->GetJsApiParameters($order);
//获取共享收货地址js函数参数
//$editAddress = $tools->GetEditAddressParameters();

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
    <title>旗明晋</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters;?>,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code+res.err_desc+res.err_msg);

                if (res.err_msg == "get_brand_wcpay_request:ok") {
                	//支付成功返回跳转到订单详情页面
                	alert("支付成功!");
                    //window.location.href="http://wswsdwsd.wicp.net/mndjk01/weixin.php/Mnzhifuok/index/action/1/dingdanhao/<?php echo $tem_pp_dingdanhao?>/taocanprice/<?php echo $tem_pp_taocanprice?>/shijiprice/<?php echo $tem_pp_num?>/taocanname/<?php echo $tem_pp_taocanname?>";
                    window.location.href="<?php echo $reload;?>";
                }else if (res.err_msg == "get_brand_wcpay_request:cancel") {  
				    message: "已取消微信支付!"
				    alert("支付取消");
				    //window.location.href="<?php echo $reload;?>";
				    //window.location.href="http://www.baidu.com";
                    //window.location.href="http://wswsdwsd.wicp.net/mndjk01/weixin.php/Mnzhifufalse/index/action/2/dingdanhao/<?php echo $tem_pp_dingdanhao?>/taocanprice/<?php echo $tem_pp_taocanprice?>/shijiprice/<?php echo $tem_pp_num?>/taocanname/<?php echo $tem_pp_taocanname?>";
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
<script type="text/javascript">
	callpay();
</script>
</body>
</html>