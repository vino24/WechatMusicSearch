<?php
include_once("wx_tpl.php");
include_once("APIForWechat.php");
$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
if (!empty($postStr))
{
      //解析数据
          $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      //发送消息方ID
          $fromUsername = $postObj->FromUserName;
      //接收消息方ID
          $toUsername = $postObj->ToUserName;
     //消息类型
          $form_MsgType = $postObj->MsgType;
    //文字消息
          if($form_MsgType=="text")
          {
           //获取用户发送的文字内容
                $form_Content = trim($postObj->Content);
                $music_detail=getResult($form_Content,"1");
                $nums=count($music_detail);
                if($nums==3) {
                  $music_name=$music_detail[0];
                  $artist_name=$music_detail[1];
                  $music_url=$music_detail[2];
                  if ($music_url == "")
    {
        $contentStr = "没有找到音乐，可能是检索失败或网络问题！\n
查看历史推荐请点击:<a href=\"http://mp.weixin.qq.com/mp/getmasssendmsg?__biz=MjM5MzU4NzcyMQ==#wechat_webview_type=1&wechat_redirect\">历史推荐</a>";
        $msgType="text";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(),$msgType,$contentStr);
              echo $resultStr;
              exit;
    } else
    {

        $resultStr="<xml>\n
             <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
             <FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
             <CreateTime>".time()."</CreateTime>\n
             <MsgType><![CDATA[music]]></MsgType>\n
             <Music>\n";
        $resultStr.="<Title><![CDATA[$music_name]]></Title>\n
             <Description><![CDATA[$artist_name]]></Description>\n
             <MusicUrl><![CDATA[$music_url]]></MusicUrl>\n
             <HQMusicUrl><![CDATA[$music_url]]></HQMusicUrl>\n";
        $resultStr.="</Music>\n
             <FuncFlag>0</FuncFlag>\n
             </xml>\n";
         echo $resultStr;
         exit;
      }
                } else {
                  $artist_name=$music_detail[0];
                  $artist_info=$music_detail[2];
                  $artist_pic=$music_detail[3];
                                 $resultStr="<xml>\n
              <ToUserName><![CDATA[".$fromUsername."]]></ToUserName>\n
              <FromUserName><![CDATA[".$toUsername."]]></FromUserName>\n
              <CreateTime>".time()."</CreateTime>\n
              <MsgType><![CDATA[news]]></MsgType>\n
              <ArticleCount>1</ArticleCount>\n
              <Articles>\n";
              $resultStr.="<item>\n
              <Title><![CDATA[$artist_name]]></Title> \n
              <Description><![CDATA[".$artist_name."的热门50单曲]]></Description>\n
              <PicUrl><![CDATA[$artist_pic]]></PicUrl>\n
              <Url><![CDATA[$artist_info]]></Url>\n
              </item>\n";
              $resultStr.="</Articles>\n
              <FuncFlag>0</FuncFlag>\n
              </xml>";
              echo $resultStr;
              exit;
                }
          }
          //事件消息
          if($form_MsgType=="event")
          {
            //获取事件类型
            $form_Event = $postObj->Event;
            //订阅事件
            if($form_Event=="subscribe")
            {
              //回复欢迎文字消息
              $msgType = "text";
                $contentStr = "听歌容易，找歌不易，且听且珍惜！受够了粗制滥造的低俗音乐？微信上最好的音乐账号，为您奉上最别样的听觉盛宴。\n
想听什么歌直接输入歌名就可以啦/::P";
              $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $msgType, $contentStr);
              echo $resultStr;
              exit;
            }
         }
}
  else
  {
          echo "";
          exit;
  }
?>
      
