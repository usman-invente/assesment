<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Hash;
use Auth;
class UserController extends Controller
{
    public function signUp(Request $request)
    {
        
        $email = $request->email;
        $pass = $request->password;
        if (empty($email)) {
            return response()->json([
                'success' => false,
                'messaage' => 'email required',
            ]);
        } else if (empty($pass)) {
            return response()->json([
                'success' => false,
                'messaage' => 'password required',
            ]);
        } else {
            $pin = random_int(100000, 999999);
            $user = new User;
            $user->email = $email;
            $user->password = Hash::make($pass);
            $user->pin =$pin ;
            $user->save();
            $details = [
                'pin' => $pin,
                
            ];
            \Mail::to($email)->send(new \App\Mail\Verify($details));
            return response()->json([
                'success' => true,
                'messaage' => 'A pin is sent to your email',
            ]);
        }

    }

    public function profile(){
          $user = Auth::user();
         $user->avatar = env('APP_URL').'public/avatar/'.$user->avatar;
         return $user;
    }

    public function userLogin(Request $request){
        $user = User::where('email', $request->email)->first();
        // print_r($data);
        if($user){
            if($user->status==1){
                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response([
                        'message' => ['These credentials do not match our records.'],
                    ], 404);
                } 
            }else{
                return response([
                    'message' => ['Your Pin is not verified'],
                ], 404); 
            }
        }else{
            return response([
                'message' => ['These credentials do not match our records.'],
            ], 404); 
        }
      
       

        $token = $user->createToken('my-app-token')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function verify(Request $request){
        try {
            $pin = $request->pin;
            if(empty($pin)){
                return response()->json([
                    'success' => false,
                    'messaage' => 'pin is required',
                ]);
            }else{
                 $user = User::where('pin',$pin)->first();
                 if($user){
                    if($pin == $user->pin){
                        User::where('pin',$pin)->update([
                            'status' =>1,
                            'pin' => Null
                        ]);
                        return response()->json([
                            'success' => true,
                            'messaage' => 'YOu can login Now',
                        ]);
                    }
                 }else{
                    return response()->json([
                        'success' => false,
                        'messaage' => 'pin is invalid',
                    ]);
                }
               
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'messaage' => $th->getMessage()
            ]);
        }
    }

    public function updateProfile(Request $request){
        try {
        
        
            $user = Auth::user();
            $input = $request->all();
            if(empty($input)){
                return response()->json([
                    'success' => false,
                    'messaage' => 'Request params not Found'
                ]);
            }
            if ($request->has('email')) {
              
                $input['email']  =  $request->email;
               
            }
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $name = time().'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/avatar');
                $image->move($destinationPath, $name);
                $input['avatar']  =  $name;
               
            }
            if ($request->has('password')) {
                $input['password'] = Hash::make($request->password);
               
            }

            User::where('id',$user->id)->update($input);
            return response()->json([
                'success' => true,
                'messaage' => 'profile Updated'
            ]);
            
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'messaage' => $th->getMessage()
            ]);
        }
    }

   
}
