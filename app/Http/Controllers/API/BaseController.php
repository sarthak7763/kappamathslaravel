<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendResponse($result, $message,$subscription=2)
    {
        if(isset($subscription) && $subscription!=2)
        {
            $response = [
                'status_code' => 200,
                'data'    => $result,
                'message' => $message,
                'subscription'=>$subscription,
            ];
        }
        else{
            $response = [
                'status_code' => 200,
                'data'    => $result,
                'message' => $message,
            ];
        }
    	


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendError($error, $errorMessages = [], $code = 400)
    {
    	$response = [
            'status_code' => 400,
            'message' => $errorMessages['error'],
        ];

        return response()->json($response, $code);
    }

    public static function sendExceptionError($error, $errorMessages = [], $code = 500)
    {
        $response = [
            'status_code' => 500,
            'message' => $errorMessages['error'],
        ];

        return response()->json($response, $code);
    }

    public static function sendValidationError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'status_code' => 400,
            'message' => $errorMessages['error'],
        ];

        return response()->json($response, $code);
    }

    public static function sendUnauthorisedError($error, $errorMessages = [], $code = 401)
    {
        $response = [
            'status_code' => 401,
            'message' => $errorMessages['error'],
        ];

        return response()->json($response, $code);
    }

    public static function sendForbiddenError($error, $errorMessages = [], $code = 403)
    {
        $response = [
            'status_code' => 403,
            'message' => $errorMessages['error'],
        ];

        return response()->json($response, $code);
    }


}