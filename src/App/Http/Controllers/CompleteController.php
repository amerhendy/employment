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
    public static function viewForm(Request $request){
        //dd($request->all());
        if(!request()->has('annonce'))return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        if(!request()->has('job'))return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        if(!request()->has('nid'))return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        $annonce=request()->annonce;
        $job=request()->job;
        $nid=request()->nid;
        $annonce=Employment_StartAnnonces::where('Slug',$annonce)->first();
        if(!$annonce)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        self::$annonce=$annonce;
        $annonce_id=$annonce->id;
        $job=Employment_Jobs::where('Slug',$job)->where('Annonce_id',$annonce_id)->first();
        if(!$job)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        self::$job=$job;
        $job_id=$job->id;
        $per=Employment_People::where('NID',$nid)->where('Annonce_id',$annonce_id)->where('Job_id',$job_id)->first();
        if(!$per)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        $nest=Employment_PeopleNewStage::where('People_id',$per->id)->get()->last();
        
        //dd($nest,$per[0]['id']);
        $nest=$nest->toArray();
        if(!$nest)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
        
        $stageid=(int) $nest['Stage_id'];
        $ldata=Employment_PeopleNewData::where('People_id',$per->id)->count();
        if($ldata !== 0)return view('errors.layout',['error_number'=>404,'error_message'=>__LINE__]);
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
        ];
        return view('Employment::apply', $sentData,compact('per'));
    }
}