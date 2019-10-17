<?php
/* *
 * 类名：EpayNotify
 * 功能：彩虹易支付通知处理类
 * 详细：处理易支付接口通知返回
 */

require_once(SYSTEM_ROOT."alipay/alipay_core.function.php");
require_once(SYSTEM_ROOT."alipay/alipay_md5.function.php");

class AlipayNotify {

	var $alipay_config;

	function __construct($alipay_config){
		$this->alipay_config = $alipay_config;
		$this->http_verify_url = $this->alipay_config['apiurl'].'api/query';
	}
    function AlipayNotify($alipay_config) {
    	$this->__construct($alipay_config);
    }
    /**
     * 针对notify_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyNotify(){
		if(empty($_POST)) {//判断POST来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = true;
			//$responseTxt = $this->getResponse($_POST["trade_no"]);
			
			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if ($responseTxt && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 针对return_url验证消息是否是支付宝发出的合法消息
     * @return 验证结果
     */
	function verifyReturn(){
		if(empty($_GET)) {//判断GET来的数组是否为空
			return false;
		}
		else {
			$responseTxt = $this->getResponse($_GET["out_trade_no"]);
			
			//验证
			if ($responseTxt) {
				return true;
			} else {
				return false;
			}
		}
	}
	
    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
		
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
		
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		
		$isSgin = false;
		$isSgin = md5Verify($prestr, $sign, $this->alipay_config['key']);
		
		return $isSgin;
	}

    /**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	function getResponse($out_trade_no) {
		$param = [
		  'appid'=> $this->alipay_config['appid'],
		  'out_trade_no' => $out_trade_no,
		];
		$sign = '';
		foreach ($param as $k => $v) {
		  if($v) $sign .= $k . '=' . $v . '&';
		}
		$param['sign'] = md5(rtrim($sign, '&') . $this->alipay_config['key']);
		$data = get_curl($this->http_verify_url,http_build_query($param));
		$arr = json_decode($data,true);
		if(isset($arr['code'])&&$arr['code']==1){
			return true;
		}else{
			return false;
		}
	}
}
?>