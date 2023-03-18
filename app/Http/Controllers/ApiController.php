<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Notifications;
use App\Models\Referral;
use App\Models\KopoKopoSTKTransaction;
use Carbon\Carbon;
use App\Helpers\Utils;
use App\Helpers\KopoKopoApi;
use App\Models\Optimization;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    //
    public function getcode($length){
        $characters = 'A0B2C3D4E5F6G7H8J9K0L1M2N3P4Q5R6S7T8U9V0W1X2Y3Z4';
        return substr(str_shuffle($characters), 0, $length);
    }
    // login...............
    public function signin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if(!Auth::attempt($credentials)){

            return response()->json([
                'message' => 'Invalid email address or password',
                'success' => '0',

            ], 200);
        }
        $user = User::where('email', $request->email)->first();
        return response()->json(['success' => '1', 'token' => $user->createToken('tokens')->plainTextToken, 'user' =>  $user, 'message' => 'Login Successful', 'phone' => '+254788462122', 'whatsapp' => '+1 (925) 217-8816', 'email' => 'support@frequencycapitalltd.com' ], 200);
    }

    public function signup(Request $request)
    {
        //

        $user = User::where('email',$request['email'])->first();
        if($user === null){
            $muser = User::where('phone',$request['phone'])->first();
            if($muser === null){
                $code = $this->getcode(6);
                $credentials = [
                    'phone' => $request->phone,
                    'password' => Hash::make($request['password']),
                    'email' => $request['email'],
                    'name' => $request['name'],
                    'id_number' => $request['id_number'],
                    'referral_code' => $code
                ];
                $user = User::create($credentials);
                if($request['referral_code'] != '' && $request['referral_code'] != null){
                    $muse = User::where('referral_code', $request['referral_code'])->first();
                    if($muse != null){
                        $refferal = Referral::create([
                            'user_id' => $user->id,
                            'other_user_id' => $muse->id,
                            'referral_code' => $request['referral_code']
                        ]);
                    }
                    
                }

                $notification = Notifications::create([
                    'user_id' => $user->id,
                    'title' => 'New Signin',
                    'narration' => 'Dear '.$request['name'].', Thankyou for registering with us. Share with friends using this referal code '.$code.';'
                ]);
                
                return response()->json(['success' => '1', 'token' => $user->createToken('tokens')->plainTextToken, 'user' =>  $user, 'message' => 'Login Successful', 'phone' => '+254788462122', 'whatsapp' => '+1 (925) 217-8816', 'email' => 'support@frequencycapitalltd.com' ], 200);
    
            }else{
            
                return response()->json(['message' => 'User with phone number already exist. Please forget password', 'success' => '0' ], 200);
    
            }
            
        }else{
            
            return response()->json(['message' => 'User with email address already exist. Please forget password', 'success' => '0' ], 200);

        }
    }

    public function editProfile(Request $request){
        auth()->user()->update($request->all());
        $notificat = Notifications::create([
            'user_id' => auth()->user()->id,
            'title' => 'User Update',
            'narration' => 'Your profile details have been updated successfuly'
        ]);
        return response()->json(['message' => 'User has been updated', 'success' => '1' ], 200);
    }

    public function optimizehustler(Request $request){
        $optimizeaction = $request['optimize_action'];
        $message="Success. Please wait for mpesa stk push to complete the process";

        $utils = new Utils();
        $code = $utils->getreferalcode(10);

        if($optimizeaction == 'fee'){
            $phone = $request['phone_number'];
            $respon = $this->initiateKopoKopo($code,'50',$phone);
            $optimization = Optimization::create([
                'user_id' => auth()->user()->id,
                'amount' => '50',
                'optimization_type' => 'hustler',
                'method' => 'mpesa',
                'status' => 'Not Paid',
                'reference_no' => $code,
                'phone_number' => $phone,
                'isApproved' => false,
                'message' => 'We have received your optimization request. Please share with friend or pay Ksh 30 to proceed'
            ]);
        }else{
            $optimization = Optimization::create([
                'user_id' => auth()->user()->id,
                'amount' => '50',
                'optimization_type' => 'hustler',
                'method' => 'share',
                'phone_number' => $request['phone_number'],
                'status' => 'Paid',
                'isApproved' => false,
                'message' => 'We have received your optimization request. We are reviewing your request, Kindly wait for approval'
            ]);
            $message = "Success. Please wait for approval";
        }
        $notificat = Notifications::create([
            'user_id' => auth()->user()->id,
            'title' => 'Hustler Optimization',
            'narration' => 'Thankyou for optimizig hustler loan limit. Please wait as we review your request'
        ]);
        return response()->json(['message' => $message, 'success' => '1' ], 200);
    }

    public function optimizecrb(Request $request){
        $message="Success. Please wait for mpesa stk push to complete the process";
        $optimizeaction = $request['optimize_action'];
        $utils = new Utils();
        $code = $utils->getreferalcode(10);
        if($optimizeaction == 'fee'){
            $phone = $request['phone_number'];
            $respon = $this->initiateKopoKopo($code,'50',$phone);
            $optimization = Optimization::create([
                'user_id' => auth()->user()->id,
                'amount' => '50',
                'optimization_type' => 'crb',
                'method' => 'mpesa',
                'reference_no' => $code,
                'status' => 'Not Paid',
                'phone_number' => $phone,
                'isApproved' => false,
                'message' => 'We are reviewing your request and submit to Credit Reference Bureau(CRB). Please keep on checking your request status'
            ]);

            return response()->json(['message' => $message, 'respon' => $respon, 'success' => '1' ], 200);
        }else{
            $optimization = Optimization::create([
                'user_id' => auth()->user()->id,
                'amount' => '50',
                'optimization_type' => 'crb',
                'method' => 'share',
                'phone_number' => $request['phone_number'],
                'status' => 'Paid',
                'isApproved' => false,
                'message' => 'We are reviewing your request and submit to Credit Reference Bureau(CRB). Please keep on checking your request status'
            ]);
            $message = "Success. Please wait for approval";
        }
        $notificat = Notifications::create([
            'user_id' => auth()->user()->id,
            'title' => 'CRB Delisting',
            'narration' => 'Your request for CRB Delisting has been received. Please wait as we reveiw'
        ]);
        return response()->json(['message' => $message, 'success' => '1' ], 200);
    }

    public function notifications(){
        return Notifications::where('user_id', auth()->user()->id)->get();
    }

    public function getDashboard(){

        return response()->json(['success' => '1', 'referals'=> Referral::where('other_user_id', auth()->user()->id)->count(), 'optimizations' => Optimization::where('user_id', auth()->user()->id)->count() ], 200);
    }

    public function checkpages(Request $request){
        
        if($request['action'] == 'hustler'){
            $message = "";
            $refered = Referral::where('other_user_id', auth()->user()->id)->count();
            $optimization = Optimization::where('optimization_type', 'hustler')->where('user_id', auth()->user()->id)->where('status', 'Paid')->where('isApproved', false)->first();
            if($optimization){
                $message = "Your application has been submited to our data center awaiting for reveiw";
                $title = "Success";
            }else{
                $moptimization = Optimization::where('optimization_type', 'hustler')->where('user_id', auth()->user()->id)->where('status', 'Paid')->where('isApproved', true)->where('isDismiss', false)->first();
                if($moptimization){
                    $message = "Your application has been approved. Please wait within 48 hours for the optimization to go through";
                    $title = "Approved";
                }else{
                    $title = "Dismiss";
                }

            }
            return response()->json(['message' => $message, 'title' => $title, 'success' => '1', 'refered' => $refered ], 200);

        }else{
            $message = "";
            $refered = Referral::where('other_user_id', auth()->user()->id)->count();
            $optimization = Optimization::where('optimization_type', 'crb')->where('user_id', auth()->user()->id)->where('status', 'Paid')->where('isApproved', false)->first();
            if($optimization){
                $message = "Your application has been submited to our data center awaiting for reveiw";
                $title = "Success";
            }else{
                $moptimization = Optimization::where('optimization_type', 'crb')->where('user_id', auth()->user()->id)->where('status', 'Paid')->where('isApproved', true)->where('isDismiss', false)->first();
                if($moptimization){
                    $message = "Your application has been approved. Please wait within 2 days for the CRB Delisting";
                    $title = "Approved";
                }else{
                    $title = "Dismiss";
                }

            }
            return response()->json(['message' => $message, 'title' => $title, 'success' => '1', 'refered' => $refered ], 200);
            

        }

    }

    public function checkDone(Request $request){
        if($request['action'] == 'hustler'){
            $moptimization = Optimization::where('optimization_type', 'hustler')->where('user_id', auth()->user()->id)->where('status', 'Paid')->where('isApproved', true)->where('isDismiss', false)->first();
            if($moptimization){
                $date = Carbon::parse($moptimization->date_approved);
                $days = Carbon::now()->diffInDays($date);
                if($days > 1){
                    $moptimization->update([
                        'isDismiss' => true
                    ]);
                    return response()->json(['message' => "Updated Successfully", 'success' => '1' ], 200);
                }
            }
            return response()->json(['message' => "Your optimization application is being reviewed", 'success' => '0' ], 200);

        }
        if($request['action'] == 'crb'){
            $moptimization = Optimization::where('optimization_type', 'crb')->where('user_id', auth()->user()->id)->where('status', 'Paid')->where('isApproved', true)->where('isDismiss', false)->first();
            if($moptimization){
                $date = Carbon::parse($moptimization->date_approved);
                $days = Carbon::now()->diffInDays($date);
                if($days > 1){
                    $moptimization->update([
                        'isDismiss' => true
                    ]);
                    return response()->json(['message' => "Updated Successfully", 'success' => '1' ], 200);
                }
            }
            return response()->json(['message' => "Your crb delisting application is being reviewed", 'success' => '0' ], 200);

        }
    }

    public function refresh(Request $request){
      
        $user = User::find($request['user_id']);
      
        return response()->json(['success' => '1', 'token' => $user->createToken('tokens')->plainTextToken, 'user' =>  $user, 'message' => 'Login Successful', 'phone' => '+254788462122', 'whatsapp' => '+1 (925) 217-8816', 'email' => 'support@frequencycapitalltd.com' ], 200);
    }

    public function initiateKopoKopo($code, $amount, $phone){

        $kopokopo = new KopoKopoApi();
        $utils = new Utils();
        $user = auth()->user();
        $name = $user->name;
        $x_name = explode(' ', $name);
        $firstname = $x_name[0];
        $lastname = $x_name[1];
        if($lastname == null){
            $lastname = $firstname;
        }
        $stk = KopoKopoSTKTransaction::create([
            'amount'=>$amount,
            'senderPhoneNumber' => '+'.$utils->sanitizePhone($phone),
            'tillNumber' => $kopokopo->stk_till_number,
            'senderFirstName' => $firstname,
            'senderLastName' => $lastname,
            'request_reference' => $code,
            'customer_id' => $user->id,
            'trans_id' => 'N/A',
            'reference' => 'N/A',
            'result_desc' => 'N/A',
            'status' => 'Pending'
        ]);
        $data = [
            'paymentChannel' => 'M-PESA STK Push',
            'tillNumber' => $kopokopo->stk_till_number,
            'firstName' => $firstname,
            'lastName' => $lastname,
            'phoneNumber' => '+'.$utils->sanitizePhone($phone),
            'amount' => $amount,
            'currency' => 'KES',
            'email' => $user->email,
            'callbackUrl' => $kopokopo->stk_callback,
            'metadata' => [
                'customerId' => 'U'.$user->id,
                'reference' => $code,
                'notes' => 'Investment deposit'
            ],
            'accessToken' => $kopokopo->generateToken(),
        ];
        $response = $kopokopo->initiate_stk_push($data);
        return $response;

    }

    public function payment_callback_stk(Request $request){

        $data = $request['data'];
        $attribute = $data['attributes'];

        if($attribute['status'] == 'Success'){
            $ref = $attribute['metadata']['reference'];
            $event = $attribute['event'];
            $resource = $event['resource'];
            $stk = KopoKopoSTKTransaction::where('request_reference', $ref)->first();
            if($stk === null){
                return "Transaction does not exist";
            }
            if($stk->trans_id == 'N/A'){
                $optimizer = Optimization::where('reference_no', $ref)->first();
                if($optimizer === null){
                    return "Failed";
                }
                $optimizer->update([
                    'status' => 'Paid',
                ]);
                $stk->update([
                    'trans_id' => $data['id'],
                    'reference' => $resource['reference'],
                    'status' => $attribute['status'],
                    'result_desc' => 'Completed'
                ]);

                $user_id = $stk->customer_id;


                $isFirsttime = false;

                return "Success";
                // return $this->checkifReferred($user_id, $investment, $isFirsttime, $ref);
            }
            else{
                return "Transaction already exist";
            }
        }else if($attribute['status'] == 'Failed'){
            $ref = $attribute['metadata']['reference'];
            $event = $attribute['event'];

            $stk = KopoKopoSTKTransaction::where('request_reference', $ref)->first();
            $stk->update([
                'trans_id' => $data['id'],
                'reference' => $ref,
                'status' => $attribute['status'],
                'result_desc' => $event['errors']
            ]);
            return "Failed";
        }

    }

}
