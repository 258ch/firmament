<?php
//v0.1 2014.4.30 Flygon - CHI

require_once "./lib/WizardHTTP.php";

/** 
 * @param WizardHTTP $wc WizardHTTP对象 请确保已经设置好cookie
 * @return string 成功时返回获取到的tbs 32位 失败时随意
 */
function GetTbs($wc)
{
  $retstr = $wc->HTTPGet("http://tieba.baidu.com/dc/common/tbs");
  $json = json_decode($retstr, true);
  return $json['tbs'];
}

/** 
 * @param WizardHTTP $wc WizardHTTP对象 请确保已经设置好cookie
 * @return bool 是否已登录 
 */
function TestLogin($wc)
{
  $retstr = $wc->HTTPGet("http://tieba.baidu.com/dc/common/tbs");
  $json = json_decode($retstr, true);
  return $json['is_login'] == 1;
}

/** 
 * @param WizardHTTP $wc WizardHTTP对象 请确保已经设置好cookie
 * @param string $kw 目标贴吧名称 utf-8编码
 * @return string 成功时返回目标贴吧的fid 失败时返回空串
 */
function GetFid($wc, $kw)
{
  $url = "http://tieba.baidu.com/f/commit/share/fnameShareApi?fname=" . 
         urlencode($kw) . "&ie=utf-8";
  $retstr = $wc->HTTPGet($url);
  $json = json_decode($retstr, true);
  if($json['no'] != 0) return "";
  return (string)$json['data']['fid'];
}

/** 
 * @param WizardHTTP $wc WizardHTTP对象 请确保已经设置好cookie
 * @param string $kw 目标贴吧名称 utf-8编码
 * @return array ['errno'] integer 签到的结果 0签到成功 1签到失败 2需要重试 3已签过
 *               ['errmsg'] string 错误信息 仅在失败时出现
 */
function Sign($wc, $kw)
{
  $kw_enco = urlencode($kw);
  $cookie = $wc->GetHdr("Cookie");
	  
  $tbs = GetTbs($wc);
  $fid = GetFid($wc, $kw);

  $poststr = $cookie . "_client_id=_client_type=2_client_version=2.5.1" .
             "_phone_imei=000000000000000fid=" . $fid . "from=tiebakw=" . 
             $kw . "net_type=1tbs=" . $tbs . "tiebaclient!!!";
  $sign = MD5($poststr);
  $poststr = $cookie . "&_client_id=&_client_type=2&_client_version=2.5.1" .
             "&_phone_imei=000000000000000&fid=" . $fid . "&from=tieba&kw=" . 
             $kw_enco . "&net_type=1&tbs=" . $tbs . "&sign=" . $sign;
  $retstr = $wc->HTTPPost("http://c.tieba.baidu.com/c/c/forum/sign", $poststr); 

  $json = json_decode($retstr, true);
  $errno = $json['error_code'];
  switch($errno)
  {
    case '0': //成功
	  return array('errno' => 0);
	case '160002': //你之前已经签过了
    case '340010':
	case '3':
      return array('errno' => 3,
	               'errmsg' => $json['error_msg'] . " 错误代码：" . $errno);
	case '340003': //服务器开小差了
	case '340011': //太快了
	case '160003': //零点 稍后再试
	case '160008': //太快了
	  return array('errno' => 2,
	               'errmsg' => $json['error_msg'] . " 错误代码：" . $errno);
	//case '1': //未登录
	//case '160004' //不支持
	default:
	  return array('errno' => 1,
	               'errmsg' => $json['error_msg'] . " 错误代码：" . $errno);
  }    
}

/** 
 * @param WizardHTTP $wc WizardHTTP对象 请确保已经设置好cookie
 * @return array ['errno'] integer 错误代码 0为成功 非零为失败
 *               ['list'] array 包含贴吧列表的数组 每个贴吧为string utf-8编码 仅在成功时出现
 *               ['errmsg'] string 错误信息 仅在失败时出现
 */
function GetTBList($wc)
{
  $cookie = $wc->GetHdr("Cookie");
  $poststr = $cookie . "&_client_id=&_client_type=2&_client_version=5.7.0" .
             "&_phone_imei=000000000000000&from=tieba&like_forum=1&recommend=0&topic=0";
  $sign = MD5(str_replace("&", "", $poststr) . "tiebaclient!!!");
  $poststr .= "&sign=" . strtoupper($sign);
  $retstr = $wc->HTTPPost("http://c.tieba.baidu.com/c/f/forum/forumrecommend", $poststr);
  $json = json_decode($retstr, true);
  if($json['error_code'] != "0")
    return array('errno' => $json['error_code'], 'errmsg' => $json['error_msg']);
  if($json['is_login'] != '1')
    return array('errno' => 1, 'errmsg' => '用户未登录或已掉线');
  $list = array();
  foreach($json['like_forum'] as $elem)
    $list[] = $elem['forum_name'];
  return array('errno' => 0, 'list' => $list);
}

/** 
 * @param WizardHTTP $wc WizardHTTP对象 请确保已经设置好cookie
 * @return string 成功时获取到的用户名称 失败时返回空串
 */
function GetUN($wc)
{
  $retstr = $wc->HTTPGet('http://tieba.baidu.com/i/sys/user_json');
  $json = json_decode($retstr, true);
  return $json['raw_name'];
}

?>
