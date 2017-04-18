<?php namespace App\Api\V1\Notification\Controllers;

use App\Api\V1\Notification\Models\UserNotificationConfig;
use JWTAuth;
use App\Http\Controllers\Controller;

class NotificationController extends Controller {


    /**
     * View list of notification config
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function config()
    {
        $currentUser = JWTAuth::parseToken()->authenticate();
        return UserNotificationConfig::where('user_id', $currentUser->id)->get();
    }

}
