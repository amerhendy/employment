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
    public function frontpage()
{
    $cacheKey = 'frontpage_data';
    $ttl = 86400; // يوم كامل (ثواني)

    $result = \Cache::remember($cacheKey, $ttl, function () {
        $data = Employment_startannonces::where('status','publish')
            ->has('Employment_Jobs')
            ->with([
                'Employment_Jobs.Mosama_JobNames',
                'Employment_Jobs.Mosama_JobNames.Mosama_JobTitles',
                'governorate',
                'Employment_Jobs.city.Governorates'
            ])
            ->whereHas('Employment_Jobs', function($query){
                return $query->where('employment_jobs.status','publish')
                             ->orderBy('code');
            })
            ->get();

        $result = [];
        foreach ($data as $b) {
            $class = new \stdClass;
            $class->id = $b->id;
            $class->number = $b->number;
            $class->year = $b->year;
            $class->description = $b->description;

            // المحافظات
            $class->governorates = $b->governorate->pluck('name')->toArray();

            // الوظائف
            $class->Employment_Jobs = [];
            foreach ($b->Employment_Jobs as $d) {
                $job = new \stdClass;
                $job->id = $d->id;
                $job->job_name = $d->Mosama_JobNames->text ?? '';
                $job->name = $d->Mosama_JobNames->Mosama_JobTitles->text ?? '';
                $job->code = $d->code;
                $job->description = $d->description;

                // المدن → جروب بالمحافظة
                $places = collect($d->city)->map(function($m) {
                    return [
                        'name' => $m->name,
                        'gov'  => $m->Governorates->name ?? ''
                    ];
                })->groupBy('gov');

                $job->place = $places->toArray();
                $class->Employment_Jobs[] = $job;
            }

            $result[] = $class;
        }

        return $result;
    });

    $data = [
        'layout' => 'FrontLayout',
        'settings' => [],
        'routeList' => [],
        'title' => '',
        'currentOperation' => '',
        'data' => $result
    ];

    return \AmerHelper::returnLayoutData($data);
}

    public static function getjob_by_id(Request $request)
    {
        self::$request  = $request;
        $check = self::ShowJobReq();
        if (gettype($check) == 'object') {
            return $check;
        }
        $annonceid = self::$annonce->slug ?? self::$request->input('annonceid');
        $jobid     = $request->input('jobid');
        // ✅ مفتاح الكاش (مميز لكل اعلان + وظيفة)
        $cacheKey = "job_{$annonceid}_{$jobid}";
        \Cache::forget($cacheKey);
        $ttl = 86400; // يوم واحد
        $data = \Cache::remember($cacheKey, $ttl, function () use ($annonceid, $jobid) {
            return jobs::with(['Employment_StartAnnonces',
                            'Mosama_JobNames.Mosama_Groups',
                            'Mosama_JobNames.Mosama_JobTitles',
                            'Mosama_JobNames.Mosama_Degrees',
                            'Mosama_JobNames.Mosama_Tasks',
                            'Mosama_JobNames.Mosama_Skills',
                            'Mosama_JobNames.Mosama_Goals',
                            'Mosama_JobNames.Mosama_Experiences',
                            'Mosama_JobNames.Mosama_Competencies',
                            'Employment_StartAnnonces.Employment_Stages',
                            'Employment_StartAnnonces.governorate',
                            'Employment_StartAnnonces.Employment_Qualifications',
                            'Employment_Ama',
                            'Employment_Army',
                            'Employment_Health',
                            'Employment_Instructions',
                            'Employment_MaritalStatus',
                            'Employment_Qualifications',
                            'Employment_Drivers',
                            'Employment_IncludedFiles',
                            'Mosama_Educations',
                            'City'
                        ])
                ->where('id', $jobid)
                ->whereHas('Employment_StartAnnonces', function ($query) use ($annonceid) {
                    return $query->where('employment_start_annonces.id', $annonceid);
                })
                ->get();
        });
        if (!$data || $data->isEmpty()) {
            self::$error->message = trans('JOBLANG::Employment_Reports.errors.publicError', [
                'name' => trans('JOBLANG::Employment_Reports.errors.informations')
            ]);
            self::$error->line = __LINE__;
            return \AmerHelper::responseError(self::$error, self::$error->number);
        }
        if (self::$request->has('page')) {
            // if(self::$request->input('page') == 'showJob'){$request->merge(['view'=>'pdf']);}
        }

        $data = self::getjobInfo($data, 'one'); 
        return $data;
    }
    public static function getjob_by_Annonce_slug($annid){
        $annonce=Employment_StartAnnonces::where('id',$annid)->first();
        if(!$annonce){
            self::$error->message=trans('JOBLANG::Employment_Reports.errors.publicError',['name'=>trans('JOBLANG::Employment_StartAnnonces.plural')]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $JobData=jobs::with('Employment_StartAnnonces')
        ->where('annonce_id',$annonce->id)
        ->whereHas('employment_startannonces',function($query)use($annid){
        return $query->where('employment_startannonces.id',$annid);
        })
        ->get();
        if(!$JobData){self::$error->message=trans('JOBLANG::Employment_Reports.errors.publicError',['name'=>trans('JOBLANG::Employment_Reports.errors.informations')]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
        self::$request=request();
        self::$request->merge(['view'=>'json']);
        $data=self::getjobInfo($JobData,'list');
        return $data;
        $eldata=[];
        foreach ($JobData as $key => $data) {
            $result=new \stdClass();
            $result->id=$data->id;
            $result->code=$data->code;
            $result->description=$data->description;
            $result->count=$data->count;
            $result->agein=$data->ageformat;
            $result->driver=$data->driver;
            $result->status=$data->status;
            $result->Mosama_JobNames=new \stdClass();
            $result->Mosama_JobNames->text=$data->Mosama_JobNames->text;
            $result->Mosama_JobNames->Mosama_JobTitles=$data->Mosama_JobNames->Mosama_JobTitles->text;
            $result->Mosama_JobNames->Mosama_Groups=$data->Mosama_JobNames->Mosama_Groups->text;
            $result->Mosama_JobNames->Mosama_Degrees=$data->Mosama_JobNames->Mosama_Degrees->text;
            $result->Mosama_JobNames->Mosama_Tasks=\Arr::map($data->Mosama_JobNames->Mosama_Tasks->toArray(),function($v,$k){return $v['text'];});
            $result->Mosama_JobNames->Mosama_Skills=\Arr::map($data->Mosama_JobNames->Mosama_Skills->toArray(),function($v,$k){return $v['text'];});
            $result->Mosama_JobNames->Mosama_Goals=\Arr::map($data->Mosama_JobNames->Mosama_Goals->toArray(),function($v,$k){return $v['text'];});
            $result->Mosama_JobNames->Mosama_Experiences=\Arr::map($data->Mosama_JobNames->Mosama_Experiences->toArray(),function($v,$k){return [$v['type'],$v['time']];});
            $result->Mosama_JobNames->Mosama_Competencies=\Arr::map($data->Mosama_JobNames->Mosama_Competencies->toArray(),function($v,$k){return $v['text'];});
            dd($result->Mosama_JobNames->Mosama_Tasks);
            $result=[];

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
        $job=Employment_Jobs::where('Slug',$jbslug)->where('annonce_id',$an->id)->first();
        if(!$job){
            self::$error->result='errorjob';self::$error->message=trans('JOBLANG::apply.nid_error_annonce');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $job_id=$job->id;
        $sckannonce=Employment_People::where('NID',$nid)->where('annonce_id',$an->id)->first();
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
    function allPlaces(){
        $result=Governorates::with(['cities' => function ($query) {
            $query->withTrashed()->select(
                [
                    'id',
                    'governorate_id',
                    'name',
                    'english',
                    'landline_code'
                ]
            );
        }])->orderBy('name')->withTrashed()->get(['id','name','nidCode']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function allgovs(){
        $result=Governorates::orderBy('Name')->get(['id','name']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function bygovid($gov){
        $result=Cities::orderBy('gov_id')->where('gov_id',$gov)->get(['id','name','LandLineCode']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function healthCollection(){
        $result=Employment_Health::orderBy('text')->get(['id','text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function mirCollection(){
        $result=Employment_MaritalStatus::orderBy('text')->get(['id','text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function armCollection(){
        $result=Employment_Army::orderBy('text')->get(['id','text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }

    function amaCollection(){
        $result=Employment_Ama::orderBy('text')->get(['id','text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function EducationCollection(){
        $result=Mosama_Educations::orderBy('text')->get(['id','text']);
        return \AmerHelper::responsedata($result,1,$result->count(),'');
    }
    function driverCollection(){
        $result=Employment_Drivers::orderBy('text')->get(['id','text']);
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
            $as=$as->where('annonce_id',$AnnonceVal)->orWhereHas('Employment_StartAnnonces',function($query)use($AnnonceVal){
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
