<?php
//v0.1 2014.4.30 Flygon - CHI

require_once "./lib/WizardHTTP.php";

function GetTbs($wc)
{
  $retstr = $wc->HTTPGet("http://tieba.baidu.com/dc/common/tbs");
  $json = json_decode($retstr, true);
  return $json['tbs'];
}

function TestLogin($wc)
{
  $retstr = $wc->HTTPGet("http://tieba.baidu.com/dc/common/tbs");
  $json = json_decode($retstr, true);
  return $json['is_login'] == 1;
}

function GetFid($wc, $kw)
{
  $url = "http://tieba.baidu.com/f/commit/share/fnameShareApi?fname=" . 
         urlencode($kw) . "&ie=utf-8";
  $retstr = $wc->HTTPGet($url);
  $json = json_decode($retstr, true);
  if($json['no'] != 0) return "";
  return (string)$json['data']['fid'];
}

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
  if($errno == "0")
    return array('errno' => 0);
  else
    return array('errno' => $errno, 'errmsg' => $json['error_msg']);
}

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
  else
  {
    $list = array();
    foreach($json['like_forum'] as $elem)
      $list[] = $elem['forum_name'];
    return array('errno' => 0, 'list' => $list); 
  }
}

function GetUN($wc)
{
  $retstr = $wc->HTTPGet('http://tieba.baidu.com/i/sys/user_json');
  $json = json_decode($retstr, true);
  return $json['raw_name'];
}

?>
