<?php

class Curator {

  //uuid generator
  public function randomString($length = 10, $strType = "nn") {
    switch($strType){
      case "Aa":
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      break;
      case "nn":
        $characters = '1234567890';
      break;
      case "aa":
        $characters = "abcdefghijklmnopqrstuvwxyz";
      break;
      case "AA":
        $characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      break;
      case "na":
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
      break;
    }
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  //Get and return clients real ip
  public function ip() {
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }
    return $ip;
  }

  //Foreach limitor
  public function limit($iterable, $limit) {
    foreach ($iterable as $key => $value) {
      if (!$limit--) break;
      yield $key => $value;
    }
  }

  //Limit echo
  public function short($message, $length) {
    if(strlen($message) <= $length) {
      echo $message;
    } else {
      $newMessage = substr($message, 0, $length) . '...';
      echo $newMessage;
    }
  }

  public function webhook($url, $linkUrl, $linkTitle, $linkDesc, $embedColor, $botName, $botAvatar, $content) {
    if(isset($embedColor)) {
      if(strpos($embedColor, "#") > -1) {
        $c=str_replace("#", "", $embedColor);
        if (!preg_match("/#([a-fA-F0-9]{3}){1,2}\b/", $c)) {
          $color = hexdec( strtolower($c) );
        }
      }
    } else {
      $color = 0;
    }
    $sys["content"] = $content;
    $sys["username"] = $botName;
    $sys["avatar_url"] = $botAvatar;
    $e = array("url" => $linkUrl, "title" => $linkTitle, "description" => $linkDesc, "color" => $color);
    $sys["embeds"] = array(0 => $e);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($sys));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_exec($curl);

  }

  public function redirect($location) {
    return header("Location: $location");
  }

  public function curl($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
    $curlData = curl_exec($curl);
    curl_close($curl);
    return $curlData;
  }

}
