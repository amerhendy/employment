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
    public $person,$stagesListData,$cachStatus,$cachStages;
    public  $forsearch=false;
    public function __construct($person){
        $this->person=$person;
        $this->cachCode();
        $this->stagesList();
        
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
    public function get($wanted){
        switch ($wanted) {
            case  \Str::startsWith($wanted,'Html'):
                $list=$this->HtmlStagesList();

                break;

            default:
                $list=$this->stagesListData;
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
                return array_filter(
                                    $list,
                                    fn ($item) =>
                                        isset($item->FNfunction)          // تأكُّد من وجود الخاصية
                                        && in_array($item->FNfunction, ['complete'])
                                );

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
    public  function lastStage($person,$Status=null,$message=null,$messagetype=null){
        $list=$this->stagesList($person,$Status);
        return $list[0];
    }
    private function cachCode(){
        $status=Employment_Status::get(['id','text','code'])->toArray();
        $this->cachStatus = collect($status)->mapWithKeys(function ($item) {
                return [$item['id'] => ['id'=>$item['id'],'text'=>$item['text'],'code'=>$item['code'],]];
            })->toArray();
        $stages=Employment_Stages::get();
        $this->cachStages = collect($stages)->mapWithKeys(function ($item) {
            $functionName=$item->functionName;
            $front=$content=$function=$control=$text=null;
            if(\Str::startsWith($item['page'],'S')){
                $text=$functionName->name;
                $content=$functionName->content;
            }else{
                $text=$functionName->text;
                $function=$functionName->function;
                $control=$functionName->control;
            }
            $front=$item['front'];
            return [$item['id'] => ['id'=>$item['id'],'text'=>$item['text'],'code'=>$item['code'],
            'FNfront'=>$front,'FNtext'=>$text,'FNfunction'=>$function,'FNcontrol'=>$control,'content'=>$content]];
            })->toArray();
    }
    private function makeStage($stageId, $newStageId, $result, $createdAt, $type, $text, $message = null, $extra = []) {
        $stageCode='';
        $resultcode='';
        $st = (object) array_merge([
            'StageId' => $stageId,
            'StageCode' => $stageCode,
            'newStageId' => $newStageId,
            'Result' => $result,
            'ResultCode' => $resultcode,
            'CreatedAt' => $createdAt,
            'Type' => $type,
            'Text' => $text,
            'Message' => $message,
        ], $extra);
        return $st;
    }
    public function stagesList($accept=null,$message=null,$messagetype=null){
        $data=$this->person;
        $person=$this->person;
        
        //$stageId, $newStageId, $result,$resultcode, $createdAt, $type, $text, $message = null, $extra = []
        $stages_w_s[]=$this->makeStage($person->stage_id,null,$person->result_id,strtotime($person->created_at),'apply',$person->employment_stages->text,$person->message ?? null,);
        //set new data Stage
        $newdata=$this->newData($accept,$message);
        if(!is_null($newdata))$stages_w_s[]=$newdata;
        // set new stage
        $newstage=$this->newStage($accept,$message);
        if(!is_null($newstage)){
            foreach($newstage as $k=>$v){
                $stages_w_s[]=$v;
            }
            }
        // set Grievance
        $newGrievance=$this->newGrievance($data,$accept,$message);
        if(!is_null($newGrievance))$stages_w_s[]=$newGrievance;
        $Seatings=self::Seatings($data,$accept,$message);
        if(!is_null($Seatings))$stages_w_s[]=$Seatings;
        $collection=collect($stages_w_s)->sortBy('CreatedAt')->values();
        $collection=$collection->all();
        $this->stagesListData=$collection;
    }
    public function HtmlStagesList($accept=null,$message=null,$messagetype=null){
        $data=$this->person;
        $list=$this->stagesListData;
        $newlist=[];
        foreach($list as $key=>$value){
            $std=new \stdClass();
            $std->StageId=$value->StageId;
            if($value->Type !== 'apply'){
                $std->newStageId=$value->newStageId;
            }
            $std->Text=$value->Text;
            $std->created_at=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',$value->CreatedAt);
            $result=Employment_Status::where('id',$value->Result)->first();
            $std->Result=$result->text ?? null;
            if($value->Message == null){
                $std->Message="";
            }else{
                $std->Message=self::prepare_message_for_print($value->Message,$messagetype);
            }
            $std->Type=$value->Type;

            if(\Str::startsWith($value->Type,'Seatings')){
                $std->Number=$value->Number;
                if(!is_null($value->Committee_Date))
                {
                    $std->Date=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',strtotime($value->Committee_Date));
                }else{
                    $std->Date=null;
                }
                if(isset($value->Committee)){
                    $std->Committee_Number=$value->Committee->Number;
                    $std->Committee_Name=$value->Committee->Name;
                    $std->Committee_Date=date(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s',strtotime($value->Committee->Committee_Date));
                    $std->Committee_Memebers=$value->Committee->Committee_Memebers;
                }
            }
            if(property_exists($value,'s')){
                $std->s=$value->s;
            }
            $newlist[$key]=$std;
        }
        return $newlist;
    }
    public function newData($accept=null,$message=null,$messagetype=null){
        $data=$this->person;
        $stages_w_s=[];
        if($data->Employment_PeopleNewData ==null)return null;
        $element=$data->Employment_PeopleNewData;
        $stage=Employment_PeopleNewStage::with('Employment_Stages')
                    ->where('id',$element->stage_id)
                    ->where('people_id',$element->people_id)
                    ->orderBy('created_at')
                    ->get('stage_id')
                    ->first();
        if(!$stage)return null;
        return $this->makeStage(
            $stage->stage_id,
            $element->stage_id,
            $element['result_id'],
            strtotime($element['created_at']),
            'complete',
            Employment_Stages::find($stage->stage_id)?->text,
            $element['message'] ?? null,);
    }
    public  function newStage($accept=null,$message=null,$messagetype=null){
        $data=$this->person;
        $stages_w_s=[];
        if($data->Employment_PeopleNewStage == null)return null;
        $er=$this->person->Employment_PeopleNewStage->map(function ($element){
            $extra=[];
            $message=null;
            $message=$this->cachStages[$element->employment_stages->id]['content'] ?? $element->message;
            if($this->cachStages[$element->employment_stages->id]['FNcontrol'] !== null){
                $extra=$this->cachStages[$element->employment_stages->id];
            }
            
            return $this->makeStage($element->stage_id,$element->id,$element->status_id,strtotime($element->created_at),'Stage',$this->cachStages[$element->employment_stages->id]['text'],$message,$extra);
        });
        return $er->toArray();
        if(count($stages_w_s)){return $stages_w_s;}else{return null;}
    }
    public  function newGrievance($data,$accept=null,$message=null,$messagetype=null){
        //Employment_Grievance
        $stages_w_s=[];$grilist=new \stdClass();
        if(count($data->Employment_Grievance) == 0)return null;
        foreach ($data->Employment_Grievance as $key => $value) {
            $element=$value;
            $Employment_PeopleNewStage=Employment_PeopleNewStage::where('id',$element->Stage_id)->where('People_id',$element->People_id)->first();
            if($element->Stage_id !== 1){
                //dd($element->Stage_id);
            }
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
                $text='AppliedGrievance';
            }elseif($stage == 5){
                $type='Editorial';
                $text='WritingGrievance';
            }else{
                $type='Practical';
                $text='PracticalGrievance';
            }
            $text=trans('JOBLANG::Employment_Grievance.'.$text);
            $st=new \stdClass();
            $st->StageId=$stage;
            $st->newStageId=$nextresult->id;
            $st->Result=$result;
            $st->CreatedAt=$created_at;
            $st->Type= 'Grievance_'.$type;
            $st->Message=null;
            ///$text=Employment_Stages::find($stage);
            $st->Text=$text;
            $stages_w_s[]=$st;
        }
        //set all grive
        if(count($stages_w_s)){return $stages_w_s;}else{return null;}
    }
    public  function Seatings($data,$accept=null,$message=null,$messagetype=null){
        //Employment_Grievance
        $stages_w_s=[];$grilist=new \stdClass();
        if(count($data->Employment_Seatings) == 0)return null;
        foreach ($data->Employment_Seatings as $key => $value) {
            $element=$value;
            $Employment_PeopleNewStage=Employment_PeopleNewStage::where('id',$element->Stage_id)->where('People_id',$element->People_id)->first();
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
    public  function allStagesIds(){
        $Employment_Stages=Employment_Stages::get('id');
        $stagws=[];
        foreach ($Employment_Stages as $key => $value) {
            $stagws[]=$value->id;
        }
        return $stagws;
    }
    public  function LastStages($data,$accept=null,$message=null,$messagetype=null){

        $list=new \stdClass();
        $list->stages=$this->HtmlStagesList($data,$accept,$message,$messagetype);
        //$list= ['stages'=>$this->HtmlStagesList($data,$accept,$message,$messagetype)];
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
    public  function prepare_message_for_print($data,$messagetype=null)
    {
        $re=[];
        if(is_null($data)){
            return null;
        }
        if(gettype($data) == 'string'){
            if(\Str::isJson($data)){
                $data=json_decode($data,true);
                foreach($data as $key=>$val){
                    if(!is_array($val)){
                        $re[]=$val;
                    }else{
                        foreach($val as $a=>$b){
                            $re[]=trans($b);
                        }
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
}
