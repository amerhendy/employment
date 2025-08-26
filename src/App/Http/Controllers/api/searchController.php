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
class searchController extends AmerController
{
    use checkRequests,printTrait,peopleTrait;
    public static $error,$request,$people,$prePeople,$peopleDB,$nid,$annonce,$job;
    public function __construct(){
        self::setErrorClass();
    }
    public static function get_result(Request $request){
        self::setErrorClass();
        self::$request=$request;
        $errors=self::checknid();
        if($errors !== true){return $errors;}
        $people=self::EmploymentPeopleUsingNIDAnnonceJob();
        if($people == false){
            dd('sdffffffff');
        }
        self::$people=self::$peopleDB;
        self::DBToText();
        $print =self::printSearchResult();
        if(is_array($print)){
            $print=$print[0];
            $print=[\AmerHelper::delete_all_between('<style>','</style>',$print)];
            return \AmerHelper::responsedata($print,1,1,'');
        }else{
            return $print;
        }
        return \AmerHelper::responsedata(self::$prePeople,1,1,'');
        $data=self::prepare_fst_stage($data);
        $data=self::prepare_stages($data);
        $data=self::prepare_newdata($data);
        $data=self::prepare_Grievance($data);
        $data=self::add_to_stage_list($data);
        
    }
    public static function prepare_stages($data)
    {
        if(!$data->Employment_PeopleNewStage){
            $data->Employment_PeopleNewStage=null;
            return $data;
        }
        foreach($data->Employment_PeopleNewStage as $a=>$b){
            //$data->Employment_PeopleNewStage[$a]=self::remove_unnec_vals($data->Employment_PeopleNewStage[$a],['publisher','updated_at','deleted_at']);
            $data->Employment_PeopleNewStage[$a]['id'] =$data->Employment_PeopleNewStage[$a]['id'];
            $data->Employment_PeopleNewStage[$a]['result'] =$data->Employment_PeopleNewStage[$a]['Status_id'];
            $data->Employment_PeopleNewStage[$a]['Message']=self::prepare_message_for_print($data->Employment_PeopleNewStage[$a]['Message']);
            $data->Employment_PeopleNewStage[$a]['Stage_id']=$data->Employment_PeopleNewStage[$a]->Employment_Stages;
            //$data->Employment_PeopleNewStage[$a]=self::remove_unnec_vals($data->Employment_PeopleNewStage[$a],'Employment_Stages');
        }
        return $data;
    }
    public static function prepare_newdata($data)
    {
        
        if(is_null($data->Employment_PeopleNewData) || !isset($data->Employment_PeopleNewData)){$data->Employment_PeopleNewData=null;return $data;}
        //$data=self::remove_unnec_vals($data->Employment_PeopleNewData,['deleted_at','updated_at','driver_end','driver_start','driver_degree','filename','malaftamin','khebra','educcation_year','education','ama','arm','mir','health','email','mobile','landline','live_address','live_city','live_gov','born_city','born_gov']);
        $data->Employment_PeopleNewData=self::addjobinfo($data->Employment_PeopleNewData,'fst');
        $data->Employment_PeopleNewData['Message']=$data->Employment_PeopleNewData->HtmlMessage;
        //$data['employment_people_new_data']=self::remove_unnec_vals($data['employment_people_new_data'],['work_jobs','statue']);        
        return $data;
    }
    public static function prepare_Grievance($data){
        if(is_null($data->Employment_Grievance)){
            self::remove_unnec_vals($data,'Employment_Grievance');return $data;
        }
        if(!count($data['tazalom'])){return $data;}
        $data['tazalom']=$data['tazalom'][0];
        return $data;
    }
    public static function add_to_stage_list($data)
    {
        $data->Stages=[];
        $stagesList=[];

        $stagesList['list'][]=[
            'key'=>0,
            'type'=>'fst',
            'stage'=>$data->Employment_Stages,
            'result'=>$data->Result,
            'message'=>$data->Message,
            'link'=>null,
            'created_at'=>$data->created_at,
        ];
        if($data->has('Employment_PeopleNewStage'))
        {
            foreach($data->Employment_PeopleNewStage as $a=>$b){
                $stagesList['list'][]=[
                    'key'=>$b->id,
                    'type'=>'stage',
                    'stage'=>$b->Stage_id,
                    'result'=>$b->Status_id,
                    'message'=>$b->Message,
                    'link'=>null,
                    'created_at'=>$b->created_at,
                ];
            }
        }
        if($data->has('Employment_PeopleNewData'))
        {
            $b=$data->Employment_PeopleNewData;
            if(!is_null($b)){
                $stagesList['list'][]=[
                    'key'=>$b->id,
                    'type'=>'entry',
                    'stage'=>$b->Stage_id,
                    'result'=>$b->Status_id,
                    'message'=>$b->Message,
                    'link'=>null,
                    'created_at'=>$b->created_at,
                ];
            }
            
        }
        if($data->has('Employment_Grievance'))
        {
            $b=$data->Employment_Grievance;
            if(!is_null($b)){
                $stagesList['list'][]=[
                    'key'=>$b->id,
                    'type'=>'stage',
                    'stage'=>$b->Stage_id->id,
                    'result'=>$b->Status_id,
                    'message'=>$b->Message,
                    'link'=>null,
                    'created_at'=>$b->created_at,
                ];
            }
        }
        $sorted = array_values(\Arr::sort($stagesList['list'], function (array $value) {
            return $value['created_at'];
        }));
        $lst=['list'=>collect($sorted)];
        $last=collect($stagesList['list']);
        $last=$last->last();
        $lst['final']=$last;
        $data->Stages=$lst;
        
        return $data;
    }

    public static function prepare_message_for_print($data,$messagetype=null)
    {
        $re=[];
        if(is_null($data)){
            return null;
        }
        if(gettype($data) == 'string'){
            if(\Str::isJson($data)){
                $data=json_decode($data,true);
                foreach($data as $key=>$val){
                    foreach($val as $a=>$b){
                        $re[]=trans($b);
                    }
                }
            }else{
                $re[]=$data;
            }
        }
            
        if($messagetype == 'array'){
            if(count($re)> 0){return $re;}else{return '';}
        }else{
            return implode(' - ',$re);
        }
    }
    public static function remove_unnec_vals($data,$val)
    {
        
        if(is_array($val)){
            foreach($val as $i){
                if(isset($data->$i)){                    
                    $data->$i=null;
                    $data=data_forget($data, $i);
                }
                
            }
        }else{
            
            data_forget($data, $val);
        }
        
        return $data;
    }
}
