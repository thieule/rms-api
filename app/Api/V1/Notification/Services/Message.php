<?php
namespace App\Api\V1\Notification\Services;
/* 
 * Message
 * @author Thieu.LeQuang <quangthieuagu@gmail.com>
 */

class Message extends MessageAbstract
{
    private static $_msg_new_proposal = "%s have send new proposal to you in %s project";
    private static $_msg_new_request = "%s have create new request resource for %s project";
    private static $_msg_need_update_cv = "Have <b>%s</b> employee need to update cv, Please <a href =\"%s\">click here</a> to update them!.";
    private static $_msg_employee_proposal = "%s have %s %s in proposal for %s project";

    public function __construct($resource, $action, $user_id)
    {
        $this->_resource = $resource;
        $this->_action = $action;
        $this->_user_id = $user_id;
    }

    /**
     * @return array
     */
    public function newProposalMessage()
    {
        $msg = [];

        foreach ($this->_resource as $item) {
            $msg[] = [
                'function' => $this->_action,
                'title' => $this->_action,
                'params' => '',
                'send_to' => $this->_user_id,
                'message' => sprintf(self::$_msg_new_proposal, $item->user->name, $item->project->name),
                'user_activity_id' => $item->id,
            ];
        }

        return $msg;
    }

    /**
     * @return array
     */
    public function newRequestMessage()
    {
        $msg = [];

        foreach ($this->_resource as $item) {
            $msg[] = [
                'function' => $this->_action,
                'title' => $this->_action,
                'params' => '',
                'send_to' => $this->_user_id,
                'message' => sprintf(self::$_msg_new_request, $item->user->name, $item->project->name),
                'user_activity_id' => $item->id,
            ];
        }

        return $msg;
    }

    /**
     * @return array
     */
    public function needUpdateCVMessage()
    {
        $msg = [];


            $msg[] = [
                'function' => $this->_action,
                'title' => $this->_action,
                'params' => '',
                'send_to' => $this->_user_id,
                'message' => sprintf(self::$_msg_need_update_cv, $this->_resource, url('employee')),
                'user_activity_id' => 0,
            ];


        return $msg;
    }

    /**
     * @return array
     */
    public function proposalMessage()
    {
        $msg = [];


        foreach ($this->_resource as $item) {
            $msg[] = [
                'function' => $this->_action,
                'title' => $this->_action,
                'params' => '',
                'send_to' => $this->_user_id,
                'message' => sprintf(self::$_msg_employee_proposal, $item->user->name, $item->proposalStatus->status, $item->employee->fullName(), $item->project->name),
                'user_activity_id' => $item->id,
            ];
        }

        return $msg;
    }




}