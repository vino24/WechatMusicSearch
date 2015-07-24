<?php
header("Content-Type:text/html; charset=utf-8");
function music_search($word, $type)
{
    $url = "http://music.163.com/api/search/pc";
    $post_data = array(
        's' => $word,
        'offset' => '0',
        'limit' => '20',
        'type' => $type,
    );
    $referrer = "http://music.163.com/";
    $URL_Info = parse_url($url);
    $values = array();
    $result = '';
    $request = '';
    foreach ($post_data as $key => $value) {
        $values[] = "$key=" . urlencode($value);
    }
    $data_string = implode("&", $values);
    if (!isset($URL_Info["port"])) {
        $URL_Info["port"] = 80;
    }
    $request .= "POST " . $URL_Info["path"] . " HTTP/1.1\n";
    $request .= "Host: " . $URL_Info["host"] . "\n";
    $request .= "Referer: $referrer\n";
    $request .= "Content-type: application/x-www-form-urlencoded\n";
    $request .= "Content-length: " . strlen($data_string) . "\n";
    $request .= "Connection: close\n";
    $request .= "Cookie: " . "appver=1.5.0.75771;\n";
    $request .= "\n";
    $request .= $data_string . "\n";
    $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);
    fputs($fp, $request);
    $i = 1;
    while (!feof($fp)) {
        if ($i >= 15) {
            $result .= fgets($fp);
        } else {
            fgets($fp);
            $i++;
        }
    }
    fclose($fp);
    return $result;
}
function getResult($word, $type)
{
    $result = music_search($word, "100");
    $arr_artist = json_decode($result, true); // true转换为数组，省略转换为对象
    $artist_count = $arr_artist['result']['artistCount'];

    if ($artist_count == 0) {
        $result = music_search($word, "1");
        $result = json_decode($result, true);
        $result = $result['result'];
        $song = $result['songs'][0];
        $music_name = $song['name'];     //  歌名
        $music_url = $song['mp3Url'];   //  mp3外链
        $artist_name = $song['artists'][0]['name'];  //  歌手
        $info=array($music_name,$artist_name,$music_url);   //  @todo 此处必须使用array()函数创建数组，直接赋值有问题
        return $info;
    }
     else {
        $result = json_decode($result, true);
        $artist = $result['result']['artists'][0];
        $artist_id = $artist['id'];
        $artist_info = "http://music.163.com/#/artist?id=" . $artist_id;
        $artist_name=$artist['name'];
        $artist_pic=$artist['picUrl'];
        $info=array($artist_name,$artist_id,$artist_info,$artist_pic);
        return $info;
    }
}
