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
use amer\mega\MegaStorageAdapter;
use League\Flysystem\Filesystem;
use \Amerhendy\Employment\App\Models\Employment_People;
use Amerhendy\Employment\App\Models\Employment_PeopleNewStage;
use Amerhendy\Employment\App\Models\Employment_PeopleNewData;
use Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use Amerhendy\Employment\App\Models\Employment_Jobs;
use Amerhendy\Employment\App\Models\Employment_Stages;
use Amerhendy\Employment\App\Models\Employment_StaticPages;
use Amerhendy\Employment\App\Models\Employment_DinamicPages;
use Amerhendy\Employment\App\Models\Employment_Status;
use Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Amer\App\Models\Governorates;
use \Amerhendy\Amer\App\Models\Cities;
use \Amerhendy\Employment\App\Http\Controllers\CompleteController;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class reviewController extends AmerController
{
    private static $annonce,$job;
    public static function review(Request $request,$reqtype=null){
        $errors=[];
        $data=[];
        if(!isset($request['_token']))return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        if(!isset($request['_method']))return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        if(!isset($request['annonce_id']))return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        if(!isset($request['job_id'])){return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);}
        if(!isset($request['annonce_id'])){return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);}
        $annonce=Employment_StartAnnonces::where('Slug',$request->input('annonce_id'))->first();
        if(!$annonce)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        self::$annonce=$annonce;
        $annonce_id=$annonce->id;
        $job=Employment_Jobs::where('Slug',$request['job_id'])->where('Annonce_id',$annonce_id)->first();
        if(!$job)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        self::$job=$job;
        $job_id=$job->id;
        //unset($request['_method']);unset($request['_token']);
        $data['user']=self::sort_request_to_review($request->all(),$annonce,$job);
        if($data['user'] == 0){return '';}
        $data['QR']=self::create_review_qrcode($data,$request);
               //////////////get annonce info//////////////////////
               $apiclass=api\AnnoncesController::class;
               $apiclass=new $apiclass();
               $annonce_job_info=$apiclass->getjob_by_job_slug($annonce->Slug,$job->Slug);
               $annonce_job_info=json_decode($annonce_job_info);
               $annonce_job_info=$annonce_job_info->data;
               $data['annonce_job']=(array) $annonce_job_info;
               $data['annonce_job']['Mosama_JobNames']=(array)$data['annonce_job']['Mosama_JobNames'];
               $data['annonce_job']['Employment_StartAnnonces']=(array) $data['annonce_job']['Employment_StartAnnonces'];
               foreach($data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'] as $a=>$b){
                   if($b[1] == 0){
                       $data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'][$a]=trans("EMPLANG::Mosama_Experiences.year0");
                   }else{
                       if($b[0] == '1'){
                           $data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'][$a][0]=trans("EMPLANG::Mosama_Experiences.enum_1");
                       }elseif($b[0] == '0'){
                           $data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'][$a][0]=trans("EMPLANG::Mosama_Experiences.enum_0");
                       }
                       //dd($data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'][$a]);
                       $translate=trans("EMPLANG::Mosama_Experiences.translate");
                       $data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'][$a] =(string) \Str::of($translate)->replaceArray('?', $data['annonce_job']['Mosama_JobNames']['Mosama_Experiences'][$a]);
                   }
               }
               $data['Employment_StartAnnonces']=$data['annonce_job']['Employment_StartAnnonces'];
               if(isset($request['id']) && isset($request['test'])){
                $data['headerTitle']=trans('JOBLANG::apply.pageHeaderTitle.successApply');
            }else{
                $data['headerTitle']=trans('JOBLANG::apply.pageHeaderTitle.preview_data');
                $data['headerTitleNote']=trans('JOBLANG::apply.pageHeaderTitle.preview_dataNote');
            }
               unset($data['annonce_job']['Employment_StartAnnonces']);
        return view ('Employment::apply_review',['data'=>$data,'request'=>$data,'annonce_slug'=>$request['annonce_id'],'job_slug'=>$request['job_id'],'page_title'=>trans('JOBLANG::apply.pagetitle.review')]);
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
    
    public static function sort_request_to_review($request,$annonce,$job){
        $data=[];
        $data['apply_date']=$request['apply_date'];
        if($request['actiontype'] == 'apply'){$request['actiontype']=__('JOBLANG::apply.apply_buttom_apply');}else{$request['actiontype']=__('JOBLANG::apply.Complete_buttom_apply');}
        $data['actiontype']=$request['actiontype'];
        $data['fullname']=$request['Fname'].' '.$request['Sname'].' '.$request['Tname'].' '.$request['Lname'];
        if(isset($request['uid'])){
            $peo=Employment_People::where('id',$request['uid'])->where('Annonce_id',$annonce->id)->where('Job_id',$job->id)->first();
            if(!$peo){return 0;}
            $data['NID']=$peo->NID;
            $data['BirthDate']=$peo->BirthDate;
            if($peo->Sex == '1'){$peo->Sex=trans('JOBLANG::Employment_People.Sex.Male');}else{$peo->Sex=trans('JOBLANG::Employment_People.Sex.Female');}
            $data['Sex']=$peo->Sex;
            $data['age']=$peo->AgeYears.'/'.$peo->AgeMonths.'/'.$peo->AgeDays;
            
        }else{
            $data['NID']=$request['NID'];
            $data['BirthDate']=$request['BirthDate'];
            if($request['Sex'] == '1'){$request['Sex']=trans('JOBLANG::Employment_People.Sex.Male');}else{$request['Sex']=trans('JOBLANG::Employment_People.Sex.Female');}
            $data['Sex']=$request['Sex'];
            $data['age']=$request['AgeYears'].'/'.$request['AgeMonths'].'/'.$request['AgeDays'];
        }
        
        $BornGov=Governorates::where('id',$request['BornGov'])->first();
        $request['BornGov']=$BornGov->Name;
        $BornCity=Cities::where('id',$request['BornCity'])->first();
        $request['BornCity']=$BornCity->Name;
        $LiveGov=Governorates::where('id',$request['LiveGov'])->first();
        $request['LiveGov']=$LiveGov->Name;
        $LiveCity=Cities::where('id',$request['LiveCity'])->first();
        $request['LiveCity']=$LiveCity->Name;
        $data['birth_blace']=$request['BornGov'].' - '.$request['BornGov'];
        $data['live_place']=$request['LiveGov'].' - '.$request['LiveCity'].' - '.$request['LiveAddress'];
        $data['ConnectLandline']=$request['ConnectLandline'];
        $data['ConnectMobile']=$request['ConnectMobile'];
        $data['ConnectEmail']=$request['ConnectEmail'];
        $Employment_Health=Employment_Health::withTrashed()->where('id',$request['Health_id'])->first();
        $data['Health_id']=$Employment_Health->Text;
        $Employment_MaritalStatus=Employment_MaritalStatus::withTrashed()->where('id',$request['MaritalStatus_id'])->first();
        $data['MaritalStatus_id']=$Employment_MaritalStatus->Text;
        $Employment_Army=Employment_Army::withTrashed()->where('id',$request['Arm_id'])->first();
        $data['Employment_Army']=$Employment_Army->Text;
        $Employment_Ama=Employment_Ama::withTrashed()->where('id',$request['Ama_id'])->first();
        $data['Employment_Ama']=$Employment_Ama->Text;
        $Mosama_Educations=Mosama_Educations::withTrashed()->where('id',$request['Education_id'])->first();
        $data['Education_id']=$Mosama_Educations->text;
        $data['EducationYear']=$request['EducationYear'];
        $Employment_Drivers=Employment_Drivers::withTrashed()->where('id',$request['DriverDegree'])->first();
        if($Employment_Drivers){$data['DriverDegree']=$Employment_Drivers->Text;$data['DriverStart']=$request['DriverStart'];$data['DriverEnd']=$request['DriverEnd'];}
        if(\Str::isJson($request['Khebra'])){
            $request['Khebra']=json_decode($request['Khebra'],true);
            if(is_array($request['Khebra'])){
                $data['Khebra']=$request['Khebra'][1];
            }else{
                $data['Khebra']=$request['Khebra'];
            }
            
        }else{
            $data['Khebra']=$request['Khebra'];
        }
        
        $data['Tamin']=$request['Tamin'];
        if(!empty($request['uploades'])){
            if(gettype($request['uploades']) == 'string'){
                $data['uploades']=$request['uploades'];
            }else{
                $data['uploades']=$request['uploades']->getClientOriginalName();
            }
        }else{
            $data['uploades']=null;
        }
        
        //$reqtype
        return $data;
    }
  
}