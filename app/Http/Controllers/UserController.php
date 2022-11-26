<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserController extends Controller
{
    public function  create(Request $req)
    {
        $requestedData = $req->all();
        $validateData = Validator::make($requestedData,[
            'name'=>'required|min:2',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|confirmed',
        ]);
        if($validateData->fails())
        {
            //throw new HttpResponseException(response()->json($validateData->errors(), 422));
            return response()->json(['errors'=>$validateData->errors(),'status'=>'failed','status_code'=>200]);
        }else{
            $requestedData['password'] = Hash::make($req->password);
            $createUser = User::create($requestedData);
            if($createUser){
                return response()->json(['status'=>'OK','status_code'=>200],200);
            }else{
                return response()->json(['status'=>'failed','status_code'=>500],500);
            }
        }
    }
    public function login(Request $req)
    {
        $validation = Validator::make($req->all(),[
            'email'=>'required|email|exists:users,email',
            'password'=>'required'
        ]);
        if($validation->fails())
        {
            return response()->json(['errors'=>$validation->errors(),'status'=>'failed','status_code'=>400],400);
        }else{
            $email = $req->email;
            $dbPassword = User::whereEmail($email)->value('password');
            $password = $req->password;
            $checkPassword = Hash::check($password ,$dbPassword);
            if($checkPassword){
                if(!$token = auth()->attempt(['email' => $email, 'password' => $password])){
                    return response()->json(['status'=>'unauthorized access','status_code'=>400],400);
                }else{
                    return $this->respondWithToken($token);
                }

            }else{
                return response()->json(['status'=>'failed','status_code'=>400],400);
            }
        }
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()->getTTL()*60
        ],200);
    }
    public function userProfile()
    {
        return response()->json(auth()->user());
        // return response()->json([
        //     'message'=>'Success',
        //     'status'=>'OK',
        //     'status_code'=>200
        // ],200);
    }
    public function refreshToken()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['status'=>'OK','status_code'=>200,'message'=>'logout successfully']);
    }
}
