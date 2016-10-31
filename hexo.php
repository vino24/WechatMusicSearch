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
function get_playlist_info($playlist_id) {
        $url = "http://music.163.com/api/playlist/detail/?id=" . $playlist_id;
    return curl_get($url);
}
function get_album_info($album_id)
{
    $url = "http://music.163.com/api/album/" . $album_id;
    return curl_get($url);
}
function batch_get($id, $type)
{   
    if($type) {
    $result = json_decode(get_album_info($album_id), true);
    $result = $result["album"]["songs"];
    } else {
     $result = json_decode(get_playlist_info($playlist_id),true);
     $result = $result['result']['tracks'];
    } 
    $song_list = "[";
    foreach ($result as $key => $song) {
        $result = json_decode(get_music_info($song['id']), true);

        $result = $result['songs'][0];
        $info = "{";
        $music_name = $result['name'];
        $artist_name = $result['artists'][0]['name'];
        $music_url = $result['mp3Url'];
        $music_pic = $result['album']['picUrl'];
        $info .= "title:'" .$music_name . "',author:'" . $artist_name . "',url:'" .$music_url . "',pic:'" . $music_pic . "'},";
        $song_list .= $info;
    }
     $song_list = rtrim($song_list,",");
     $song_list .= "]";
     return $song_list;
}
