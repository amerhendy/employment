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
    public static $error;
    public function __invoke(Request $request)
    {
        //
    }
    public function __construct(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public function index(){
        $data=$this->api_index();
        return view('Employment::home',['data'=>$data]);
    }
    public static function api_index()
    {
        $annonces=Employment_StartAnnonces::with('Employment_Qualifications')->with('Governorate')->where('status','Publish')->get()->toArray();
        //dd($annonces);
        if(count($annonces) !== '0'){
            foreach($annonces as $k=>$v){
                $annonces[$k]['stage_id']=Employment_stages::where('id',$v['stage_id'])->get(['id','text','page','front'])->toArray();
                $annonces[$k]['jobs']=Employment_Jobs::with(
                                                            [
                                                                'Mosama_Educations','Employment_IncludedFiles','Employment_Instructions','Employment_Qualifications',
                                                                'Mosama_Groups'
                                                            ]
                                                            )
                                                        ->where('annonce_id',$v['id'])->where('status','Publish')->get()->toArray();
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
    public function selectview ($ann_id, $job_id,$nid=null,$stage=null){
        if(!isset($_SERVER['HTTP_REFERER'])){
            self::$error->number=405;self::$error->message=trans("AMER::errors.HTTP_REFERER");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);
        }
        $from = request()->getSchemeAndHttpHost();
        if(\Str::contains($_SERVER['HTTP_REFERER'],$from) == false){self::$error->number=405;self::$error->message=trans("AMER::errors.HTTP_REFERER");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        $query=Employment_startannonces::with(['Employment_Jobs'=>function($query)use($job_id){
            return $query->where('id',$job_id);
        }])->where('id',$ann_id)->where('status','Publish')->get()->first();
        if(empty($query)){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.errors.startannoncesnotfound");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        self::$annonce=$query;
        if(!count($query->Employment_Jobs)){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.nid_not_Exists");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        self::$job=$query->Employment_Jobs[0];
        $annid=self::$annonce->id;
        $jobid=self::$job->id;
        $data = [];
        if(request()->has('lastStage')){
            $stage=Employment_Stages::find(request()->lastStage);
            if(!$stage){
                $stage=Employment_PeopleNewStage::find(request()->lastStage);
                $stage=$stage->Employment_Stages;
            }
        }else{
            $stage=self::$annonce->Employment_Stages;
        }
        $data['stage_name']=$stage->text;
        $currentstage=$stage->page;
        $Stagetype=\Str::substrCount($currentstage,'D:') ? "D:":"S:";
        if ($Stagetype === 'D:') {
            $page=$stage->functionname;
            $Control=$page->control;
            $Function=$page->function;
            if($Control == \Str::afterLast(__CLASS__,'\\')){
                if(method_exists($this,$Function)){
                    $Controler=__NAMESPACE__."\\".$Control;
                }
                //dd(__FUNCTION__);
                //dd(__NAMESPACE__ );
                //dd(\Str::afterLast(__CLASS__,'\\'));
            }else{
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
            }
                $reflectionMethod = new \ReflectionMethod($Controler, $Function);
                    $params = $reflectionMethod->getParameters();
                    $pars=[];
                    foreach ($params as $param) {
                        $pars[]=$param->getName();
                    }
                    if($jobid == null){
                        return $reflectionMethod->invoke(new $Controler(), $ann_id);
                    }elseif($nid !== null) {
                        return $reflectionMethod->invoke(new $Controler(), $ann_id,$job_id,$nid,$stage->id);
                    }else{
                        return $reflectionMethod->invoke(new $Controler(), $ann_id,$job_id);
                    }


        }else{
            $page=Employment_StaticPages::find((int) \Str::after($currentstage,':'));
            if(!$page){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
            $data['content'] = $page->Content;
            $Breadcrumbs='workview';
            return view('Employment::static', ['page_title' => $data['stage_name'], 'page' => 'jobs', 'data' => $data,'Breadcrumbs'=>$Breadcrumbs,'annonce'=>$annid]);
        }
    }
    public static function search($ann_id,$job_id){
        $data=[];
        $data['status']=Employment_Status::get(['id','Text']);
        return view('Employment::search',['annonce'=>$ann_id,'job'=>$job_id,'data'=>$data]);
    }
    public static function create($ann_id,$job_id)
    {
        if(!request()->page){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        if(request()->page !== 'showjob'){return view('errors.layout',['error_number'=>405,'error_message'=>__LINE__]);}
        $ann=Employment_StartAnnonces::where('id',$ann_id)->get();
        if(!count($ann)){return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);}

        $job=Employment_Jobs::where('id',$job_id)->where('annonce_id',$ann[0]->id)->get();
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
        return view('Employment::apply',['page_title' => trans("JOBLANG::apply.apply"),'annonce'=>$ann_id,'job'=>$job_id,'data'=>$data,'request'=>'apply']);
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

    public static function complete(){
        return CompleteController::viewForm(request());
    }
}
