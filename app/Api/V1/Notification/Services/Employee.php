<?php
namespace App\Api\V1\Notification\Services;
/* 
 * Employee
 * @author Thieu.LeQuang <quangthieuagu@gmail.com>
 */
use App\Api\V1\Notification\Models\UserActivity;
use App\Api\V1\Notification\Models\UserNotificationCondition;

class Employee extends ProviderAbstract
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
                'employee'              => [
                                                    'title' => 'Employee',
                                                    'logicList' => [
                                                                        'needUpdate' => [
                                                                                    'title' => 'CV need update after',
                                                                                    'logic_func' => 'needUpdateCV',
                                                                                    'msg_func' => 'needUpdateCVMessage',
                                                                                    'param' => true,
                                                                        ],
                                                                        'proposal_status' => [
                                                                            'title' => 'Proposal',
                                                                            'logic_func' => 'proposal',
                                                                            'msg_func' => 'proposalMessage',
                                                                            'param' => false,
                                                                        ],
                                                                    ]
                                                ]
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
    public function needUpdateCV()
    {
        $numberOfUpdateCv = App\Api\V1\Notification\Models\Employee::getNumberEmployeeNeedUpdateCVOverDay($this->getParam());

        if (!$numberOfUpdateCv) {
            return false;
        }

        $this->setResultData($numberOfUpdateCv);

        return true;
    }

    /**
     * @return bool
     */
    public function proposal()
    {
        $user_activity = UserActivity::getEmployeeProposalActivity($this->_condition_data->user_id);

        if (!count($user_activity)) {
            return false;
        }

        $this->setResultData($user_activity);

        return true;
    }

}
