<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CustomFieldRepository;
use App\Repositories\CustomPageRepository;
use App\Repositories\FaqCategoryRepository;
use App\Repositories\FaqRepository;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendlinkMail;
use App\Models\InstallLink;
use Illuminate\Support\Facades\Mail;



class DefaultController extends Controller
{
    /** @var  CustomPageRepository */
    private $customPageRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /** @var  FaqRepository */
    private $faqRepository;

    /**
     * @var FaqCategoryRepository
     */
    private $faqCategoryRepository;


    public function __construct(CustomPageRepository $customPageRepo, CustomFieldRepository $customFieldRepo, FaqRepository $faqRepo, FaqCategoryRepository $faq_categoryRepo)
    {
        parent::__construct();
        $this->customPageRepository = $customPageRepo;
        $this->customFieldRepository = $customFieldRepo;

        $this->faqRepository = $faqRepo;
        $this->faqCategoryRepository = $faq_categoryRepo;

    }
	public function sendLinkInEmail(Request $request)
    {
			
        $email=$request->email;
        if($email){
        
        Mail::to($request->email)->send(new SendlinkMail());
		$data=new InstallLink();
		$data->email=$email;
		$data->type= $request->service_link;
		$data->save();
        return response()->json([
            'msg' => "Email Send successfully",
            'status' =>200
        ]);
        }
		else{
			
			//     $receiverNumber = $request->phone;
        //     //dd($receiverNumber);
        // $message = '<a href="https://play.google.com/store/apps/details?id=com.jhoice.customer_app2"></a>';
  
        // try {
  
        //     $account_sid = getenv("TWILIO_SID");
        //     $auth_token = getenv("TWILIO_TOKEN");
        //     $twilio_number = getenv("TWILIO_FROM");
  
        //     $client = new Client($account_sid, $auth_token);
        //     $client->messages->create($receiverNumber, [
        //         'from' => $twilio_number, 
        //         'body' => $message]);
  
        //    // dd('SMS Sent Successfully.');
  
        // } catch (Exception $e) {                     
        //     dd("Error: ". $e->getMessage());
        // }
			
            $mobile= $request->phone;
			
        $message = '<a href="https://play.google.com/store/apps/details?id=com.jhoice.customer_app2"></a>';
        $encode= urlencode($message);
        
        $authKey= "";
        $senderId= "";
	    $route= 4;
        $data=array(
            'authKey' => $authKey,
            'mobiles' => $mobile,
            'message' => $encode,
            'sender' => $senderId,
            'route' => $route
        );
        
        $url= "http://api.msg91.com/api/sendhttp.php";
        $ch= curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $output= curl_exec($ch);
        if(curl_errno($ch)){
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);
		$data=new InstallLink();
		$data->phone=$mobile;
		$data->type= $request->service_link;
		$data->save();
        return response()->json([
            'msg' => "SMS Send successfully",
            'status' =>200
        ]);
		}
	}
	public function sendLinkProvider(Request $request)
    {
			
        $email=$request->email;
        if($email){
        
        Mail::to($request->email)->send(new SendlinkMail());
		$data=new InstallLink();
		$data->email=$email;
		$data->type= $request->service_link;
		$data->save();
        return response()->json([
            'msg' => "Email Send successfully",
            'status' =>200
        ]);
        }
		else{
			
			//     $receiverNumber = $request->phone;
        //     //dd($receiverNumber);
        // $message = '<a href="https://play.google.com/store/apps/details?id=com.jhoice.provider_app"></a>';
  
        // try {
  
        //     $account_sid = getenv("TWILIO_SID");
        //     $auth_token = getenv("TWILIO_TOKEN");
        //     $twilio_number = getenv("TWILIO_FROM");
  
        //     $client = new Client($account_sid, $auth_token);
        //     $client->messages->create($receiverNumber, [
        //         'from' => $twilio_number, 
        //         'body' => $message]);
  
        //    // dd('SMS Sent Successfully.');
  
        // } catch (Exception $e) {                     
        //     dd("Error: ". $e->getMessage());
        // }
			
            $mobile= $request->phone;
			
        $message = '<a href="https://play.google.com/store/apps/details?id=com.jhoice.provider_app"></a>';
        $encode= urlencode($message);
        
        $authKey= "";
        $senderId= "";
	    $route= 4;
        $data=array(
            'authKey' => $authKey,
            'mobiles' => $mobile,
            'message' => $encode,
            'sender' => $senderId,
            'route' => $route
        );
        
        $url= "http://api.msg91.com/api/sendhttp.php";
        $ch= curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $output= curl_exec($ch);
        if(curl_errno($ch)){
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);
		$data=new InstallLink();
		$data->phone=$mobile;
		$data->type= $request->service_link;
		$data->save();
        return response()->json([
            'msg' => "SMS Send successfully",
            'status' =>200
        ]);
		}
	}
    public function index(){
        if (Auth::check()) {
          return redirect(route('dashboard'));
        }

        $faqs = $this->faqRepository->all();

        return view('welcome')->with('faqs', $faqs);
    }

    public function terms(){
        $customPage = $this->customPageRepository->findWithoutFail(2);

        if (empty($customPage)) {
            return redirect(route('default.index'));
        }

        return view('terms')->with('title', "Terms and Conditions")->with('data', $customPage);
    }

    public function privacy(){
        $customPage = $this->customPageRepository->findWithoutFail(1);

        if (empty($customPage)) {
            return redirect(route('default.index'));
        }

        return view('terms')->with('title', "Terms and Conditions")->with('data', $customPage);
    }
}
