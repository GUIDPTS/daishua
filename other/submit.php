<?php
require 'inc.php';
session_start();
@header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>正在为您跳转到支付页面，请稍候...</title>
    <style type="text/css">
        body {margin:0;padding:0;}
        p {position:absolute;
            left:50%;top:50%;
            width:330px;height:30px;
            margin:-35px 0 0 -160px;
            padding:20px;font:bold 14px/30px "宋体", Arial;
            background:#f9fafc url(../assets/load.gif) no-repeat 20px 26px;
            text-indent:22px;border:1px solid #c5d0dc;}
        #waiting {font-family:Arial;}
    </style>
<script>
function open_without_referrer(link){
document.body.appendChild(document.createElement('iframe')).src='javascript:"<script>top.location.replace(\''+link+'\')<\/script>"';
}
</script>
</head>
<body>
<?php

$type=isset($_GET['type'])?daddslashes($_GET['type']):exit('No type!');
$orderid=isset($_GET['orderid'])?daddslashes($_GET['orderid']):exit('No orderid!');
if(!is_numeric($orderid))exit('订单号不符合要求!');
$row=$DB->get_row("SELECT * FROM shua_pay WHERE trade_no='{$orderid}' limit 1");
if(!$row['trade_no'])exit('该订单号不存在，请返回来源地重新发起请求！');
if($row['money']=='0' || !preg_match('/^[0-9.]+$/', $row['money']))exit('订单金额不合法');
if($row['status']>=1)exit('该订单已支付完成，请<a href="/">返回重新生成订单</a>');

$DB->query("update `shua_pay` set `type` ='$type' where `trade_no`='{$orderid}'");

if($type=='alipay'&&$conf['alipay_api']==5 || $type=='qqpay'&&$conf['qqpay_api']==5 || $type=='wxpay'&&$conf['wxpay_api']==5){
	echo "<script>window.location.href='./codepay.php?type={$type}&trade_no={$orderid}';</script>";
	exit;
}elseif($type=='wxpay'&&$conf['wxpay_api']==6){
	require_once(SYSTEM_ROOT."epay/micro.config.php");
	require_once(SYSTEM_ROOT."epay/micro_submit.class.php");
	$parameter = array(
		"appid" => trim($alipay_config['appid']),
		"name"	=> $row['name'],
		"type" => "cashier",
		"money"	=> $row['money'],
		"out_trade_no"	=> $orderid,
		"notify_url"	=> $siteurl.'micro_notify.php',
		"return_url"	=> $siteurl.'micro_return.php',
		"mchid"	=> trim($alipay_config['mchid'])
	);
	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"POST", "正在跳转");
	echo $html_text;
}elseif($type=='alipay'&&$conf['alipay_api']==2 || $type=='qqpay'&&$conf['qqpay_api']==2 || $type=='wxpay'&&$conf['wxpay_api']==2){
	require_once(SYSTEM_ROOT."epay/epay.config.php");
	require_once(SYSTEM_ROOT."epay/epay_submit.class.php");
	$parameter = array(
		"pid" => trim($alipay_config['partner']),
		"type" => $type,
		"notify_url"	=> $siteurl.'epay_notify.php',
		"return_url"	=> $siteurl.'epay_return.php',
		"out_trade_no"	=> $orderid,
		"name"	=> $row['name'],
		"money"	=> $row['money'],
		"sitename"	=> $conf['sitename']
	);
	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"POST", "正在跳转");
	echo $html_text;

}elseif($type=='alipay'){
	if($conf['alipay_api']==3){
		echo "<script>window.location.href='./alipay.php?trade_no={$orderid}';</script>";
		exit;
	}
	require_once(SYSTEM_ROOT."alipay/alipay.config.php");
	require_once(SYSTEM_ROOT."alipay/alipay_submit.class.php");
	//构造要请求的参数数组，无需改动
	if(checkmobile()==true && $conf['alipay2_api']==1){
		$alipay_service = "alipay.wap.create.direct.pay.by.user";
	}else{
		$alipay_service = "create_direct_pay_by_user";
	}
	$parameter = array(
		"service" => $alipay_service,
		"partner" => trim($conf['alipay_pid']), //合作身份者id
		"seller_id" => trim($conf['alipay_pid']), //收款支付宝用户号
		"payment_type"	=> "1", //支付方式
		"notify_url"	=> $siteurl.'alipay_notify.php', //服务器异步通知页面路径
		"return_url"	=> $siteurl.'alipay_return.php', //页面跳转同步通知页面路径
		"out_trade_no"	=> $orderid, //商户订单号
		"subject"	=> $row['name'], //订单名称
		"total_fee"	=> $row['money'], //付款金额
		"_input_charset"	=> strtolower('utf-8')
	);
	if(checkmobile()==true && $conf['alipay2_api']==1){
		$parameter['app_pay'] = "Y";
	}

	//建立请求
	$alipaySubmit = new AlipaySubmit($alipay_config);
	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "正在跳转");
	echo $html_text;
}elseif($type=='wxpay'){
	$DB->query("update `shua_pay` set `domain` ='{$_SERVER['HTTP_HOST']}' where `trade_no`='{$orderid}'");
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')!==false){
		echo "<script>window.location.href='./wxjspay.php?trade_no={$orderid}&d=1';</script>";
	}elseif(checkmobile()==true){
		echo "<script>window.location.href='./wxwappay.php?trade_no={$orderid}';</script>";
	}else{
		echo "<script>window.location.href='./wxpay.php?trade_no={$orderid}';</script>";
	}
}elseif($type=='qqpay'){
	if(checkmobile()==true){
		echo "<script>window.location.href='./qqwappay.php?trade_no={$orderid}';</script>";
	}else{
		echo "<script>window.location.href='./qqpay.php?trade_no={$orderid}';</script>";
	}
}

?>
<p>正在为您跳转到支付页面，请稍候...</p>
</body>
</html>