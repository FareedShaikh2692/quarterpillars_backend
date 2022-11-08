<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CommonUtility extends Controller
{
    private $response= 200;
    private $msg   = '';
    private $error = null;
    private $data  = null;
    private $aadharToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MjM2NTkwNzcsIm5iZiI6MTYyMzY1OTA3NywianRpIjoiOWQ4YzczY2EtNWU0NC00NTU3LTljMTItNTcwOTdjNGFlZjliIiwiZXhwIjoxOTM5MDE5MDc3LCJpZGVudGl0eSI6ImRldi5tb2F6YW1odXNzYWluMTZAYWFkaGFhcmFwaS5pbyIsImZyZXNoIjpmYWxzZSwidHlwZSI6ImFjY2VzcyIsInVzZXJfY2xhaW1zIjp7InNjb3BlcyI6WyJyZWFkIl19fQ.7YqR49ovgkRV0yDw18ScURNpSTo7aqDlsSrh0KGf4WQ';

    public function get_response($error, $msg, $data, $response ){
        return response(['error'=>$error, 'response'=>$response, 'msg'=>$msg, 'data'=>$data], $response);
    }
    public function send_sms($mobile, $sms ){
        $ch = curl_init();
        $otp=$sms;
        $valMins="10";    
        $url="https://www.fast2sms.com/dev/bulkV2?authorization=Pp0sqr2SoAVBl8Oy3F157h4DLXcimguvneN9IZzCfYaKwdWJtRGDbZlpgqXEwsB1NPAYReUmfdaJ3h6u&route=dlt&sender_id=QTRPLR&message=147891&variables_values=".urlencode($otp."|".$valMins)."&flash=0&numbers="; 
        curl_setopt($ch, CURLOPT_URL, $url . $mobile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        curl_close($ch); 
        return $result;
    }
    public function aadhaar_otp($aadharNo){
        $response = Http::post("https://uat.dhansewa.com/Verification/AadhaarVerificationOTP", ["Client_refid"=>"testing1_aadhar",
                "aadhaarno"=>"$aadharNo",
                "cpid"=>"CP12321311",
                "token"=>"ANk67No8j7ksBxSTKCNRKKnVHEkuBIf0s+xb0gjkO4zq+vWu9KtvCLmhUUnmEWep"
            ]);
        return $response->body();
    }
    public function aadhaar_verification($otp, $Mahareferid){
        $response = Http::post("https://uat.dhansewa.com/Verification/SubmitAadhaarOTP", ["Client_refid"=>"testing1_aadhar",
                "otp"=>"$otp",
                "Mahareferid"=>$Mahareferid,
                "cpid"=>"CP12321311",
                "token"=>"ANk67No8j7ksBxSTKCNRKKnVHEkuBIf0s+xb0gjkO4zq+vWu9KtvCLmhUUnmEWep"
            ]);
        return $response->body();
    }
}
