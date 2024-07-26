<?php

namespace App\Http\Controllers;

use App\Models\FavoriteModel;
use Illuminate\Http\Request;
use App\Services\JWTService;
use Illuminate\Validation\ValidationException;

class FavoriteController extends Controller
{
    //
    // public function index()
    // {
    // }

    public function favorites(Request $request)
    {
        $failMsg = '搜尋收藏清單失敗';
        $successMsg = '搜尋收藏清單成功';
        try {
            // 1. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $user_uid = (new JWTService())->simple_verify($token);
                if (empty($user_uid)) {
                    return response()->json(['error' => 'invalid token', 'msg' => $failMsg], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => $failMsg], 401);
            }

            $favoritesModel = new FavoriteModel();
            $favorites = $favoritesModel->get_favorites($user_uid); 
            return response()->json(['data' => $favorites, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    public function add_favorite(Request $request)
    {
        // $id = $request->input('user_uid');
        $failMsg = '新增收藏清單失敗';
        $successMsg = '新增收藏清單成功';
        try {

            $requestBody = json_decode($request->getContent(), false);
            $user_uid = $requestBody->user_uid ?? null;
            $store_uid = $requestBody->store_uid ?? null;

            // 1. check required data provided
            $validatedData = $request->validate([
                'user_uid' => 'required|uuid',
                'store_uid' => 'required|uuid',
            ]);

            // 2. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $user_uid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => $failMsg], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => $failMsg], 401);
            }

            // 3. insert data
            if (FavoriteModel::where('user_uid', $user_uid)->where('store_uid', $store_uid)->doesntExist()) {
                $favoriteModel = new FavoriteModel();
                $data = $favoriteModel->favorite(
                    $user_uid,
                    $store_uid
                );
                return response()->json(['data' => $data, 'msg' => $successMsg], 200);
            } else {
                return response()->json(['data' => null, 'msg' => '該收藏項目已存在'], 201);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);

        }
    }

    public function remove_favorite(Request $request)
    {
        // $id = $request->input('user_uid');
        $failMsg = '移除收藏清單失敗';
        $successMsg = '移除收藏清單成功';
        try {

            $requestBody = json_decode($request->getContent(), false);
            $user_uid = $requestBody->user_uid ?? null;
            $store_uid = $requestBody->store_uid ?? null;

            // 1. check required data provided
            $validatedData = $request->validate([
                'user_uid' => 'required|uuid',
                'store_uid' => 'required|uuid',
            ]);

            // 2. check header auth
            if ($request->hasHeader('Authorization')) {
                // 如果 Authorization header 存在
                $token = $request->header('Authorization');
                $isValid = (new JWTService())->verify($token, $user_uid);
                if (!$isValid) {
                    return response()->json(['error' => 'invalid token', 'msg' => $failMsg], 401);
                }
            } else {
                // 如果 Authorization header 不存在
                return response()->json(['error' => 'missing Authorization', 'msg' => $failMsg], 401);
            }

            // 3. insert data
            if (FavoriteModel::where('user_uid', $user_uid)->where('store_uid', $store_uid)->exists()) {
                $favoriteModel = new FavoriteModel();
                $data = $favoriteModel->unfavorite(
                    $user_uid,
                    $store_uid
                );
                return response()->json(['data' => $data, 'msg' => $successMsg], 200);
            } else {
                return response()->json(['data' => null, 'msg' => '該收藏項目不存在'], 201);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);

        }
    }

    // token
    // public function get_favorite_stores(Request $request, $id)
    // {
    //     // $id = $request->input('user_uid');
    //     $failMsg = '取得收藏清單失敗';
    //     $successMsg = '取得收藏清單成功';
    //     try {
    //         $userUid = $id;
    //         // 1. check required data provided
    //         if (empty(trim($userUid))) {
    //             return response()->json(['error' => 'user_id is required', 'msg' => $failMsg], 400);
    //         }

    //         // 2. check header auth
    //         if ($request->hasHeader('Authorization')) {
    //             // 如果 Authorization header 存在
    //             $token = $request->header('Authorization');
    //             $isValid = (new JWTService())->verify($token, $userUuid);
    //             if (!$isValid) {
    //                 return response()->json(['error' => 'invalid token', 'msg' => $failMsg], 401);
    //             }
    //         } else {
    //             // 如果 Authorization header 不存在
    //             return response()->json(['error' => 'missing Authorization', 'msg' => $failMsg], 401);
    //         }

    //         // 3. insert data
    //         $favoriteModel = new FavoriteModel();
    //         $updateUser = $userModel->update_user(
    //             $userUuid,
    //             $name
    //         );
    //         return response()->json(['data' => $updateUser, 'msg' => $successMsg], 200);
    //     } catch (\Exception $e) {

    //     }
    // }
}
