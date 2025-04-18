<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\ProductModel;
use App\Services\JWTService;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @unauthenticated
     */
    public function index()
    {
        $failMsg = '查詢失敗';
        $successMsg = '查詢成功';

        try {
            $products = ProductModel::all();
            $response = ProductResource::collection($products);
            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * @unauthenticated
     */
    public function get_by_store(Request $request, $store_id)
    {
        $failMsg = '查詢失敗';
        $successMsg = '查詢成功';

        try {
            $products = (new ProductModel)->get_by_store_id($store_id);
            $response = ProductResource::collection($products);
            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    // auth-token
    public function add_product(Request $request)
    {
        try {
            $failMsg = '新增商品失敗';
            $successMsg = '新增商品成功';

            // validation
            $request->validate([
                'user_uid' => 'required|uuid',
                'store_uid' => 'required|uuid',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'spec' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:255',
                'price' => 'required|decimal:0,2|min:0',
                'special_price' => 'nullable|decimal:0,2|min:0',
                'special_price_start' => 'nullable|date_format:Y-m-d\TH:i:sP',
                'special_price_end' => 'nullable|date_format:Y-m-d\TH:i:sP',
                'stock' => 'nullable|integer|min:0',
                'image_url' => 'nullable|url',
                'link' => 'nullable|url'
            ]);

            $requestBody = json_decode($request->getContent(), false);

            $userUuid = $requestBody->user_uid;

            $store_uid = $requestBody->store_uid ?? null;
            $name = $requestBody->name ?? null;
            $description = $requestBody->description ?? null;
            $spec = $requestBody->spec ?? null;
            $note = $requestBody->note ?? null;
            $price = $requestBody->price ?? null;
            $special_price = $requestBody->special_price ?? null;
            $special_price_start = $requestBody->special_price_start ?? null;
            $special_price_end = $requestBody->special_price_end ?? null;
            $stock = $requestBody->stock ?? 0;
            $image_url = $requestBody->image_url ?? null;
            $link = $requestBody->link ?? null;

            // 1. check required data provided
            if (empty($userUuid) || empty($store_uid) || empty($name) || empty($price)) {
                return response()->json(['error' => 'user_uid, store_uid, name and price are required', 'msg' => $failMsg], 400);
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
            $productModel = new ProductModel();
            $product = $productModel->add_product(
                $store_uid,
                $name,
                $description,
                $spec,
                $note,
                $price,
                $special_price,
                $special_price_start,
                $special_price_end,
                $stock,
                $image_url,
                $link
            );

            $response = new ProductResource($product);
            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    // auth-token
    public function delete_product(Request $request)
    {
        try {
            $failMsg = '刪除商品失敗';
            $successMsg = '刪除商品成功';

            // validation
            $request->validate([
                'user_uid' => 'required|uuid',
                'store_uid' => 'required|uuid',
                'product_uid' => 'required|uuid'
            ]);

            $requestBody = json_decode($request->getContent(), false);

            $userUuid = $requestBody->user_uid;

            $store_uid = $requestBody->store_uid ?? null;
            $product_uid = $requestBody->product_uid ?? null;

            // 1. check required data provided
            if (empty($store_uid) || empty($product_uid)) {
                return response()->json(['error' => 'store_uid, product_uid are required', 'msg' => $failMsg], 400);
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
            $productModel = new ProductModel();
            $productModel->delete_product(
                $store_uid,
                $product_uid
            );
            return response()->json(['data' => null, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    // auth-token
    public function update_product_info(Request $request)
    {
        try {
            $failMsg = '更新商品資訊失敗';
            $successMsg = '更新商品資訊成功';

            // validation
            $request->validate([
                'user_uid' => 'required|uuid',
                'store_uid' => 'required|uuid',
                'name' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
                'spec' => 'nullable|string|max:255',
                'note' => 'nullable|string|max:255',
                'price' => 'nullable|decimal:0,2|min:0',
                'special_price' => 'nullable|decimal:0,2|min:0',
                'special_price_start' => 'nullable|date_format:Y-m-d\TH:i:sP',
                'special_price_end' => 'nullable|date_format:Y-m-d\TH:i:sP',
                'stock' => 'nullable|integer|min:0',
                'image_url' => 'nullable|url',
                'link' => 'nullable|url'
            ]);

            $requestBody = json_decode($request->getContent(), false);

            $userUuid = $requestBody->user_uid;

            $store_uid = $requestBody->store_uid ?? null;
            $product_uid = $requestBody->product_uid ?? null;
            $name = $requestBody->name ?? null;
            $description = $requestBody->description ?? null;
            $spec = $requestBody->spec ?? null;
            $note = $requestBody->note ?? null;
            $price = $requestBody->price ?? null;
            $special_price = $requestBody->special_price ?? null;
            $special_price_start = $requestBody->special_price_start ?? null;
            $special_price_end = $requestBody->special_price_end ?? null;
            $stock = $requestBody->stock ?? null;
            $image_url = $requestBody->image_url ?? null;
            $link = $requestBody->link ?? null;

            // 1. check required data provided
            if (empty($store_uid) || empty($product_uid)) {
                return response()->json(['error' => 'store_uid, product_uid, name and price are required', 'msg' => $failMsg], 400);
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
            $productModel = new ProductModel();
            $updateProduct = $productModel->update_product(
                $store_uid,
                $product_uid,
                $name,
                $description,
                $spec,
                $note,
                $price,
                $special_price,
                $special_price_start,
                $special_price_end,
                $stock,
                $image_url,
                $link
            );

            $response = new ProductResource($updateProduct);
            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }
    // auth-token
    public function enable_product(Request $request)
    {
        try {
            $failMsg = '更新商品狀態失敗';
            $successMsg = '更新商品狀態成功';

            // validation
            $request->validate([
                'user_uid' => 'required|uuid',
                'store_uid' => 'required|uuid',
                'product_uid' => 'required|uuid',
                'is_enable' => 'required|boolean'
            ]);


            $requestBody = json_decode($request->getContent(), false);

            $userUuid = $requestBody->user_uid;

            $store_uid = $requestBody->store_uid ?? null;
            $product_uid = $requestBody->product_uid ?? null;
            $is_enable = $requestBody->is_enable ?? null;

            // 1. check required data provided
            if (empty($store_uid) || empty($product_uid) || ($is_enable === null)) {
                return response()->json(['error' => 'store_uid, product_uid and is_enable are required', 'msg' => $failMsg], 400);
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

            // 3. update data
            $productModel = new ProductModel();
            $updateProduct = $productModel->enable_product(
                $store_uid,
                $product_uid,
                $is_enable
            );
            $response = new ProductResource($updateProduct);
            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }
}
