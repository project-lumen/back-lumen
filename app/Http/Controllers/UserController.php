<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;

use Auth;

class UserController extends Controller
{
    private $salt;
    public function __construct()
    {
        $this->salt="userloginregister";
    }

    public function login(Request $request){
      if ($request->has('pseudo') && $request->has('password')) {
        $user = User:: where("pseudo", "=", $request->input('pseudo'))
                      ->where("password", "=", sha1($this->salt.$request->input('password')))
                      ->first();
        if ($user) {
                    $token=str_random(10);
                    $user->api_token=$token;
                    $user->save();
                    $res = array('api_token' => $user->api_token);
                    return json_encode($res);
        } else {
          $res = array('error' => "Le nom d'utilisateur ou le mot de passe est incorrect, la connexion a échoué!");
                   return json_encode($res);
        }
      } else {
        return "Les informations de connexion sont incomplètes, veuillez entrer le nom d'utilisateur et le mot de passe!";
      }
    }

    public function info(){
      var_dump("hello");
      die;
      return Auth::auth();
    }

// VALIDE

    public function register(Request $request){
      if ($request->has('pseudo') && $request->has('password')&& $request->has('email')) {
        if ($request->input('pseudo') != null && $request->input('password') != null && $request->input('email') != null) {

          // checkUsersUniq($request->input('pseudo'), $request->input('email')) ;

          $user = new User;
          $user->pseudo=$request->input('pseudo');
          $user->password=sha1($this->salt.$request->input('password'));
          $user->email=$request->input('email');
          $user->codeUser=str_random(5);
          $user->role=16;
          $user->list=[];
          if($user->save()){
            $res['success'] = true;
            $res['message'] = "L'inscription de l'utilisateur a réussi!";
            return response($res);

          } else {
            $res['success'] = false;
            $res['message'] = "L'inscription à echoué";
            return response($res);
          };
        }else{
          $res['success'] = false;
          $res['message'] = "Saisir toutes les données";
          return response($res);
        };

      }else{
        $res['success'] = false;
        $res['message'] = "Saisir toutes les données";
        return response($res);      }
    }
//LOGOUT
    public function logout(Request $request){
      if ($request->has('pseudo') && $request->has('token')) {
        $user = User::where("pseudo", "=", $request->input('pseudo'))
                      ->where("api_token", "=", $request ->input('token'))
                      ->first();
        if($user){
          $user->api_token=NULL;
          $user->save();
          return "Déconnection";
        } else {
          return "Erreur de déconnection";
        };
      }else{
        return "Saisir toutes les donneés";
      }
    }



    public function get_user(Request $request, $token)
       {
           $user = User::where('api_token', $token)->get();
           if ($user) {
                 $res['success'] = true;
                 $res['message'] = $user;
                 return response($res);
           }else{
             $res['success'] = false;
             $res['message'] = 'Cannot find user!';
             return response($res);
           }
       }


       public function infoUser(Request $request)
       {
         $user = User::where('api_token', $request->input('api_token'))->first();
         $res['name'] = $user->pseudo;
         $res['code'] = $user->codeUser;
         return response($res);


       }













}
