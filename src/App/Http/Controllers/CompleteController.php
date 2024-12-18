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
use Illuminate\Database\Eloquent\Model;
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
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class CompleteController extends AmerController
{
    private static $annonce,$job;
    public static $error;
    public function __construct(){
        self::setErrors();
    }
    public static function setErrors(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function viewForm(Request $request){
        self::setErrors();
        if(!request()->has('annonce')){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.errors.startannoncesnotfound");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        if(!request()->has('job')){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.errors.jobNotFound");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        if(!request()->has('nid')){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.nid_not_Exists");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        $annonce=request()->annonce;
        $job=request()->job;
        $nid=request()->nid;
        $annonce=Employment_StartAnnonces::where('Slug',$annonce)->first();
        if(!$annonce){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.errors.startannoncesnotfound");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        self::$annonce=$annonce;
        $annonce_id=$annonce->id;
        $job=Employment_Jobs::where('Slug',$job)->where('annonce_id',$annonce_id)->first();
        if(!$job){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.errors.jobNotFound");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        self::$job=$job;
        $job_id=$job->id;
        $per=Employment_People::where('NID',$nid)->where('annonce_id',$annonce_id)->first();
        if(!$per){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.nid_not_Exists");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        $nest=Employment_PeopleNewStage::where('People_id',$per->id)->get()->last();
        //dd($nest,$per[0]['id']);
        $nest=$nest->toArray();
        if(!$nest){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.nid_not_Exists");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        $stageid=(int) $nest['Stage_id'];
        $ldata=Employment_PeopleNewData::where('People_id',$per->id)->count();
        if($ldata !== 0){self::$error->number=405;self::$error->message=trans("JOBLANG::Apply.nid_not_Exists");self::$error->line=__LINE__;return view('errors.layout',['console'=>self::$error]);}
        $data=new \stdClass();
        $data->places=Governorates::with('Cities')->get();
        $data->Employment_Health=Employment_Health::all();
        $data->Employment_Ama=Employment_Ama::all();
        $data->Employment_Army=Employment_Army::all();
        $data->Employment_Drivers=Employment_Drivers::all();
        $data->Employment_MaritalStatus=Employment_MaritalStatus::all();
        $data->Mosama_Educations=Mosama_Educations::all();
        $data->value=$per;
        $data->NID=request()->nid;
        $PeopleNewStage=$per->Employment_PeopleNewStage->where('Stage_id',5)->last();
        $PeopleNewStageId=$PeopleNewStage->id;
        $sentData=[
            'page_title' =>trans("JOBLANG::apply.Complete.Complete"),
            'page' => 'jobs', 
            'annonce' => $annonce,
            'job'=>$job,
            'nid'=>$nid,
            'uid'=>$per->id,
            'data'=>$data,
            'request'=>'complete',
            'per'=>$per,
            'stageid'=>$stageid,
            'PeopleNewStageId'=>$PeopleNewStageId
        ];
        return view('Employment::apply', $sentData,compact('per'));
    }
}