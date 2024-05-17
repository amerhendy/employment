<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Support\Collection;
use Amerhendy\Amer\App\Models\Governorates;
use Amerhendy\Amer\App\Models\Cities;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Jobs as jobs;
use \Amerhendy\Employment\App\Models\Employment_Stages;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class ApplyController extends AmerController
{
    use \Amerhendy\Employment\App\Http\Controllers\api\applyTrait;
    use \Amerhendy\Employment\App\Http\Controllers\api\printTrait;
    use \Amerhendy\Employment\App\Http\Controllers\api\checkRequests;
    private static $validator,$annonce,$job,$request,$error,$nid;
    //return \AmerHelper::responsedata($result,1,1,'');
    public static function setErrorClass(){//29310021499811
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    
    
    public static function applycheck ($annid,$jobid,$process,Request $request){
        //check request
        self::$request=$request;
        $req=self::ReviewReq();
        if($req !== true){return ($req);}
        return self::store();
    }
    
    public static function sstore(Request $request,$validator){
            if($request->actiontype == 'apply'){
                $submit=self::apply_submit($request);
                if($submit[1] == 'bad'){
                    return redirect (redirect()->getUrlGenerator()->previous())->withErrors(['msg'=>1]);
                }else{
                    $data['QR']='<img src="data:image/png;base64,' . $d->getBarcodePNG(implode('TROJAN',$submit), 'QRCODE') . '" alt="barcode"   />';
                    return view ('layout.jobs.applyconfirm',['data'=>$data,'request'=>$data,'annonce_slug'=>$request['annonce_id'],'job_slug'=>$request['job_id'],'page_title'=>trans('trojan.data_entered')]);
                }
            }
            if($request['actiontype'] == 'complete'){
    
                $submit=self::complete_submit($request);
                if($submit[1] == 'bad'){
                    return redirect (redirect()->getUrlGenerator()->previous())->withErrors(['msg'=>1]);
                }else{
                    $data['QR']='<img src="data:image/png;base64,' . $d->getBarcodePNG(implode('CoTROJAN',$submit), 'QRCODE') . '" alt="barcode"   />';
                    return view ('layout.jobs.applyconfirm',['data'=>$data,'request'=>$data,'annonce_slug'=>$request['annonce_id'],'job_slug'=>$request['job_id'],'page_title'=>trans('trojan.data_entered')]);
                }
            }
    }
}
