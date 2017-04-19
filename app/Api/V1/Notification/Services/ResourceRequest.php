<?php
namespace App\Api\V1\Notification\Services;
/* 
 * ResourceRequest
 * @author Thieu.LeQuang <quangthieuagu@gmail.com>
 */
use App\Modules\Project\Models\UserActivity;
use App\Modules\Project\Models\UserActivityInvolved;
use App\Modules\Notification\Models\UserNotificationCondition;

class ResourceRequest extends ProviderAbstract
{

    /**
     * ProposalCondition constructor.
     * @param UserNotificationCondition $condition
     */
    public function __construct(UserNotificationCondition $condition)
    {
        $this->_condition_data = $condition;
    }

    /**
     * @return array
     */
    public static function getEventConfig()
    {
        return [
                'resource_request'              => [
                                                    'title' => 'Proposal',
                                                    'logicList' => [
                                                                        'new' => [
                                                                                    'title' => 'New resource request',
                                                                                    'logic_func' => 'newRequest',
                                                                                    'msg_func' => 'newRequestMessage',
                                                                                    'param' => false,
                                                                        ],
                                                                        'expire' => [
                                                                                    'title' => 'New request be over time (hours)',
                                                                                    'logic_func' => 'expireRequest',
                                                                                    'param' => true,
                                                                        ]
                                                                    ]
                                                ],
        ];
    }

    /**
     * @return null
     */
    public function getResource()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getParam()
    {
        return $this->_condition_data->param;
    }


    /**
     * @return bool
     */
    public function newRequest()
    {
        $user_new_request_activity = UserActivity::getNewResourceRequestActivity($this->_condition_data->user_id);

        if (!count($user_new_request_activity)) {
            return false;
        }

        $this->setResultData($user_new_request_activity);

        return true;
    }

    /**
     * @return bool
     */
    public function expireRequest()
    {
        $in_id = UserActivityInvolved::getAllActivityIdByUser($this->_condition_data->user_id);
        $user_activity = UserActivity::whereIn('id', $in_id)->where('type', UserActivity::TYPE['ResourceRequest'])->get();
        $user_activity_over = [];
        foreach ($user_activity as $item) {
            if ($item->getSpentHoursFromAtCreated() > $this->getParam()) {
                $user_activity_over[] = $item;
            }
        }

        $this->setResultData($user_activity_over);

        return count($user_activity_over) ?  true : false;
    }
}
