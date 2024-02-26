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
use \Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Employers\App\Models\Employment_Committee;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
class PeopleStagesController extends AmerController
{
    public static $person;
    public function __construct($person){
        self::$person=$person;
    }
    /*
    get
        stageList
        HtmlStageList
        lastStage
        HtmlLastStage
        EntryStages
        HtmlEntryStages
        Grievance
        HtmlGrievance
        Seating
        completeEntry
        HtmlSeating
        SeatingsEditorial
        HtmlSeatingsEditorial
     */
    public static function get($wanted){
        switch ($wanted) {
            case  \Str::startsWith($wanted,'Html'):
                $list=self::HtmlStagesList();
                break;
            
            default:
                $list=self::StagesList();
                break;
        }
        switch ($wanted) {
            case 'StageList':
            case 'HtmlStageList':
                return $list;
                break;
            case 'completeEntry':
                return array_filter($list, fn($key) => in_array($key->Type, ['complete']), ARRAY_FILTER_USE_BOTH);
                break;
            case 'completeStage':
                return array_filter($list, fn($key) => in_array($key->StageId, [5]), ARRAY_FILTER_USE_BOTH);
                break;
            case 'lastStage':
            case 'HtmlLastStage':
                return end($list);
                break;
            case 'EntryStages':
            case 'HtmlEntryStages':
                return array_filter($list, fn($key) => in_array($key->Type, ['apply','complete']), ARRAY_FILTER_USE_BOTH);
                break;
            case 'Grievance':
            case 'HtmlGrievance':
                return array_filter($list, fn($key) => \Str::startsWith($key->Type,'Grievance'), ARRAY_FILTER_USE_BOTH);
                break;
            case 'Seating':
            case 'HtmlSeatings':
                return array_filter($list, fn($key) => \Str::startsWith($key->Type,'Seating'), ARRAY_FILTER_USE_BOTH);
                break;
            case 'SeatingsEditorial':
            case 'HtmlSeatingsEditorial':
                return array_filter($list, fn($key) => \Str::startsWith($key->Type,'Seatings_Editorial'), ARRAY_FILTER_USE_BOTH);
                break;
            case 'SeatingsPractical':
            case 'HtmlSeatingsPractical':
                return array_filter($list, fn($key) => \Str::startsWith($key->Type,'Seatings_Practical'), ARRAY_FILTER_USE_BOTH);
                break;
            case 'Seatingsapply':
            case 'HtmlSeatingsapply':
                return array_filter($list, fn($key) => \Str::startsWith($key->Type,'Seatings_apply'), ARRAY_FILTER_USE_BOTH);
                break;
        }
        return $list;
    }
    public static function lastStage($person,$Status=null,$message=null,$messagetype=null){
        $list=self::stagesList($person,$Status);
        return $list[0];
    }
    public static function stagesList($accept=null,$message=null,$messagetype=null){
        $data=self::$person;
        $stages_w_s=[];
        $st=new \stdClass();
        $st->StageId=$data->Stage_id;
        $st->Result=$data->Result;
        $st->CreatedAt=strtotime($data->created_at);
        $st->Type='apply';
        $text=Employment_Stages::where('id',$data->Stage_id)->first();
        $st->Text=$text->Text;
        if($data->Message){$st->Message=$data->Message;}else{
            $st->Message=null;
        }
        $stages_w_s[]=$st;
        //set new data Stage
        $newdata=self::newData($data,$accept,$message);
        if(!is_null($newdata))$stages_w_s[]=$newdata;
        // set new stage
        $newstage=self::newStage($data,$accept,$message);
        if(!is_null($newstage))$stages_w_s[]=$newstage;
        // set Grievance
        $newGrievance=self::newGrievance($data,$accept,$message);
        if(!is_null($newGrievance))$stages_w_s[]=$newGrievance;
        $Seatings=self::Seatings($data,$accept,$message);
        if(!is_null($Seatings))$stages_w_s[]=$Seatings;
        $stages=[];
        $stages[]=$stages_w_s[0];
        if(isset($stages_w_s[1])){foreach($stages_w_s[1] as $a=>$b){$stages[]=$b;}}
        if(isset($stages_w_s[2])){foreach($stages_w_s[2] as $a=>$b){$stages[]=$b;}}
        if(isset($stages_w_s[3])){foreach($stages_w_s[3] as $a=>$b){$stages[]=$b;}}
        $collection=collect($stages)->sortBy('CreatedAt')->values();
        $collection=$collection->all();
        return $collection;
    }
    public static function HtmlStagesList($accept=null,$message=null,$messagetype=null){
        $data=self::$person;
        $list=self::stagesList($accept,$message);
        $newlist=[];
        foreach($list as $key=>$value){
            $std=new \stdClass();
            $std->StageId=$value->StageId;
            $std->Text=$value->Text;
            $std->created_at=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',$value->CreatedAt);
            $result=Employment_Status::where('id',$value->Result)->first();
            $std->Result=$result->Text;
            if($value->Message == null){
                $std->Message="";
            }else{
                $std->Message=searchController::prepare_message_for_print($value->Message,$messagetype);
            }
            $std->Type=$value->Type;
            
            if(\Str::startsWith($value->Type,'Seatings')){
                $std->Number=$value->Number;
                $std->Date=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',strtotime($value->Committee_Date));
                if(isset($value->Committee)){
                    $com=new \stdClass();
                    $com->id=$value->Committee->id;
                    $com->Annonce_id=$value->Committee->Annonce_id;
                    $com->Number=$value->Committee->Number;
                    $com->Name=$value->Committee->Name;
                    $com->Committee_Date=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',strtotime($value->Committee->Committee_Date));
                    $com->Committee_Memebers=$value->Committee->Committee_Memebers;
                    $com->created_at=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',strtotime($value->Committee->created_at));
                    $std->Committee=$com;
                    //$value->Committe->created_at
                }
            }
            $newlist[$key]=$std;
        }
        return $newlist;
    }
    public static function newData($data,$accept=null,$message=null,$messagetype=null){
        $stages_w_s=[];
        if($data->Employment_PeopleNewData ==null)return null;
        $element=$data->Employment_PeopleNewData;
        $stage=Employment_PeopleNewStage::with('Employment_Stages')->where('id',$element->Stage_id)->get('Stage_id')->toArray();
        
        $stage=$stage[0]['Stage_id'];
        if(isset($stage['Stage_id'])){
            $stage=$stage['Stage_id'];
        }
        $created_at=strtotime($element['created_at']);
        $st=new \stdClass();
        $st->StageId=$stage;
        $st->Result=$element['Result'];
        $st->CreatedAt=$created_at;
        $st->Type='complete';
        
        $text=Employment_Stages::find($stage);
        $st->Text=$text->Text;
        if($message == true){
            $st->Message=$element['Message'];
        }else{
            $st->Message=null;
        }
        $stages_w_s[]=$st;
        if(count($stages_w_s)){return $stages_w_s;}else{return null;}
    }
    public static function newStage($data,$accept=null,$message=null,$messagetype=null){
        $stages_w_s=[];
        if($data->Employment_PeopleNewStage == null)return null;
        for($i=0; $i<=$data->Employment_PeopleNewStage->count()-1; $i++){
            $element=$data->Employment_PeopleNewStage[$i];
                $stage=$element->Stage_id;
                $result=$element->Status_id;
                $created_at=strtotime($element->created_at);
                $text=Employment_Stages::find($element->Stage_id);
                $st=new \stdClass();
                $st->StageId=$stage;
                $st->Result=$result;
                $st->CreatedAt=$created_at;
                $st->Type='Stage';
                $st->Text=$text->Text;
                $st->Message=$element->Message;
                if($message == true){
                    $st->Message=$element['Message'];
                }
                $stages_w_s[]=$st;
        }
        if(count($stages_w_s)){return $stages_w_s;}else{return null;}
    }
    public static function newGrievance($data,$accept=null,$message=null,$messagetype=null){
        //Employment_Grievance
        $stages_w_s=[];$grilist=new \stdClass();
        if(count($data->Employment_Grievance) == 0)return null;
        foreach ($data->Employment_Grievance as $key => $value) {
            $element=$value;
            $Employment_PeopleNewStage=Employment_PeopleNewStage::where('id',$element->Stage_id)->first();
            $stage=$Employment_PeopleNewStage->Stage_id;
            /////////////////////////////get result//////////////////////
            $nextresult=Employment_PeopleNewStage::where('People_id',$data->id)->where('id','>',$element->Stage_id)->first();
            if($nextresult){
                $result=$nextresult->Status_id;
            }else{
                $result=$Employment_PeopleNewStage->Status_id;
            }
            
            $created_at=strtotime($element->created_at);
            if($stage == 5){
                $type='apply';
            }elseif($stage == 5){
                $type='Editorial';
            }else{
                $type='Practical';
            }
            $st=new \stdClass();
            $st->StageId=$stage;
            $st->Result=$result;
            $st->CreatedAt=$created_at;
            $st->Type= 'Grievance_'.$type;
            $st->Message=null;
            $text=Employment_Stages::find($stage);
            $st->Text=$text->Text;
            $stages_w_s[]=$st;
        }
        //set all grive
        if(count($stages_w_s)){return $stages_w_s;}else{return null;}
    }
    public static function Seatings($data,$accept=null,$message=null,$messagetype=null){
        //Employment_Grievance
        $stages_w_s=[];$grilist=new \stdClass();
        if(count($data->Employment_Seatings) == 0)return null;
        foreach ($data->Employment_Seatings as $key => $value) {
            $element=$value;
            $Employment_PeopleNewStage=Employment_PeopleNewStage::where('id',$element->Stage_id)->first();
            if(is_null($Employment_PeopleNewStage))return false;
            $stage=$Employment_PeopleNewStage->Stage_id;
            //////////////// /////////////get result//////////////////////
            $nextresult=Employment_PeopleNewStage::where('People_id',$data->id)->where('id','>',$element->Stage_id)->first();
            if($nextresult){
                $result=$nextresult->Status_id;
            }else{
                $result=$Employment_PeopleNewStage->Status_id;
            }
            $created_at=strtotime($element->created_at);
            if($stage == 14){
                $type='Editorial';
            }elseif($stage == 13){
                $type='Meating';
            }else{
                $type='Practical';
            }
            $st=new \stdClass();
            $st->StageId=$stage;
            $st->Result=$result;
            $st->CreatedAt=$created_at;
            $st->Type= 'Seatings_'.$type;
            $st->Message=null;
            $text=Employment_Stages::find($stage);
            $st->Text=$text->Text;
            $st->Number=$element->Employment_Committee->Number;
            $st->Committee_Date=$element->Committee_Date;
            $st->Committee=$element->Employment_Committee;
            //dd($element);
            if(is_null($st->Committee->Committee_Memebers) || $st->Committee->Committee_Memebers == ''){
                $st->Committee->Committee_Memebers=[];
            }else{
                if(\Str::isJson($st->Committee->Committee_Memebers)){
                    $st->Committee->Committee_Memebers=json_encode($st->Committee->Committee_Memebers,true);
                }
            }
            //////get committee informations
            $stages_w_s[]=$st;
        }

        //set all grive
        if(count($stages_w_s)){return $stages_w_s;}else{return null;}
    }
    public static function allStagesIds(){
        $Employment_Stages=Employment_Stages::get('id');
        $stagws=[];
        foreach ($Employment_Stages as $key => $value) {
            $stagws[]=$value->id;
        }
        return $stagws;
    }
    public static function LastStages($data,$accept=null,$message=null,$messagetype=null){
        
        $list=new \stdClass();
        $list->stages=self::HtmlStagesList($data,$accept,$message,$messagetype);
        //$list= ['stages'=>self::HtmlStagesList($data,$accept,$message,$messagetype)];
        $entrylist=[];$stagelist=[];$lastStage=end($list->stages);$apply=[];
            foreach( $list->stages as $key => $value ){
                $entryarr=['apply','complete','Grievance'];
                if(in_array($value->Type,$entryarr)){
                    $entrylist[]  =$value;
                }else{
                    $stagelist[]  =$value;
                }
                if($value->Type== 'apply'){$apply=$value;}
            }
            $list->Last=$lastStage;
            $list->LastEntry=end($entrylist);
            if($data['id'] == 30){
                //dd($entrylist);
            }
            $list->apply=$apply;
            if(count($stagelist)){$list->LastStage=$stagelist[0];}else{$list->LastStage=null;}           
            return $list;
    }
}   