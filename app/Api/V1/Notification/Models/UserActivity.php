<?php
namespace App\Api\V1\Notification\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Modules\Project\Models\ProjectRequest;
/**
 * Activity Model class
 * @author Thieu Le Quang <quangthieuagu@gmail.com>
 */
class UserActivity extends Eloquent
{
    protected $table = 'user_activity';
    protected $fillable = ['id','user_id','project_id','content','created_at','updated_at','request_id', 'employee_id', 'proposal_id', 'type', 'proposal_employee_status_id'];
    const TYPE = ['CreateProject' => 1,
                    'ProposalRequest' => 2,
                        'ResourceBooking' => 3,
                            'ResourceRequest' => 4,
                                'ProposalEmployeeStatus' => 5];


    /**
     * @param $user_id
     * @return mixed
     */
    public static function getNewProposalActivity($user_id)
    {
        $in_id = UserActivityInvolved::getAllActivityIdByUser($user_id);
        return self::where('type', self::TYPE['ProposalRequest'])->whereIn('id',$in_id) ->get();
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function getNewResourceRequestActivity($user_id)
    {
        $in_id = UserActivityInvolved::getAllActivityIdByUser($user_id);
        return self::where('type', self::TYPE['ResourceRequest'])->whereIn('id',$in_id) ->get();
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function getEmployeeProposalActivity($user_id)
    {
        $in_id = UserActivityInvolved::getAllActivityIdByUser($user_id);
        return self::where('type', self::TYPE['ProposalEmployeeStatus'])->whereIn('id',$in_id) ->get();
    }

    /**
     * @return array
     */
    public function getAllUserInvolved()
    {
        $user_activity_involved = UserActivityInvolved::where('user_activity_id', $this->id);
        $users = [];
        foreach ($user_activity_involved as $item) {
            $users[] = $item->user;
        }
        return $users;
    }


    public function getSpentHoursFromAtCreated()
    {
        $current = \Carbon\Carbon::now();
        return $current->diffInHours($this->created_at);

    }

    public function getSpentDaysFromAtCreated()
    {
        $current = \Carbon\Carbon::now();
        return $current->diffInDays($this->created_at);
    }



}

