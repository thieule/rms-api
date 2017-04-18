<?php
namespace App\Api\V1\Notification\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
/**
 * UserNotificationMessage Model class
 * @author Thieu Le Quang <quangthieuagu@gmail.com>
 */
class UserNotificationMessage extends Eloquent
{
    protected $table = 'user_notification_message';
    protected $fillable = ['id','function','title','message','params','send_to','user_activity_id','seen','read','created_at', 'updated_at'];


    /**
     * created notification after created request
     */
    public static function createFromRequest(ProjectRequest $request)
    {
        return [
            'title' => 'Create new request resource on ' . $request->project->name . ' project',
            'content' => $request->user->name . ' created new request resource on ' . $request->project->name . ' project',
            'send_to' => 24,
            'user_id' => $request->user_id,
            'request_id' => $request->id,
            'link' => url('request/booking?project_id=' . $request->project_id . '&request_id=' . $request->id)
        ];
    }


    /**
     * created notification after created project
     */
    public static function createFromProject(Project $project)
    {
        return [
            'title' => 'Create new project ' . $project->name,
            'content' => $project->user->name . ' created new project ' . $project->name,
            'send_to' => 24,
            'user_id' => $project->user->id,
            'link' => url('project/details/' . $project->id)
        ];
    }

    /**
     * created notification after created project
     */
    public static function createFromProposal(EmployeeProposal $employee_proposal)
    {
        $project = Project::find($employee_proposal->proposal->project_id);
        return [
            'title' => $employee_proposal->user->name . ' have proposal ' . $employee_proposal->employee->fullName() . ' to project ' . $project->name,
            'content' => $employee_proposal->user->name . ' have proposal ' . $employee_proposal->employee->fullName() . ' to project ' . $project->name,
            'send_to' => $project->user->id,
            'user_id' => $employee_proposal->user->id,
            'link' => url('project/details/' . $project->id)
        ];
    }

    /**
     * created notification after created project
     */
    public static function createFromProposalEmployeeStatus(EmployeeProposalStatus $employee_proposal_status)
    {
        $employee = Employee::find($employee_proposal_status->employeeProposal->employee_id);
        $proposal = Proposal::find($employee_proposal_status->employeeProposal->proposal_id);

        return [
            'title' => $employee_proposal_status->user->name . ' have ' . $employee_proposal_status->status . ' ' . $employee->fullName() . '  in project ' . $proposal->project->name,
            'content' => $employee_proposal_status->user->name . ' have ' . $employee_proposal_status->status . ' ' . $employee->fullName() . '  in project ' . $proposal->project->name,
            'send_to' => $employee_proposal_status->employeeProposal->user_id,
            'user_id' => $employee_proposal_status->user->id,
            'link' => url('project/details/' . $proposal->project->id)
        ];
    }

    /**
     * created notification after created project
     */
    public static function createFromBooking(ProjectBooking $project_booking)
    {
        return [
            'title' => 'New assign member to project ' . $project_booking->project->name,
            'content' => $project_booking->user->name . ' assign ' . $project_booking->employee->fullName() . ' role ' . $project_booking->role->name,
            'send_to' => 24,
            'user_id' => $project_booking->user->id,
            'link' => url('project/details/' . $project_booking->project->id)
        ];
    }


    public function send()
    {

    }

    public function seen($is_seen = 1)
    {

    }

    public function read($is_read = 1)
    {

    }


}

