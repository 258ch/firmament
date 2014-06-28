<?php
//ver 1.0 by flygon - chi  2014.4.3

class WizardHTTP
{
  private $hdr = array();
  private $timeout = 4000;
  private $loc_charset = 'utf-8';
  private $svr_charset = 'utf-8';
  
  public function GetLocCharset()
  {
    return $this->loc_charset;
  }
  
  public function SetLocCharset($charset)
  {
    $this->loc_charset = $charset;
  }
  
  public function GetSvrCharset()
  {
    return $this->svr_charset;
  }
  
  public function SetSvrCharset($charset)
  {
    $this->svr_charset = $charset;
  }
  
  public function GetHdr($key)
  {
    return $this->hdr[$key];
  }

  public function SetHdr($key, $val)
  {
    if($val == "" && isset($this->hdr[$key]))
	  unset($this->hdr[$key]);
	else
	  $this->hdr[$key] = $val;
  }

  public function DelHdr($key)
  {
    if(isset($this->hdr[$key]))
	  unset($this->hdr[$key]);
  }

  public function SetDefHdr($mobile = false)
  {
    $this->hdr["Accept"] = "*/*";
    $this->hdr["Accept-Language"] = "zh-cn";
    if($mobile)
      $this->hdr["User-Agent"] = "Dalvik/1.1.0 (Linux; U; Android 2.1; sdk Build/ERD79)";
    else
      $this->hdr["User-Agent"] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";
    $this->hdr["Content-Type"] = "application/x-www-form-urlencoded";
    $this->hdr["Cache-Control"] = "no-cache";
  }
  
  private function HTTPSubmit($method, $url, $data)
  {
    $hdr_str = "";
    foreach($this->hdr as $key => $val)
      $hdr_str .= $key . ": " . $val . "\r\n";
    if($method == "POST")
      $hdr_str .= "Content-Length: " . strlen($data) . "\r\n";
    $hdr_str .= "\r\n";
  
    $options = array
    (
      "http" => array
	  (
	    "method" => $method,
	    "header" => $hdr_str,
	    "content" => $data,
	    "timeout" => $this->timeout
	  )
    );
  
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if($this->loc_charset != $this->svr_charset)
      $result = iconv($this->svr_charset, $this->loc_charset, $result);
    return $result;
  }
  
  public function HTTPGet($url)
  {
    return $this->HTTPSubmit("GET", $url, "");
  }
  
  public function HTTPPost($url, $data)
  {
    return $this->HTTPSubmit("POST", $url, $data);
  }
}
?>