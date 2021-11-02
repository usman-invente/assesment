<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function sendInvitation(Request $request){
        try {
            $email = $request->email;
            if($email){
              \Mail::to($email)->send(new \App\Mail\SendInvitation);
              return response()->json([
                'success'=>true,
                'messaage' => 'Invitation email sent'
            ]); 
            }else{
               return response()->json([
                   'success'=>false,
                   'messaage' => 'email required'
               ]); 
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success'=>false,
                'messaage' => $th->getMessage()
            ]); 
        }
        
       
        
    }
}
