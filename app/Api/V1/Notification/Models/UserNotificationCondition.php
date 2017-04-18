<?php namespace App\Api\V1\Notification\Models;
use Illuminate\Database\Eloquent\Model as Eloquent;
/**
 * UserNotificationCondition Model class
 * @author Thieu Le Quang <quangthieuagu@gmail.com>
 */
class UserNotificationCondition extends Eloquent
{
    protected $table = 'user_notification_condition';
    protected $fillable = ['id','logic','param','user_notification_config_id','event','user_id','created_at','updated_at'];



}

