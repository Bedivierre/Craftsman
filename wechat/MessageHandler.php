<?php
namespace wechat;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
/**
 * 处理消息基类
 * @author fzf
 *
 */
class MessageHandler implements EventHandlerInterface
{
    
    protected $from;
    protected $to;     
    protected $msgType;    
    protected $msgId;
    protected $createTime;
    protected $message;
    
   protected function init($message){
        $this->from = $message['FromUserName'];
        $this->to = $message['ToUserName'];
        $this->createTime=$message['CreateTime'];
        //$this->msgId=$message['MsgId'];
        $this->msgType=empty($message['MsgType'])?null:$message['MsgType'];        
        $this->message=$message;        
    }
    
    function handle($message=null){     
       $this->init($message);
       return $this->run();
    }
    
    /**
     * 处理消息
     * @return string
     */
    function run(){
        switch ($this->msgType) {
            case 'event':
                return '收到事件消息';
                break;
            case 'text':
                return '收到文字消息';
                break;
            case 'image':
                return '收到图片消息';
                break;
            case 'voice':
                return '收到语音消息';
                break;
            case 'video':
                return '收到视频消息';
                break;
            case 'location':
                return '收到坐标消息';
                break;
            case 'link':
                return '收到链接消息';
                break;
            case 'file':
                return '收到文件消息';
                // ... 其它消息
            default:
                return '收到其它消息';
                break;
        }
    }
    
}