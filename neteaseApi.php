<?php
//	curl公共函数
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
    $output=json_decode($output,true);	// true转换为数组，省略转换为对象
    return $output;
}


//	歌曲详情
function get_music_info($music_id)
{
    $url = "http://music.163.com/api/song/detail/?id=" . $music_id . "&ids=%5B" . $music_id . "%5D";
    $output= curl_get($url);
    $song=$output["songs"][0];	
    $song_name=$song["name"];
    $song_url=$song["mp3Url"];
    $song_album=$song["album"]["name"];
    $song_artist=$song["artists"][0]["name"];
    print_r($song_name."<br>".$song_url."<br>".$song_album."<br>".$song_artist);

}

//	歌手专辑信息
function get_artist_album($artist_id, $limit)
{
    $url = "http://music.163.com/api/artist/albums/" . $artist_id . "?limit=" . $limit;
    $output=curl_get($url);
    $album=$output["hotAlbums"][0]["name"];
    print_r($album);
}

//	专辑详情
function get_album_info($album_id)
{
    $url = "http://music.163.com/api/album/" . $album_id;
    $output=curl_get($url);
    $songs=$output["album"]["songs"];
    $arr=[];
    foreach ($songs as $key => $song) {
    	$song_name=$song['name'];
    	$song_url=$song['mp3Url'];
    	$song_info=$song_name." ".$song_url;
    	array_push($arr, $song_info);
    }
        print_r($arr);
}

//	歌单
function get_playlist_info($playlist_id)
{
    $url = "http://music.163.com/api/playlist/detail?id=" . $playlist_id;
    $output=curl_get($url);
    $result=$output["result"];
    $playlist_name=$result["name"];
    $playlist_creator=$result['creator']['nickname'];
    $playlist_coverImgUrl=$result["coverImgUrl"];
    $songs=$result["tracks"];
    $playlist_info=$playlist_name."<br>".$playlist_creator."<br>".$playlist_coverImgUrl."<br>";	
    foreach ($songs as $key => $song) {
    	$song_name=$song['name'];
    	$song_url=$song['mp3Url'];
    	$playlist_info.=$song_name."<br>".$song_url."<br>";
    }
    print_r($playlist_info);
}

//	歌词
function get_music_lyric($music_id)
{
    $url = "http://music.163.com/api/song/lyric?os=pc&id=" . $music_id . "&lv=-1&kv=-1&tv=-1";
    $output=curl_get($url);
    $lyric=$output['lrc']['lyric'];
    print_r($lyric);
}

//	MV
function get_mv_info($mv_id)
{
    $url = "http://music.163.com/api/mv/detail?id=$mv_id&type=mp4";
	$output=curl_get($url);
	$mvs=$output['data']['brs'];
	$mv="1080P:".$mvs['1080']."<br>720P:".$mvs['720']."<br>480P:".$mvs['480']."<br>240P:".$mvs['240'];   
    print_r($mv);
}
//	get_music_info("30967307");
    get_artist_album("3681", "10");
//	get_album_info("2336486");
//	get_playlist_info("71404249");
//	get_music_lyric("26508235");
//	get_mv_info("290194");
?>
