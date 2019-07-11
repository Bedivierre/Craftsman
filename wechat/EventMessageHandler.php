<?php
namespace wechat;
use app\common\model\WechatAutoreply;
use app\common\model\WechatResponse;

/**
 * 处理菜单消息
 * @author fzf
 *
 */
class EventMessageHandler extends MessageHandler
{
    
    function run(){
        $event=strtolower($this->message['Event']);
        $eventKey=strtolower($this->message['EventKey']);
       // echo $event;die;
        switch ($event){            
            case 'click':
               $res=WechatResponse::get(['eventkey'=>$eventKey]);
               if($res){
                   if($res->type=='text'){
                       if($data=json_decode($res->content,true)){
                           return $data['content'];
                       }
                   }else{
                       return 'app';
                   }
               }else{
                   return '未知菜单';
               }
                
                break;
            case 'view':
                break;
             
                
            
        }
         
    }
    
}