<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Support\Collection;
use Amerhendy\Amer\App\Models\Governorates;
use Amerhendy\Amer\App\Models\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Stages;
use \Amerhendy\Employment\App\Models\Employment_PeopleNewStage;
use \Amerhendy\Employment\App\Models\Employment_PeopleDegrees;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use Amerhendy\Employment\App\Models\Employment_Status;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use Laravel\Passport\Passport;  //import Passport here
class AdminUpToDate extends AmerController
{
    use \Amerhendy\Employment\App\Http\Controllers\api\applyTrait;
    use \Amerhendy\Employment\App\Http\Controllers\api\printTrait;
    private static $validator,$annonce,$job,$request;
    public static $error,$people,$prePeople,$peopleDB;
    public function __construct(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    //return \AmerHelper::responsedata($result,1,1,'');
    public static function index(Request $request){
        if(!$request->has('_token') || !$request->has('uptoids') || !$request->has('publisher') || !$request->has('new_stage') || !$request->has('new_res') || !$request->has('editor1'))
        {return response()->json(['result'=>'error'],209);}
        foreach ($request->all() as $key => $value) {
            if(\Str::startsWith($key,'Degree_ids') || \Str::startsWith($key,'DegreeTahriry') || \Str::startsWith($key,'DegreeAmaly') || \Str::startsWith($key,'DegreeMeeting')){
                unset($request[$key]);
            }
        }
        $ids=self::formatIds($request);
        $results=[];
        foreach($ids as $a=>$b){
            $results[$a]=['id'=>$b['id'],'errors'=>[]];
            ///set Stages
            $setStages=self::SetStages($b);
            if($setStages !== true){
                $results[$a]['errors']['stage']=$setStages;
            }
            // set Degrees
            if(isset($b['Tahriry']) || isset($b['Amaly']) || isset($b['Meeting'])){
                $setDegree=self::setDegrees($b);
                if($setDegree !== true){
                    $results[$a]['errors']['degree']=$setDegree;
                }
            }
            
            //return $b;
        }
        return $results;
    }
    public static function formatIds(Request $request){
        if(!\AmerHelper::isJson($request->input('uptoids'))){return response()->json(['result'=>'error'],209);}
        $idsobj=json_decode($request->input('uptoids'),true);
        foreach ($idsobj as $key => $value) {
            $idsobj[$key]['publisher']=$request->input('publisher');
            $idsobj[$key]['Stage_id']=$request->input('new_stage');
            $idsobj[$key]['Status_id']=$request->input('new_res');
            $idsobj[$key]['Message']=$request->input('editor1');
        }
        return $idsobj;
    }
    public static function SetStages($userData){
        $People_id=$userData['id'];
        $Employment_PeopleNewStage=Employment_PeopleNewStage::where('People_id',$People_id)->get();
        if(!count($Employment_PeopleNewStage)){
            return self::InsertEmployment_PeopleNewStage($userData);
        }else{
            $last= $Employment_PeopleNewStage->last();
            if($last->Status_id == $userData['Status_id'] && $last->Stage_id == $userData['Stage_id']){
                $userData['db_id']= $last->id;
                return self::UpdateEmployment_PeopleNewStage($userData);
            }else{
                return self::InsertEmployment_PeopleNewStage($userData);
            }
        }
    }
    public static function InsertEmployment_PeopleNewStage($userData){
        $insert=new Employment_PeopleNewStage();
            $insert->People_id=$userData['id'];
            $insert->Status_id=$userData['Status_id'];
            $insert->Message=$userData['Message'];
            $insert->Stage_id=$userData['Stage_id'];
            try {$insert->save();return true;} catch (\Exception $e) {return $e->getMessage();}
    }
    public static function UpdateEmployment_PeopleNewStage($userData){
        $update = Employment_PeopleNewStage::find($userData['db_id']);
        $update->Status_id=$userData['Status_id'];
        $update->Message=$userData['Message'];
        $update->Stage_id=$userData['Stage_id'];
        $update->updated_at=now();
        try {$update->save();return true;} catch (\Exception $e) {return $e->getMessage();}
    }
    public static function setDegrees($userData){
        $Employment_PeopleDegrees=Employment_PeopleDegrees::where('People_id',$userData['id'])->get();
        if(!count($Employment_PeopleDegrees)){
            return self::InsertEmployment_PeopleDegrees($userData);
        }else{
            $last= $Employment_PeopleDegrees->last();
            return self::UpdateEmployment_PeopleDegrees($userData,$last->id);
        }
    }
    public static function InsertEmployment_PeopleDegrees($userData){
        if(
            !isset($userData['Tahriry']) && !isset($userData['Amaly']) && !isset($userData['Meeting'])
            ){ return 'error';}
        $insert=new Employment_PeopleDegrees();
        $insert->People_id= $userData['id'];
        //get annonce
        //check if tahriry exists
        if(isset($userData['Tahriry'])){$insert->Editorial=$userData['Tahriry'];}
        if(isset($userData['Amaly'])){$insert->Practical=$userData['Amaly'];}
        if(isset($userData['Meeting'])){$insert->Interview=$userData['Meeting'];}
        $insert->created_at=now();
        try {$insert->save();return true;} catch (\Exception $e) {return $e->getMessage();}
    }
    public static function UpdateEmployment_PeopleDegrees($userData,$id){
        $update = Employment_PeopleDegrees::find($id);
        if(isset($userData['Tahriry'])){$update->Editorial=$userData['Tahriry'];}
        if(isset($userData['Amaly'])){$update->Practical=$userData['Amaly'];}
        if(isset($userData['Meeting'])){$update->Interview=$userData['Meeting'];}
        $update->updated_at=now();
        try {$update->save();return true;} catch (\Exception $e) {dd($e);return $e->getMessage();}
    }
    public static function downloadZip(Request $request){
        if(!$request->has('ids')){return response()->json(['result'=>'error'],209);}
        $ids=$request->input('ids');
        if(!$request->has('Minutes')){$minutes=1;}else{$minutes=$request->input('Minutes');}
        
        if(! \AmerHelper::isJson($ids)){return response()->json(['result'=>'error'],209);}
        $ids=json_decode($ids,true);
        $people=Employment_People::with('Employment_PeopleNewData')->whereIn('id',$ids)->get();
        $files=[];
        $disk=\Storage::disk(config('Amer.employment.root_disk_name'));
        foreach($people as $a=>$b){
            $filelink=[];
            if($disk->exists($b->FileName)){
                $filelink[]=$disk->temporaryUrl($b->FileName, now()->addMinutes($minutes));
            }
            
            if($b->Employment_PeopleNewData !== null){
                if($disk->exists($b->Employment_PeopleNewData->FileName)){
                    $filelink[]=$disk->temporaryUrl($b->Employment_PeopleNewData->FileName, now()->addMinutes($minutes));
                }
                
            }
            if(count($filelink)){
                $files[$b->id]=$filelink;
            }
        }
        return $files;
    }
    public static function khebraToStr($khebra){
        if(gettype($khebra) == 'string'){
            if(\AmerHelper::isJson($khebra)){
                $khebra=json_decode($khebra,true);
                if(!count($khebra)){return Null;}
                if(is_array($khebra[0])){
                    foreach($khebra as $a=>$b){
                        $khebra[$a]=self::khebraToStr($b);
                    }
                    return $khebra;
                }
            }else{
                return null;
            }
        }else{
            if(array_key_exists(0,$khebra)){
                if(is_array($khebra[0])){
                    foreach($khebra as $a=>$b){
                        $khebra[$a]=self::khebraToStr($b);
                    }
                }
            }
        }
            $keys=array_keys($khebra);
            $time=$khebra[$keys[0]];
            
            if(isset($keys[1])){
                $type=$khebra[$keys[1]];
            }else{
                $type=$keys[0];
            }
            if($time == 0){
                $khebra=trans('EMPLANG::Mosama_Experiences.enum_2');
            }else{
                if($type == 1){
                    $type=trans('EMPLANG::Mosama_Experiences.enum_0');
                }else{
                    $type=trans('EMPLANG::Mosama_Experiences.enum_1');
                }
                $khebra=\Str::replaceArray('?',[$type,$time],trans('JOBLANG::Employment_Reports.printForm.khebra'));
                
            }
        return $khebra;
    }
    public static function setEmploymentNewData($data){
        $person=$data->Employment_PeopleNewData;
        
        unset($person->People_id);
        
        $person->Job_id=[
                                    'Code'=>$person->Employment_Job->Code
                                    ,'Mosama_JobNames'=>$person->Employment_Job->Mosama_JobNames->text
                                    ,'Mosama_Groups'=>$person->Employment_Job->Mosama_JobNames->Mosama_Groups->text
                                    ,'Mosama_JobTitles'=>$person->Employment_Job->Mosama_JobNames->Mosama_JobTitles->text
                                    ,'Mosama_Degrees'=>$person->Employment_Job->Mosama_JobNames->Mosama_Degrees->text];
        unset($person->Employment_Job);
        
            $person->NID=$data->NID;
            $person->BirthDate=$data->BirthDate;
            if($data->Sex == '1'){$person->Sex=trans('JOBLANG::Employment_People.Sex.Male');}else{$person->Sex=trans('JOBLANG::Employment_People.Sex.Female');}
            $person->LiveGov=$person->LiveGovernorates->Name;
            $person->LiveCity=$person->LiveCities->Name;
            $person->BornGov=$person->BornGovernorates->Name;
            $person->BornCity=$person->BornCities->Name;
            $person->Health_id=$person->Employment_Health->Text;
            $person->MaritalStatus_id=$person->Employment_MaritalStatus->Text;
            $person->Arm_id=$person->Employment_Army->Text;
            $person->Ama_id=$person->Employment_Ama->Text;
            $person->Khebra=self::khebraToStr($person->Khebra);
            $person->Education_id=$person->Employment_Education->text;
            return $person;
            
            
        return $data;
    }
    public static function getApplydata($person){
        $data=new \stdClass();
            $data->id=$person['id'];
            $data->Annonce_id=$person['Annonce_id'];$data->Job_id=$person['Job_id'];
            $data->NID=$person['NID'];$data->Sex=$person['Sex'];
            $data->Fname=$person['Fname'];$data->Sname=$person['Sname'];$data->Tname=$person['Tname'];$data->Lname=$person['Lname'];
            $data->LiveGov=$person['LiveGov'];$data->LiveCity=$person['LiveCity'];$data->LiveAddress=$person['LiveAddress'];
            $data->BornGov=$person['BornGov'];$data->BornCity=$person['BornCity'];
            $data->BirthDate=$person['BirthDate'];$data->AgeYears=$person['AgeYears'];$data->AgeMonths=$person['AgeMonths'];$data->AgeDays=$person['AgeDays'];
            $data->ConnectLandline=$person['ConnectLandline'];$data->ConnectMobile=$person['ConnectMobile'];$data->ConnectEmail=$person['ConnectEmail'];
            $data->Health_id=$person['Health_id'];$data->MaritalStatus_id=$person['MaritalStatus_id'];$data->Arm_id=$person['Arm_id'];$data->Ama_id=$person['Ama_id'];
            $data->Tamin=$person['Tamin'];
            $data->Khebra=$person['Khebra'];
            $data->Education_id=$person['Education_id'];
            $data->EducationYear=$person['EducationYear'];
            $data->Stage_id=$person['stageList']->apply;
            $data->Result=$person['stageList']->apply->Result;
            $data->Message=$person['stageList']->apply->Message;
            $data->DriverDegree=$person['DriverDegree'];
            $data->DriverStart=$person['DriverStart'];
            $data->DriverEnd=$person['DriverEnd'];
            $data->created_at=$person['created_at'];
        
        return $data;
    }
    public static function getDownloads($person){
        $data=new \stdClass;
        $FileName=[];
        $FileName[]=$person['FileName'];
        if($person['Employment_PeopleNewData']){
            $FileName[]=$person['Employment_PeopleNewData']['FileName'];
        }
        $files=[];
        $disk=\Storage::disk(config('Amer.employment.root_disk_name'));
        $minutes=30;
        $filelink=[];
        foreach($FileName as $a=>$b){
            //if($person['NID'] == '29901252937958'){}
            if($disk->exists($b)){
                $filelink[]=Amerurl("SHURL/".\AmerHelper::createShortUrl($disk->temporaryUrl($b, now()->addMinutes($minutes)),30));
            }
        }
        return $filelink;
    }
    
    public static function getGrievance($person,$type){
        $Data = new \stdClass();
        if(count($person->Employment_Grievance) == 0){
            return [];
        }else{
                $HtmlGrievance=new PeopleStagesController($person);
                $HtmlGrievance=$HtmlGrievance::get('HtmlGrievance',null,'array');
                $type='Grievance_'.$type;
                $list = array_filter($HtmlGrievance,function($key) use($type){return $key->Type == $type;}, ARRAY_FILTER_USE_BOTH);
                $newlist=[];
                foreach ($list as $key => $value) {
                    
                    $carbon =new \Carbon\Carbon($value->created_at);
                    $carbon =$carbon->locale('ar')->translatedFormat('l j F Y H:i:s');
                    $value->created_at=$carbon;
                    $newlist[]=$value;
                }
                return $newlist;
        }
    }
    public static function getLastEntry($person){
        if($person['Employment_PeopleNewData']){
            $person['Employment_PeopleNewData']['AgeYears']=$person->AgeYears;
            $person['Employment_PeopleNewData']['AgeMonths']=$person->AgeMonths;
            $person['Employment_PeopleNewData']['AgeDays']=$person->AgeDays;
            return $person['Employment_PeopleNewData']->toArray();
        }else{
            return $person->only(['id','Annonce_id','Job_id','Sex','Fname','Sname','Tname','Lname','LiveGov','LiveCity','LiveAddress','BornGov','BornCity','BirthDate','AgeYears','AgeMonths','AgeDays','ConnectMobile','ConnectEmail','ConnectLandline','Health_id','MaritalStatus_id','Arm_id','Ama_id','Tamin','Khebra','Education_id','EducationYear','DriverDegree','DriverStart','DriverEnd','FileName','created_at','NID']);
        }
    }
    
    
    
    
    
        
    /**
     * PrintForm
     *
     * @param  mixed $request
     * @return void
     */
    public static function PrintForm(Request $request){
        self::$request=$request;
        // check the form requirements
        $check=self::checkPrintForm();
        if($check !== true){
            return $check;
        }
        //create Query
        $query=self::query();
        if($query !== true){
            return $query;
        }
        //convert data to readable
        self::DBToText();
        self::printWanted();
        if(self::$request->input('section') == 'Seatings'){
            if(self::$request->input('type') == 'Table'){
                if(self::$request->input('table') == 'tableForSign'){}
                return self::createTicketTableForSign();
            }
            return self::createTicket();
            //$a= View::make("Employment::admin.seatings.ticket",['data'=>$peopleData]);
            //return $a;
        }
        return(self::$people);
    }
}
