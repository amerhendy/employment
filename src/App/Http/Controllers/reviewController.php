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
class reviewController extends AmerController
{
    use checkRequests,printTrait,applyTrait,api\peopleTrait;
    public static $annonce,$job,$request,$error,$nid,$peopleDB,$htmlPerson;
    public static function setErrorClass(){//29310021499811
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function review(Request $request,$reqtype=null){
        self::$request=$request;
        $req=self::ReviewReq();
        if($req !== true){
            $msgs=$req->getOriginalContent()['message']->message-> all();
            $msgs=implode('<br>',$msgs);
            return view('errors.layout',['error_number'=>$req->status(),'error_message'=>$msgs]);
            
        }
        $errors=[];
        $data=[];
        $annonce_id=self::$annonce->id;
        $job_id=self::$job->id;
        //unset($request['_method']);unset($request['_token']);
        $data['user']=self::sort_request_to_review();
        if($data['user'] == 0){return '';}
        if(self::$request->has('uid')){
            if(self::EmploymentPeopleUsingID([self::$request->input('uid')]) == false){return __LINE__.__FILE__;}
            self::__toHtml();
            $data['Employment_People']=self::$htmlPerson[0];
        }
        $data['QR']=self::create_review_qrcode($data,$request);
               //////////////get annonce info//////////////////////
               $apiclass=api\AnnoncesController::class;
               $apiclass=new $apiclass();
               $request['page']=$request['actiontype'];
               $request['view']='json';
               self::$job=self::getjobInfo();
               self::$job=self::$job->getOriginalContent()['data'];
               
               foreach(self::$job->Mosama_JobNames->Mosama_Experiences as $a=>$b){
                   if($b[1] == 0){
                       self::$job->Mosama_JobNames->Mosama_Experiences[$a]=trans("EMPLANG::Mosama_Experiences.year0");
                   }else{
                       if($b[0] == '1'){
                           self::$job->Mosama_JobNames->Mosama_Experiences[$a][0]=trans("EMPLANG::Mosama_Experiences.enum_1");
                       }elseif($b[0] == '0'){
                           self::$job->Mosama_JobNames->Mosama_Experiences[$a][0]=trans("EMPLANG::Mosama_Experiences.enum_0");
                       }
                       $translate=trans("EMPLANG::Mosama_Experiences.translate");
                       self::$job->Mosama_JobNames->Mosama_Experiences[$a] =(string) \Str::of($translate)->replaceArray('?', self::$job->Mosama_JobNames->Mosama_Experiences[$a]);
                   }
               }
               if(isset($request['id']) && isset($request['test'])){
                $data['headerTitle']=trans('JOBLANG::apply.pageHeaderTitle.successApply');
            }else{
                $data['headerTitle']=trans('JOBLANG::apply.pageHeaderTitle.preview_data');
                $data['headerTitleNote']=trans('JOBLANG::apply.pageHeaderTitle.preview_dataNote');
            }
            $data['job']['user']=self::$job;
            $pdf=self::applyReviewPrint($data);
        return view ('Employment::apply_review',['data'=>$pdf,'page_title'=>$data['headerTitle']]);
    }
    public static function create_review_qrcode($data,$req){
        $qr=[];
        if($req['actiontype'] == 'apply'){
            if(isset($req['id']) && isset($req['test'])){
                $qr['requestType']='apply-review';
                $qr['id']=$req['id'];
                $qr['test']=$req['test'];
            }else{
                ////set test
                $prep=$req->all();
                if(gettype($prep['uploades']) !== 'string'){
                    $prep['uploades']=$prep['uploades']->getClientOriginalName();
                }
                $testid=self::insertLogDB($prep);
                $qr['test']=$testid;
                $qr['requestType']='review';
            }
        }
        $qr['annonce']=self::$annonce->id;
        $qr['job']=self::$job->id;
        $qr['date']=$data['user']['apply_date'];
        $qr['NID']=$data['user']['NID'];
        $qr['actiontype']=$req['actiontype'];
        //$qr=\Arr::query($qr);
        $qr=json_encode($qr);
        $amerhelper=new \AmerHelper();
        return $amerhelper::tokenencrypt($qr);
    }
    public static function insertLogDB($request){
        $lsid=\DB::table('Employment_ApplyLog')->get()->max('id');
        $id=$lsid+1;
        $testid=\DB::table('Employment_ApplyLog')->insertGetId(['id'=>$id,'userData' => json_encode($request)]);
        return $testid;
    }
    /*
    used in api/apply trait
     */
    
    
  
}