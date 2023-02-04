<?php
/*
 * File name: NotificationAPIController.php
 * Last modified: 2021.09.15 at 13:28:01
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\Http\Controllers\API;


use App\Criteria\Notifications\UnReadCriteria;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Notifications\NewMessage;
use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\DB;

/**
 * Class NotificationController
 * @package App\Http\Controllers\API
 */
class NotificationAPIController extends Controller
{
    /** @var  NotificationRepository */
    private $notificationRepository;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(NotificationRepository $notificationRepo, UserRepository $userRepository)
    {
        $this->notificationRepository = $notificationRepo;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    /**
     * Display a listing of the Notification.
     * GET|HEAD /notifications
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->notificationRepository->pushCriteria(new RequestCriteria($request));
            $this->notificationRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage(), 200);
        }
        $notifications = $this->notificationRepository->all();
        $this->filterCollection($request, $notifications);

        return $this->sendResponse($notifications->toArray(), 'Notifications retrieved successfully');
    }

    /**
     * Display a count of Notifications.
     * GET|HEAD /notifications/count
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function count(Request $request): JsonResponse
    {
        try {
            $this->notificationRepository->pushCriteria(new RequestCriteria($request));
            $this->notificationRepository->pushCriteria(new UnReadCriteria());
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage(), 200);
        }
        $count = $this->notificationRepository->count();

        return $this->sendResponse($count, 'Notifications count retrieved successfully');
    }

    /**
     * Store a newly created Notification in storage.
     * POST /notifications
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $usersId = $request->get('users');
            $fromId = $request->get('from');
            $text = $request->get('text');
            $messageId = $request->get('id');
			$usersDevice =  DB::table('users')->get()->where('id', '=' , $usersId ) ;
            $users = $this->userRepository->findWhereIn('id',explode(',',$usersId));
			
			$tkn = "";
			foreach($usersDevice as $people)
			{
				$tkn = $people->device_token ;
			}
			
			//$firebaseToken = array($tkn);
			
			$firebaseToken = array('ffSUSxdNT5OIuxBmDCSSfM:APA91bG0otY53JV86kxZA_aGjE2EMO10km9p9WXeSXpwBa4jYTtRCqZoYiGLxD1I95kG6Khht5I4lVegqIjJ1LwtIkg4mGGbLnLhlvzqQzTb1OE8vTmbefT8amTGsiwZUPDHl5-AT_5w');
			          
			$SERVER_API_KEY = 'AAAAXHWSsTo:APA91bFM0liNiCFK0faLTnjeElKPrBz6BbYhadVVqabW_a-Z53xELWAsLoxlyZ7W5H-7uE4S6gCzxQSR34WWZgd61tzufqtfwKquzZV3zfYNj4mI2SKgGeE_IYPT7a_cPnjnBgSiew0j';

			$data = [
				"registration_ids" => $firebaseToken,
				"notification" => [
					"title" => "New Message",
					"body" => ucwords($text),  
				]
			];
			$dataString = json_encode($data);

			$headers = [
				'Authorization: key=' . $SERVER_API_KEY,
				'Content-Type: application/json',
			];

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

			$response = curl_exec($ch);
			
            $from = $this->userRepository->find($fromId);
            \Illuminate\Support\Facades\Notification::send($users, new NewMessage($from, $text, $messageId));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        return $this->sendResponse(true, __('lang.saved_successfully', ['operator' => __('lang.notification')]));
    }

    /**
     * Display the specified Notification.
     * GET|HEAD /notifications/{id}
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        /** @var Notification $notification */
        if (!empty($this->notificationRepository)) {
            $notification = $this->notificationRepository->findWithoutFail($id);
        }

        if (empty($notification)) {
            return $this->sendError('Notification not found', 200);
        }

        return $this->sendResponse($notification->toArray(), 'Notification retrieved successfully');
    }

    /**
     * Update the specified Notification in storage.
     *
     * @param $id
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update($id, Request $request): JsonResponse
    {
        $notification = $this->notificationRepository->findWithoutFail($id);

        if (empty($notification)) {
            return $this->sendError('Notification not found', 200);
        }
        $input = $request->all();

        if (isset($input['read_at'])) {
            if ($input['read_at'] == true) {
                $input['read_at'] = Carbon::now();
            } else {
                unset($input['read_at']);
            }
        }
        try {
            $notification = $this->notificationRepository->update($input, $id);

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 200);
        }

        return $this->sendResponse($notification->toArray(), __('lang.saved_successfully', ['operator' => __('lang.notification')]));
    }

    /**
     * Remove the specified Favorite from storage.
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $notification = $this->notificationRepository->findWithoutFail($id);

        if (empty($notification)) {
            return $this->sendError('Notification not found', 200);
        }

        if ($this->notificationRepository->delete($id) < 1) {
            $this->sendError('Notification not deleted', 200);
        }

        return $this->sendResponse($notification, __('lang.deleted_successfully', ['operator' => __('lang.notification')]));

    }
	
	
	public function new_notify(Request $request)
	{
			$usersId = $request->get('users');
			$fromId = $request->get('from');
			$text = $request->get('text');
			$messageId = $request->get('id');
		//echo $usersId;
		
			/*$usersId = $request->input('users');
            $fromId = $request->input('from');
            $text = $request->input('text');
            $messageId = $request->input('id');*/
			//$usersDevice = $this->userRepository->findWhereIn('id',explode(',',$usersId));
			$usersDevice =  DB::table('users')->get()->whereIn('id', str_replace("[","",str_replace("]","",$usersId)) ) ;
			$token = array();
			foreach($usersDevice as $typ)
			{
				$token[] = $typ->device_token ;
			}
		
		
			//$token = array($usersDevice->device_token);
			
			$url = 'https://fcm.googleapis.com/fcm/send';
			$fields['notification']['title']="New Message";
			$fields['notification']['body']=ucwords($text);
		   // $fields['notification']['sound']="https://foods.sewacity.com/images/notification/notification.mp3";
			//$fields['notification']['icon']="https://foods.sewacity.com/images/website/1631598429.png";
			$fields['registration_ids'] = $token ; // ARRAY FORMATE
			$fields['priority'] = "high";
			$fields['restricted_package_name'] = "";
			$json = json_encode($fields);
    
			$serverKey = "AAAAXHWSsTo:APA91bFM0liNiCFK0faLTnjeElKPrBz6BbYhadVVqabW_a-Z53xELWAsLoxlyZ7W5H-7uE4S6gCzxQSR34WWZgd61tzufqtfwKquzZV3zfYNj4mI2SKgGeE_IYPT7a_cPnjnBgSiew0j"; // YOUR SERVER KEY
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    
        $response = curl_exec($ch);
    
        if ($response === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
    
        curl_close($ch); 
		
		return $this->sendResponse(200,'Notification sent successfully');
	}
}
