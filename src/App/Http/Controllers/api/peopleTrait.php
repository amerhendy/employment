<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use \Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Amer\App\Models\Cities;
trait peopleTrait{
    //work in reviewController
    public static function EmploymentPeopleUsingID(Array $id){
        $peopleDB=Employment_People::whereIn('id', $id)->orderBy('id');
        $peopleDB=$peopleDB->get();
        if(!count($peopleDB)){return false;}
        self::$peopleDB=$peopleDB;
        return true;
    }
    public static function getNid(){
        if(isset(self::$nid)){
            if(is_numeric(self::$nid)){$nidSearch=self::$nid;}
        }elseif(self::$request->has('nid')){$nidSearch=self::$request->input('nid');}elseif(self::$request->has('NID')){$nidSearch=self::$request->input('NID');}
        return $nidSearch;
    }
    public static function getAnnonce($get=null){
        $annonceIdIN=null;
        $annonceIDSLugIN=null;
        if(isset(self::$annonce)){
            if(is_object(self::$annonce)){
                $annonceIdIN=[self::$annonce->id];
            }elseif(\Str::isUuid(self::$annonce)){
                $annonceIdIN=[self::$annonce];
            }elseif(is_string(self::$annonce)){
                $annonceIDSLugIN=[self::$annonce];
            }elseif(is_array(self::$annonce)){
                $annonceIDSLugIN=self::$annonce;
            }
            
        }else{
            $annonceTags=['annonce','annonceslug','annonceSlug','Annonce','Annonceslug','AnnonceSlug','annonceid','annonceId','Annonceid','AnnonceId'];
            foreach($annonceTags as $l=>$m){
                if(self::$request->has($m)){
                    if(is_array(self::$request->input($m))){$annonceIDSLugIN=self::$request->input($m);}else{$annonceIDSLugIN=[self::$request->input($m)];}
                }
            }
        }
        if($get==null){
            return [$annonceIdIN,$annonceIDSLugIN];
        }elseif($get=='id'){
            return $annonceIdIN;
        }elseif ($get == 'slug') {
            return $annonceIDSLugIN;
        }
    }
    public static function getJob($get){
        $jobIdIN=null;
        $JobIDSLugIN=null;
        if(isset(self::$job)){
            if(is_object(self::$job)){
                $jobIdIN=[self::$job->id];
            }elseif(\Str::isUuid(self::$job)){
                $jobIdIN=[self::$job];
            }elseif(is_string(self::$job)){
                $JobIDSLugIN=[self::$job];
            }elseif(is_array(self::$job)){
                $JobIDSLugIN=self::$job;
            }
        }else{
            $jobTags=['job','jobslug','jobSlug','Job','Jobslug','JobSlug','jobid','jobId','Jobid','JobId'];
            foreach($jobTags as $l=>$m){
                if(self::$request->has($m)){
                    if(is_array(self::$request->input($m))){$JobIDSLugIN=self::$request->input($m);}else{$JobIDSLugIN=[self::$request->input($m)];}
                }
            }
        }
        if($get == null){
            return [$jobIdIN,$JobIDSLugIN];
        }elseif ($get == 'id') {
            return $jobIdIN;
        }elseif($get == 'slug'){
            return $JobIDSLugIN;
        }
    }
    public static function prepareEmploymentPeopleUsingNIDAnnonceJobSql($nidSearch=null,$annonceIdIN=null,$annonceIDSLugIN=null,$jobIdIN=null,$JobIDSLugIN=null){
        $peopleDB=Employment_People::with('employment_startannonces');
        if(!is_null($nidSearch)){
            $peopleDB=$peopleDB->where('nid','LIKE',$nidSearch);
        }
        if(!is_null($annonceIdIN)){
            $peopleDB=$peopleDB->whereIn('annonce_id',$annonceIdIN);
        }elseif(!is_null($annonceIDSLugIN)){
            $peopleDB=$peopleDB->whereHas('employment_startannonces',function($q)use($annonceIDSLugIN){
                return $q->whereIn('slug',$annonceIDSLugIN)->orWhereIn('id',$annonceIDSLugIN);
            });
        }
        if(in_array(self::$request->page,['search','showjob'])){
            $JobIDSLugIN=$jobIdIN=null;
        }
        if(!is_null($jobIdIN)){
            $peopleDB=$peopleDB->whereIn('job_id',$jobIdIN);
        }
        if(!is_null($JobIDSLugIN)){
            $peopleDB=$peopleDB->whereHas('employment_jobs',function($q)use($JobIDSLugIN){
                return $q->whereIn('slug',$JobIDSLugIN)->orWhereIn('id',$JobIDSLugIN);
            });
        }

        return $peopleDB;

    }
    public static function EmploymentPeopleUsingNIDAnnonceJob(){
        $nidSearch= self::getNid();
        $annonceIdIN=self::getAnnonce('id');
        $annonceIDSLugIN=self::getAnnonce('slug');
        $jobIdIN=self::getJob('id');
        $JobIDSLugIN=self::getJob('slug');
        $peopleDB=self::prepareEmploymentPeopleUsingNIDAnnonceJobSql(self::getNid(),self::getAnnonce('id'),self::getAnnonce('slug'),self::getJob('id'),self::getJob('slug'));
        $peopleDB=$peopleDB->get();
        if(!count($peopleDB)){return false;}
        self::$peopleDB=$peopleDB;
        return true;
    }
    public static function __toHtml(){
        $json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json(self::$request);
        $htmlPerson=[];
        foreach(self::$peopleDB  as $a=>$person){
            $data=new \stdClass;
            $data->id=$person->id;
            $annonce_id=new \stdClass;
            $annonce_id->Number=$person->Employment_StartAnnonces->number;
            $annonce_id->Year=$person->Employment_StartAnnonces->year;
            $data->annonce_id=$annonce_id;
            if($person->Employment_PeopleNewData !== null){
                $personalData=$person->Employment_PeopleNewData;
            }else{
                $personalData=$person;
            }
            $job_id=new \stdClass;
            $job_id->Text=$personalData->Employment_Job->Mosama_JobNames->text;
            $job_id->Code=$personalData->Employment_Job->code;
            $job_id->Driver=$personalData->Employment_Job->driver;
            $data->job_id=$job_id;
            $data->NID=$person->nid;
            $data->AgeYears=$person->age_years;$data->AgeMonths=$person->age_months;$data->AgeDays=$person->age_days;
            $data->BirthDate=$person->birth_date;
            $sex = $json 
                ? 'employment.Employment_People.Sex.' . ($person->sex == '1' ? 'Male' : 'Female') 
                : trans('JOBLANG::Employment_People.Sex.' . ($person->sex == '1' ? 'Male' : 'Female'));
            $data->Sex = $sex;
            $data->FullName=$personalData->FullName;
            $data->LivePlace = (object)[
                'LiveGov'     => optional(optional(Cities::find($personalData->live_place))->Governorates)->name,
                'LiveCity'    => optional(Cities::find($personalData->live_place))->name,
                'liveaddress' => $personalData->live_address,
            ];
            $data->BornPlace = (object)[
                'BornGov' => optional(optional(Cities::find($personalData->born_place))->Governorates)->name,
                'BornCity' => optional(Cities::find($personalData->born_place))->name,
            ];
            
            $data->Connection = (object)[
                'Landline' => $personalData->connect_landline,
                'Mobile'   => $personalData->connect_mobile,
                'Email'    => $personalData->connect_email,
            ];
            $data->Health_id=$personalData->Employment_Health->text;
            $data->MaritalStatus_id=$personalData->Employment_MaritalStatus->text;
            $data->Arm_id=$personalData->Employment_Army->text;
            $data->Ama_id=$personalData->Employment_Ama->text;
            $data->Tamin=$personalData->tamin;
            $data->Khebra=$personalData->khebraToStr;
            $data->Education_id=$personalData->Employment_Education->text;
            $data->EducationYear=$personalData->education_year;
            $stages=new \stdClass();
            $stagescontroller=new PeopleStagesController($person);
            $stages->Last=$stagescontroller->get('HtmlLastStage',null,'array');
            $stages->stages=$stagescontroller->get('HtmlStageList',null,'array');
            $entrylist=$stagescontroller->get('HtmlEntryStages',null,'array');
            $stages->LastEntry=end($entrylist);
            $stages->apply=head($entrylist);
            $data->Stage_id=$stages; // stages
            $data->Result=$personalData->Employment_Status->text;
            $data->Message=$personalData->HtmlMessage;
            if($person->Employment_Drivers == null){
                $data->DriverDegree=null;$data->DriverStart=null;$data->DriverEnd=null;
            }else{
                $data->DriverDegree=$person->Employment_Drivers->text;
                $data->DriverStart=$person->DriverStart;$data->DriverEnd=$person->DriverEnd;
            }
            if($person->Employment_PeopleDegrees !== null){
                $data->Degrees=['Editorial'=>$person->Employment_PeopleDegrees->Editorial,'Practical'=>$person->Employment_PeopleDegrees->Practical,'Interview'=>$person->Employment_PeopleDegrees->Interview];
            }else{
                $data->Degrees=[];
            }
            $data->Seatings=$stagescontroller->get('Seating');
            $data->FileName=self::fileList($person);//$personalData->FileName; // FileName
            $htmlPerson[$a]=$data;
            
        }
        self::$htmlPerson=$htmlPerson;
    }
        public static function fileList($data){
            $files=array();
            $files[]=$data->file_name;
            if($data->Employment_PeopleNewData !== null){
                $files[]=$data->Employment_PeopleNewData->file_name;
            }
            $lo=[];
            foreach($files as $file){
                $disk=\Storage::disk(config('Amer.employment.root_disk_name'));
                if($disk->exists($file)){
                    $exists=true;
                    $link=$disk->temporaryUrl($file, now()->addMinutes(15));
                }else{
                    $exists=false;
                    $link=null;
                }
                $lo[]=['exists'=>$exists,'link'=>$link,'name'=>$file];
            }
            return $lo;
        }
    }
