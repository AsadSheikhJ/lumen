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
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
    }

    public function index()
    {
        return response()->json('Have a great day!');
    }

    // FireBase json Insertion Function
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

    // For Finding Table Primary Key and Checking if it matches
    private function PrimaryKeyCheck( $table_name, $primaryKey = null){
        try{
            // Getting Primary Key for the table.
            $primaryKeylocal = DB::select('SHOW KEYS FROM `'."$table_name".'` WHERE Key_name = "PRIMARY"');
            $primaryKeylocal = $primaryKeylocal[0]->Column_name;
            
            //Checks if provided Key matches
            return response()->json(( $primaryKey != $primaryKeylocal && !empty($primaryKey) ) ? 
            'provided key doesn\'t match with actual key...' : $primaryKeylocal );

        } catch (\Exception $e){
            return response()->json('Unable to find id from table to insert with...');
        }
    }

    // Simple json Insertion Function
    public function DataEntry_v2(Request $request, $route)
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

        // // Test if the provided Table Exist in DataBase.
        // if (!Schema::hasTable($crfDB)) {
        //     fwrite($file, response()->json($data) . "\n\n");
        //     fclose($file);

        //     return response()->json('the specified table doesn\'t exist..  
        //         -- given table: ' . $crfDB);
        // }

        // Getting the primary key from table for data insertion..
        // $primaryKey = $this->PrimaryKeyCheck($crfDB, request()->segment(4));
        return $this->PrimaryKeyCheck($crfDB, request()->segment(4));

        // try{
        //     // Objectifing the Array to Insert in Table
        //     $objectifiedData = $DeviceApi_model->RawDataToObject($crfDB, $data);
        //     unset($objectifiedData[$primaryKey]);
        // }catch(\Exception $e){
        //     $msg['error']   = $e->getMessage(); 
        //     $msg['status']  = "Failed, Empty dataset...";
        //     $msg['code']    = 409;
        //     return ($msg);
        // }

        // // Inserting the objectified data
        // if ($objectifiedData) {
        //     try{
        //         // Function (Table Name(Required), DataID(Required), User Name, Data Timestamp, Objectified Data(Required), Primary Key(Required) )
        //         $msg = $DeviceApi_model->DataEntryModel($crfDB, $data->$primaryKey, "Unknown" , null , $objectifiedData, $primaryKey);
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
        // return ($msg);
    }
}
