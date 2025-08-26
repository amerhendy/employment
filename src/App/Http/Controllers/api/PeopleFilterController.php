<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Support\Collection;
use Amerhendy\Amer\App\Models\Governorates;
use Amerhendy\Amer\App\Models\Cities;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Models\Employment_PeopleNewStage;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Jobs as jobs;
use \Amerhendy\Employment\App\Models\Employment_Stages;
use Amerhendy\Employment\App\Models\Employment_Status;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Employment\App\Models\Employment_Seatings;
use \Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class PeopleFilterController extends AmerController
{
    public static $request;
    public static $elequent;
    public static $annonceSection='AnnonceAnnonce';
    public static $GrievanceSection='GrievanceAnnonce';
    public static $SeatingSection='SeatingAnnonce';
    private static function prepareforSeating(){
        //dd(config('Amer.employment.examStages'));
        $data=self::$elequent;
        $request=self::$request;
        $after=[];
        foreach($data as $a=>$b){
            $stages=new PeopleStagesController($b);
            $stages=$stages::get('lastStage',null,'array');
            $jobs=PeopleJobsController::lastJobs($b);
            $afterclass=new \stdClass();
            $afterclass->id=$b['id'];
            $afterclass->Stage_id=(int) $stages->StageId;
            $afterclass->Status_id=(int) $stages->Result;
            $afterclass->job_id=$jobs;
            $after[]=$afterclass;
        }
        $collection=collect($after);
        $sorted = $collection->sortBy('id');
        return $sorted
                ->whereIn('Stage_id',$request->input('Stage_id'))
                ->whereIn('Status_id',$request['Status_id'])
                ->whereIn('job_id',$request->input('job_id'));
    }
    private static function prepareforAnnonce(){
        $data=self::$elequent;
        $request=self::$request;
        $after=[];
        foreach($data as $a=>$b){
            $stages=new PeopleStagesController($b);
            $stages=$stages::get('lastStage',null,'array');
            $jobs=PeopleJobsController::lastJobs($b);
            $afterclass=new \stdClass();
            $afterclass->id=$b['id'];
            $afterclass->Stage_id=(int) $stages->StageId;
            $afterclass->Status_id=(int) $stages->Result;
            $afterclass->job_id=$jobs;
            $after[]=$afterclass;
        }
        $collection=collect($after);
        $sorted = $collection->sortBy('id');
        if(($request['Stage_id'] == null) || ($request['Stage_id'] == 'null') || ($request['Stage_id'] == '')){
            $stagws=PeopleStagesController::allStagesIds();
            $request->merge(['Stage_id'=>$stagws]);
        }else{$request['Stage_id']=$request['Stage_id'];}
        return $collection
                ->whereIn('Stage_id',$request->input('Stage_id'))
                ->whereIn('Status_id',$request['Status_id'])
                ->whereIn('job_id',$request->input('job_id'));
    }
    private static function prepareforGrievance(){
        $after=[];
        foreach(self::$elequent as $a=>$b){
            $stages=new PeopleStagesController($b);
            $stages=$stages->get('Grievance');
            $stages=collect($stages);
            $sort=$stages->last();
            $jobs=PeopleJobsController::lastJobs($b);
            $afterclass=new \stdClass();
            $afterclass->id=$b['id'];
            $afterclass->Stage_id=(int) $sort->StageId;
            $afterclass->Status_id=(int) $sort->Result;
            $afterclass->GrievanceType= $sort->Type;
            $afterclass->job_id=$jobs;
            $after[]=$afterclass;
        }
        $collection=collect($after);
        $sorted = $collection->sortBy('id');
        if((self::$request['job_id'] == null) || (self::$request['job_id'] == '')){
            self::$request->merge(['job_id'=>PeopleJobsController::allJobsIsd(self::$request['annonce_id'])]);
        }

        if((self::$request['GrievanceType'] == null) || (self::$request['GrievanceType'] == 'null') || (self::$request['GrievanceType'] == '')){
            $stagws=['Grievance_Practical','Grievance_apply','WritingGrievance'];
            self::$request->merge(['GrievanceType'=>$stagws]);
        }else{self::$request['GrievanceType']=self::$request['GrievanceType'];}
/*
        dd(
            self::$request->input('GrievanceType'),
            $collection
            ->whereIn('job_id',self::$request->input('job_id'))
            ->whereIn('GrievanceType',self::$request->input('GrievanceType'))
            ->whereIn('Status_id',self::$request->input('Status_id')),
            $collection
        );**/
        return $collection
                ->whereIn('job_id',self::$request->input('job_id'))
                ->whereIn('GrievanceType',self::$request->input('GrievanceType'))
                ->whereIn('Status_id',self::$request->input('Status_id'));
    }
    private static function query(){
        $request=self::$request;
        $annonceInput=$request->input('annonce_id');
        //check annonce first
        $data= Employment_People::with(['Employment_PeopleNewStage','Employment_PeopleNewData','Employment_Grievance','Employment_StartAnnonces','Employment_Job',
        'Employment_Education','Employment_Stages','Employment_PeopleDegrees','Employment_Seatings']);
        $data=$data->whereHas('Employment_StartAnnonces',function($query)use($annonceInput){
            if(isset($annonceInput)){
                if(($annonceInput <> null) || ($annonceInput <> '')){
                    $query->where('annonce_id',$annonceInput);
                }
            }
        });
        if($request->input('Section') == self::$GrievanceSection){
            $data=$data->whereHas('Employment_Grievance');
        }
        if($request->input('Section') == self::$SeatingSection){
            $data=$data->whereHas('Employment_Seatings');
        }
        $data=$data->get();
        self::$elequent=$data;
    }
    private static function prepareAnnonceRequest($request,$errors){
        $Stage_id=self::GetRequestElements($request,'Stage_id','annonce_id',$errors);
        $Status_id=self::GetRequestElements($request,'Status_id','annonce_id',$errors);
        $job_id=self::GetRequestElements($request,'job_id','annonce_id',$errors);
        if(is_object($Stage_id)){return $Stage_id;}
        if(is_object($Status_id)){return $Status_id;}
        if(is_object($job_id)){return $job_id;}
                $data=[
                    'annonce_id'=>$request->input('annonce_id'),
                    'job_id'=>$job_id,
                    'Status_id'=>$Status_id,
                    'Stage_id'=>$Stage_id,
                    'Section'=>self::$annonceSection
                ];
                $request->request->replace($data);
                self::$request=$request;
                
                return true;
    }
    private static function prepareGrievanceRequest($request,$errors){
            $GrievanceType=self::GetRequestElements($request,'GrievanceType','annonce_id',$errors);
            $GrievanceJob=self::GetRequestElements($request,'job_id','annonce_id',$errors);
            $GrievanceResult=self::GetRequestElements($request,'GrievanceResult','annonce_id',$errors);
            if(is_object($GrievanceType)){return $GrievanceType;}
            if(is_object($GrievanceJob)){return $GrievanceJob;}
            if(is_object($GrievanceResult)){return $GrievanceResult;}
            $data=[
                'annonce_id'=>$request->input('annonce_id'),
                'job_id'=>$GrievanceJob,
                'Status_id'=>$GrievanceResult,
                'Stage_id'=>[null],
                'GrievanceType'=>$GrievanceType,
                'Section'=>self::$GrievanceSection
            ];
            $request->request->replace($data);
            self::$request=$request;
            return true;
    }
    private static function prepareSeatingsRequest($request,$errors){
        $Stage_id=config('Amer.employment.examStages')??[14,13,7];
        $job_id=self::GetRequestElements($request,'job_id','annonce_id',$errors);
        if(is_object($Stage_id)){return $Stage_id;}
        if(is_object($job_id)){return $job_id;}
        $data=[
            'annonce_id'=>$request->input('annonce_id'),
            'job_id'=>$job_id,
            'Stage_id'=>$Stage_id,
            'Status_id'=>[1,4],
            'Section'=>self::$SeatingSection
        ];
        $request->request->replace($data);
        self::$request=$request;
        return true;
    }
    private static function GetRequestElements($request,$wanted,$annonceInput,$errors){
        if(!$request->has($wanted)){
            $errors->message=trans("JOBLANG::Employment_Reports.errors.pleaseSelectAnnonce"); $errors->line=__LINE__; $errors->wanted=$wanted; return $errors;
        }
        $annonceElem='annonce_id';
        if($wanted == 'job_id'){
            $ann=Employment_StartAnnonces::where('Slug',$request[$annonceElem])->get('id')->first();
            if(!$ann){
                $errors->message=trans("JOBLANG::Employment_Reports.errors.pleaseSelectAnnonce"); $errors->line=__LINE__; $errors->wanted=$wanted; return $errors;
            }else{
                $request->merge(['annonce_id'=>$ann->id]);
                self::$request=$request;
            }
            $allData=PeopleJobsController::allJobsIsd($request[$annonceElem]);
            
        }elseif($wanted == 'Stage_id'){
            $allData=PeopleStagesController::allStagesIds();
        }elseif($wanted == 'Status_id'){
            $allData=PeopleStatusController::allStatusIsd();
        }elseif($wanted == 'GrievanceType'){
            $allData=['Grievance_Practical','Grievance_apply','WritingGrievance'];
        }elseif($wanted == 'GrievanceResult'){
            $allData=[1,2];
        }
        $WantedVal=$request->input($wanted);
        if(is_array($WantedVal)){
            if(count($WantedVal) == 0){
                $WantedVal=$allData;
            }else{
                if($WantedVal[0] == null){
                    $WantedVal=$allData;
                }
            }
        }else{
            if($WantedVal == null || $WantedVal == 'null' || $WantedVal == '' || !is_numeric($WantedVal)){
                $WantedVal=$allData;
            }else{
                $WantedVal=[$WantedVal];
            }
            
        }
        return $WantedVal;
        dd($request->all(),self::$SeatingSection,$wanted);
    }
    private static function perpareRequests($request){
        $errors=new \stdClass();
        $errors->number=402;
        $errors->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
        if(!$request->has('section')){
            $errors->message=trans("JOBLANG::Employment_Reports.errors.PleaseSelectSection"); $errors->line=__LINE__; return $errors;
        }
        if(!$request->has('annonce_id')){
            $errors->message=trans("JOBLANG::Employment_Reports.errors.pleaseSelectAnnonce"); $errors->line=__LINE__; return $errors;
        }else if($request->input('annonce_id') == ''){
            $errors->message=trans("JOBLANG::Employment_Reports.errors.pleaseSelectAnnonce"); $errors->line=__LINE__; return $errors;
        }
        $sections=[self::$GrievanceSection,self::$annonceSection,self::$SeatingSection];
        if(!in_array($request->input('section'),$sections)){$errors->message=trans("JOBLANG::Employment_Reports.errors.PleaseSelectSection"); $errors->line=__LINE__; return $errors;}
        $section=$request->input('section');
        if($section == self::$annonceSection){
            $retu=self::prepareAnnonceRequest($request,$errors);
        }elseif($section == self::$GrievanceSection){
            $retu=self::prepareGrievanceRequest($request,$errors);
        }elseif($section == self::$SeatingSection){
            $retu=self::prepareSeatingsRequest($request,$errors);
        }
        if(is_object($retu)){
            return $retu;
        }
        return true;
        //Stage_id,Status_id,job_id
    }
    function Counts(Request $request){
        $request=self::perpareRequests($request);
        if($request !== true){if($request->number){return \AmerHelper::responseError($request,$request->number);}}
        $request=self::$request;
        $data=self::query();
        if($request->Section == self::$GrievanceSection){
            $data=self::prepareforGrievance();
        }
        if($request->Section == self::$annonceSection){
            $data=self::prepareforAnnonce();    
        }
        if($request->Section == self::$SeatingSection){
            $data=self::prepareforSeating();
        }
        if(count($data) == 0){
            return \AmerHelper::responsedata([],null,0,0);
        }else{
            $data=collect($data);
            $abo=$data->map(function ($item, int $key) {
                return $item->id;
            });
            $arr=[];
            foreach ($abo as $key => $value) {
                $arr[]=$value;
            }
            return \AmerHelper::responsedata($arr,null,count($arr),0);
        }
    }
    
}
