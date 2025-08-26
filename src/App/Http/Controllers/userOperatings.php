<?php
namespace Amerhendy\Employment\App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use \Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use League\Flysystem\Filesystem;


use Amerhendy\Employment\App\Http\Controllers\api\PeopleController;
use Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Employment\App\Http\Controllers\CompleteController;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Employment\App\Http\Controllers\api\checkRequests;
use \Amerhendy\Employment\App\Http\Controllers\api\printTrait;
use \Amerhendy\Employment\App\Http\Controllers\api\applyTrait;
use stdClass;

class userOperatings extends AmerController
{
    use checkRequests,printTrait,applyTrait,api\peopleTrait;
    public static $request,$json,$function,$annonce,$job,$error,$nid,$returnData;
    public static $peopleDB,$htmlPerson;
    public static function setErrorClass(){//29310021499811
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function review(Request $request,$reqtype=null){
        $json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json($request);
        self::$json=$json;
        self::$request=$request;
        self::$function=__FUNCTION__;
        return self::storOrReview();
        
        self::setReturnData();
        return \AmerHelper::responsedata(self::$returnData,1,1,'');
        $pdf=self::applyReviewPrint($data);
        if($json){
            return \AmerHelper::responsedata($pdf,1,1,'');
        }
        return view ('Employment::apply_review',['data'=>$pdf,'page_title'=>$data->headerTitle]);
    }
    public static function addTodaTabase(Request $request){
        $json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json($request);
        self::$json=$json;
        self::$request=$request;
        self::$function=__FUNCTION__;
        return self::storOrReview();
    }
    public static function storOrReview(){
        /*
        $id=self::$request['uid'];
        $peopleDB=Employment_People::where('id', $id)->orderBy('id')->first();
        $stagescontroller=new \Amerhendy\Employment\App\Http\Controllers\api\PeopleStagesController($peopleDB);
        $stagescontroller->get('stageList');
        dd($stagescontroller);
        return;*/
        $req=self::checkRequest();
        if($req !== true){
            if(self::$json){
                return $req;
            }else{
                $msgs=$req->getOriginalContent()['message']->message-> all();
            $msgs=implode('<br>',$msgs);
            return view('errors.layout',['error_number'=>$req->status(),'error_message'=>$msgs]);
            }
        }
        if(self::$function !== 'review'){
            self::store();
        }
        self::setReturnData();
        self::setBasicJobInfo();
        return \AmerHelper::responsedata(self::$returnData,1,1,'');
    }
    public static function checkRequest(){
        if(self::$function === 'review') return self::ReviewReq(self::$json);
        if (self::$function === 'addTodaTabase') return self::ReviewReq(self::$json);
        //self::$request->input('actiontype');
    }
    public static function setReturnData(){
        //Title
        //SubTitle
        //new with trans
        //old with trans if exists
        
        //new Job Info
        //QrLink

        
        self::$returnData=new \stdClass;
        if(self::$function == 'review'){
            self::$returnData->headerTitle=self::$json ? 'employment.apply.pageHeaderTitle.preview_data' : trans('JOBLANG::apply.pageHeaderTitle.preview_data');
            self::$returnData->headerTitleNote=self::$json ? 'employment.apply.pageHeaderTitle.preview_dataNote' : trans('JOBLANG::apply.pageHeaderTitle.preview_dataNote');
        }else{
            self::$returnData->headerTitle=self::$json ? 'employment.apply.pageHeaderTitle.successApply': trans('JOBLANG::apply.pageHeaderTitle.successApply');
            self::$returnData->headerTitleNote=null;
        }
        self::$returnData->newData=self::sort_request_to_review();
        //get old Data
        if(self::$request->has('uid')){
            if(!self::$peopleDB){return __LINE__.__FILE__;}
            if(is_object(self::$peopleDB)){
                        self::$peopleDB=[self::$peopleDB];
                    }
            self::__toHtml();
            self::$returnData->OldData=self::$htmlPerson[0];
        }
        self::$returnData->QR=self::create_review_qrcode(self::$returnData,self::$request);
        self::setBasicJobInfo();
        //بيانات الوظيفة الجديدة
        self::$returnData->job=self::$job;

    }

    /*
    used in api/apply trait
     */



}
