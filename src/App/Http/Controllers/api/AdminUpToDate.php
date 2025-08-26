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
    use \Amerhendy\Employment\App\Http\Controllers\api\checkRequests;
    private static $validator,$annonce,$job,$request;
    public static $error,$people,$prePeople,$peopleDB,$pages;
    public function __construct(){
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    //return \AmerHelper::responsedata($result,1,1,'');
    public static function index(Request $request){
        ///work with requests
        self::$request=$request;
        $check=self::AdminUpToDatecheck();
        if($check !== true){return $check;}
        foreach (self::$request->all() as $key => $value) {
            if(\Str::startsWith($key,'Degree_ids') || \Str::startsWith($key,'DegreeTahriry') || \Str::startsWith($key,'DegreeAmaly') || \Str::startsWith($key,'DegreeMeeting')){
                unset(self::$request[$key]);
            }
        }
        self::mainHeader();
        self::formatIds();
        $led=[];
        $results=[];
        //dd(self::$request->input('users'));
        foreach(self::$request->input('users') as $a=>$b){
            ///set Stages
            $setStages=self::SetStages($b['uid']['id']);
            if(!is_numeric($setStages)){
                $results[$a]['errors']=[];
                $results[$a]['errors']['stage']=$setStages;
            }else{$results[$a]['Stage']=$setStages;}
            // set Degrees
            
            if(isset($b['Editorial']) || isset($b['Practical']) || isset($b['Interview'])){
                if(!isset($results[$a]['errors'])){$results[$a]['errors']=[];}
                $results[$a]['Degree']=[];
                $setDegree=self::setDegrees($b);
                if(!is_numeric($setDegree)){
                    $results[$a]['errors']['degree']=$setDegree;
                }else{$results[$a]['Degree']=$setDegree;}
            }
        }
        //dd(self::$request->toArray());
        $led['result']=$results;
        return \AmerHelper::responsedata(self::AdminUpToDateResult(self::$request),1,1,'');
    }
    public static function mainHeader(){
        $header=[];
        $header['publisher']=['text'=>trans('JOBLANG::Employment_Reports.publisher'),'value'=>\Amerhendy\Security\App\Models\User::where('id',self::$request->input('publisher'))->get(['id','name'])->toArray()[0]];
        $header['Stage_id']=['text'=>trans('JOBLANG::Employment_Stages.singular'),'value'=>\Amerhendy\Employment\App\Models\Employment_Stages::where('id',self::$request->input('new_stage'))->get(['id','Text'])->toArray()[0]];
        $header['Status_id']=['text'=>trans('JOBLANG::Employment_Reports.Employment_Status'),'value'=>\Amerhendy\Employment\App\Models\Employment_Status::where('id',self::$request->input('new_res'))->get(['id','Text'])->toArray()[0]];
        $header['Message']=['text'=>trans('JOBLANG::Employment_Reports.UpToDateForm.messageText'),'value'=>self::$request->input('editor1')];
        self::$request->merge(['header'=>$header]);
    }
    public static function formatIds(){
        self::$request->replace( self::$request->except(['_token','new_stage','new_res','publisher','editor1']) );         
        $idsobj=json_decode(self::$request->input('uptoidsTextarea'),true);
        $newd=[];
        foreach ($idsobj as $key => $value) {
            $newcl=[];
            $newcl['uid']=self::returnUserData((int) $value['id']);
            $lstStge=Employment_PeopleNewStage::where('People_id',(int) $value['id'])->get()->last();
            if($lstStge !== null){
                $newcl['LastStage']=['Text'=>$lstStge->Employment_Stages->Text,'id'=>$lstStge->id];
                $newcl['LastStatus']=$lstStge->Employment_Status->Text;
            }
            //get lst stge &stts
            //dd($lstStge->Employment_Status);
            if(isset($idsobj[$key]['Tahriry'])){$newcl['Editorial']=floatval($idsobj[$key]['Tahriry']);}
            if(isset($idsobj[$key]['Amaly'])){$newcl['Practical']=floatval($idsobj[$key]['Amaly']);}
            if(isset($idsobj[$key]['Meeting'])){$newcl['Interview']=floatval($idsobj[$key]['Meeting']);}
            $newd[$key]=$newcl;
        }
        self::$request->merge(['users'=>$newd]);
        self::$request->replace( self::$request->except(['uptoidsTextarea']) ); 
    }
    
    public static function returnUserData($id){
        $did=\Amerhendy\Employment\App\Models\Employment_PeopleNewData::where('People_id',$id)->get()->first();
        if($did){
            return ['id'=>$did->id,'FullName'=>$did->FullName];
        }else{
            $did=\Amerhendy\Employment\App\Models\Employment_People::where('id',$id)->first();
            return ['id'=>$did->id,'FullName'=>$did->FullName];
        }
    }
    public static function SetStages($userData){
        
        $Employment_PeopleNewStage=Employment_PeopleNewStage::where('People_id',$userData)->get();
        if(!count($Employment_PeopleNewStage)){
            return self::InsertEmployment_PeopleNewStage($userData);
        }else{
            $last= $Employment_PeopleNewStage->last();
            if($last->Status_id == self::$request->header['Status_id']['value']['id'] && $last->Stage_id == self::$request->header['Stage_id']['value']['id']){
                return self::UpdateEmployment_PeopleNewStage($userData,$last->id);
            }else{
                
                return self::InsertEmployment_PeopleNewStage($userData);
            }
        }
    }
    public static function InsertEmployment_PeopleNewStage($userData){
        $insert=new Employment_PeopleNewStage();
            $insert->People_id=$userData;
            $insert->Status_id=self::$request->header['Status_id']['value']['id'];
            $insert->Message=self::$request->header['Message']['value'];
            $insert->Stage_id=self::$request->header['Stage_id']['value']['id'];
            try {$insert->save();return $insert->id;} catch (\Exception $e) {return $e->getMessage();}
    }
    public static function UpdateEmployment_PeopleNewStage($userData,$lstid){
        
        $update = Employment_PeopleNewStage::find($lstid);
        $update->Status_id=self::$request->header['Status_id']['value']['id'];
        $update->Message=self::$request->header['Message']['value'];
        
        $update->Stage_id=self::$request->header['Stage_id']['value']['id'];
        $update->updated_at=now();
        try {$update->save();return $update->id;} catch (\Exception $e) {return $e->getMessage();}
    }
    public static function setDegrees($userData){
        $Employment_PeopleDegrees=Employment_PeopleDegrees::where('People_id',$userData)->get();
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
        try {$insert->save();return $insert->id;} catch (\Exception $e) {return $e->getMessage();}
    }
    public static function UpdateEmployment_PeopleDegrees($userData,$id){
        $update = Employment_PeopleDegrees::find($id);
        dd($update);
        $returnData=[$id,[
            'Tahriry'=>[floatval($update->Editorial),$userData->Editorial],
            'Amaly'=>[floatval($update->Practical),$userData->Practical],
            'Meeting'=>[floatval($update->Interview),$userData->Interview],
        ]];
        if(isset($userData->Editorial)){$update->Editorial=$userData->Editorial;}
        if(isset($userData->Practical)){$update->Practical=$userData->Practical;}
        if(isset($userData->Interview)){$update->Interview=$userData->Interview;}
        $update->updated_at=now();
        try {$update->save();return $returnData;} catch (\Exception $e) {dd($e);return $e->getMessage();}
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
                if(\Str::contains($b->Employment_PeopleNewData->FileName,config('Amer.employment.root_disk_name'))){
                    $b->Employment_PeopleNewData->FileName = \Str::replace(config('Amer.employment.root_disk_name'), '', $b->Employment_PeopleNewData->FileName);
                }
                //config('Amer.employment.root_disk_name')
                if($disk->exists($b->Employment_PeopleNewData->FileName)){
                    $filelink[]=$disk->temporaryUrl($b->Employment_PeopleNewData->FileName, now()->addMinutes($minutes));
                }
                
            }
            if(count($filelink)){
                $files[$b->id]=\Str::replace('//', '/', $filelink);
            }
        }
        return $files;
    }
    
    public static function setEmploymentNewData($data){
        $person=$data->Employment_PeopleNewData;
        unset($person->People_id);
        
        $person->job_id=new \stdClass;dd($person);
        $person->job_id->dfd='ds';
        dd($person->job_id,$person->Employment_Job);
        dd($person->job_id);
        $person->job_id->Code=$person->Employment_Job->Code;
        $person->job_id->Slug=$person->Employment_Job->Slug;
        
        //$person->job_id->Mosama_JobNames=$person->Employment_Job->Mosama_JobNames->text;
        dd($person->Employment_Job->Mosama_JobNames);
        $person->job_id->Mosama_Groups=$person->Employment_Job->Mosama_JobNames->Mosama_Groups->text;
        $person->job_id->Mosama_JobTitles=$person->Employment_Job->Mosama_JobNames->Mosama_JobTitles->text;
        $person->job_id->Mosama_Degrees=$person->Employment_Job->Mosama_JobNames->Mosama_Degrees->text;
        
        unset($person->Employment_Job);
        $person->FullName=$person->FullName; 
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
        $person->Khebra=$person->khebraToStr;
        $person->Education_id=$person->Employment_Education->text;
        return $person;
    }
    public static function getApplydata($person){
        $data=new \stdClass();
            $data->id=$person['id'];
            $data->annonce_id=$person['annonce_id'];$data->job_id=$person['job_id'];
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
            return $person->only(['id','annonce_id','job_id','Sex','Fname','Sname','Tname','Lname','LiveGov','LiveCity','LiveAddress','BornGov','BornCity','BirthDate','AgeYears','AgeMonths','AgeDays','ConnectMobile','ConnectEmail','ConnectLandline','Health_id','MaritalStatus_id','Arm_id','Ama_id','Tamin','Khebra','Education_id','EducationYear','DriverDegree','DriverStart','DriverEnd','FileName','created_at','NID']);
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
        }elseif (self::$request->input('section') == 'file') {
            return self::FileSection();
        }
        //dd(self::$request->all());
        return(self::$people);
    }
}
