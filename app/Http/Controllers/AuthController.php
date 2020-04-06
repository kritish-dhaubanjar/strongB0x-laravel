<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Role;

class AuthController extends Controller
{
    public function roles(){
        return Role::get(['id', 'name']);
    }

    public function login(Request $request){
    	$request->validate([
    		'email'=>'required',
    		'password'=>'required'
    	]);

    	$credentials = $request->only('email', 'password');

    	if(Auth::attempt($credentials)){
    		$user = Auth::user();
    		$token = $user->createToken('api-token');
    		return [	
    			'status'=>200,
    			'token'=>$token->plainTextToken
    		];
    	}else{
    		return [
    			'status'=>401,
    			'message'=>'These credentials do not match our records.'
    		];
    	}
    }

    public function logout(){
        if(Auth::check()){
            $user = Auth::user();
            $user->tokens()->delete();
            return [
                'status'=>200
            ];
        }
    }

    public function register(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'phone'=>'required|unique:users',
            'password'=>'required',
            'password_confirmation'=>'required|same:password',
            'role_id'=>'required',
            'enabled'=>'required'
        ]);

        $input = $request->all();
        // return $input;
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input);
        $user->role = $user->role;
        return [    
            'status'=>201,
            'user'=>$user
        ];
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);

        $email_confirmation =  User::where('email', $request->email)->first(['id']);
        $phone_confirmation =  User::where('phone', $request->phone)->first(['id']);

        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'phone'=>'required',
            'role_id'=>'required',
            'enabled'=>'required'
        ]);

        if((is_null($email_confirmation) || $email_confirmation->id == $id) && (is_null($phone_confirmation) || $phone_confirmation->id == $id)){
            $input = $request->all();

            if($request->has('password') && $request->has('password_confirmation')){
                $request->validate([
                    'password'=>'required',
                    'password_confirmation'=>'required|same:password',
                ]);
                $input['password'] = bcrypt($input['password']); 
            }

            $user->update($input);
            $user['role'] = $user->role;
            return [    
                'status'=>200,
                'user'=>$user,
            ];
        }else if(!is_null($email_confirmation) && $email_confirmation->id != $id && !is_null($phone_confirmation) && $phone_confirmation->id != $id){
            $request->validate([
                'email'=>'required|email|unique:users',
                'phone'=>'required|unique:users',
            ]);
        }
        else if(!is_null($email_confirmation) && $email_confirmation->id != $id){
            $request->validate([
                'email'=>'required|email|unique:users',
            ]);
        }else if(!is_null($phone_confirmation) && $phone_confirmation->id != $id){
            $request->validate([
                'phone'=>'required|unique:users',
            ]);
        }
    }

    public function destroy($id){
        $user = User::findOrFail($id);
        if($user->delete()){
            return $user;
        }
    }
}
