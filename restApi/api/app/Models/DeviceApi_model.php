<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpOption\None;

class DeviceApi_model extends Model
{
    // FireBase Data Receiving Function
    public function DataEntryModel($crfDB, $studyID, $user, $timestamp, $refresheddata)
    {
        $file = fopen("logs/" . $crfDB . "_logs.txt", "a");                     // For Logging Errors Incounterd By the Api..
        $IdExistStat = false;                                                   // For Data Status if it's Being Inserted or Not..

        // Custom Predefined Variables For Data Information
        $newdataa['syncdate']   = date('Y-m-d');                                // Server Date
        $newdataa['synctime']   = date('H:i:s');                                // Server Time
        $newdataa['user']       = $user;                                        // from Data
        $newdataa['timestamp']  = date('Y-m-d H:i:s', $timestamp->_seconds);    // from Data
        $newdataa['studyID']    = $studyID;

        try {
            $db = DB::table($crfDB)->insert($newdataa);
        } catch (\Exception $e) {
            fwrite($file, response()->json($e->getMessage()) . "\n\n");         // Logging Error in file
            $IdExistStat = true;                                                // Changing Id Status to Being Updated 
        }

        // Variables to be Synced Count
        $totalObjects = count((array)$refresheddata);
        $succObjects = 0;

        foreach ($refresheddata as $key => $value) {
            // After Id Insertion, Updating Data in Chunks
            try {
                if (!DB::table($crfDB)->where('studyID', $studyID)->update([$key => $value])) {
                    fwrite($file, date("F j, Y, g:i a") . " | " . $studyID . " | Coloumn:" . $key . " | Data:" . $value . PHP_EOL);
                } else {
                    ++$succObjects;
                }
            } catch (\Exception $e) {
                fwrite($file, $e->getMessage() . "\n\n");       // Inserting in Logs
                $id_status = ($IdExistStat == false) ? 'Inserted' : 'Updated';    // Checking Id Status
                fwrite($file, date("F j, Y, g:i a") . " | " . $id_status . " | " . $studyID . " | Coloumn:" . $key . " | Data:" . $value . PHP_EOL);
            }
        }

        ($totalObjects != $succObjects) ? $msg['error'] = 'Check Error Logs': false ;
        $msg['id-status'] = ($IdExistStat == false) ? 'Inserted' : 'Updated';
        $msg['status'] = "Success, " . $succObjects . " of " . $totalObjects . " object(s) synced...";
        $msg['code'] = 200;

        fclose($file);
        return $msg;
    }

    public function RawDataToObject($crfDB = "default", $data_objects)
    {
        // Parsing Data into Objects
        $refresheddata = null;
        if ($data_objects) {
            foreach ($data_objects as $key => $value) {
                // if Data is Json Array
                if (is_array($value)) {
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
