<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $id = $request->get('id');
        if ($request->has('product')) {
            if (isset($id)) {
                Cache::tags('product_'.$id)->flush();
                return response()->json([
                    'message' => 'Cached data of product '.$id.' successfully cleared',
                ]);
            } else {
                Cache::tags('product')->flush();
                return response()->json([
                    'message' => 'Product cache successfully cleared',
                ]);
            }
        } else {
            if ($request->has('order')) {
                if (isset($id)) {
                    Cache::tags('order_'.$id)->flush();
                    return response()->json([
                        'message' => "Cached data of order $id successfully cleared",
                    ]);
                } else {
                    Cache::tags('order')->flush();
                    return response()->json([
                        'message' => 'Order cache successfully cleared',
                    ]);
                }
            } else {
                if ($request->has('orderproduct')) {
                    if (isset($id)) {
                        Cache::tags('orderproduct_'.$id)->flush();
                        return response()->json([
                            'message' => "Cached data of orderproduct $id successfully cleared",
                        ]);
                    } else {
                        Cache::tags('orderproduct')->flush();
                        return response()->json([
                            'message' => 'Orderproduct cache successfully cleared',
                        ]);
                    }
                } else {
                    if ($request->has('user')) {
                        if (isset($id)) {
                            Cache::tags('user_'.$id)->flush();
                            return response()->json([
                                'message' => "Cached data of user $id successfully cleared",
                            ]);
                        } else {
                            Cache::tags('user')->flush();
                            return response()->json([
                                'message' => 'User Cache successfully cleared',
                            ]);
                        }

                    } else {
                        if ($request->has('transaction')) {
                            if (isset($id)) {
                                Cache::tags('transaction_'.$id)->flush();
                                return response()->json([
                                    'message' => "Cached data of transaction $id successfully cleared",
                                ]);
                            } else {
                                Cache::tags('transaction')->flush();
                                return response()->json([
                                    'message' => 'Transaction cache successfully cleared',
                                ]);
                            }
                        } else {
                            if ($request->has('content')) {
                                if (isset($id)) {
                                    Cache::tags('content_'.$id)->flush();
                                    return response()->json([
                                        'message' => "Cached data of content $id successfully cleared",
                                    ]);
                                } else {
                                    Cache::tags('content')->flush();
                                    return response()->json([
                                        'message' => 'Content cache successfully cleared',
                                    ]);
                                }
                            } else {
                                if ($request->has('set')) {
                                    if (isset($id)) {
                                        Cache::tags('set_'.$id)->flush();
                                        return response()->json([
                                            'message' => "Cached data of set $id successfully cleared",
                                        ]);
                                    } else {
                                        Cache::tags('set')->flush();
                                        return response()->json([
                                            'message' => 'Set cache successfully cleared',
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        Artisan::call('cache:clear');
        return response()->json([
            'message' => 'Total Cache successfully cleared',
        ]);
    }
}
