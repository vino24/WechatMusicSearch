<?php
//从文件读取歌曲名称，批量获取歌曲信息
require('api.php');
header("Content-Type:text/html; charset=utf-8");
$names = file('file.txt');  //file()将整个文件读到一个数组，一行算一个元素
foreach ($names as $name) {
    $json = music_search($name, "1");
    $arr = json_decode($json, true);    // true转换为数组，省略转换为对象
    $result = $arr['result'];
    $song = $result['songs'][0];
    $music_name = $song['name'];        //	歌名
    $music_url = $song['mp3Url'];    //	mp3外链
    $music_url = substr($music_url, 24);        //去除服务器前缀地址http://m1.music.126.net/
    $picUrl = $song['album']['picUrl'];        //	歌曲封面链接
    $album_name = $song['album']['name'];        //	专辑名称
    //判断是否为p3服务器，若为p3服务器切换为p4服务器
    $p3 = substr($picUrl, 8, 1);
    if ($p3 == '3') {
        $picUrl = str_replace("p3", "p4", $picUrl);
    }
    $artist_name = $song['artists'][0]['name'];    //	歌手
    print_r($music_name . ' ' . $artist_name . ' ' . $album_name . '<br>' . $music_url . '<br>' . $picUrl);
    echo "<hr />";
}
?>
