<?php

namespace App\Http\Controllers;

use App\Models\UsersModel;
use Illuminate\Http\Request;

use App\Services\JWTService;
use App\Util\GoogleOauth;

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

    public function info(Request $request)
    {
        try {

            $uuid = $request->input('id');
            // $idArray = explode(',', $ids); 

            $user = (new UsersModel)
                ->where(
                    'uuid',
                    $uuid
                )->get([
                        'name',
                        'buy_count',
                        'reported_count'
                    ])
                ->first();
            if (empty($user)) {
                return response()->json(['error' => 'user not found', 'msg' => '查無使用者'], 404);
            } else {
                return response()->json(['data' => $user, 'msg' => '查詢成功'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => '查詢失敗'], 500);
        }
    }

    public function update_info(Request $request)
    {
        try {
            $failMsg = '更新失敗';
            $successMsg = '更新成功';

            $requestBody = json_decode($request->getContent(), false);

            $userUuid = $requestBody->user_uid;
            $name = $requestBody->name ?? null;

            // 1. check required data provided
            if (empty(trim($name))) {
                return response()->json(['error' => 'name is required and can not be empty', 'msg' => $failMsg], 400);
            }

            // 2. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $userUuid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => $failMsg], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => $failMsg], 401);
            }

            // 3. insert data
            $userModel = new UsersModel();
            $updateUser = $userModel->update_user(
                $userUuid,
                $name
            );
            return response()->json(['data' => $updateUser, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
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
                    // $url = get_google_check_url($authToken);
                    // $json = json_decode(file_get_contents($url), true);
                    // $oauthUniqueId = array_key_exists("sub", $json) ? $json["sub"] : null;
                    // $email = array_key_exists("email", $json) ? $json["email"] : null;
                    // $given_name = array_key_exists("given_name", $json) ? $json["given_name"] : null;
                    // if (empty($oauthUniqueId)) {
                    //     return response()->json(['error' => 'invalid oauth token', 'msg' => '註冊失敗'], 400);
                    // }
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
            // $email = array_key_exists("email", $requestBody) ? $requestBody["email"] : null;
            // $newUserName = array_key_exists("user_name", $requestBody) ? $requestBody["user_name"] : "新使用者";

            if (empty($platform) || empty($authToken)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'email, platform and token is required', 'msg' => '註冊失敗'], 400);
            }

            // 2. check if oauth token valid and get user info, add userName here
            $newUserName = "新使用者";
            $email = '';
            $oauthId = '';
            switch ($platform) {
                case 'google':
                    if ($authToken === "9999" || $authToken === "9998") {
                        $oauthId = $authToken; // todo by api
                        $email = 'esc-test@shop.com';
                        if ($authToken === "9998") {
                            $email = 'esc-test2@shop.com';
                        }
                    } else {
                        $url = (new GoogleOauth)->get_google_check_url($authToken);
                        try {
                            $response = file_get_contents($url);
                        } catch (\Exception $e) {
                            return response()->json(['error' => 'invalid oauth token, oauth2 validation failed', 'msg' => '註冊失敗'], 400);
                        }
                        $json = json_decode($response, true);

                        $oauthUniqueId = array_key_exists("sub", $json) ? $json["sub"] : null;
                        $email = array_key_exists("email", $json) ? $json["email"] : null;
                        $newUserName = array_key_exists("name", $json) ? $json["name"] : "新使用者";

                        if (empty($oauthUniqueId)) {
                            return response()->json(['error' => 'invalid oauth token', 'msg' => '註冊失敗'], 400);
                        }

                        $oauthId = $oauthUniqueId;
                    }

                    break;
            }

            if (empty($email)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'email is not valid', 'msg' => '註冊失敗'], 400);
            }

            // 3. check if oauth_token registered 

            $userModel = new UsersModel();

            switch ($platform) {
                case 'google':

                    $user = $userModel->get_user_by_email($email);

                    if (empty($user)) {
                        // register

                        $uuid = (string) \Str::orderedUuid();

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
                            "user_uid" => $uuid
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
                            "id" => $user->id,
                            "user_uid" => $oldUserUid
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
            // $email = array_key_exists("email", $requestBody) ? $requestBody["email"] : null;

            if (empty($platform) || empty($authToken)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'platform and token is required', 'msg' => $failMessage], 400);
            }

            // 2. check if oauth token valid and get user info, add userName here
            $email = '';
            $oauthId = '';
            switch ($platform) {
                case 'google':
                    if ($authToken === "9999" || $authToken === "9998") {
                        $oauthId = $authToken; // todo by api
                        $email = 'esc-test@shop.com';
                        if ($authToken === "9998") {
                            $email = 'esc-test2@shop.com';
                        }
                    } else {
                        $url = (new GoogleOauth)->get_google_check_url($authToken);
                        try {
                            $response = file_get_contents($url);
                        } catch (\Exception $e) {
                            return response()->json(['error' => 'invalid oauth token, oauth2 validation failed', 'msg' => '註冊失敗'], 400);
                        }
                        $json = json_decode($response, true);

                        $oauthUniqueId = array_key_exists("sub", $json) ? $json["sub"] : null;
                        $email = array_key_exists("email", $json) ? $json["email"] : null;
                        // $newUserName = array_key_exists("name", $json) ? $json["name"] : "新使用者";

                        if (empty($oauthUniqueId)) {
                            return response()->json(['error' => 'invalid oauth token', 'msg' => '註冊失敗'], 400);
                        }

                        $oauthId = $oauthUniqueId;
                    }

                    break;
            }

            if (empty($email)) {
                // throw new HttpException(400, "platform and token are required");
                return response()->json(['error' => 'email is not valid', 'msg' => '註冊失敗'], 400);
            }
            ///////

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
                            return response()->json(["error" => "invalid oauth token", "msg" => $failMessage], 400);
                        }

                        $uuid = $user["uuid"];
                        $jwtService = new JWTservice();
                        $jwtToken = $jwtService->encode($uuid, $platform);
                        $data = [
                            "token" => $jwtToken,
                            "is_registered" => true,
                            "user_name" => $user->name,
                            "id" => $user->id,
                            "user_uid" => $user->uuid
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

    public function refresh_token(Request $request)
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $userUuid = array_key_exists("user_uid", $requestBody) ? $requestBody["user_uid"] : null;

            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $jwtService = new JWTservice();
                $isValidButExpired = $jwtService->verify_expired($token, $userUuid);
                if (!$isValidButExpired) {
                    return response()->json(['error' => 'invalid token', 'msg' => '更新失敗'], 401);
                } else {
                    $jwtToken = $jwtService->encode($userUuid, "refresh");
                    $data = [
                        "token" => $jwtToken,
                    ];
                    return response()->json(["data" => $data, "msg" => "更新成功"], 200);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => '更新失敗'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => '更新失敗'], 500);
        }
    }
}
