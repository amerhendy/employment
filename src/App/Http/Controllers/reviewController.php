<?php
namespace Amerhendy\Employment\App\Http\Controllers;
use Amerhendy\Employment\App\Http\Controllers\api\PeopleController;
use Amerhendy\Employment\App\Models\Employment_People;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use \Milon\Barcode\DNS2D;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use amer\mega\MegaStorageAdapter;
use League\Flysystem\Filesystem;


use \Amerhendy\Employment\App\Http\Controllers\CompleteController;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Employment\App\Http\Controllers\api\checkRequests;
use \Amerhendy\Employment\App\Http\Controllers\api\printTrait;
use \Amerhendy\Employment\App\Http\Controllers\api\applyTrait;
use stdClass;

class reviewController extends AmerController
{
    use checkRequests,printTrait,applyTrait,api\peopleTrait;
    public static $annonce,$job,$request,$error,$nid,$peopleDB,$htmlPerson,$json;
    public static function setErrorClass(){//29310021499811
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function review(Request $request,$reqtype=null){
        $json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json($request);
        self::$json=$json;
        self::$request=$request;
        $req=self::ReviewReq($json);
        if($req !== true){
            if($json){
                return $req;
            }else{
                $msgs=$req->getOriginalContent()['message']->message-> all();
            $msgs=implode('<br>',$msgs);
            return view('errors.layout',['error_number'=>$req->status(),'error_message'=>$msgs]);
            }
        }
        /**
         * محتاجين البيانات القديمة وفيها بيانات الوظيفة
         * محتاجين البيانات الجديدة وفيها بيانات الوظيفة
         */
        
        //new user data
        $data=new stdClass;
        $data->newData=self::sort_request_to_review();
        
        if($data->newData === 0){
            return '';
        }
        //old user data
        if(self::$request->has('uid')){
            if(self::EmploymentPeopleUsingID([self::$request->input('uid')]) == false){return __LINE__.__FILE__;}
            self::__toHtml();
            if(gettype($data) == 'object'){
                $data->OldData=self::$htmlPerson[0];
            }else{
                $data->OldData=self::$htmlPerson[0];
            }
        }
        $data->QR=self::create_review_qrcode($data,$request);
        $request['page']=$request['actiontype'];
        self::setBasicJobInfo();
        
        if(self::$request->has('id') && self::$request->has('test')){
            $data->headerTitle=trans('JOBLANG::apply.pageHeaderTitle.successApply');
        }else{
            $data->headerTitle=trans('JOBLANG::apply.pageHeaderTitle.preview_data');
            $data->headerTitleNote=trans('JOBLANG::apply.pageHeaderTitle.preview_dataNote');
        }
        //بيانات الوظيفة الجديدة
        $data->job=self::$job;
        return \AmerHelper::responsedata($data,1,1,'');
        $pdf=self::applyReviewPrint($data);
        if($json){
            return \AmerHelper::responsedata($pdf,1,1,'');
        }
        return view ('Employment::apply_review',['data'=>$pdf,'page_title'=>$data->headerTitle]);
    }
    /*
    used in api/apply trait
     */



}
