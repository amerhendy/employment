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
use Amerhendy\Employment\App\Rules\FileOrArray;

trait checkRequests
{
public static $erros,$json;
    public function __construct(){
        self::$json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json(Request());
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
            'annonceid'=>['required','exists:employment_start_annonces,id'],
            
            'page'=>[
                'required',
                Rule::in(['search','create','complete','showJob','apply']),
            ],
            'nid'=>[
                'required',
                'digits_between:14,14',
                'numeric',
            ],
        ];
        if($request->page !== 'showJob'){
            $rules['jobid']=[
                Rule::excludeIf(fn()=> $request->page !== 'showJob'),
                Rule::exists(Employment_Jobs::class, 'id'),
            ];
        }
        $attributes=[
            'nid'=>'employment.Employment_People.NID',
            'page'=>trans('JOBLANG::Employment_Stages.page'),
            'annonceSlug'=>trans('JOBLANG::Employment_StartAnnonces.plural'),
        ];
        $errorMessages=[
            'required' => 'employment.apply.errors.required',[':attribute'],
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
        if($request->page !== 'showJob'){
            self::$job=Employment_Jobs::where('id',self::$request->input('jobid'))->where('annonce_id',self::$annonce->id)->first();
            if(!self::$job){
                $validator->errors()->add('jobid',trans('JOBLANG::apply.Employment_Jobs.selectJob'));
                return response()->json(['error'=>$validator->errors()]);
            }
        }
        if(self::$request->input('page') !== 'create'){
            //get job action
            $action=self::$annonce->Employment_Stages->functionName->function;
            if($action == 'complete'){
                $people=Employment_People::where('nid',self::$nid)->where('annonce_id',self::$annonce->id)->with('Employment_PeopleNewStage')->first();
                if(!$people){
                    $validator->errors()->add('nid','employment.apply.nid_not_Exists');
                    self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
                }
                $lststge=$people->Employment_PeopleNewStage->last();
                $fun=$lststge->employment_stages->functionName->function;
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
            }elseif($action == 'showJob'){
                
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
            'annonceid'=>['required','exists:employment_start_annonces,id'],
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
        $request->merge([
            'accept_driver' => filter_var($request->accept_driver, FILTER_VALIDATE_BOOLEAN),
        ]);
        $rules=[
            'actiontype'=>['required','in:create,complete'],
            'uid'=>['required_if:actiontype,complete','uuid','unique:employment_people_new_data,people_id'],
            'apply_date'=>['required','date'],
            'annonce_id'=>['required','exists:employment_start_annonces,id'],
            'job_id'=>['required','exists:employment_jobs,id'],
            'fname'=>['required','string','min:3'],
            'sname'=>['required','string','min:3'],
            'tname'=>['required','string','min:3'],
            'lname'=>['required','string','min:3'],
            'nid'=>['required','numeric','digits:14'],
            'birth_date'=>['required','date'],
            'sex'=>['required','numeric','in:0,1'],
            'age_years'=>['required','numeric','between:15,60'],
            'age_months'=>['required','numeric','between:0,12'],
            'age_days'=>['required','numeric','between:0,30'],
            'born_place'=>['required','uuid','exists:cities,id'],
            'live_place'=>['required','uuid','exists:cities,id'],
            'live_address'=>['required','string'],
            'connect_landline'=>['required','numeric','digits_between:6,11'],
            'connect_mobile'=>['required','numeric','digits_between:11,11'],
            'connect_email'=>['required',
                                //'email:rfc,dns,spoof'
            ],
            'health_id'=>['required','uuid','exists:employment_health,id'],
            'marital_status_id'=>['required','uuid','exists:employment_marital_status,id'],
            'arm_id'=>['required','uuid','exists:employment_armies,id'],
            'ama_id'=>['required','uuid','exists:employment_amas,id'],
            'education_id'=>['required','uuid','exists:mosama_educations,id'],
            'education_year'=>['required'],
            'accept_driver'=>['required','boolean'],
            'driverdegree'=>['exclude_if:accept_driver,false','required','uuid','exists:employment_drivers,id'],
            'driverstart'=>['exclude_if:accept_driver,false','required','date'],
            'driverend'=>['exclude_if:accept_driver,false','required','date','after:driverstart'],
            'khebra_type'=>['required','in:No_experience,Interval,Work_experience'],
            'khebra_years'=>['required','numeric','min:0'],
            'acceptall'=>['required','accepted'],
            'tamin'=>['required','numeric','min:0'],
            'file_name' => ['required', new FileOrArray],
            'peoplenewstageid'=>['exclude_if:actiontype,create','required',Rule::exists('employment_people_new_stage','id')->where('people_id',request()->uid)],
            'stageid'=>['exclude_if:actiontype,create','required','uuid',Rule::exists('employment_people_new_stage','stage_id')->where('people_id',request()->uid)],
        ];
        $attributes=self::convertTrToJSON('ReviewReq','attributes');
        $errorMessages=self::convertTrToJSON('ReviewReq','error');
        $validator = Validator::make(self::$request->all(), $rules,$errorMessages,$attributes);
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        //set nid
        self::setNid();
        $validator=self::setAnnonceJob($validator);
        if(count($validator->errors())){
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        
        if(self::$request->input('actiontype') !== 'create'){
            //get job action
            $action=self::$annonce->Employment_Stages->functionName->function;
            if($action == 'complete'){
                $people=Employment_People::where('id',self::$request->input('uid'))->where('nid',self::$nid)->where('annonce_id',self::$annonce->id)->with('Employment_PeopleNewStage')->first();
                if(!$people){$validator->errors()->add('NID',trans('JOBLANG::Employment_People.NID'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                self::$peopleDB=$people;
                if(!$people->Employment_PeopleNewStage){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                if(!$people->Employment_PeopleNewStage->last()){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                $lastStage=$people->Employment_PeopleNewStage->last();
                if(!$lastStage->employment_stages){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                if(!$lastStage->employment_stages->functionName){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
                if(!$lastStage->employment_stages->functionName->function){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            }elseif($action == 'create'){
                $people=Employment_People::where('nid',self::$nid)->where('annonce_id',self::$annonce->id)->first();
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
    public static function setNid(){
        if(self::$request->has('nid')){
            self::$nid=trim(self::$request->input('nid'));
        }elseif(self::$request->has('NID')){
            self::$nid=trim(self::$request->input('NID'));
        }
    }
    public static function setAnnonceJob($validator){
        self::$annonce= Employment_StartAnnonces::with('Employment_Stages')->where('id',self::$request->input('annonce_id'))->first();
        if(!self::$annonce){
            $validator->errors()->add('annonce_id',trans('JOBLANG::apply.errors.required',[trans('JOBLANG::Employment_StartAnnonces.plural')]));
        }else{
            self::$job=Employment_Jobs::where('id', self::$request->input('job_id'))->where('annonce_id',self::$annonce->id)->first();
            if(!self::$job){
                $validator->errors()->add('job_id',trans('JOBLANG::apply.Employment_Jobs.selectJob'));
            }
        }
        return $validator;
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
    //'ReviewReq','attributes'
    public static function convertTrToJSON($fn,$type = null){
        $json=self::$json;
        if($type == 'error'){
            return trans('AMER::errors');
        }
        if($type == 'attributes'){
            if($fn=='ReviewReq'){
                $attributes=[
                    'actiontype'=>'JOBLANG::Employment_People.NID',
                    'apply_date'=>'JOBLANG::Employment_People.applyDate',
                    'annonce_id'=>'JOBLANG::Employment_People.NID',
                    'job_id'=>'JOBLANG::Employment_People.NID',
                    'fname'=>'JOBLANG::Employment_People.Fname',
                    'sname'=>'JOBLANG::Employment_People.Sname',
                    'tname'=>'JOBLANG::Employment_People.Tname',
                    'lname'=>'JOBLANG::Employment_People.Lname',
                    'nid'=>'JOBLANG::Employment_People.NID',
                    'birth_date'=>'JOBLANG::Employment_People.BirthDate',
                    'sex'=>'JOBLANG::Employment_People.Sex.Sex',
                    'age_years'=>'JOBLANG::Employment_People.Age.AgeYears',
                    'age_months'=>'JOBLANG::Employment_People.Age.AgeMonths',
                    'age_days'=>'JOBLANG::Employment_People.Age.AgeDays',
                    'born_place'=>'JOBLANG::Employment_people.bornPlace.City',
                    'live_place'=>'JOBLANG::Employment_People.LivePlace.City',
                    'live_address'=>'JOBLANG::Employment_People.LivePlace.Address',
                    'connect_landline'=>'JOBLANG::Employment_people.Connection.LandLine',
                    'connect_mobile'=>'JOBLANG::Employment_people.Connection.Mobile',
                    'connect_email'=>'JOBLANG::Employment_people.Connection.Email',
                    'health_id'=>'JOBLANG::Employment_Health.Employment_Health',
                    'marital_status_id'=>'JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus',
                    'arm_id'=>'JOBLANG::Employment_Army.Employment_Army',
                    'ama_id'=>'JOBLANG::Employment_Ama.Employment_Ama',
                    'education_id'=>'EMPLANG::Mosama_Educations.Mosama_Educations',
                    'education_year'=>'JOBLANG::Employment_People.Mosama_Educations.year',
                    'accept_driver'=>"JOBLANG::Employment_People.Employment_Drivers.DriverDegree",
                    'driverdegree'=>"JOBLANG::Employment_People.Employment_Drivers.DriverDegree",
                    'driverstart'=>"JOBLANG::Employment_People.Employment_Drivers.DriverStart",
                    'driverend'=>"JOBLANG::Employment_People.Employment_Drivers.DriverEnd",
                    'Khebra_type'=>'JOBLANG::Employment_People.Khebra.type',
                    'khebra_years'=>'JOBLANG::Employment_People.Khebra.years',
                    'acceptall'=>'JOBLANG::Employment_People.acceptall',
                    'tamin'=>'JOBLANG::Employment_people.Tamin.Tamin',
                    'file_name'=>'JOBLANG::Employment_IncludedFiles.Employment_IncludedFiles',
                ];
            }
        }
        if($json){
           foreach ($attributes as $key => $value) {
            $slice = \Str::before($value, '::');
            if($slice == 'JOBLANG'){
                $file='employers.';
            }elseif($slice == 'EMPLANG'){
                $file='employment.';
            }else{
                dd($value);
            }
            $attributes[$key]=\Str::replace($slice."::", $file, $value);
            
           } 
        }else{
            foreach ($attributes as $key => $value) {
                if(\Str::contains($value, '::')){
                    //tr
                    $attributes[$key]=trans($value);
                }
            }
        }
        return $attributes;
    }
}
