<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class DeviceApi_model extends Model 
{
    
    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->load->database();
    // }

    

    public function DataEntryModel($crfDB, $studyID, $user, $timestamp, $refresheddata) {

        $file = fopen("logs/".$crfDB."_logs.txt","a");
        
        //$data_plain['studyID'] = $this->input->get('studyID', TRUE);
        //$refresheddata['studyID'] = $this->input->get('studyID', TRUE);

        $newdataa['syncdate'] = date('Y-m-d');
        $newdataa['synctime'] = date('H:i:s');
        $newdataa['user'] = $user;
        // $newdataa['timestamp'] = date('Y-m-d H:i:s',$timestamp->_seconds);
        $newdataa['studyID'] = $studyID;
        
        try {
            // $db = app('db')->insert($crfDB, $newdataa);
            $db = DB::table($crfDB)->insert($newdataa);
        } catch(\Exception $e) {
           return $e;
        }
        
        $studyID =  $newdataa['studyID'];

        $totalObjects = count((array)$refresheddata);
        $succObjects = 0;
        
        //if($db) {
        foreach($refresheddata as $key => $value) {
            try {
                // if( DB::update("UPDATE `$crfDB` SET $key='$value' WHERE studyID='$studyID'")){
                if(! DB::table($crfDB)->where('studyID', $studyID)->update([$key => $value])){
                    fwrite($file,date("F j, Y, g:i a")." | ".$studyID." | ".DB::error()['message']." | Data:".$value.PHP_EOL);
                    // fwrite($file,date("F j, Y, g:i a")." | ".$studyID." | ".app('db')->error()['message']."| Data:".$value.PHP_EOL);
                } else {
                    ++$succObjects;
                }
            } catch(\Exception $e) {
                fwrite($file,$e->getMessage());
                $msg['status'] = $e;
                $msg['code'] = 409;
            }
        }
        $msg['status'] = "Success, ".$succObjects." of ".$totalObjects." object(s) synced...";
        $msg['code'] = 200;
        // } else {
        //     $msg['status'] = "Failed, Record already exists...";
        // 	$msg['code'] = 409;
        // }
        
        fclose($file);
        return $msg;
    }


    public function RawDataToObject($crfDB = "default", $data_objects) {
        
        $refresheddata = null;
        if($data_objects) {
            foreach($data_objects as $key => $value) {
                if(is_array($value)) {
                    //$refresheddata[$key] = implode(',',$value);
                    $refresheddata[$key] = json_encode($value);
                } else {
                    $refresheddata[$key] = $value;
                }
            }
        } else {
            return $refresheddata = null;
        }

        return $refresheddata;
    }



}
