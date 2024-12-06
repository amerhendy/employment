<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Amerhendy\Employment\App\Models\Employment_Stages;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Amerhendy\Employment\App\Rules\uptoidsTextarea;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
trait checkRequests
{
public static $erros;
    public function __construct(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function setErrorClass(){//29310021499811
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    public static function SearchPagecheck(){
        $request=self::$request;

    }
    public static function checknid(){
        $request=self::$request;
        $rules=[
            'annonceid'=>['required','exists:employment_startannonces,id'],
            'jobid'=>'required|exists:employment_jobs,id',
            'page'=>[
                'required',
                Rule::in(['search','create','complete','showjob','apply']),
            ],
            'nid'=>[
                'required',
                'digits_between:14,14',
                'numeric',
            ],
        ];
        $attributes=[
            'nid'=>trans('JOBLANG::Employment_People.NID'),
            'page'=>trans('JOBLANG::Employment_Stages.page'),
            'annonceSlug'=>trans('JOBLANG::Employment_StartAnnonces.plural'),
        ];
        $errorMessages=[
            'required' => trans('JOBLANG::apply.errors.required',[':attribute']),
            'string' => trans('JOBLANG::apply.errors.string',[':attribute']),
            'digits'=> trans('JOBLANG::apply.errors.digits',[':attribute']),
            'integer'=> trans('JOBLANG::apply.errors.digits',[':attribute']),
            'required_if'=>trans('JOBLANG::apply.errors.required',[':attribute']),
            'date'=> trans('JOBLANG::apply.errors.date',[':attribute']),
            'in'=> trans('JOBLANG::apply.errors.in',[':attribute']),
            'exists'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
            'max'=>trans('JOBLANG::apply.errors.max',[':attribute',':max']),
            'size'=>trans('JOBLANG::apply.errors.size',[':attribute',':size']),
            'digits_between'=>trans('JOBLANG::apply.errors.size',[':attribute',':min']),
            'email'=>trans('JOBLANG::apply.errors.email',[':attribute']),
            'file'=>trans('JOBLANG::apply.errors.file',[':attribute']),
            'mimetypes'=>trans('JOBLANG::apply.errors.mimetypes',[':attribute']),
        ];
        $validator = Validator::make(self::$request->all(), $rules,$errorMessages,$attributes);
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::$nid=trim(self::$request->input('nid'));
        /////check annonce////////////
        self::$annonce= Employment_StartAnnonces::with('employment_stages')->where('id',self::$request->input('annonceid'))->first();
        if(!self::$annonce){
            $validator->errors()->add('jobid',trans('JOBLANG::apply.errors.required',[trans('JOBLANG::Employment_StartAnnonces.plural')]));
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        /////check job////////////
        self::$job=Employment_Jobs::where('id',self::$request->input('jobid'))->where('annonce_id',self::$annonce->id)->first();
        if(!self::$job){
            $validator->errors()->add('jobid',trans('JOBLANG::apply.Employment_Jobs.selectJob'));
            return response()->json(['error'=>$validator->errors()]);
        }
        if(self::$request->input('page') !== 'create'){
            //get job action
            $action=self::$annonce->Employment_Stages->functionName->Function;
            if($action == 'complete'){
                $people=Employment_People::where('nid',self::$nid)->where('annonce_id',self::$annonce->id)->with('Employment_PeopleNewStage')->first();
                if(!$people){
                    $validator->errors()->add('nid',trans('JOBLANG::Employment_People.NID'));
                    self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
                }
                $lststge=$people->Employment_PeopleNewStage->last()->Stage_id;
                $fun=Employment_Stages::find($lststge)->functionName->Function;
                if(!in_array($fun,[self::$request->input('page'),$action])){$validator->errors()->add('page',trans('JOBLANG::Employment_Stages.page'));}
            }elseif($action == 'create'){
                $people=Employment_People::where('nid',self::$nid)->where('annonce_id',self::$annonce->id)->first();
                if($people){
                    $validator->errors()->add('nid',trans('JOBLANG::Apply.nid_Already_Exists'));
                }
            }elseif($action == 'search'){
                $people=Employment_People::where('nid',self::$nid)->where('annonce_id',self::$annonce->id)->first();
                if(!$people){
                    $validator->errors()->add('nid',trans('JOBLANG::Apply.nid_Already_Exists'));
                    self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
                }
            }


        }else{

            if(self::$annonce->Employment_Stages->functionName->function !== self::$request->input('page')){$validator->errors()->add('page',trans('JOBLANG::Employment_Stages.page'));}
        }
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        return true;
    }
    public static function ShowJobReq(){
        $request=self::$request;
        if($request->has('actiontype')){
            $request['page']=$request['actiontype'] ;
        }
        $rules=[
            'annonceid'=>['required','exists:employment_startannonces,id'],
            'jobid'=>'required|exists:employment_jobs,id',
            'page'=>[
                'required',
                Rule::in(['showJob','create','apply']),
            ],
            'view'=>[
                'required',
                Rule::in(['json','pdf']),
            ],
        ];
        $attributes=[
            'jobid'=>trans('JOBLANG::apply.Employment_Jobs.plural'),
            'nid'=>trans('JOBLANG::Employment_People.NID'),
            'page'=>trans('JOBLANG::Employment_Stages.page'),
            'annonceSlug'=>trans('JOBLANG::Employment_StartAnnonces.plural'),
        ];
        $errorMessages=[
            'required' => trans('JOBLANG::apply.errors.required',[':attribute']),
            'in'=> trans('JOBLANG::apply.errors.in',[':attribute']),
            'exists'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
        ];

        $validator = Validator::make(self::$request->all(), $rules,$errorMessages,$attributes);
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        return true;
    }
    public static function ReviewReq(){
        self::setErrorClass();
        $request=self::$request;
        //dd($request->toArray());
        //dd(strlen($request->connectmobile));
        $rules=[
            'actiontype'=>['required','in:create,complete'],
            'apply_date'=>['required','date'],
            'annonceid'=>['required','exists:employment_startannonces,id'],
            'jobid'=>['required','exists:employment_jobs,id'],
            'fname'=>['required','string','min:3'],
            'sname'=>['required','string','min:3'],
            'tname'=>['required','string','min:3'],
            'lname'=>['required','string','min:3'],
            'nid'=>['required','numeric','digits:14'],
            'birthdate'=>['required','date'],
            'sex'=>['required','numeric','in:0,1'],
            'ageyears'=>['required','numeric','between:15,60'],
            'agemonths'=>['required','numeric','between:0,12'],
            'agedays'=>['required','numeric','between:0,30'],
            'borngov'=>['required','uuid','exists:governorates,id'],
            'borncity'=>['required','uuid','exists:cities,id'],
            'livegov'=>['required','uuid','exists:governorates,id'],
            'livecity'=>['required','uuid','exists:cities,id'],
            'liveaddress'=>['required','string'],
            'connectlandline'=>['required','numeric','digits_between:6,11'],
            'connectmobile'=>['required','numeric','digits_between:11,11'],
            'connectemail'=>['required','email:rfc,dns,spoof'],
            'health_id'=>['required','uuid','exists:employment_healths,id'],
            'maritalstatus_id'=>['required','uuid','exists:employment_maritalstatus,id'],
            'arm_id'=>['required','uuid','exists:employment_armies,id'],
            'ama_id'=>['required','uuid','exists:employment_amas,id'],
            'education_id'=>['required','uuid','exists:mosama_educations,id'],
            'educationyear'=>['required','numeric','digits:4'],
            'accept_driver'=>['required','numeric','in:0,1'],
            'driverdegree'=>['exclude_if:accept_driver,1','required','numeric','exists:employment_drivers,id'],
            'driverstart'=>['exclude_if:accept_driver,1','required','date'],
            'driverend'=>['exclude_if:accept_driver,1','required','date','after:driverstart'],
            'Khebra_type'=>['required','numeric','in:0,1,2'],
            'Khebra'=>['required','numeric','min:0'],
            'acceptall'=>['required','accepted'],
            'tamin'=>['required','numeric','min:0'],
            'uploades'=>['required','file','mimetypes:application/pdf','mimes:pdf','max:8192'],
            'peoplenewstageid'=>['exclude_if:actiontype,create','required','numeric',Rule::exists('employment_peoplenewstage','id')->where('people_id',request()->uid)],
            'stageid'=>['exclude_if:actiontype,create','required','numeric',Rule::exists('employment_peoplenewstage','stage_id')->where('people_id',request()->uid)],
        ];
        $attributes=[
            'actiontype'=>trans('JOBLANG::Employment_People.NID'),
            'apply_date'=>trans('JOBLANG::Employment_People.applyDate'),
            'annonceid'=>trans('JOBLANG::Employment_People.NID'),
            'jobid'=>trans('JOBLANG::Employment_People.NID'),
            'fname'=>trans('JOBLANG::Employment_People.Fname'),
            'sname'=>trans('JOBLANG::Employment_People.Sname'),
            'tname'=>trans('JOBLANG::Employment_People.Tname'),
            'lname'=>trans('JOBLANG::Employment_People.Lname'),
            'nid'=>trans('JOBLANG::Employment_People.NID'),
            'birthdate'=>trans('JOBLANG::Employment_People.BirthDate'),
            'sex'=>trans('JOBLANG::Employment_People.Sex.Sex'),
            'ageyears'=>trans('JOBLANG::Employment_People.Age.AgeYears'),
            'agemonths'=>trans('JOBLANG::Employment_People.Age.AgeMonths'),
            'agedays'=>trans('JOBLANG::Employment_People.Age.AgeDays'),
            'borngov'=>trans('JOBLANG::Employment_people.bornPlace.Governorate'),
            'borncity'=>trans('JOBLANG::Employment_people.bornPlace.City'),
            'livegov'=>trans('JOBLANG::Employment_People.LivePlace.Governorator'),
            'livecity'=>trans('JOBLANG::Employment_People.LivePlace.City'),
            'liveaddress'=>trans('JOBLANG::Employment_People.LivePlace.Address'),
            'connectlandline'=>trans('JOBLANG::Employment_people.Connection.LandLine'),
            'connectmobile'=>trans('JOBLANG::Employment_people.Connection.Mobile'),
            'connectemail'=>trans('JOBLANG::Employment_people.Connection.Email'),
            'health_id'=>trans('JOBLANG::Employment_Health.Employment_Health'),
            'maritalstatus_id'=>trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus'),
            'arm_id'=>trans('JOBLANG::Employment_Army.Employment_Army'),
            'ama_id'=>trans('JOBLANG::Employment_Ama.Employment_Ama'),
            'education_id'=>trans('EMPLANG::Mosama_Educations.Mosama_Educations'),
            'educationyear'=>trans('JOBLANG::Employment_People.Mosama_Educations.year'),
            'accept_driver'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree"),
            'driverdegree'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree"),
            'driverstart'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart"),
            'driverend'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd"),
            'Khebra_type'=>trans('JOBLANG::Employment_People.Khebra.type'),
            'Khebra'=>trans('JOBLANG::Employment_People.Khebra.years'),
            'acceptall'=>trans('JOBLANG::Employment_People.acceptall'),
            'tamin'=>trans('JOBLANG::Employment_people.Tamin.Tamin'),
            'uploades'=>trans('JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles'),
        ];

        $errorMessages=[
            'required' => trans('JOBLANG::apply.errors.required',[':attribute']),
            'digits'=>trans('JOBLANG::apply.errors.digits',[':attributes']),
            'string'=> trans('JOBLANG::apply.errors.string',[':attribute']),
            'in'=> trans('JOBLANG::apply.errors.in',[':attribute']),
            'exists'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
            'file'=>trans('JOBLANG::apply.errors.file',[':attribute']),
            'mimetypes'=>trans('JOBLANG::apply.errors.mimetypes',[':attribute']),
            'mimes'=>trans('JOBLANG::apply.errors.mimes',[':attribute']),
            'max'=>trans('JOBLANG::apply.errors.max',[':attribute',':max']),
            'accepted'=>trans('JOBLANG::apply.errors.accepted',[':attribute']),
            'numeric'=>trans('JOBLANG::apply.errors.digits',[':attribute']),
            'min'=>trans('JOBLANG::apply.errors.min',[':attribute']),
            'date'=>trans('JOBLANG::apply.errors.date',[':attribute']),
            'after'=>trans('JOBLANG::apply.errors.gt',[':attribute',':field']),
            'digits_between'=>trans('JOBLANG::apply.errors.digits_between',[':attribute',':min',':max']),
            'between'=>trans('JOBLANG::apply.errors.between',[':attribute',':min',':max']),
            'email'=>trans('JOBLANG::apply.errors.email',[':attribute']),
        ];
        $validator = Validator::make(self::$request->all(), $rules,$errorMessages,$attributes);
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        //checknid if exists
        if(self::$request->has('nid')){
            self::$nid=trim(self::$request->input('nid'));
        }elseif(self::$request->has('NID')){
            self::$nid=trim(self::$request->input('NID'));
        }

        self::$annonce= Employment_StartAnnonces::with('Employment_Stages')->where('id',self::$request->input('annonceid'))->first();
        if(!self::$annonce){
            $validator->errors()->add('annonceid',trans('JOBLANG::apply.errors.required',[trans('JOBLANG::Employment_StartAnnonces.plural')]));
        }else{
            self::$job=Employment_Jobs::where('id', self::$request->input('jobid'))->where('annonce_id',self::$annonce->id)->first();
            if(!self::$job){
                $validator->errors()->add('jobid',trans('JOBLANG::apply.Employment_Jobs.selectJob'));
            }
        }
        if(self::$request->input('actiontype') !== 'create'){
            //get job action
            $action=self::$annonce->Employment_Stages->functionName->Function;
            if($action == 'complete'){
                $people=Employment_People::where('id',self::$request->input('uid'))->where('NID',self::$nid)->where('annonce_id',self::$annonce->id)->with('Employment_PeopleNewStage')->first();
                if(!$people){$validator->errors()->add('NID',trans('JOBLANG::Employment_People.NID'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                if(!$people->Employment_PeopleNewStage){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                if(!$people->Employment_PeopleNewStage->last()){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                if(!$people->Employment_PeopleNewStage->last()->Stage_id){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                $lststge=$people->Employment_PeopleNewStage->last()->Stage_id;
                $fun=Employment_Stages::find($lststge);
                if(!$fun){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                $fun=$fun->functionName->Function;
                if($fun !== self::$request->input('actiontype')){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));}
            }elseif($action == 'create'){
                $people=Employment_People::where('NID',self::$nid)->where('annonce_id',self::$annonce->id)->first();
                if($people){
                    $validator->errors()->add('nid',trans('JOBLANG::Apply.nid_Already_Exists'));
                }
            }elseif($action == 'search'){}


        }else{
            if(self::$annonce->Employment_Stages->functionName->function !== self::$request->input('actiontype')){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));}
        }

        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        return true;
    }
    public static function AdminUpToDatecheck(){
        $request=self::$request;
        $rules=[
            '_token'=>['required'],
            'publisher'=>['required','exists:users,id','numeric'],
            'uptoidsTextarea'=>['required','json', new uptoidsTextarea],
            'new_stage'=>['required','exists:Employment_Stages,id','numeric'],
            'new_res'=>['required','exists:Employment_Status,id','numeric'],
            'editor1'=>['required']
        ];
        $attributes=[
            'publisher'=>'publisher',
            'new_res'=>trans('JOBLANG::Employment_People.NID'),
            'editor1'=>trans('JOBLANG::Employment_Stages.page'),
            'uptoidsTextarea'=>trans('JOBLANG::Employment_People.uid'),
        ];
        $errorMessages=[
            'required' => trans('JOBLANG::apply.errors.required',[':attribute']),
            'numeric'=> trans('JOBLANG::apply.errors.numeric',[':attribute']),
            'exists'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
            'uptoidsTextarea'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
            'json'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
        ];
        $validator = Validator::make(self::$request->all(), $rules,$errorMessages,$attributes);
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        return true;
    }
}
