<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\DriverManager;

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

    public function index()
    {
        return response()->json('Have a great day!');
    }

    public function DataEntry(Request $request, $route)
    {
        // here $route will contain this value "whatever/comes/here/1"
        // if you want to know what method was posted, use $request->method()

        $DeviceApi_model    =   new DeviceApi_model();                      // Model initializing
        $dbName             =   DB::connection()->getDatabaseName();        // Getting Table Name
        $json               =   file_get_contents('php://input');           // Receiving Data
        $data               =   json_decode($json);                         // Decoding Data from json
        $crfDB              =   request()->segment(3);                      // Table Name to insert Data.

        $file               =   fopen("logs/dump/" . $dbName . ".txt", "a");     // File to InsertnData if Needed

        // Test database connection connectivity.
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            fwrite($file, $e->getMessage()."\n". response()->json($data) . "\n\n");
            fclose($file);

            return response()->json("Could not connect to the database.  
                Please check your configuration. error:" . $e->getMessage());
        }

        // Test if the provided Table Exist in DataBase.
        if (!Schema::hasTable($crfDB)) {
            fwrite($file, response()->json($data) . "\n\n");
            fclose($file);

            return response()->json('the specified table doesn\'t exist..  
                -- given table: ' . $crfDB);
        }

        try{
            // Objectifing the Array to Insert in Table
            $objectifiedData = $DeviceApi_model->RawDataToObject($crfDB, $data->rawData->data);
        }catch(\Exception $e){
            $msg['error']   = $e->getMessage(); 
            $msg['status']  = "Failed, Empty dataset...";
            $msg['code']    = 409;
            return ($msg);
        }

        // Inserting the objectified data
        if ($objectifiedData) {
            try{
                $msg = $DeviceApi_model->DataEntryModel($crfDB, $data->studyID, $data->rawData->user, $data->rawData->date, $objectifiedData);
            } catch(\Exception $e){
                fwrite($file,  $e->getMessage()."\n".response()->json($data) ."\n\n");
                $msg['error']   = $e->getMessage();
                $msg['status']  = "Failed";
                $msg['code']    = 409;
            }
        } else {
            $msg['status'] = "Failed, Empty dataset...";
            $msg['code'] = 409;
        }

        // http_response_code($msg['code']);
        return ($msg);
    }

    public function DataEntry2(Request $request, $route)
    {
        // here $route will contain this value "whatever/comes/here/1"
        // if you want to know what method was posted, use $request->method()

        $DeviceApi_model    =   new DeviceApi_model();                      // Model initializing
        $dbName             =   DB::connection()->getDatabaseName();        // Getting Table Name
        $json               =   file_get_contents('php://input');           // Receiving Data
        $data               =   json_decode($json);                         // Decoding Data from json
        $crfDB              =   request()->segment(3);                      // Table Name to insert Data.

        $file               =   fopen("logs/dump/" . $dbName . ".txt", "a");     // File to InsertnData if Needed

        // Test database connection connectivity.
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            fwrite($file, $e->getMessage()."\n". response()->json($data) . "\n\n");
            fclose($file);

            return response()->json("Could not connect to the database.  
                Please check your configuration. error:" . $e->getMessage());
        }

        // Test if the provided Table Exist in DataBase.
        if (!Schema::hasTable($crfDB)) {
            fwrite($file, response()->json($data) . "\n\n");
            fclose($file);

            return response()->json('the specified table doesn\'t exist..  
                -- given table: ' . $crfDB);
        }
        
        // $columnTypes = [];
        // $columns = Schema::getColumnListing($crfDB);
        // foreach($columns as $column) {
        //     //  echo $column;
        //      echo Schema::getColumnType($crfDB, $column)."<br>";
        // }
        echo Schema::getKeyName($crfDB);
        // echo Schema::getColumnType($crfDB, 'studyID');
        //var_dump($columnTypes);

        // try{
        //     // Objectifing the Array to Insert in Table
        //     $objectifiedData = $DeviceApi_model->RawDataToObject($crfDB, $data);
        // }catch(\Exception $e){
        //     $msg['error']   = $e->getMessage(); 
        //     $msg['status']  = "Failed, Empty dataset...";
        //     $msg['code']    = 409;
        //     return ($msg);
        // }

        // // Inserting the objectified data
        // if ($objectifiedData) {
        //     try{
        //         $msg = $DeviceApi_model->DataEntryModel($crfDB, $data->studyID, $data->user, $data->date, $objectifiedData);
        //     } catch(\Exception $e){
        //         fwrite($file,  $e->getMessage()."\n".response()->json($data) ."\n\n");
        //         $msg['error']   = $e->getMessage();
        //         $msg['status']  = "Failed";
        //         $msg['code']    = 409;
        //     }
        // } else {
        //     $msg['status'] = "Failed, Empty dataset...";
        //     $msg['code'] = 409;
        // }

        // // http_response_code($msg['code']);
        // return json_encode($msg);
    }
}
