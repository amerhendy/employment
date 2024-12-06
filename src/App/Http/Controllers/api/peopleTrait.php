<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use \Amerhendy\Employment\App\Models\Employment_People;
trait peopleTrait{
    //work in reviewController
    public static function EmploymentPeopleUsingID(Array $id){
        $peopleDB=Employment_People::whereIn('id', $id)->orderBy('id');
        $peopleDB=$peopleDB->get();
        if(!count($peopleDB)){return false;}
        self::$peopleDB=$peopleDB;
        return true;
    }
    public static function EmploymentPeopleUsingNIDAnnonceJob(){
        if(isset(self::$nid)){
            if(is_numeric(self::$nid)){$nidSearch=self::$nid;}
        }elseif(self::$request->has('nid')){$nidSearch=self::$request->input('nid');}elseif(self::$request->has('NID')){$nidSearch=self::$request->input('NID');}
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

        //dd($annonceIDSLugIN);
        //dd(self::$request);
        if(isset($nidSearch)){if(\Str::of($nidSearch)->length() == 0){unset($nidSearch);}}
        if(isset($annonceIdIN)){if(count($annonceIdIN) == 0){unset($annonceIdIN);}}
        if(isset($annonceIDSLugIN)){if(count($annonceIDSLugIN) == 0){unset($annonceIDSLugIN);}}
        if(isset($jobIdIN)){if(count($jobIdIN) == 0){unset($jobIdIN);}}
        if(isset($JobIDSLugIN)){if(count($JobIDSLugIN) == 0){unset($JobIDSLugIN);}}
        $peopleDB=Employment_People::with('employment_startannonces');
        if(isset($nidSearch)){
            $peopleDB=$peopleDB->where('nid','LIKE',$nidSearch);
        }
        if(isset($annonceIdIN)){
            $peopleDB=$peopleDB->whereIn('annonce_id',$annonceIdIN);
        }
        if(self::$request->page === 'search'){
            unset($jobIdIN);
        }
        if(isset($jobIdIN)){
            $peopleDB=$peopleDB->whereIn('job_id',$jobIdIN);
        }

        if(isset($annonceIDSLugIN)){
            $peopleDB=$peopleDB->whereHas('employment_startannonces',function($q)use($annonceIDSLugIN){
                return $q->whereIn('slug',$annonceIDSLugIN)->orWhereIn('id',$annonceIDSLugIN);
            });
        }
        if(isset($JobIDSLugIN)){
            $peopleDB=$peopleDB->whereHas('employment_jobs',function($q)use($JobIDSLugIN){
                return $q->whereIn('slug',$JobIDSLugIN)->orWhereIn('id',$JobIDSLugIN);
            });
        }
        $peopleDB=$peopleDB->get();
        if(!count($peopleDB)){return false;}
        self::$peopleDB=$peopleDB;
        return true;
    }
    public static function __toHtml(){
        $htmlPerson=[];
        foreach(self::$peopleDB  as $a=>$person){
            $data=new \stdClass;
            $data->id=$person->id;
            $annonce_id=new \stdClass;
            $annonce_id->Number=$person->Employment_StartAnnonces->Number;
            $annonce_id->Year=$person->Employment_StartAnnonces->Year;
            $data->annonce_id=$annonce_id;
            if($person->Employment_PeopleNewData !== null){
                $personalData=$person->Employment_PeopleNewData;
            }else{
                $personalData=$person;
            }
            $job_id=new \stdClass;
            $job_id->Text=$personalData->Employment_Job->Mosama_JobNames->text;
            $job_id->Code=$personalData->Employment_Job->Code;
            $job_id->Driver=$personalData->Employment_Job->Driver;
            $data->job_id=$job_id;
            $data->NID=$person->NID;
            $data->AgeYears=$person->AgeYears;$data->AgeMonths=$person->AgeMonths;$data->AgeDays=$person->AgeDays;
            $data->BirthDate=$person->BirthDate;
            if($person->Sex == '1'){$data->Sex=trans('JOBLANG::Employment_People.Sex.Male');}else{$data->Sex=trans('JOBLANG::Employment_People.Sex.Female');}
            $data->FullName=$personalData->FullName;
            $data->LivePlace=$personalData->LivePlace;
            $data->BornPlace=$personalData->BornPlace;
            $data->Connection=$personalData->Connection;
            $data->Health_id=$personalData->Employment_Health->Text;
            $data->MaritalStatus_id=$personalData->Employment_MaritalStatus->Text;
            $data->Arm_id=$personalData->Employment_Army->Text;
            $data->Ama_id=$personalData->Employment_Ama->Text;
            $data->Tamin=$personalData->Tamin;
            $data->Khebra=$personalData->khebraToStr;
            $data->Education_id=$personalData->Employment_Education->text;
            $data->EducationYear=$personalData->EducationYear;
            $stages=new \stdClass();
            $stagescontroller=new PeopleStagesController($person);
            $stages->Last=$stagescontroller::get('HtmlLastStage',null,'array');
            $stages->stages=$stagescontroller::get('HtmlStageList',null,'array');
            $entrylist=$stagescontroller::get('HtmlEntryStages',null,'array');
            $stages->LastEntry=end($entrylist);
            $stages->apply=$entrylist[0];
            $data->Stage_id=$stages; // stages
            $data->Result=$personalData->Employment_Status->Text;
            $data->Message=$personalData->HtmlMessage;
            if($person->Employment_Drivers == null){
                $data->DriverDegree=null;$data->DriverStart=null;$data->DriverEnd=null;
            }else{
                $data->DriverDegree=$person->Employment_Drivers->Text;
                $data->DriverStart=$person->DriverStart;$data->DriverEnd=$person->DriverEnd;
            }
            if($person->Employment_PeopleDegrees !== null){
                $data->Degrees=['Editorial'=>$person->Employment_PeopleDegrees->Editorial,'Practical'=>$person->Employment_PeopleDegrees->Practical,'Interview'=>$person->Employment_PeopleDegrees->Interview];
            }else{
                $data->Degrees=[];
            }
            $data->Seatings=$stagescontroller::get('Seating');
            $data->FileName=self::fileList($person);//$personalData->FileName; // FileName
            $htmlPerson[$a]=$data;
        }
        self::$htmlPerson=$htmlPerson;
    }
        public static function fileList($data){
            $files=array();
            $files[]=$data->FileName;
            if($data->Employment_PeopleNewData !== null){
                $files[]=$data->Employment_PeopleNewData->FileName;
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
