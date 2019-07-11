<?php
namespace wechat;
use app\common\model\WechatAutoreply;

/**
 * 处理文本消息
 * @author fzf
 *
 */
class TextMessageHandler extends MessageHandler
{
    
    function run(){
        $content=$this->message['Content'];
        //自动回复
        $autoreply=WechatAutoreply::get(['text'=>$content, 'status' => '1']);
         
        if($autoreply){
            $res=$autoreply->response;
             
            if($res){
                if($res->type=='text'){
                     if($data=json_decode($res->content,true)){
                         return $data['content'];
                     }
                       
                     
                }else{
                    //app
                    
                }
            }
            
        }
    }
    
}