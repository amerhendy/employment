<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Amerhendy\Amer\App\Models\Governorates;
use Amerhendy\Amer\App\Models\Cities;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Models\Employment_PeopleNewStage;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use Amerhendy\Employment\App\Models\Employment_PeopleNewData;
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
use \Amerhendy\Employment\App\Models\Employment_Seatings;

use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class PeopleController extends AmerController
{
    use peopleTrait;
    public static $request;
    public static $annonceSection,$GrievanceSection,$NIDSection,$UidSection,$nameSection,$SeatingSection,$error;
    public function __construct(){
        self::$annonceSection='AnnonceAnnonce';
        self::$GrievanceSection='GrievanceAnnonce';
        self::$NIDSection='NIDINPUT';
        self::$UidSection='UIDINPUT';
        self::$nameSection='NameINPUT';
        self::$SeatingSection='SeatingAnnonce';
        self::$error=new \stdClass();
        self::$error->number=402;
        self::$error->page=\Str::between(\Str::after(__FILE__,__DIR__),'\\','.php');
    }
    private static function checkRequest(){
        $people=Employment_People::with('Employment_PeopleNewStage')->whereHas('Employment_PeopleNewStage',function($query){
            return $query->where('Stage_id',14);
        })->get();
        foreach ($people as $key => $value) {
            $uid=$value->id;
            foreach($value->Employment_PeopleNewStage as $a=>$b){
                if($b->Stage_id == 14){
                    $stage=$b->id;
                    $number=rand(1,100);
                }
            }
            $Employment_Seatings=new Employment_Seatings();
            $Employment_Seatings->People_id=$uid;
            $Employment_Seatings->Stage_id=$stage;
            $Employment_Seatings->Number=$number;
            $Employment_Seatings->Committee_number=1;
            $Employment_Seatings->created_at=now();
         //   $Employment_Seatings->save();
        }
        ////////////////////////
        $request=self::$request;
        if(!$request->has('Section')){
            self::$error->message=trans("JOBLANG::Employment_Reports.errors.PleaseSelectSection");
            self::$error->line=__LINE__;
            return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $sections=[self::$annonceSection,self::$GrievanceSection,self::$NIDSection,self::$UidSection,self::$nameSection,self::$SeatingSection];
        if(!in_array($request->input('Section'),$sections))
        {
            self::$error->message=trans("JOBLANG::Employment_Reports.errors.PleaseSelectSection");
            self::$error->line=__LINE__;
            self::$error->wanted=$request->input('Section');
            return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        if($request['Section'] == self::$annonceSection || $request['Section'] == self::$GrievanceSection || $request['Section'] == self::$SeatingSection || $request['Section'] == self::$UidSection)
        {
            self::$error->message=trans("JOBLANG::Employment_Reports.errors.PleaseSelectSection");
            if(!$request->has('id')){self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            if(count($request->input('id')) == 0){self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            if(!is_array($request->input('id'))){self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
        }elseif($request['Section'] == self::$NIDSection){
            if(!$request->has('nid')){self::$error->message=trans("JOBLANG::Employment_Reports.errors.NidInputEmpty");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            if(!is_numeric($request->input('nid'))){self::$error->message=trans("JOBLANG::Employment_Reports.errors.NidInputEmpty");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            if(strlen($request->input('nid')) <6 ){self::$error->message=trans("JOBLANG::Employment_Reports.errors.indInputLessThan");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            $query=Employment_People::where('NID','like','%'.$request['nid'].'%')->get('id')->toArray();
            if(count($query) == 0){self::$error->message=trans("JOBLANG::Employment_Reports.errors.nidNotFound");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            $request->request->add(['id' => \Arr::map($query,function($v,$k){
                return $v['id'];
            })]);
        }elseif($request['Section'] == self::$nameSection){
            if(!$request->has('name')){self::$error->message=trans("JOBLANG::Employment_Reports.errors.NameInputEmpty");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            if(strlen($request->input('name')) <3 ){self::$error->message=trans("JOBLANG::Employment_Reports.errors.nameInputLessThan");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            $name=$request->input('name');
            if(\Str::contains($name,' ')){
                $name=\Str::of($name)->explode(' ');
            }else{
                $name=[$name];
            }
            $query=[];
            $ids=[];
            foreach($name as $a=>$b){
                $query=Employment_People::where('Fname','like','%'.$b.'%')->orWhere('Sname','like','%'.$b.'%')->orWhere('Tname','like','%'.$b.'%')->orWhere('Lname','like','%'.$b.'%')->get('id')->toArray();
                if(count($query) !== 0){$ids[]=\Arr::map($query,function($v,$k){return $v['id'];});}
            }
            if(count($query) == 0){self::$error->message=trans("JOBLANG::Employment_Reports.errors.NameNotFound");self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            $ids=array_unique(\Arr::flatten($ids));
            $ids=\Arr::sort($ids);
            $request->request->add(['id' => $ids]);
        }
        if(!$request->has('length')){
            $request->request->add(['length' => 15]);
        }
        if(!$request->has('start')){
            $request->request->add(['start' => 1]);
        }
        if(!$request->has('search')){
            $request->request->add(['search' => null]);
        }
        self::$request=$request;
    }
    
    public static function peopleFullInfo(Array $id){
        $peopleDB=Employment_People::with(
            'Employment_StartAnnonces','Employment_Stages','Employment_Job','Employment_Job.Mosama_JobNames',
            'BornGovernorates','BornCities','LiveGovernorates','Employment_PeopleNewStage','Employment_PeopleNewData',
            'Employment_PeopleDegrees','Employment_Grievance','LiveCities','Employment_Health',
            'Employment_MaritalStatus','Employment_Army','Employment_Ama','Employment_Education',
            'Employment_Drivers','Employment_PeopleNewStage','Employment_Seatings','Employment_Seatings.Employment_Committee')
        ->whereIn('id', $id)->orderBy('id')->get();
        //dd($peopleDB[0]);
        $people=array();
        foreach($peopleDB as $a=>$person){
            //if($person->id == 30){dd($person->Employment_PeopleNewStage);}
            //if($person->Employment_PeopleNewData !== Null){dd("SSSSSSSSSSS");}
            $people[$a]['id']=$person->id;
            $people[$a]['annonce_id']=['Number'=>$person->Employment_StartAnnonces->Number, "Year"=>$person->Employment_StartAnnonces->Year];
            $people[$a]['NID']=$person->NID;
            $people[$a]['AgeYears']=$person->AgeYears;$people[$a]['AgeMonths']=$person->AgeMonths;$people[$a]['AgeDays']=$person->AgeDays;
            $people[$a]['BirthDate']=$person->BirthDate;
            if($person->Sex == '1'){$people[$a]['Sex']=trans('JOBLANG::Employment_People.Sex.Male');}else{$people[$a]['Sex']=trans('JOBLANG::Employment_People.Sex.Female');}
            if($person->Employment_PeopleNewData !== null){
                $personalData=$person->Employment_PeopleNewData;
            }else{
                $personalData=$person;
            }
            
            $people[$a]['job_id']=['Text'=>$personalData->Employment_Job->Mosama_JobNames->text,'Code'=>$personalData->Employment_Job->Code,'Driver'=>$personalData->Employment_Job->Driver];
            $people[$a]['Fname']=$personalData->Fname;$people[$a]['Sname']=$personalData->Sname;$people[$a]['Tname']=$personalData->Tname;$people[$a]['Lname']=$personalData->Lname;
            $people[$a]['LiveGov']=$personalData->LiveGovernorates->Name;
            $people[$a]['LiveCity']=$personalData->LiveCities->Name;
            $people[$a]['LiveAddress']=$personalData->LiveAddress;
            $people[$a]['BornGov']=$personalData->BornGovernorates->Name;
            $people[$a]['BornCity']=$personalData->BornCities->Name;
            $people[$a]['ConnectLandline']=$personalData->ConnectLandline;$people[$a]['ConnectMobile']=$personalData->ConnectMobile;$people[$a]['ConnectEmail']=$personalData->ConnectEmail;
            $people[$a]['Health_id']=$personalData->Employment_Health->Text;
            $people[$a]['MaritalStatus_id']=$personalData->Employment_MaritalStatus->Text;
            $people[$a]['Arm_id']=$personalData->Employment_Army->Text;
            $people[$a]['Ama_id']=$personalData->Employment_Ama->Text;
            $people[$a]['Tamin']=$personalData->Tamin;
            if(\AmerHelper::isJson($personalData->Khebra)){
                $personalData->Khebra=json_decode($personalData->Khebra,true);
                if(!count($personalData->Khebra)){
                    $personalData->Khebra=Null;
                }else{
                    $personalData->Khebra=\AmerHelper::arrayFlattenWKey($personalData->Khebra);
                    $khebra=array();
                    foreach($personalData->Khebra as $key=>$value){
                        /*
                        if($key == 0){$key=trans('EMPLANG::Mosama_Experiences.enum_0');}
                        elseif($key == 1){$key=trans('EMPLANG::Mosama_Experiences.enum_1');}
                        elseif($key == 2){$key=trans('EMPLANG::Mosama_Experiences.enum_2');}*/
                        $fst=[$key,$value];
                        $khebra[]=$fst;
                    }
                }
                if(is_array($khebra[0])){
                    $personalData->Khebra=$khebra[0];
                }else{
                    $personalData->Khebra=$khebra;
                }
            }else{
                $personalData->Khebra=Null;
            }
            $people[$a]['Khebra']=$personalData->Khebra; //khebra
            $people[$a]['Education_id']=$personalData->Employment_Education->text;
            $people[$a]['EducationYear']=$personalData->EducationYear;
            $stages=new \stdClass();
            $stagescontroller=new PeopleStagesController($person);
            $stages->Last=$stagescontroller::get('HtmlLastStage',null,'array');
            $stages->stages=$stagescontroller::get('HtmlStageList',null,'array');
            $entrylist=$stagescontroller::get('HtmlEntryStages',null,'array');
            $stages->LastEntry=end($entrylist);
            $stages->apply=$entrylist[0];
            $people[$a]['Stage_id']=$stages; // stages
            $people[$a]['Result']=Employment_Status::where('id',$personalData->Result)->first()->Text; // result
            $people[$a]['Message']=searchController::prepare_message_for_print($personalData->Message,'array'); // Message
            if($person->Employment_Drivers == null){
                $people[$a]['DriverDegree']=null;$people[$a]['DriverStart']=null;$people[$a]['DriverEnd']=null;
            }else{
                $people[$a]['DriverDegree']=$person->Employment_Drivers->Text;
                $people[$a]['DriverStart']=$person->DriverStart;$people[$a]['DriverEnd']=$person->DriverEnd;
            }
            if($person->Employment_PeopleDegrees !== null){
                $peopleDB[$a]['Degrees']=['Editorial'=>$person->Employment_PeopleDegrees->Editorial,'Practical'=>$person->Employment_PeopleDegrees->Practical,'Interview'=>$person->Employment_PeopleDegrees->Interview];
            }else{
                $peopleDB[$a]['Degrees']=[];
            }
            unset($person->Employment_PeopleDegrees);
            $people[$a]['Seatings']=$stagescontroller::get('Seating');
            $people[$a]['FileName']=self::fileList($person);//$personalData->FileName; // FileName
        }
        return self::paginate(collect($people));
        return \AmerHelper::responsedata($people,null,count($people),0);
    }
    
    public static function paginate($items)
    {
        $options=['path'=>'','pageName'=>'Page'];
        $startIndex=self::$request['start']??0;
        $perPage=self::$request['length']??25;
        
        $currentPage = ceil(($startIndex - 1) / $perPage) + 1;
        $page = $currentPage ?: (AbstractPaginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        if((isset(self::$request['search'])) && (self::$request['search'] === null)){
            $needle=false;    
        }else{
            $needle=self::$request['search']['value'];
        }
        
        if(!empty($needle)){
            
            //2023
            $exists = $items->filter(function($item) use($needle) { 
                return  
                $item['id']==$needle || strpos($item['id'],$needle) || 
                $item['annonce_id']['Number'] ==$needle || strpos($item['annonce_id']['Number'],$needle) || $item['annonce_id']['Year'] == $needle || strpos($item['annonce_id']['Year'],$needle) || 
                $item['NID'] == $needle || strpos($item['NID'],$needle) || 
                $item['AgeYears'] == $needle || strpos($item['AgeYears'],$needle) || $item['AgeMonths'] == $needle || strpos($item['AgeMonths'],$needle) || $item['AgeDays'] == $needle || strpos($item['AgeDays'],$needle) || 
                $item['Sex'] == $needle || strpos($item['Sex'],$needle) || $item['BirthDate'] == $needle || strpos($item['BirthDate'],$needle) || 
                $item['job_id']['Text'] == $needle || strpos($item['job_id']['Text'],$needle) || $item['job_id']['Code'] == $needle || strpos($item['job_id']['Code'],$needle) || 
                $item['Fname'] == $needle || strpos($item['Fname'],$needle) || $item['Sname'] == $needle || strpos($item['Sname'],$needle) || $item['Tname'] == $needle || strpos($item['Tname'],$needle) || $item['Lname'] == $needle || strpos($item['Lname'],$needle) || 
                $item['LiveGov'] == $needle || strpos($item['LiveGov'],$needle) || $item['LiveCity'] == $needle || strpos($item['LiveCity'],$needle) || 
                $item['LiveAddress'] == $needle || strpos($item['LiveAddress'],$needle) || $item['BornGov'] == $needle || strpos($item['BornGov'],$needle) || 
                $item['BornCity'] == $needle || strpos($item['BornCity'],$needle) || $item['ConnectLandline'] == $needle || strpos($item['ConnectLandline'],$needle) || $item['ConnectMobile'] == $needle || strpos($item['ConnectMobile'],$needle) || $item['ConnectEmail'] == $needle || strpos($item['ConnectEmail'],$needle) || 
                $item['Health_id'] == $needle || strpos($item['Health_id'],$needle) || $item['MaritalStatus_id'] == $needle || strpos($item['MaritalStatus_id'],$needle) || 
                $item['Arm_id'] == $needle || strpos($item['Arm_id'],$needle) || $item['Ama_id'] == $needle || strpos($item['Ama_id'],$needle) || $item['Tamin'] == $needle || strpos($item['Tamin'],$needle) || 
                $item['Education_id'] == $needle || strpos($item['Education_id'],$needle) || $item['EducationYear'] == $needle || strpos($item['EducationYear'],$needle) || 
                $item['Result'] == $needle || strpos($item['Result'],$needle);
            });
            $items=$exists;
         }
        $newitems=[];
        $items=$items->sortBy(fn($item, $key) => (int) $item['id'])->values();
        foreach($items->forPage($page, $perPage) as $item){
            $newitems[]=$item;
        }
        return [
            'data'=>$newitems,
            'recordsTotal'=>count($newitems),
            'draw'=>self::$request->draw ?? 0,
            'recordsFiltered'=>count($items),
        ];
        $data= new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        $data->useBootstrapFive();
        return $data;
        $data=$data->toArray();
        $avo=[];
        $data['recordsTotal']=$data['total'];
        //get current page
        return $data;
        $avo['recordsFiltered']=0;
        $avo['data']=$data['data'];
        return $avo;
    }
    public static function message_template(Request $request){
        $messages=Employment_PeopleNewStage::select('Message')->distinct()->get();
        $ArrMessage=[];
        foreach ($messages as $key => $value) {
            if(\AmerHelper::isJson($value->Message)){
                $ArrMessage[]=searchController::prepare_message_for_print($value->Message,'array');
            }else{
                $ArrMessage[]= $value->Message;
            }
        }
        $ArrMessage= \AmerHelper::array_flatten($ArrMessage);
        unset($messages);
        $messages=[];
        foreach ($ArrMessage as $key => $value) {
            $fulltext=$value;
            $cleantext=\AmerHelper::decodeHTMLEntities($value);
            $messages[$key]=['html'=>$fulltext,'select'=>\Str::limit($cleantext,100)];
        }
        return $messages;
    
    }
    public static function userInfoForComplete(Request $request) {
        
        $isjson=\AmerHelper::is_Json($request);
        if(!request()->has('annonce')){
            self::$error->number=405;
            self::$error->message="employment.Apply.errors.startannoncesnotfound";
            self::$error->line=__LINE__;
            if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}
            
        }
        if(!request()->has('job')){self::$error->number=405;self::$error->message="employment.Apply.errors.jobNotFound";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        if(!request()->has('nid')){self::$error->number=405;self::$error->message="employment.Apply.nid_not_Exists";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        $annonce=request()->annonce;
        $job=request()->job;
        $nid=request()->nid;
        $annonce=Employment_StartAnnonces::where('id',$annonce)->first();
        if(!$annonce){self::$error->number=405;self::$error->message="employment.Apply.errors.startannoncesnotfound";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        $annonce_id=$annonce->id;
        $job=Employment_Jobs::where('id',$job)->where('annonce_id',$annonce_id)->first();
        if(!$job){self::$error->number=405;self::$error->message="employment.Apply.errors.jobNotFound";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        $job_id=$job->id;
        $per=Employment_People::where('nid',$nid)->where('annonce_id',$annonce_id)->first();
        if(!$per){self::$error->number=405;self::$error->message="employment.Apply.nid_not_Exists";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        $nest=Employment_PeopleNewStage::where('people_id',$per->id)->get()->last();
        //dd($nest,$per[0]['id']);
        $nest=$nest->toArray();
        if(!$nest){self::$error->number=405;self::$error->message="employment.Apply.nid_not_Exists";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        $peoplenewstageid=$nest['id'];
        $stageid=$nest['stage_id'];
        $ldata=Employment_PeopleNewData::where('people_id',$per->id)->count();
        if($ldata !== 0){self::$error->number=405;self::$error->message="employment.Apply.nid_not_Exists";self::$error->line=__LINE__;if($isjson){return \AmerHelper::responseError(self::$error,self::$error->number);}else{return view('errors.layout',['console'=>self::$error]);}}
        $sentData=[
            'page_title' =>trans("employment.apply.Complete.Complete"),
            'page' => 'jobs', 
            'annonce' => $annonce->id,
            'job'=>$job_id,
            'nid'=>$nid,
            'uid'=>$per->id,
            'request'=>'complete',
            'per'=>$per,
            'stageid'=>$stageid,
            'peoplenewstageid'=>$peoplenewstageid
        ];
        return \AmerHelper::responsedata($sentData,200,1,0);
    }
    public static function index(Request $request){
        self::$request=$request;
        $chk=self::checkRequest();
        if(is_object($chk)){
            return $chk;
        }
        return self::peopleFullInfo(self::$request->input('id'));
    }
}
