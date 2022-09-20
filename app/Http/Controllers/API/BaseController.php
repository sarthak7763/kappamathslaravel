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
    public function sendResponse($result, $message)
    {
    	$response = [
            'status_code' => 200,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 401)
    {
    	$response = [
            'status_code' => 401,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages['error'];
        }


        return response()->json($response, $code);
    }

    public function sendExceptionError($error, $errorMessages = [], $code = 500)
    {
        $response = [
            'status_code' => 500,
            'message' => $error,
        ];


        if(!empty($errorMessages)){
            $response['data'] = $errorMessages['error'];
        }


        return response()->json($response, $code);
    }

    public function sendValidationError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status_code' => 404,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}