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
    $URL_Info = parse_url($url);    //  解析URL   Array ( [scheme] => http [host] => music.163.com [path] => /api/search/pc )
    $values = array();
    $result = '';
    $request = '';
    foreach ($post_data as $key => $value) {
        $values[] = "$key=" . urlencode($value);
    }
    //  print_r($post_data);
    $data_string = implode("&", $values);    // 将一个一维数组的值转化为字符串
    //  print_r($data_string);
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
    $fp = fsockopen($URL_Info["host"], $URL_Info["port"]);  //  fsockopen — 打开一个网络连接或者一个Unix套接字连接,fsockopen()将返回一个文件句柄，之后可以被其他文件类函数调
    fputs($fp, $request);
    $i = 1;
    while (!feof($fp)) {    //   测试文件指针是否到了文件结束的位置
        if ($i >= 15) {
            $result .= fgets($fp);  //  从文件指针中读取一行
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
        $music_url = substr($music_url, 24);       //去除服务器前缀地址http://m1.music.126.net/
        $picUrl = $song['album']['picUrl'];     //  歌曲封面链接
        $album_name = $song['album']['name'];       //  专辑名称
        //判断是否为p3服务器，若为p3服务器切换为p4服务器
        $p3 = substr($picUrl, 8, 1);
        if ($p3 == '3') {
            $picUrl = str_replace("p3", "p4", $picUrl);
        }
        $artist_name = $song['artists'][0]['name'];  //  歌手
        print_r($music_name . ' ' . $artist_name . ' ' . $album_name . '<br>' . $music_url . '<br>' . $picUrl);


    } else {
        $artist = $arr_artist['result']['artists'][0];
        $artist_id = $artist['id'];
        $artist_info = "http://music.163.com/#/artist?id=" . $artist_id;
        $artist_pic=$artist['picUrl'];
        print_r($artist_info."<br>".$artist_pic);
    }
}
getResult("李志", "100");