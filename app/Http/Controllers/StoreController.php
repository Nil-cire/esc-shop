<?php

namespace App\Http\Controllers;

use App\Models\FavoriteModel;
use App\Models\ProductModel;
use App\Models\StoreModel;
use App\Models\UsersModel;
use App\Services\JWTService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function test()
    {

        try {
            $stores = '';
            $stores = StoreModel::all();

            // ProductModel::truncate();

            // \Schema::table('products', function (Blueprint $table) {
            //     // $table->boolean('is_enable')->default(true);
            //     // $table->dropColumn('uuid');
            //     // $uuid = \Str::uuid();    
            //     $table->uuid('uuid')->unique()->nullable(false);
            //     // $table->decimal('special_price')->unsigned()->nullable()->change();
            // });
            // $stores = (new StoreModel)->index();
            // \Schema::dropIfExists("stores");
            // \Schema::table("stores", function ($table) {
            //     $table->string('banner_image_path')->nullable();
            // });
            return response()->json(['data' => $stores, 'msg' => "get stores success"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => 'get store fail'], 500);
        }
    }

    public function stores(Request $request)
    {
        try {
            $stores = StoreModel::all();
            return response()->json(['data' => $stores, 'msg' => "get stores success"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => 'get store fail'], 500);
        }
    }

    public function start(Request $request)
    {

        try {
            // body: user_uid, name, description
            // check oauth token or password
            $requestBody = json_decode($request->getContent(), true);
            $userUuid = array_key_exists("user_uid", $requestBody) ? $requestBody["user_uid"] : null;
            $storeName = array_key_exists("store_name", $requestBody) ? $requestBody["store_name"] : null;
            $description = array_key_exists("description", $requestBody) ? $requestBody["description"] : '';
            $type = array_key_exists("type", $requestBody) ? $requestBody["type"] : null;

            if (empty($storeName) || empty($userUuid) || empty($type)) {
                return response()->json(['error' => 'store name, type and user_id are required', 'msg' => '建立失敗'], 400);
            }

            // 1. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $userUuid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => '建立失敗'], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => '建立失敗'], 401);
            }

            // 2. add store data
            $storeModel = new StoreModel();
            $existStore = $storeModel->get_by_user($userUuid);

            if ($existStore) {
                return response()->json(['error' => 'user store existed', 'msg' => '建立失敗'], 400);
            } else {
                $store = $storeModel->add($userUuid, $storeName, $description, $type);
                return response()->json(['data' => $store, 'msg' => '建立成功'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => '建立失敗'], 400);
        }
    }

    public function update_info(Request $request)
    {
        try {
            // body: user_uid, name, description
            // check oauth token or password
            $requestBody = json_decode($request->getContent(), true);
            $userUuid = array_key_exists("user_uid", $requestBody) ? $requestBody["user_uid"] : null;
            $storeUuid = array_key_exists("store_uid", $requestBody) ? $requestBody["store_uid"] : null;
            $storeName = array_key_exists("store_name", $requestBody) ? $requestBody["store_name"] : null;
            $description = array_key_exists("description", $requestBody) ? $requestBody["description"] : null;
            $type = array_key_exists("type", $requestBody) ? $requestBody["type"] : null;

            if (empty($userUuid)) {
                return response()->json(['error' => 'user_id is required', 'msg' => '更改失敗'], 400);
            }

            // 1. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $userUuid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => '更改失敗'], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => '更改失敗'], 401);
            }

            // 2. add store data

            $storeModel = new StoreModel();
            $store = $storeModel->updateInfo($storeUuid, $storeName, $description, $type);
            return response()->json(['data' => $store, 'msg' => '更改成功'], 200);
            // $existStore = $storeModel->get_by_uid($storeUuid);
            // if (!$existStore) {
            //     return response()->json(['error' => 'user store not exist', 'msg' => '更改失敗'], 400);
            // } else {
            //     $store = $storeModel->updateInfo($storeUuid, $storeName, $description, $type);
            //     return response()->json(['data' => $store, 'msg' => '更改成功'], 200);
            // }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => '更改失敗'], 400);
        }
    }

    public function open_close(Request $request)
    {
        try {
            // body: user_uid, store_uid, open
            // check oauth token or password
            $requestBody = json_decode($request->getContent(), true);
            $userUuid = array_key_exists("user_uid", $requestBody) ? $requestBody["user_uid"] : null;
            $storeUuid = array_key_exists("store_uid", $requestBody) ? $requestBody["store_uid"] : null;
            $open = array_key_exists("open", $requestBody) ? $requestBody["open"] : null;

            if (empty($userUuid) || empty($storeUuid) || ($open === null)) {
                return response()->json(['error' => 'user_uid, store_uid, open are required', 'msg' => '更改失敗'], 400);
            }

            // 1. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $userUuid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => '更改失敗'], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => '更改失敗'], 401);
            }

            // 2. add store data

            $storeModel = new StoreModel();
            $storeModel->open($storeUuid, $open);
            return response()->json(['data' => null, 'msg' => '更改成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => '更改失敗'], 400);
        }
    }

    function temporary_delete(Request $request) {
        try {
            // body: user_uid, name, description
            // check oauth token or password
            $requestBody = json_decode($request->getContent(), true);
            $userUuid = array_key_exists("user_uid", $requestBody) ? $requestBody["user_uid"] : null;
            $storeUuid = array_key_exists("store_uid", $requestBody) ? $requestBody["store_uid"] : null;
            // $delete = array_key_exists("open", $requestBody) ? $requestBody["open"] : null;

            if (empty($storeUuid) || empty($userUuid)) {
                return response()->json(['error' => 'user_uid and store_uid are required', 'msg' => '更改失敗'], 400);
            }

            // 1. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $userUuid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => '更改失敗'], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => '更改失敗'], 401);
            }

            // 2. set delete data
            $storeModel = new StoreModel();
            $storeModel->temporary_delete($storeUuid, false);
            return response()->json(['data' => null, 'msg' => '更改成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => '更改失敗'], 400);
        }
    }
}
