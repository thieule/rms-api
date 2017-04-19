<?php
namespace App\Api\V1\Notification\Services;
/* 
 * Proposal
 * @author Thieu.LeQuang <quangthieuagu@gmail.com>
 */
use App\Modules\Project\Models\UserActivity;
use App\Modules\Project\Models\UserActivityInvolved;
use App\Api\V1\Notification\Models\UserNotificationCondition;

class Proposal extends ProviderAbstract
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
                'proposal'              => [
                                                    'title' => 'Proposal',
                                                    'logicList' => [
                                                                        'new' => [
                                                                                    'title' => 'Add new',
                                                                                    'logic_func' => 'newProposal',
                                                                                    'msg_func' => 'newProposalMessage',
                                                                                    'param' => false,
                                                                        ],
                                                                        'expire' => [
                                                                                    'title' => 'Add new over time (hours)',
                                                                                    'logic_func' => 'expireProposal',
                                                                                    'msg_func' => 'expireProposalMessage',
                                                                                    'param' => true,
                                                                        ]
                                                                    ]
                                                ],
                'proposal_employee_status'  => 'When change status of employee',
        ];
    }


    public function getResource()
    {
        return null;
    }

    public function getParam()
    {
        return $this->_condition_data->param;
    }

    /**
     * @return bool
     */
    public function newProposal()
    {
        $user_new_proposal_activity = UserActivity::getNewProposalActivity($this->_condition_data->user_id);

        if (!count($user_new_proposal_activity)) {
            return false;
        }

        $this->setResultData($user_new_proposal_activity);

        return true;
    }



    /**
     * @return bool
     */
    public function proposalExpire()
    {
        $in_id = UserActivityInvolved::getAllActivityIdByUser($this->_condition_data->user_id);
        $user_activity = UserActivity::whereIn('id',$in_id)->where('type', UserActivity::TYPE['ProposalRequest'])->get();
        $user_proposal_activity_over = [];
        foreach ($user_activity as $item) {
            if ($item->getSpentHoursFromAtCreated() > $this->getParam()) {
                $user_proposal_activity_over[] = $item;
            }
        }

        $this->setResultData($user_proposal_activity_over);

        return count($user_proposal_activity_over) ?  true : false;
    }


}
