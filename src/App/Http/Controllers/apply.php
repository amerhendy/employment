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
class apply extends AmerController
{
    use api\applyTrait;
    private static $annonce,$job;
    public function __invoke(Request $request)
    {
        //
    }
    public function index(){
        $data=$this->api_index();
        return view('Employment::home',['data'=>$data]);
    }
    public static function api_index()
    {
        $annonces=Employment_StartAnnonces::with('Employment_Qualifications')->with('Governorates')->where('Status','Publish')->get()->toArray();
        //dd($annonces);
        if(count($annonces) !== '0'){
            foreach($annonces as $k=>$v){
                $annonces[$k]['stage_id']=Employment_stages::where('id',$v['Stage_id'])->get(['id','Text','Page','Front'])->toArray();
                $annonces[$k]['jobs']=Employment_Jobs::with('Mosama_Educations')->with('Employment_IncludedFiles')->with('Employment_Instructions')->with('Employment_Qualifications')->with('Mosama_Groups')->where('Annonce_id',$v['id'])->where('Status','Publish')->get()->toArray();
            }
            //$data=json_encode(['result'=>'success','data'=>$annonces]);
            $data=['result'=>'success','data'=>$annonces];
        }else{
            $data=json_encode(['result'=>'empty']);
        }
        return $data;
    }
    public function getannonce_job_info($annid,$jobid){
        return view('Employment::showjobs',['annid'=>$annid,'jobid'=>$jobid]);
    }
    public function selectview ($ann_slug, $job_slug,$nid=null,$stage=null){
        
        if(!isset($_SERVER['HTTP_REFERER'])){
            return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);
        }
        $from = request()->getSchemeAndHttpHost();
        if(\Str::contains($_SERVER['HTTP_REFERER'],$from) == false){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        $an= Employment_StartAnnonces::where('Slug',$ann_slug)->where('Status','Publish')->first();
        if(!$an){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        $annid=$an->id;
        $ann_Stage=$an->Stage_id;
        $job=Employment_Jobs::where('Slug',$job_slug)->where('Status','Publish')->first();
        if(!$job){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        $jobid=$job->id;
        $data = [];
        if(request()->has('lastStage')){
            $stage=Employment_Stages::find(request()->lastStage);
        }else{
            $stage=Employment_Stages::find($ann_Stage);
        }
        $data['stage_name']=$stage->Text;
        $currentstage=$stage->Page;
        $Stagetype=\Str::substrCount($currentstage,'D:') ? "D:":"S:";
        $stage_id=(int)\Str::afterLast($currentstage,$Stagetype);
        if ($Stagetype === 'D:') {
            $page=Employment_DinamicPages::find($stage_id);
            if(!$page){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
            $Control=$page->Control;
            $Function=$page->Function;
            $allcontrollers=\AmerHelper::findController();
            $Controler=\Arr::where($allcontrollers,function($v,$k)use($Control){
                return \Str::endsWith($v,$Control);
            });
            if(!count($Controler)){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
            $Controler=($Controler[array_keys($Controler)[0]]);
            $Controler=new $Controler();
            $reflectionClass = new \ReflectionClass($Controler);
            if(!$reflectionClass->hasMethod($Function)){
                return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__.'<br> Method: '.$Function]);
            }
            $reflectionMethod = new \ReflectionMethod($Controler, $Function);
                $params = $reflectionMethod->getParameters();
                $pars=[];
                foreach ($params as $param) {
                    $pars[]=$param->getName();
                }
                if($jobid == null){
                    return $reflectionMethod->invoke(new $Controler(), $ann_slug);
                }elseif($nid !== null) {
                    return $reflectionMethod->invoke(new $Controler(), $ann_slug,$job_slug,$nid,$stage->id);
                }else{
                    return $reflectionMethod->invoke(new $Controler(), $ann_slug,$job_slug);
                }
                
        }else{
            $page=Employment_StaticPages::find($stage_id);
            if(!$page){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
            $data['content'] = $page->Content;
            $Breadcrumbs='workview';
            return view('Employment::static', ['page_title' => $data['stage_name'], 'page' => 'jobs', 'data' => $data,'Breadcrumbs'=>$Breadcrumbs,'annonce'=>$an]);
        }
    }
    public static function search($ann_slug,$job_slug){
        $data=[];
        $data['status']=Employment_Status::get(['id','Text']);
        return view('Employment::search',['annonce'=>$ann_slug,'job'=>$job_slug,'data'=>$data]);
    }
    public static function create($ann_slug,$job_slug)
    {
        if(!request()->page){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        if(request()->page !== 'showjob'){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        $ann=Employment_StartAnnonces::where('Slug',$ann_slug)->get();
        if(!count($ann)){return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);}
        $job=Employment_Jobs::where('Slug',$job_slug)->where('Annonce_id',$ann[0]->id)->get();
        if(!count($job)){return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);}
        $data=[];
        $data['places']=Governorates::with('Cities')->get();
        $data['Employment_Health']=Employment_Health::all();
        $data['Employment_Ama']=Employment_Ama::all();
        $data['Employment_Army']=Employment_Army::all();
        $data['Employment_Drivers']=Employment_Drivers::all();
        $data['Employment_MaritalStatus']=Employment_MaritalStatus::all();
        $data['Mosama_Educations']=Mosama_Educations::all();
        if(request()->nid){$data['searchnid']=request()->nid;}
        return view('Employment::apply',['page_title' => trans("JOBLANG::apply.apply"),'annonce'=>$ann_slug,'job'=>$job_slug,'data'=>$data,'request'=>'apply']);
    }
    public static function review(Request $request,$reqtype=null){
        return reviewController::review($request,$reqtype);
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
            $peo=Employment_People::where('id',$request['uid'])->where('Annonce_id',$annonce->id)->first();
            if($peo){return 0;}
            $data['NID']=$peo->NID;
            $data['BirthDate']=$peo->BirthDate;
            if($peo->Sex == '1'){$peo->Sex=trans('JOBLANG::Employment_People.sex.male');}else{$peo->Sex=trans('JOBLANG::Employment_People.sex.female');}
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
    public static function complete(){
        return CompleteController::viewForm(request());
    }
}