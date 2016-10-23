<?php
header("Content-type:application/json;charset=utf-8");
function curl_get($url)
{
    $refer = "http://music.163.com/";
    $header[] = "Cookie: " . "appver=1.5.0.75771;";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $refer);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function music_search($s,$type=1)
{
    $type||$type=1;
    $url= "http://music.163.com/api/search/get/web?csrf_token=";
    $curl = curl_init();
    $post_data = 'hlpretag=&hlposttag=&s='. $s . '&type='.$type.'&offset=0&total=true&limit=1';
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

    $header =array(
        'Host: music.163.com',
        'Origin: http://music.163.com',
        'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.90 Safari/537.36',
        'Content-Type: application/x-www-form-urlencoded',
        'Referer: http://music.163.com/search/',
    );

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    $src = curl_exec($curl);
    curl_close($curl);
    return $src;
}

function get_music_info($music_id)
{
    $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
    return curl_get($url);
}
function get_artist_info($artist_id)
{
    $url = "http://music.163.com/api/artist/" . $artist_id;
    return curl_get($url);
}
function getResult($word)
{
    $is_Song = false;
    $isEnglish = preg_match("/^[a-zA-Z\s]+$/",$word);
    $arr = preg_split("/[\s,]+/", $word);
    $result = json_decode(music_search($word), true); // true转换为数组，省略转换为对象
    if(!$isEnglish) {
        if(count($arr) == 1) {
        $is_Song = $result['result']['songs'][0]['name'] == $word;
    } else {
        foreach ($arr as $key => $keyword) {
            $result = json_decode(music_search($keyword) ,true);
            if($result['result']['songs'][0]['name'] == $keyword) {
                $is_Song = true;
                $result = json_decode(music_search($arr[$key]) , true);
                break;
            }
        }
    }
    } else {
        $is_Song = stristr($word,$result['result']['songs'][0]['name']);
    }
    if ($is_Song) {
        $result =$result['result'];
        $song = $result['songs'][0];
        $music_name = $song['name'];     //  歌名
        $artist_name = $song['artists'][0]['name'];  //  歌手
        $music_id = $song['id'];
        $result = json_decode(get_music_info($music_id), true);
        $music_url = $result['songs'][0]['mp3Url'];
        $info=array($music_name,$artist_name,$music_url);   //  @todo 此处必须使用array()函数创建数组，直接赋值有问题
        return $info;
    }
     else {
        $result = json_decode(music_search($word, 100), true);
        $artist = $result['result']['artists'][0];
        $artist_id = $artist['id'];
        $artist_info = "http://music.163.com/#/artist?id=" . $artist_id;
        $artist_name=$artist['name'];
        $artist_pic=$artist['picUrl'];
        $info=array($artist_name,$artist_info,$artist_pic,$artist_id);
        return $info;
    }
}
print_r(getResult("heal the world"));
