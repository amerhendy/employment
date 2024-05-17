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
use Amerhendy\Employment\App\Models\Employment_Status;
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
class AnnoncesController extends AmerController
{
    use checkRequests,printTrait,applyTrait;
    public static $error,$jobSlug,$annonceSlug,$page,$request,$job,$annonce;
    public function __construct(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public function frontpage(){
        $data=Employment_startannonces::has('Employment_Jobs')->with('Employment_Jobs.Mosama_JobNames','Employment_Jobs.Mosama_JobTitles')
        ->with('Governorates')
        ->whereHas('Employment_Jobs',function($query){
            return $query->where('Employment_Jobs.Status','Publish')->orderBy('Code');
        })->where('Status','Publish')->orderBy('id')->get();
        $result=[];
        //dd($data[0]->Employment_Stages->Text);
        foreach($data as $a=>$b) {
            $result[$a]['number']=$b->Number;
            $result[$a]['year']=$b->Year;
            $result[$a]['description']=$b->Description;
            $result[$a]['Slug']=$b->Slug;
            foreach($b['Governorates'] as $c=>$d){
                $place=$data[$a]['Governorates'][$c];
                $result[$a]['place'][$c]=$place->Name;
            }
            foreach($b['Employment_Jobs'] as $c=>$d){
                $job=$d;
                //dd($d['Mosama_JobTitles']->text);
                $result[$a]['Employment_Jobs'][$c]['code']=$d->Code;
                $result[$a]['Employment_Jobs'][$c]['name']=$d['Mosama_JobTitles']->text;
                $result[$a]['Employment_Jobs'][$c]['job_description']=$d->Description;
                $result[$a]['Employment_Jobs'][$c]['job_name']=$d['Mosama_JobNames']->text;
                $result[$a]['Employment_Jobs'][$c]['Slug']=$d->Slug;
            }
        }
        //return $data;
        return $result;
    }
    public static function getjob_by_job_slug(Request $request){
        self::$request  =$request;
        return self::getjobInfo();
        dd($request);
    }
    public static function getjob_by_Annonce_slug($annslug){
        $annonce=Employment_StartAnnonces::where('Slug',$annslug)->first();
        if(!$annonce){
            self::$error->message=trans('JOBLANG::Employment_Reports.errors.publicError',['name'=>trans('JOBLANG::Employment_StartAnnonces.plural')]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $JobData=jobs::with('Employment_StartAnnonces')
        ->with('Employment_StartAnnonces.Employment_Stages')
        ->with('Employment_StartAnnonces.Governorates')
        ->with('Employment_StartAnnonces.Employment_Qualifications')
        ->with('Mosama_JobTitles')
        ->with(['Mosama_JobNames','Mosama_JobNames.Mosama_Competencies','Mosama_JobNames.Mosama_Experiences','Mosama_JobNames.Mosama_Goals','Mosama_JobNames.Mosama_Skills','Mosama_JobNames.Mosama_Tasks','Mosama_JobNames.Mosama_Degrees'])
        ->with('Mosama_Groups')
        ->with('Employment_Ama')
        ->with('Employment_Army')
        ->with('Cities')
        ->with('Mosama_Educations')
        ->with('Employment_Health')
        ->with('Employment_IncludedFiles')
        ->with('Employment_Instructions')
        ->with('Employment_MaritalStatus')
        ->with('Employment_Qualifications')
        ->with('Employment_Drivers')
        ->where('Annonce_id',$annonce->id)
        ->whereHas('Employment_StartAnnonces',function($query)use($annslug){
        return $query->where('Employment_StartAnnonces.Slug',$annslug);
        })
        ->get();
        if(!$JobData){self::$error->message=trans('JOBLANG::Employment_Reports.errors.publicError',['name'=>trans('JOBLANG::Employment_Reports.errors.informations')]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
        $eldata=[];
        foreach ($JobData as $key => $data) {
            //$data=$data[0];
            $result=[];
            $result['code']=$data->Code;
            $result['Description']=$data->Description;
            $result['Slug']=$data->Slug;
            $result['Count']=$data->Count;
            $result['AgeIn']=[
                'day'=>\Carbon\Carbon::parse($data->AgeIn)->format('d'),
                'month'=>\Carbon\Carbon::parse($data->AgeIn)->format('m'),
                'year'=>\Carbon\Carbon::parse($data->AgeIn)->format('Y'),
            ];
            $result['Age']=$data->Age;
            $result['Driver']=$data->Driver;
            $result['Mosama_JobNames']['Text']=$data->Mosama_JobNames->text;
            $result['Mosama_JobNames']['Mosama_Degrees']=$data->Mosama_JobNames->Mosama_Degrees->text;
            $result['Mosama_JobNames']['Mosama_Tasks']=\Arr::map($data->Mosama_JobNames->Mosama_Tasks->toArray(),function($v,$k){return $v['text'];});
            $result['Mosama_JobNames']['Mosama_Skills']=\Arr::map($data->Mosama_JobNames->Mosama_Skills->toArray(),function($v,$k){return $v['text'];});
            $result['Mosama_JobNames']['Mosama_Goals']=\Arr::map($data->Mosama_JobNames->Mosama_Goals->toArray(),function($v,$k){return $v['text'];});
            $result['Mosama_JobNames']['Mosama_Experiences']=\Arr::map($data->Mosama_JobNames->Mosama_Experiences->toArray(),function($v,$k){return [$v['type'],$v['time']];});
            $result['Mosama_JobNames']['Mosama_Competencies']=\Arr::map($data->Mosama_JobNames->Mosama_Competencies->toArray(),function($v,$k){return $v['text'];});
            $result['Mosama_JobTitles']=$data->Mosama_JobTitles->text;
            $result['Employment_StartAnnonces']['Number']=$data->Employment_StartAnnonces->Number;
            $result['Employment_StartAnnonces']['Year']=$data->Employment_StartAnnonces->Year;
            $result['Employment_StartAnnonces']['Description']=$data->Employment_StartAnnonces->Description;
            $result['Employment_StartAnnonces']['Employment_Stages']=[$data->Employment_StartAnnonces->Employment_Stages->Text,(int)$data->Employment_StartAnnonces->Employment_Stages->Front,$data->Employment_StartAnnonces->Employment_Stages->Page];
            $result['Employment_StartAnnonces']['Governorates']=\Arr::map($data->Employment_StartAnnonces->Governorates->toArray(),function($v,$k){return $v['Name'];});
            $result['Employment_StartAnnonces']['Employment_Qualifications']=\Arr::map($data->Employment_StartAnnonces->Employment_Qualifications->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_Ama']=\Arr::map($data->Employment_Ama->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_Army']=\Arr::map($data->Employment_Army->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_Health']=\Arr::map($data->Employment_Health->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_Instructions']=\Arr::map($data->Employment_Instructions->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_MaritalStatus']=\Arr::map($data->Employment_MaritalStatus->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_Qualifications']=\Arr::map($data->Employment_Qualifications->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_Drivers']=\Arr::map($data->Employment_Drivers->toArray(),function($v,$k){return $v['Text'];});
            $result['Employment_IncludedFiles']=\Arr::map($data->Employment_IncludedFiles->toArray(),function($v,$k){return $v['FileName'];});
            $result['Mosama_Educations']=\Arr::map($data->Mosama_Educations->toArray(),function($v,$k){return $v['text'];});
            $result['Cities']=\Arr::map($data->Cities->toArray(),function($v,$k){return $v['Name'];});
            $result['Mosama_Groups']=$data->Mosama_Groups->text;
            $eldata[$key]=$result;
}
        return \AmerHelper::responsedata($eldata,1,1,'');
    }
    public function employment_apply_checknid($an_slug,$jbslug,$nid,Request $request){
        trim($nid);
        if(strlen($nid) !== 14){
            self::$error->result='not14';self::$error->message=trans('JOBLANG::apply.nid_phisical_error');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $an= Employment_StartAnnonces::with('Employment_Stages')->where('Slug',$an_slug)->first();
        if(!$an){
            self::$error->result='errorannonce';self::$error->message=trans('JOBLANG::apply.nid_error_annonce');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $job=Employment_Jobs::where('Slug',$jbslug)->where('Annonce_id',$an->id)->first();
        if(!$job){
            self::$error->result='errorjob';self::$error->message=trans('JOBLANG::apply.nid_error_annonce');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $job_id=$job->id;
        $sckannonce=Employment_People::where('NID',$nid)->where('Annonce_id',$an->id)->first();
        ////////////////check Stage//////////////////
        $pageid=\Str::after($an->Employment_Stages->Page,":");
        if(\Str::before($an->Employment_Stages->Page,":") == 'D'){
            $page=\Amerhendy\Employment\App\Models\Employment_DinamicPages::where('id',$pageid)->first();
            if($page->Function == 'complete'){
                if($sckannonce){
                    return ['result'=>'success','message'=>trans("JOBLANG::apply.nidtestSuccess")];
                }else{
                    return ['result'=>'isset','message'=>trans("JOBLANG::apply.nidIssetBefore")];
                }
            }
        }
        if($sckannonce){
            return ['result'=>'isset','message'=>trans("JOBLANG::apply.nidIssetBefore")];
        }else{
            return ['result'=>'success','message'=>trans("JOBLANG::apply.nidtestSuccess")];
        }
        self::$error->result='error';self::$error->message=trans('JOBLANG::apply.nid_error_annonce');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
    }
    function allgovs(){
        
        
        $result=Governorates::orderBy('Name')->get(['id','Name']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function bygovid($gov){
        $result=Cities::orderBy('Gov_id')->where('Gov_id',$gov)->get(['id','Name','LandLineCode']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function healthCollection(){
        $result=Employment_Health::orderBy('Text')->get(['id','Text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function mirCollection(){
        $result=Employment_MaritalStatus::orderBy('Text')->get(['id','Text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function armCollection(){
        $result=Employment_Army::orderBy('Text')->get(['id','Text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    
    function amaCollection(){
        $result=Employment_Ama::orderBy('Text')->get(['id','Text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function EducationCollection(){
        $result=Mosama_Educations::orderBy('text')->get(['id','text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function driverCollection(){
        $result=Employment_Drivers::orderBy('Text')->get(['id','Text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function statusCollection(){
        $result= Employment_Status::get();
        return \AmerHelper::responsedata($result,1,count($result),'');
    }
    function stagesCollection(Request $request){
        if($request->has('sections')){
           if($request->input('section') == 'seatings'){
            $result= Employment_Stages::whereIn([[14,13,7]])->get();
           }
        }else{
            $result= Employment_Stages::get();
        }
        return \AmerHelper::responsedata($result,1,count($result),'');
    }
    function annonceCollection(){
        $result= Employment_StartAnnonces::get();
        return \AmerHelper::responsedata($result,1,count($result),'');
    }
    function annonceJobsCollection(Request $request){
        $AnnonceVal=$request->input('AnnonceVal');
        $as= Employment_Jobs::with('Mosama_JobNames');
        if(is_numeric($AnnonceVal)){
            $as=$as->where('Annonce_id',$AnnonceVal)->orWhereHas('Employment_StartAnnonces',function($query)use($AnnonceVal){
                return $query->where('Employment_StartAnnonces.Slug',$AnnonceVal);
                });
        }else{
            $as=$as->WhereHas('Employment_StartAnnonces',function($query)use($AnnonceVal){
                return $query->where('Employment_StartAnnonces.Slug',$AnnonceVal);
                });
        }
        $as=$as->get();
        $result=[];

        foreach($as as $a=>$b){
            $class=new \stdClass();
            $class->id=$b->id;
            $class->Code=$b->Code;
            $class->text=$b->Mosama_JobNames->text;
            $result[$a]=$class;
        }
        return \AmerHelper::responsedata($result,1,count($result),'');
    }
    public static function FilterCollection(Request $request){
        $filterslng=trans('JOBLANG::Employment_Reports.Filters');
        if(!$request->has('FilterVal')){
            self::$error->message=trans('JOBLANG::Employment_Reports.errors.publicError',['name'=>trans('JOBLANG::Employment_Reports.Filters')]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        dd($request);
    }
}
