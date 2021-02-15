<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User; 
use App\Models\Make_attendance_model; 
use Illuminate\Support\Facades\Auth;
use Session;

class AuthController extends Controller
{
    //
    public function signup(Request $request){

      
        $validator=Validator::make($request->all(),[
            'name' => 'required',
            'mobileno'=>'required',
            'password'=>'required',
            'faceid'=>'required',
        ]);
        if ($validator->fails())
        {
            return response()->json(['status'=>0,'message'=>'internal  error']);
        }

      $userinfo=  User::where('mobileno',$request->mobileno)->get();//for checking exist moblie no
      if(count($userinfo) >0){
        return response()->json(['status'=>2,'message'=>'already exists']);
 
      }else{
        
       
        $user=new User();
        $user->name=$request->name;
        $user->mobileno=$request->mobileno;
        $user->password= $request->password;
        $user->faceid= $request->faceid;
        $user->save();
        return response()->json(['status'=>1,'message'=>'User Created']);
      }
    }

    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            'mobileno'=>'required',
            'password'=>'required',
        ]);
        if ($validator->fails())
        {
            return response()->json(['status'=>0,'message'=>'internal  error']);
        }

        // $credentials=request(['mobile','password']);

      
        $user=User::where('mobileno',$request->mobileno)->where('password',$request->password)->get();
        if(count($user) >0){
            $userinfo=User::where('mobileno',$request->mobileno)->first();
         
            $tokenResult=$userinfo->createToken('authToken')->plainTextToken;
            return response()->json(['status_code'=>1,'token'=>$tokenResult,'message'=>'success'.$userinfo]);
        }else{
            $checkmobileno=User::where('mobileno',$request->mobileno,)->get();
            if(count($checkmobileno) >0){
                return response()->json(['status_code'=>2,'message'=>'wrong password']);

            }else{
                return response()->json(['status_code'=>2,'message'=>'wrong mobile no']);

            }
        }

     }
     public function markattandance(Request $request){
        $validator=Validator::make($request->all(),[
            'mobileno'=>'required',
            'locationaddress'=>'required',
            'longitude'=>'required',
            'latitude'=>'required',
            'datetime'=>'required',
            'user_id'=>'required',
        ]);
        if ($validator->fails())
        {
            return response()->json(['status'=>0,'message'=>'internal  error']);
        }else{

           $date= Date('Y-m-d H:i:s', strtotime($request->datetime));

            $attadance=new Make_attendance_model();
            $attadance->mobile_no=$request->mobileno;
            $attadance->location_address=$request->locationaddress;
            $attadance->longitude= $request->longitude;
            $attadance->latitude= $request->latitude;
            $attadance->datetime= $date;
            $attadance->user_id= $request->user_id;
            $attadance->save();
            return response()->json(['status'=>1,'message'=>'success']);
        }
     }
     public function viewattandance(Request $request){
         $attadance=array();
        $validator=Validator::make($request->all(),[
            'mobileno'=>'required' ]);
            if ($validator->fails())
            {
                return response()->json(['status'=>0,'message'=>'internal  error']);
            }else{
                $fromdate=$request->fromdate;
                $todate=$request->todate;
                if(($fromdate !="") && ($todate !="")){
                    $fromdate= date('Y-m-d', strtotime($fromdate));
                    $todate= date('Y-m-d', strtotime($todate));
                }
             

                $viewattandance=Make_attendance_model::where('mobile_no',$request->mobileno);
                if(($fromdate !="") && ($todate !="")){
                    
                    $viewattandance= $viewattandance->whereDate('datetime','>=',$fromdate);
                    $viewattandance= $viewattandance->whereDate('datetime','<=',$todate);
                }
                $viewattandance= $viewattandance->get();
                foreach($viewattandance as $attadanceinfo){
                    $attadance[]=array(
                        'location_address'=>$attadanceinfo->location_address,
                        'datetime'=>$attadanceinfo->datetime,
                    );
                }
                return response()->json(['status'=>1,'data'=>$attadance,'message'=>'success']);
            }
     }
     public function authlogin(Request $request){
        return response()->json(['status'=>0,'message'=>'Un Authorized User']);

     }
     public function tokengenerate(Request $request){
       $userinfo=User::first();
     
      $tokenResult=$userinfo->createToken('authToken')->plainTextToken;

       
        return response()->json(['status_code'=>1,'token'=>$tokenResult,'message'=>'success']);

     }
}
