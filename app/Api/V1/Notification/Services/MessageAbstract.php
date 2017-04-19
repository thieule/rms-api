<?php
namespace App\Api\V1\Notification\Services;
/* 
 * Message
 * @author Thieu.LeQuang <quangthieuagu@gmail.com>
 */

abstract class MessageAbstract
{
    protected $_resource;
    protected $_action;
    protected $_user_id;

    /**
     * @return mixed
     */
    public function buildMessage()
    {
        $action_function_name = $this->_action . 'BuildMsg';
        return $this->{$action_function_name}();
    }

    /**
     * @return mixed
     */
    public function sendMailBuildMsg()
    {
        return $this->baseBuildMsg('email');
    }

    /**
     * @return mixed
     */
    public function popupBuildMsg()
    {
        return $this->baseBuildMsg('Popup');
    }

    /**
     * @return array
     */
    public function inlineRedBuildMsg()
    {
        return $this->baseBuildMsg('inline red');
    }

    /**
     * @param $title
     * @param string $param
     * @return array
     */
    public function baseBuildMsg($title, $param = '')
    {
        $msg = [];

        foreach ($this->_resource as $item) {
            $msg[] = [
                'function' => $this->_action,
                'title' => $title,
                'params' => $param,
                'send_to' => $this->_user_id,
                'message' => $item->content,
                'user_activity_id' => $item->id,
            ];
        }

        return $msg;

    }
}