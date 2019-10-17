<?php
/*支付接口订单监控文件
说明：用于请求支付接口订单列表，同步未通知到本站的订单，防止漏单。
监控频率建议5分钟一次
监控地址：/cron.php?key=监控密钥
注意：千万不要监控太快或使用多节点监控！！！否则会被支付接口自动屏蔽IP地址
*/

if(preg_match('/Baiduspider/', $_SERVER['HTTP_USER_AGENT']))exit;
include("./includes/common.php");

if (function_exists("set_time_limit"))
{
	@set_time_limit(0);
}
if (function_exists("ignore_user_abort"))
{
	@ignore_user_abort(true);
}

@header('Content-Type: text/html; charset=UTF-8');

if(empty($conf['cronkey']))exit("请先设置好监控密钥");
if($conf['cronkey']!=$_GET['key'])exit("监控密钥不正确");

if($_GET['do']=='pricejk'){
	$cron_lasttime = $DB->get_column("SELECT v FROM shua_config WHERE k='pricejk_lasttime' LIMIT 1");
	$pricejk_time = $conf['pricejk_time']?$conf['pricejk_time']-50:50;
	if(!isset($_GET['test']) && time()-strtotime($cron_lasttime)<$pricejk_time)exit('上次更新时间:'.$cron_lasttime);
	saveSetting('pricejk_lasttime',$date);
	$success = 0;
	$is_need = 0;
	$rs=$DB->query("SELECT * FROM shua_shequ WHERE type IN (0,1,2,9,11,12) ORDER BY id ASC");
	while($res = $DB->fetch($rs))
	{
		$tcount = $DB->count("SELECT count(*) FROM shua_tools WHERE is_curl=2 AND shequ='{$res['id']}' AND cid IN ({$conf['pricejk_cid']})");
		if($tcount>0 && $res['username'] && $res['password']){
			$is_need++;
			if($res['type']==0 || $res['type']==2){
				$results = pricejk_jiuwu($res['id'],$success);
				if($results===true){
					saveSetting('pricejk_status','ok');
				}else{
					saveSetting('pricejk_status',$results);
					echo '对接站点ID'.$res['id'].'：'.$results.'<br/>';
				}
			}elseif($res['type']==1){
				$results = pricejk_yile($res['id'],$success);
				if($results===true){
					saveSetting('pricejk_status','ok');
				}else{
					saveSetting('pricejk_status',$results);
					echo '对接站点ID'.$res['id'].'：'.$results.'<br/>';
				}
			}elseif($res['type']==9){
				$results = pricejk_kashangwl($res['id'],$success);
				if($results===true){
					saveSetting('pricejk_status','ok');
				}else{
					saveSetting('pricejk_status',$results);
					echo '对接站点ID'.$res['id'].'：'.$results.'<br/>';
				}
			}elseif($res['type']==11){
				$results = pricejk_jumeng($res['id'],$success);
				if($results===true){
					saveSetting('pricejk_status','ok');
				}else{
					saveSetting('pricejk_status',$results);
					echo '对接站点ID'.$res['id'].'：'.$results.'<br/>';
				}
			}elseif($res['type']==12){
				$results = pricejk_this($res['id'],$success);
				if($results===true){
					saveSetting('pricejk_status','ok');
				}else{
					saveSetting('pricejk_status',$results);
					echo '对接站点ID'.$res['id'].'：'.$results.'<br/>';
				}
			}
		}
	}
	if($is_need==0){
		exit('没有需要监控价格的商品');
	}else{
		exit('成功更新'.$success.'个商品的价格');
	}
}
elseif($_GET['do'] == 'rank') {
	if(!$conf['rank_reward'])exit('当前站点未开启分站排行榜奖励');
	$limit = intval($conf['rank_reward']);
	$cron_lasttime = $DB->get_column("SELECT `v` FROM `shua_config` WHERE `k` = 'cron_rank_time' LIMIT 1");
	if(strtotime($cron_lasttime)>=strtotime(date("Y-m-d").' 00:00:00'))exit('今日发放任务已完成');
	$re = $DB->query("SELECT a.zid,SUM(money) AS money FROM shua_orders AS a WHERE (TO_DAYS(NOW()) - TO_DAYS(addtime) = 1) AND zid>1 GROUP BY zid HAVING money>0 ORDER BY money DESC LIMIT {$limit}");
	$allmoney = 0;
	$count = 0;
	while ($site = $DB->fetch($re)) {
		$reward = round($site['money'] * $conf['rank_percentage'] / 100, 2);
		if($reward>0){
			$allmoney += $reward;
			$count++;
			$DB->query("UPDATE `shua_site` SET `rmb` = `rmb` + {$reward} WHERE `zid` = {$site['zid']}");
			addPointRecord($site['zid'], $reward, '赠送', '网站昨日销量排行前'.$limit.'名奖励'.$reward.'元');
		}
	}
	saveSetting('cron_rank_time' , $date);
	saveSetting('cron_rank_money' , $allmoney);
	exit('奖励发放完成，发放站点数量：'.$count.'&nbsp;总金额：'.$allmoney.'元');
}
elseif($_GET['do']=='daily'){
	$cron_lasttime = $DB->get_column("SELECT v FROM shua_config WHERE k='daily_lasttime' LIMIT 1");
	if(time()-strtotime($cron_lasttime)<3600*12)exit('日常维护任务今天已执行过');
	saveSetting('daily_lasttime',$date);
	$DB->query("DELETE FROM `shua_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
	$sq1 = $DB->affected();
	$DB->query("DELETE FROM `shua_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-3 hours"))."' and AND (status=0 OR money='0')");
	$sq2 = $DB->affected();
	$DB->query("DELETE FROM `shua_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
	$sq3 = $DB->affected();
	$DB->query("DELETE FROM `shua_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-24 hours"))."' and status<2");
	$sq4 = $DB->affected();
	$DB->query("OPTIMIZE TABLE `shua_pay`");
	$DB->query("DELETE FROM `shua_giftlog` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
	$sq5 = $DB->affected();
	$DB->query("OPTIMIZE TABLE `shua_giftlog`");
	$DB->query("DELETE FROM `shua_invitelog` WHERE date<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
	$sq6 = $DB->affected();
	$DB->query("OPTIMIZE TABLE `shua_invitelog`");
	$count = $sq1+$sq2+$sq3+$sq4+$sq5+$sq6;
	exit('日常维护任务已成功执行，本次共清理'.$count.'条数据');
}
elseif($conf['epay_pid'] && $conf['epay_key']){
	$cron_lasttime = $DB->get_column("SELECT v FROM shua_config WHERE k='cron_lasttime' LIMIT 1");
	if(time()-strtotime($cron_lasttime)<30)exit('ok');
	$trade_no = date("YmdHis",strtotime($cron_lasttime)).'000';
	$limit = $DB->count("SELECT count(*) FROM shua_pay WHERE trade_no>'$trade_no'");
	if($limit<1)exit('ok');
	if($limit>50)$limit=50;
	saveSetting('cron_lasttime',$date);
	$payapi=pay_api(true);
	$data = get_curl($payapi.'api.php?act=orders&limit='.$limit.'&pid='.$conf['epay_pid'].'&key='.$conf['epay_key']);
	$arr = json_decode($data, true);
	if($arr['code']==1){
		foreach($arr['data'] as $row){
			if($row['status']==1){
				$out_trade_no = $row['out_trade_no'];
				$srow=$DB->get_row("SELECT * FROM shua_pay WHERE trade_no='{$out_trade_no}' LIMIT 1 FOR UPDATE");
				if($srow && $srow['status']==0){
					$DB->query("update `shua_pay` set `status` ='1',`endtime` ='$date' where `trade_no`='{$out_trade_no}'");
					processOrder($srow);
					echo '已成功补单:'.$out_trade_no.'<br/>';
				}
			}
		}
		exit('ok');
	}else{
		exit($arr['msg']);
	}
}else{
	exit('未配置易支付信息');
}