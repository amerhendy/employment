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
            'annonceSlug'=>['required','exists:Employment_StartAnnonces,Slug'],
            'jobSlug'=>'required|exists:Employment_Jobs,Slug',
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
        
        self::$nid=trim(self::$request->input('nid'));
        self::$annonce= Employment_StartAnnonces::with('Employment_Stages')->where('Slug',self::$request->input('annonceSlug'))->first();
        if(!self::$annonce){
            $validator->errors()->add('jobSlug',trans('JOBLANG::apply.errors.required',[trans('JOBLANG::Employment_StartAnnonces.plural')]));
            return response()->json(['error'=>$validator->errors()]);
        }
        self::$job=Employment_Jobs::where('Slug',self::$request->input('jobSlug'))->where('Annonce_id',self::$annonce->id)->first();
        
        if(!self::$job){
            $validator->errors()->add('jobSlug',trans('JOBLANG::apply.Employment_Jobs.selectJob'));
            return response()->json(['error'=>$validator->errors()]);
        }
        if(self::$request->input('page') !== 'create'){
            //get job action
            $action=self::$annonce->Employment_Stages->functionName->Function;
            if($action == 'complete'){
                $people=Employment_People::where('NID',self::$nid)->where('Annonce_id',self::$annonce->id)->with('Employment_PeopleNewStage')->first();
                $lststge=$people->Employment_PeopleNewStage->last()->Stage_id;
                $fun=Employment_Stages::find($lststge)->functionName->Function;
                if(!in_array($fun,[self::$request->input('page'),$action])){$validator->errors()->add('page',trans('JOBLANG::Employment_Stages.page'));}
            }elseif($action == 'create'){
                $people=Employment_People::where('NID',self::$nid)->where('Annonce_id',self::$annonce->id)->first();
                if($people){
                    $validator->errors()->add('nid',trans('JOBLANG::Apply.nid_Already_Exists'));
                }
            }elseif($action == 'search'){}
            
            
        }else{
            if(self::$annonce->Employment_Stages->functionName->Function !== self::$request->input('page')){$validator->errors()->add('page',trans('JOBLANG::Employment_Stages.page'));}
        }
        //٢٩٦٠٧٠٥١٢٠٣٠٠٤
        if(count($validator->errors())){
            
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        return true;
    }
    public static function ShowJobReq(){
        $request=self::$request;
        $request['page']=$request['actiontype'];

        $rules=[
            'annonceSlug'=>['required','exists:Employment_StartAnnonces,Slug'],
            'jobSlug'=>'required|exists:Employment_Jobs,Slug',
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
        $rules=[
            'actiontype'=>['required','in:create,complete'],
            'apply_date'=>['required','date'],
            'annonceSlug'=>['required','exists:Employment_StartAnnonces,Slug'],
            'jobSlug'=>['required','exists:Employment_Jobs,Slug'],
            'Fname'=>['required','string','min:3'],
            'Sname'=>['required','string','min:3'],
            'Tname'=>['required','string','min:3'],
            'Lname'=>['required','string','min:3'],
            'NID'=>['required','numeric','digits:14'],
            'BirthDate'=>['required','date'],
            'Sex'=>['required','numeric','in:0,1'],
            'AgeYears'=>['required','numeric','between:15,60'],
            'AgeMonths'=>['required','numeric','between:0,12'],
            'AgeDays'=>['required','numeric','between:0,30'],
            'BornGov'=>['required','numeric','exists:Governorates,id'],
            'BornCity'=>['required','numeric','exists:Cities,id'],
            'LiveGov'=>['required','numeric','exists:Governorates,id'],
            'LiveCity'=>['required','numeric','exists:Cities,id'],
            'LiveAddress'=>['required','string'],
            'ConnectLandline'=>['required','numeric','digits_between:6,11'],
            'ConnectMobile'=>['required','numeric','digits:11'],
            'ConnectEmail'=>['required','email:rfc,dns,spoof'],
            'Health_id'=>['required','numeric','exists:Employment_Health,id'],
            'MaritalStatus_id'=>['required','numeric','exists:Employment_MaritalStatus,id'],
            'Arm_id'=>['required','numeric','exists:Employment_Army,id'],
            'Ama_id'=>['required','numeric','exists:Employment_Ama,id'],
            'Education_id'=>['required','numeric','exists:Mosama_Educations,id'],
            'EducationYear'=>['required','numeric','digits:4'],
            'accept_driver'=>['required','numeric','in:0,1'],
            'DriverDegree'=>['exclude_if:accept_driver,1','required','numeric','exists:Employment_Drivers,id'],
            'DriverStart'=>['exclude_if:accept_driver,1','required','date'],
            'DriverEnd'=>['exclude_if:accept_driver,1','required','date','after:DriverStart'],
            'Khebra_type'=>['required','numeric','in:0,1,2'],
            'Khebra'=>['required','numeric','min:0'],
            'acceptall'=>['required','accepted'],
            'Tamin'=>['required','numeric','min:0'],
            'uploades'=>['required','file','mimetypes:application/pdf','mimes:pdf','max:8192'],
            'PeopleNewStageId'=>['exclude_if:actiontype,create','required','numeric',Rule::exists('Employment_PeopleNewStage','id')->where('People_id',request()->uid)],
            'StageId'=>['exclude_if:actiontype,create','required','numeric',Rule::exists('Employment_PeopleNewStage','Stage_id')->where('People_id',request()->uid)],
        ];
        $attributes=[
            'actiontype'=>trans('JOBLANG::Employment_People.NID'),
            'apply_date'=>trans('JOBLANG::Employment_People.applyDate'),
            'annonceSlug'=>trans('JOBLANG::Employment_People.NID'),
            'job_id'=>trans('JOBLANG::Employment_People.NID'),
            'Fname'=>trans('JOBLANG::Employment_People.Fname'),
            'Sname'=>trans('JOBLANG::Employment_People.Sname'),
            'Tname'=>trans('JOBLANG::Employment_People.Tname'),
            'Lname'=>trans('JOBLANG::Employment_People.Lname'),
            'NID'=>trans('JOBLANG::Employment_People.NID'),
            'BirthDate'=>trans('JOBLANG::Employment_People.BirthDate'),
            'Sex'=>trans('JOBLANG::Employment_People.Sex.Sex'),
            'AgeYears'=>trans('JOBLANG::Employment_People.Age.AgeYears'),
            'AgeMonths'=>trans('JOBLANG::Employment_People.Age.AgeMonths'),
            'AgeDays'=>trans('JOBLANG::Employment_People.Age.AgeDays'),
            'BornGov'=>trans('JOBLANG::Employment_people.bornPlace.Governorate'),
            'BornCity'=>trans('JOBLANG::Employment_people.bornPlace.City'),
            'LiveGov'=>trans('JOBLANG::Employment_People.LivePlace.Governorator'),
            'LiveCity'=>trans('JOBLANG::Employment_People.LivePlace.City'),
            'LiveAddress'=>trans('JOBLANG::Employment_People.LivePlace.Address'),
            'ConnectLandline'=>trans('JOBLANG::Employment_people.Connection.LandLine'),
            'ConnectMobile'=>trans('JOBLANG::Employment_people.Connection.Mobile'),
            'ConnectEmail'=>trans('JOBLANG::Employment_people.Connection.Email'),
            'Health_id'=>trans('JOBLANG::Employment_Health.Employment_Health'),
            'MaritalStatus_id'=>trans('JOBLANG::Employment_MaritalStatus.Employment_MaritalStatus'),
            'Arm_id'=>trans('JOBLANG::Employment_Army.Employment_Army'),
            'Ama_id'=>trans('JOBLANG::Employment_Ama.Employment_Ama'),
            'Education_id'=>trans('EMPLANG::Mosama_Educations.Mosama_Educations'),
            'EducationYear'=>trans('JOBLANG::Employment_People.Mosama_Educations.year'),
            'accept_driver'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree"),
            'DriverDegree'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverDegree"),
            'DriverStart'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverStart"),
            'DriverEnd'=>trans("JOBLANG::Employment_People.Employment_Drivers.DriverEnd"),
            'Khebra_type'=>trans('JOBLANG::Employment_People.Khebra.type'),
            'Khebra'=>trans('JOBLANG::Employment_People.Khebra.years'),
            'acceptall'=>trans('JOBLANG::Employment_People.acceptall'),
            'Tamin'=>trans('JOBLANG::Employment_people.Tamin.Tamin'),
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
        
        self::$annonce= Employment_StartAnnonces::with('Employment_Stages')->where('Slug',self::$request->input('annonceSlug'))->first();
        if(!self::$annonce){
            $validator->errors()->add('annonceSlug',trans('JOBLANG::apply.errors.required',[trans('JOBLANG::Employment_StartAnnonces.plural')]));
        }else{
            self::$job=Employment_Jobs::where('Slug', self::$request->input('jobSlug'))->where('Annonce_id',self::$annonce->id)->first();
            if(!self::$job){
                $validator->errors()->add('jobSlug',trans('JOBLANG::apply.Employment_Jobs.selectJob'));
            }
        }
        if(self::$request->input('actiontype') !== 'create'){
            //get job action
            $action=self::$annonce->Employment_Stages->functionName->Function;
            if($action == 'complete'){
                $people=Employment_People::where('id',self::$request->input('uid'))->where('NID',self::$nid)->where('Annonce_id',self::$annonce->id)->with('Employment_PeopleNewStage')->first();
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
                $people=Employment_People::where('NID',self::$nid)->where('Annonce_id',self::$annonce->id)->first();
                if($people){
                    $validator->errors()->add('nid',trans('JOBLANG::Apply.nid_Already_Exists'));
                }
            }elseif($action == 'search'){}
            
            
        }else{
            if(self::$annonce->Employment_Stages->functionName->Function !== self::$request->input('actiontype')){$validator->errors()->add('actiontype',trans('JOBLANG::Employment_Stages.page'));}
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