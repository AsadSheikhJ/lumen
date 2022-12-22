<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class DataApiController extends BaseController
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		header('Content-Type: application/json');
		header('Access-Control-Allow-Origin: *');
    }

    public function index(){
        return response()->json('Have a great day!');
    }

    public function DataEntry(Request $request, $route) {
        // here $route will contain this value "whatever/comes/here/1"
        // if you want to know what method was posted, use $request->method()
        // echo 'Hello BRO !'.$request->method();

        
		$json = file_get_contents('php://input');
		$data = json_decode($json);

		$crfDB =  request()->segment(3);

        echo 'Hello BRO !'.$crfDB;
		// $this->load->model('DeviceApi_model');
		
		// $objectifiedData = $this->DeviceApi_model->RawDataToObject($crfDB ,$data);

		// //var_dump($objectifiedData);
		// if($objectifiedData) {
		// 	$msg = $this->DeviceApi_model->DataEntryModel($crfDB, $data->_id, 'none', 'none', $objectifiedData);
		// } else {
		// 	$msg['status'] = "Failed, Empty dataset... ".var_dump($data)."";
        // 	$msg['code'] = 409;
		// }
		
		// http_response_code($msg['code']);
		// echo json_encode($msg);
        
    }
}
