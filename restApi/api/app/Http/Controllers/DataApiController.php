<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Models\DeviceApi_model;
use Exception;

class DataApiController extends BaseController
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		// $this->isSecure();

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

        // Test database connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            die("Could not connect to the database.  Please check your configuration. error:" . $e );
        }
        
        if (!Schema::hasTable($crfDB))
        {
            return 'the specified table doesn\'t exist..  --- given table: '.$crfDB.'';
        }

        // echo 'Hello BRO !'.$crfDB;
		$json = file_get_contents('php://input');
        // echo '<pre>';
        // var_dump($json);
		$data = json_decode($json);
        // var_dump($data);

        $DeviceApi_model = new DeviceApi_model();
        

        // return $DeviceApi_model->RawDataToObject($crfDB , $data->rawData);
		
		$objectifiedData = $DeviceApi_model->RawDataToObject($crfDB ,$data->rawData->data);

		//var_dump($objectifiedData);
		if($objectifiedData) {
			$msg = $DeviceApi_model->DataEntryModel($crfDB, $data->studyID, $data->rawData->user, $data->rawData->date, $objectifiedData);
		} else {
			$msg['status'] = "Failed, Empty dataset...";
        	$msg['code'] = 409;
		}
		
		// http_response_code($msg['code']);
		echo json_encode($msg);
		
        
    }
}
