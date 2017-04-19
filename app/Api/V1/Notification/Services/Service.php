<?php
namespace App\Api\V1\Notification\Services;
use App\Modules\Project\Models\UserActivity;
use App\Modules\Project\Models\UserActivityInvolved;
use App\Modules\Notification\Models\UserNotificationCondition;
use App\Modules\Notification\Models\UserNotificationConfig;
use App\Modules\Notification\Models\UserNotificationMessage;
use Illuminate\Support\Facades\Mail;

/**
 * Notification service
 * @author Thieu Le Quang <quangthieuagu@gmail.com>
 */
class Service
{

    /**
     * @return array
     */
    public static $_action_list = [
                                    'send_mail'   => [
                                                        'title' => 'Send mail',
                                                        'func_msg_default' => 'sendMail',
                                                        'param' => true ],
                                    'inline_red'  => [
                                                        'title' => 'Inline red',
                                                        'func_msg_default' => 'inlineRed',
                                                        'param' => true ],
                                    'popup'  => [
                                                    'title' => 'Popup',
                                                    'func_msg_default' => 'popup',
                                                    'param' => true ] ];


    /**
     * @return array
     */
    public static $_event_list = ['proposal' => 'Proposal', 'resource_request' => 'Project Request Resource', 'employee' => 'Employee'];

    /**
     * Scan for event to notification
     * @param null $user_id
     */
    public static function scanForMessage($user_id = null)
    {
        if ($user_id) {
            $notification_configs = UserNotificationConfig::where('user_id', $user_id)->get();
        } else {
            $notification_configs = UserNotificationConfig::get();
        }

        foreach ($notification_configs as $config) {

            $conditions_data = UserNotificationCondition::where('user_notification_config_id', $config->id)->get();

            $is_action = false;
            $message = [];

            foreach ($conditions_data as $condition) {

                $condition_class_name = self::getMapConditionClass($condition->event);

                $condition_object = new $condition_class_name($condition);
                $checked_status = $condition_object->check();

                $is_action = $config->is_all_net ? ($is_action && $checked_status) : ($is_action || $checked_status);
                if ($checked_status && $condition_object->getResultData() != null) {

                    $msg_class_name = self::getMapMessageClass( $condition->event );

                    $service_msg= new $msg_class_name( $condition_object->getResultData(), self::$_action_list[$config->action]['func_msg_default'], $condition->user_id );

                    if (method_exists($service_msg, $condition_object->getFuncMsg())) {
                        $message[] = $service_msg->{$condition_object->getFuncMsg()}();
                    } else {
                        $message[] = $service_msg->buildMessage();
                    }

                    $notification_msg[$condition->event] = $condition_object->getResultData();
                }
            }

            /**
             * do action
             */
            if ($is_action) {
                foreach ( $message as $msg ) {
                    self::storageMessage( $msg );
                }
            }
        }

        UserActivityInvolved::where('user_id', $user_id)->where('read', 0)->update(['read' => 1]);
    }

    /**
     * Send mail
     */
    public function sendMailAction()
    {
        foreach (UserNotificationMessage::where('has_send', 0)->get() as $item) {
            if ($item->func == 'send_mail') {
                Mail::send('emails.reminder', ['user' => $item], function ($m) use ($item) {
                        $m->from('reminder@rms.com', 'RMS system');

                        $m->to($item->user->email, $item->user->name)->subject($item->title);
                });
            }
        }
    }

    /**
     * @param $user_id
     * @param int $limit
     * @return array
     */
    public static function inlineRed($user_id)
    {
        $list = UserNotificationMessage::where('send_to', $user_id)->where('function','inlineRed')->get();
        $inline_item  = self::convertDataForInlineRed($list);
        UserNotificationMessage::where('has_send', 0)->where('send_to', $user_id)->where('function','inlineRed')->update(['has_send' => 1]);
        return $inline_item;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function inlineRedCount($user_id)
    {
        return UserNotificationMessage::where('seen', 0)->where('function','inlineRed')->where('send_to', $user_id)->count();
    }

    /**
     * @param $list
     * @return array
     */
    public static function convertDataForInlineRed($list)
    {
        $inline_item = [];
        foreach ($list as $item) {
            $new_item = [   'title' => $item->message,
                'created_at' => $item->created_at,
                'user' => $item->user,
                'seen' => $item->seen,
                'from' => $item->userActivity->user,
                'id' => $item->id
            ];

            switch ($item->userActivity->type) {
                case UserActivity::TYPE['ResourceRequest']:
                    $new_item['link'] = url('/request/details/' . $item->userActivity->request_id);
                    $new_item['type'] = 'popup';
                    break;
                case UserActivity::TYPE['ProposalRequest']:
                    $new_item['link'] = url('/project/details/' . $item->userActivity->project_id);
                    $new_item['type'] = 'page';
                    break;
                case UserActivity::TYPE['ResourceBooking']:
                    $new_item['link'] = url('/project/details/' . $item->userActivity->project_id);
                    $new_item['type'] = 'page';
                    break;
                default:
                    $new_item['link'] = url('/project/details/' . $item->userActivity->project_id);
                    $new_item['type'] = 'page';

            }
            $inline_item[] = $new_item;

        }
        return $inline_item;
    }

    /**
     * Return json to show inline red
     */
    public static function inlineRedAction($user_id)
    {
        $list = UserNotificationMessage::where('has_send', 0)->where('send_to', $user_id)->where('function','inlineRed')->get();
        $inline_item  = self::convertDataForInlineRed($list);
        UserNotificationMessage::where('has_send', 0)->where('send_to', $user_id)->where('function','inlineRed')->update(['has_send' => 1]);

        return $inline_item;
    }

    /**
     * Return json to show inline red
     */
    public static function popupAction($user_id)
    {
        $inline_item = [];
        foreach (UserNotificationMessage::where('has_send', 0)->where('send_to', $user_id)->where('function','popup')->get() as $item) {

            $inline_item[] = [
                'message' => $item->message . ' '. $item->when()->diffForHumans()
            ];

        }
        UserNotificationMessage::where('has_send', 0)->where('send_to', $user_id)->where('function','popup')->update(['has_send' => 1]);

        return json_encode($inline_item);
    }

    /**
     * Storage message to database
     * @param $messages
     */
    public static function storageMessage($messages)
    {
        foreach ($messages as $item ) {
            UserNotificationMessage::create($item);
        }

    }

    /**
     * Map condition service class
     * @param $condition_name
     * @return string | null
     */
    private static function getMapConditionClass($condition_name)
    {
        $mapping = [
                    'proposal' => 'App\Modules\Notification\Services\Proposal',
                    'resource_request' => 'App\Modules\Notification\Services\ResourceRequest',
                    'proposal_employee_status' => 'App\Modules\Notification\Services\Proposal',
                    'employee' => 'App\Modules\Notification\Services\Employee',
        ];

        if (isset($mapping[$condition_name])) {
            return $mapping[$condition_name];
        }

        return null;
    }

    /**
     * Mapping message service class
     * @param $event
     * @return null | string
     */
    private static function getMapMessageClass($event)
    {
        $mapping = [
            'proposal' => 'App\Modules\Notification\Services\Message',
            'resource_request' => 'App\Modules\Notification\Services\Message',
            'employee' => 'App\Modules\Notification\Services\Message',
        ];

        if (isset($mapping[$event])) {
            return $mapping[$event];
        }

        return null;
    }

    /**
     * @param $event
     * @return mixed
     */
    public static function getLogicList($event)
    {
        $condition_class_name = self::getMapConditionClass($event);
        return $condition_class_name::getEventConfig()[$event];
    }

}
