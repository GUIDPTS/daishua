<?php
require_once("./inc.php");

@header('Content-Type: text/html; charset=UTF-8');

require_once(SYSTEM_ROOT."epay/micro.config.php");
require_once(SYSTEM_ROOT."epay/micro_notify.class.php");

if(!isset($_GET['out_trade_no'])) exit();
$out_trade_no = daddslashes($_GET['out_trade_no']);
$srow=$DB->get_row("SELECT * FROM shua_pay WHERE trade_no='{$out_trade_no}' limit 1");
if(!$srow)exit('该订单号不存在');

if ($srow['status']==1) {
	showalert('您所购买的商品已付款成功，感谢购买！',1,$out_trade_no,$srow['tid']);
	exit;
}else{
	$alipayNotify = new AlipayNotify($alipay_config);
	$verify_result = $alipayNotify->verifyReturn();
	if($verify_result){
		if($srow['status']==0){
			$DB->query("update `shua_pay` set `status` ='1' where `trade_no`='{$out_trade_no}'");
			if($DB->affected()>=1){
				$DB->query("update `shua_pay` set `endtime` ='$date' where `trade_no`='{$out_trade_no}'");
				processOrder($srow);
			}
		}
		showalert('您所购买的商品已付款成功，感谢购买！',1,$out_trade_no,$srow['tid']);
		exit;
	}
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
    <link href="//cdn.staticfile.org/ionic/1.3.2/css/ionic.min.css" rel="stylesheet" />
</head>
<body>
<div class="bar bar-header bar-light" align-title="center">
	<h1 class="title">支付结果页面</h1>
</div>
<div class="has-header" style="padding: 5px;position: absolute;width: 100%;">
<div class="text-center" style="color: #a09ee5;">
<i class="icon ion-close-circled" style="font-size: 80px;"></i><br>
<span>支付未完成</span>
</div>
</div>
<script>
document.querySelector('body').addEventListener('touchmove', function (event) {
	event.preventDefault();
});
</script>
</body>
</html>