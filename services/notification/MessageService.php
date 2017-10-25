<?php

namespace wocenter\services\notification;

use wocenter\core\Service;

/**
 * 系统消息服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class MessageService extends Service
{
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'message';
    }
    
    /**
     * 发送私信
     *
     * @param integer|array $toUid 接受消息的用户ID
     * @param integer $fromUid 发送者ID
     * @param string $title 标题，默认为`您有新的消息`
     * @param string $content 内容
     *
     * @return boolean
     */
    public function send($toUid, $fromUid, $title, $content)
    {
        return true;
    }
    
}
