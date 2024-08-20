<?php

namespace App\Http\Controllers;

use App\Enums\QueueMode;
use App\Http\Controllers\Controller;
use App\Http\Resources\QueueResource;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $successMsg = '建立成功';
        $failMsg = '建立失敗';

        try {
            $validData = $request->validate(
                [
                    'store_name' => 'required|string|max:60',
                    'start_time' => 'required|date_format:Y-m-d\TH:i:s\Z|after_or_equal:today',
                    'end_time' => 'required|date_format:Y-m-d\TH:i:s\Z|after_or_equal:today',
                    'note' => 'nullable|string|max:60',
                    'mode' => [Rule::enum(QueueMode::class)],
                    'store_uuid' => 'nullable|uuid'
                ]
            );
        
            $queue = (new Queue)->createQueue(
                $validData["store_name"] ?? null,
                $validData["note"] ?? null,
                $validData["start_time"] ?? null,
                $validData["end_time"] ?? null,
                $validData["mode"] ?? null,
                $validData["store_uuid"] ?? null
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Restart with exist queue.
     */
    public function restart(Request $request)
    {
        $successMsg = '建立成功';
        $failMsg = '建立失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid',
                    'start_time' => 'required|date_format:Y-m-d\TH:i:s\Z|after_or_equal:today',
                    'end_time' => 'required|date_format:Y-m-d\TH:i:s\Z|after_or_equal:today',
                    'note' => 'nullable|string|max:60',
                    'mode' => [Rule::enum(QueueMode::class)],
                    'update_qr_code' => 'nullable|boolean'
                ]
            );
        
            $queue = (new Queue)->restartQueue(
                $validData["uuid"] ?? null,
                $validData["note"] ?? null,
                $validData["start_time"] ?? null,
                $validData["end_time"] ?? null,
                $validData["mode"] ?? null,
                $validData["update_qr_code"] ?? null
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function nextUser(Request $request)
    {
        $successMsg = '操作成功';
        $failMsg = '操作失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid'
                ]
            );
        
            $queue = (new Queue)->nextCustomer(
                $validData["uuid"] ?? null
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function enqueue(Request $request)
    {
        $successMsg = '操作成功';
        $failMsg = '操作失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid'
                ]
            );
        
            $queue = (new Queue)->enqueue(
                $validData["uuid"] ?? null
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function pause(Request $request)
    {
        $successMsg = '操作成功';
        $failMsg = '操作失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid',
                    'is_pause' => 'required|boolean'
                ]
            );
        
            $queue = (new Queue)->pause(
                $validData["uuid"] ?? null,
                $validData["is_pause"] ?? null,
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function close(Request $request)
    {
        $successMsg = '操作成功';
        $failMsg = '操作失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid',
                    'is_close' => 'required|boolean'
                ]
            );
        
            $queue = (new Queue)->close(
                $validData["uuid"] ?? null,
                $validData["is_close"] ?? null,
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function updateInfo(Request $request)
    {
        $successMsg = '更新成功';
        $failMsg = '更新失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid',
                    'start_time' => 'nullable|date_format:Y-m-d\TH:i:s\Z|after_or_equal:today',
                    'end_time' => 'nullable|date_format:Y-m-d\TH:i:s\Z|after_or_equal:today',
                    'store_name' => 'nullable|string|max:60',
                    'note' => 'nullable|string|max:60',
                    'pause_message' => 'nullable|string|max:60',
                    'terminal_message' => 'nullable|string|max:60'
                ]
            );
        
            $queue = (new Queue)->updateInfo(
                $validData["uuid"] ?? null,
                $validData["store_name"] ?? null,
                $validData["note"] ?? null,
                $validData["start_time"] ?? null,
                $validData["end_time"] ?? null,
                $validData["pause_message"] ?? null,
                $validData["terminal_message"] ?? null
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function bindStore(Request $request)
    {
        $successMsg = '綁定成功';
        $failMsg = '綁定失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid',
                    'store_uuid' => 'required|uuid'
                ]
            );
        
            $queue = (new Queue)->bindStore(
                $validData["uuid"] ?? null,
                $validData["store_uuid"] ?? null,
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $successMsg = '刪除成功';
        $failMsg = '刪除失敗';

        try {
            $validData = $request->validate(
                [
                    'uuid' => 'required|uuid'
                ]
            );
        
            $queue = (new Queue)->remove(
                $validData["uuid"] ?? null
            );

            $response = new QueueResource($queue);

            return response()->json(['data' => $response, 'msg' => $successMsg], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'msg' => $failMsg], 500);
        }
    }
}
