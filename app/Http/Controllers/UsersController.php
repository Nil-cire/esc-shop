<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;

use App\Services\JWTService;

class UsersController extends Controller
{
    //
    public function index()
    {
        try {
            // $users = "";
            // \Schema::table('users', function($table) {
            //     $table->string('e-mail')->unique();
            // });

            $users = UsersModel::all();
            return response()->json(['data' => $users, 'msg' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => 'fail'], 500);
        }
    }

    public function register(Request $request)
    {

        $failMessage = '註冊失敗';
        $successMessage = '註冊成功';

        try {
            // request body = platform, oauth_token
            // 1. check request body valid
            $requestBody = json_decode($request->getContent(), true);
            $platform = array_key_exists("platform", $requestBody) ? $requestBody["platform"] : null;
            $authToken = array_key_exists("oauth_token", $requestBody) ? $requestBody["oauth_token"] : null;

            if (empty($platform) || empty($authToken)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'platform and token is required', 'msg' => '註冊失敗'], 400);
            }

            // 2. check if oauth token valid and get user info, add userName here
            $newUserName = "新使用者";
            $oauthId = '';
            switch ($platform) {
                case 'google':
                    $oauthId = $authToken;
                    break;
            }


            // 3. check if oauth_token registered 

            $userModel = new UsersModel();
            switch ($platform) {
                case 'google':

                    $user = $userModel->get_user_by_platform($platform, $oauthId);

                    if (empty($user)) {
                        // register

                        $uuid = (string) \Str::uuid();

                        $userModel->add_user(
                            $uuid,
                            $newUserName,
                            $platform,
                            $authToken
                        );

                        $jwtService = new JWTService();
                        $jwtToken = $jwtService->encode($uuid, $platform);
                        echo "success register \n";
                        $data = [
                            "token" => $jwtToken,
                            "new_register" => true,
                            "name" => $newUserName
                        ];

                        return response()->json(["data" => $data, "msg" => '註冊成功'], 200);


                    } else {
                        // login
                        $oldUserUid = $user['uuid'];
                        $oldUserName = $user['name'];
                        $jwtService = new JWTservice();
                        $jwtToken = $jwtService->encode($oldUserUid, $platform);
                        $data = [
                            "token" => $jwtToken,
                            "new_register" => false,
                            "name" => $oldUserName
                        ];
                        return response()->json(["data" => $data, "msg" => '登入成功'], 200);
                    }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

    }

    public function register_valid(Request $request)
    {

        $failMessage = '註冊失敗';
        $successMessage = '註冊成功';

        try {
            // request body = platform, oauth_token, email
            // 1. check request body valid
            $requestBody = json_decode($request->getContent(), true);
            $platform = array_key_exists("platform", $requestBody) ? $requestBody["platform"] : null;
            $authToken = array_key_exists("oauth_token", $requestBody) ? $requestBody["oauth_token"] : null;
            $email = array_key_exists("email", $requestBody) ? $requestBody["email"] : null;
            // $newUserName = array_key_exists("user_name", $requestBody) ? $requestBody["user_name"] : "新使用者";

            if (empty($platform) || empty($authToken) || empty($email)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'email, platform and token is required', 'msg' => '註冊失敗'], 400);
            }

            // 2. check if oauth token valid and get user info, add userName here
            $newUserName = "新使用者";
            $oauthId = '';
            switch ($platform) {
                case 'google':
                    $oauthId = $authToken; // todo by api
                    break;
            }

            // 3. check if oauth_token registered 

            $userModel = new UsersModel();

            switch ($platform) {
                case 'google':

                    $user = $userModel->get_user_by_email($email);

                    if (empty($user)) {
                        // register

                        $uuid = (string) \Str::uuid();

                        $insertUser = $userModel->add_user(
                            $email,
                            $uuid,
                            $newUserName,
                            $platform,
                            $oauthId
                        );

                        $jwtService = new JWTService();
                        $jwtToken = $jwtService->encode($uuid, $platform);
                        $data = [
                            "token" => $jwtToken,
                            "new_register" => true,
                            "name" => $newUserName,
                            "id" => $insertUser->id,
                        ];

                        return response()->json(["data" => $data, "msg" => '註冊成功'], 200);


                    } else {
                        // login
                        $oldUserUid = $user['uuid'];
                        $oldUserName = $user['name'];
                        $jwtService = new JWTservice();
                        $jwtToken = $jwtService->encode($oldUserUid, $platform);
                        $data = [
                            "token" => $jwtToken,
                            "new_register" => false,
                            "name" => $oldUserName,
                            "id" => $user->id
                        ];
                        return response()->json(["data" => $data, "msg" => '登入成功'], 200);
                    }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function login(Request $request)
    {

        $failMessage = '登入失敗';
        $successMessage = '登入成功';
        
        try {
            // body: platform, oauth_token, email
            // check oauth token or password
            $requestBody = json_decode($request->getContent(), true);
            $platform = array_key_exists("platform", $requestBody) ? $requestBody["platform"] : null;
            $authToken = array_key_exists("oauth_token", $requestBody) ? $requestBody["oauth_token"] : null;
            $email = array_key_exists("email", $requestBody) ? $requestBody["email"] : null;

            if (empty($platform) || empty($authToken) || empty($email)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'email, platform and token is required', 'msg' => $failMessage], 400);
            }

            // 2. check if oauth token valid and get user info, add userName here
            $newUserName = "新使用者";
            $oauthId = '';
            switch ($platform) {
                case 'google':
                    $oauthId = $authToken; // todo by api
                    break;
            }

            $usersModel = new UsersModel();
            switch ($platform) {
                case "google":
                    $user = $usersModel->where("email", $email)->first();
                    if (empty($user)) {
                        $data = [
                            "token" => '',
                            "is_registered" => false,
                        ];
                        return response()->json(["data" => $data, "msg" => '尚未註冊'], 200);
                    } else {
    
                        $hashId = $user["google_id"];
                        // $inputHashToken = \Hash::make($authToken);
                        if (!\Hash::check($oauthId, $hashId)) {
                            return response()->json(["error"=> "invalid oauth token", "msg"=> $failMessage], 400);
                        }

                        $uuid = $user["uuid"];
                        $jwtService = new JWTservice();
                        $jwtToken = $jwtService->encode($uuid, $platform);
                        $data = [
                            "token" => $jwtToken,
                            "is_registered" => true,
                            "user_name" => $user->name,
                            "id" => $user->id,
                        ];

                        // add to database
                        return response()->json(["data" => $data, "msg" => '登入成功'], 200);
                    }
                // break;
            }
            return response()->json(['error' => 'invalid platform', 'msg' => '登入失敗'], 400);
            // 
        } catch (\Exception $e) {
            // echo $e->getMessage();
            return response()->json(["error" => $e->getMessage(), "msg" => "登入失敗"], 500);
        }
    }
}
