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
trait printTrait{
    /**
     * query
     * create the query for people
     * need ids:array(),section,
     * return self::$people
     * @return void
     */
    public static function query(){
        $query=Employment_People::with(
            'Employment_StartAnnonces','Employment_Job','Employment_PeopleNewData',
            'Employment_Stages','Employment_PeopleNewStage','Employment_PeopleDegrees',
            'BornGovernorates','BornCities','LiveGovernorates',
            'LiveCities','Employment_Grievance','Employment_Seatings',
            'Employment_Health','Employment_MaritalStatus','Employment_Army',
            'Employment_Ama','Employment_Education','Employment_Drivers'
        )
        ->with(
            'Employment_Job.Mosama_JobNames','Employment_Job.Mosama_JobNames.Mosama_Groups','Employment_Job.Mosama_JobNames.Mosama_JobTitles',
            'Employment_Job.Mosama_JobNames.Mosama_Degrees'
            )
        ->with(
            'Employment_PeopleNewData.Employment_PeopleNewStage','Employment_PeopleNewData.Employment_Health','Employment_PeopleNewData.Employment_MaritalStatus',
            'Employment_PeopleNewData.Employment_Army','Employment_PeopleNewData.Employment_Ama','Employment_PeopleNewData.Employment_Education',
            'Employment_PeopleNewData.Employment_Drivers','Employment_PeopleNewData.Employment_Stages','Employment_PeopleNewData.BornGovernorates',
            'Employment_PeopleNewData.BornCities','Employment_PeopleNewData.LiveGovernorates'
            )
            ->with(
                'Employment_PeopleNewData.Employment_Job','Employment_PeopleNewData.Employment_Job.Mosama_JobNames','Employment_PeopleNewData.Employment_Job.Mosama_JobNames.Mosama_Groups',
                'Employment_PeopleNewData.Employment_Job.Mosama_JobNames.Mosama_JobTitles','Employment_PeopleNewData.Employment_Job.Mosama_JobNames.Mosama_Degrees'
                )
                
        ->whereIn('id', self::$request->input('ids'))->orderBy('id');
        if(self::$request->has('section')){
            if(self::$request->input('section') == 'Seatings'){
                $stage=self::$request->input('stage');
                $query=$query->whereHas("Employment_Seatings")->whereHas('Employment_PeopleNewStage',function($q)use($stage){
                    return $q->where('Stage_id',$stage);
                });
            }
        }
        $query=$query->get();
        if(count($query) == 0){
            self::$error->message=trans('JOBLANG::Employment_Reports.errors.UIDNotFound');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::$people=$query;
        return true;
    }    
    /**
     * DBToText
     *return self::$prePeople
     * @return void
     */
    public static function DBToText(){
        $request=self::$request;
        $peopleDB=self::$people;
        foreach($peopleDB as $a=>$person){
            $peopleDB[$a]->id=$person->id;
            $peopleDB[$a]['Annonce_id']=self::convertAnnonceToStdClass($person->Employment_StartAnnonces);
            $peopleDB[$a]['Job_id']=self::convertJobToStdClass($person->Employment_Job);
            $peopleDB[$a]['NID']=$person->NID;
            $peopleDB[$a]['BirthDate']=$person->BirthDate;
            if($person->Sex == '1'){$peopleDB[$a]['Sex']=trans('JOBLANG::Employment_People.Sex.Male');}else{$peopleDB[$a]['Sex']=trans('JOBLANG::Employment_People.Sex.Female');}
            $peopleDB[$a]['LiveGov']=$person->LiveGovernorates->Name;
            $peopleDB[$a]['LiveCity']=$person->LiveCities->Name;
            $peopleDB[$a]['LiveAddress']=$person->LiveAddress;
            $peopleDB[$a]['BornGov']=$person->BornGovernorates->Name;
            $peopleDB[$a]['BornCity']=$person->BornCities->Name;
            $peopleDB[$a]['ConnectLandline']=$person->ConnectLandline;$peopleDB[$a]['ConnectMobile']=$person->ConnectMobile;$peopleDB[$a]['ConnectEmail']=$person->ConnectEmail;
            $peopleDB[$a]['Health_id']=$person->Employment_Health->Text;
            $peopleDB[$a]['MaritalStatus_id']=$person->Employment_MaritalStatus->Text;
            $peopleDB[$a]['Arm_id']=$person->Employment_Army->Text;
            $peopleDB[$a]['Ama_id']=$person->Employment_Ama->Text;
            $peopleDB[$a]['Tamin']=$person->Tamin;
            $peopleDB[$a]['Khebra']=self::khebraToStr($person->Khebra);
            $peopleDB[$a]['Education_id']=$person->Employment_Education->text;
            $peopleDB[$a]['EducationYear']=$person->EducationYear;
            if($person->Employment_Drivers == null){
                $peopleDB[$a]['DriverDegree']=null;$peopleDB[$a]['DriverStart']=null;$peopleDB[$a]['DriverEnd']=null;
            }else{
                $peopleDB[$a]['DriverDegree']=$person->Employment_Drivers->Text;
                $peopleDB[$a]['DriverStart']=$person->DriverStart;$peopleDB[$a]['DriverEnd']=$person->DriverEnd;
            }
            $stageList=new PeopleStagesController($person);
            if(in_array('Seatings',self::$request->input('actions'))){
                if(self::$request->input('stage') == 7){
                    $Seatings=$stageList::get('HtmlSeatingsPractical',null,'array');
                }elseif(self::$request->input('stage') == 14){
                    $Seatings=$stageList::get('HtmlSeatingsEditorial',null,'array');
                }elseif(self::$request->input('stage') == 13){
                    $Seatings=$stageList::get('HtmlSeatingsapply',null,'array');
                }else{
                    $Seatings=null;
                }
                $peopleDB[$a]['printSeatings']=$Seatings;
            }
            $SeatingList=$stageList::get('HtmlSeatings',null,'array');
            $stageList=$stageList::get('HtmlStageList',null,'array');
            $stageList=PeopleStagesController::LastStages($person,null,true,'array');
            $peopleDB[$a]['stageList']=$stageList;
            if($person->Employment_PeopleDegrees !== null){
                $degrees=new \stdClass();
                $degrees->Editorial=$person->Employment_PeopleDegrees->Editorial;
                $degrees->Practical=$person->Employment_PeopleDegrees->Practical;
                $degrees->Interview=$person->Employment_PeopleDegrees->Interview;
                $peopleDB[$a]->Degrees=$degrees;
            }else{
                $peopleDB[$a]['Degrees']=null;
            }
            if($person->Employment_PeopleNewData !== null)
            {
                $person->Employment_PeopleNewData=self::setEmploymentNewData($person);
            }
            $person->Employment_Seatings=$SeatingList;
            $peopleDB[$a]['Face']=self::setFace($person);
            unset($person->Employment_PeopleDegrees);
            unset($peopleDB[$a]['LiveGovernorates']);unset($peopleDB[$a]['LiveCities']);
            unset($peopleDB[$a]['BornGovernorates']);unset($peopleDB[$a]['BornCities']);
            unset($peopleDB[$a]['Employment_Health']);unset($peopleDB[$a]['Employment_MaritalStatus']);
            unset($peopleDB[$a]['Employment_Army']);unset($peopleDB[$a]['Employment_Ama']);
            unset($peopleDB[$a]['Employment_Education']);unset($peopleDB[$a]['Employment_Drivers']);
            unset($peopleDB[$a]['Employment_Job']);
            unset($peopleDB[$a]['Employment_StartAnnonces']);
            unset($peopleDB[$a]['updated_at']);unset($peopleDB[$a]['deleted_at']);
        }
        self::$prePeople=$peopleDB;
    }
    public static function convertAnnonceToStdClass($StartAnnonces){
            $annonceinfo=new \stdClass();
            $annonceinfo->Number=$StartAnnonces->Number;
            $annonceinfo->Year=$StartAnnonces->Year;
            return $annonceinfo;
    }
    public static function convertJobToStdClass($Job){
        $Job_id=new \stdClass();
            $Job_id->Code=$Job->Code;
            $Job_id->Mosama_JobNames=$Job->Mosama_JobNames->text;
            $Job_id->Mosama_Groups=$Job->Mosama_JobNames->Mosama_Groups->text;
            $Job_id->Mosama_JobTitles=$Job->Mosama_JobNames->Mosama_JobTitles->text;
            $Job_id->Mosama_Degrees=$Job->Mosama_JobNames->Mosama_Degrees->text;
            return $Job_id;
    }
    
    public static function setFace($person){
        $face=new \stdClass();
        $face->id=$person->id;
        $face->NID=$person->NID;
        $face->Annonce_id=$person->Annonce_id;
        //last stage
        $face->lastStage=$person->stageList->Last;
        //fullname
        if($person->Employment_PeopleNewData !== null){
            $face->Fname=$person->Employment_PeopleNewData->Fname;
            $face->Sname=$person->Employment_PeopleNewData->Sname;
            $face->Tname=$person->Employment_PeopleNewData->Tname;
            $face->Lname=$person->Employment_PeopleNewData->Lname;
            $face->Job_id=$person->Employment_PeopleNewData->Job_id;
            $face->ConnectLandline=$person->Employment_PeopleNewData->ConnectLandline;
            $face->ConnectMobile=$person->Employment_PeopleNewData->ConnectMobile;
            $face->ConnectEmail=$person->Employment_PeopleNewData->ConnectEmail;
            $face->Health_id=$person->Employment_PeopleNewData->Health_id;
        }else{
            $face->Job_id=$person->Job_id;
            $face->Fname=$person->Fname;
            $face->Sname=$person->Sname;
            $face->Tname=$person->Tname;
            $face->Lname=$person->Lname;
            $face->ConnectLandline=$person->ConnectLandline;
            $face->ConnectMobile=$person->ConnectMobile;
            $face->ConnectEmail=$person->ConnectEmail;
            $face->Health_id=$person->Health_id;
        }
        return $face;
}
    public static function printWanted(){
        $items=self::$prePeople;
        $ret=[];
        $request=self::$request;
        //dd($request->toArray());
        foreach ($items as $key => $person) {
            $data=new \stdClass;
            //set face page for all
                $data->Face=$person['Face'];
                $data->Grievance=[];
                if(in_array('Full',$request->input('actions'))){
                    $data->LastEntry=self::getLastEntry($person);
                    $data->Applydata=self::getApplydata($person);
                    $data->Grievance['apply']=self::getGrievance($person,'apply');
                    $data->Grievance['Editorial']=self::getGrievance($person,'Editorial');
                    $data->Grievance['Practical']=self::getGrievance($person,'Practical');
                    $data->Downloads=self::getDownloads($person);
                    $data->Degrees=$person->Degrees;
                    $data->Seatings=self::getSeatings($person);
                }else{
                    if(in_array('LastEntry',$request->input('actions'))){
                        $data->LastEntry=self::getLastEntry($person);
                    }
                    if(in_array('CheckApplyData',$request->input('actions'))){
                        $data->Applydata=self::getApplydata($person);
                    }
                    if(in_array('GrievanceApply',$request->input('actions'))){
                        $data->Grievance['apply']=self::getGrievance($person,'apply');
                    }
                    if(in_array('GrievanceEditorial',$request->input('actions'))){
                        $data->Grievance['Editorial']=self::getGrievance($person,'Editorial');
                    }
                    if(in_array('GrievancePractical',$request->input('actions'))){
                        $data->Grievance['Practical']=self::getGrievance($person,'Practical');
                    }
                    if(in_array('Downloads',$request->input('actions'))){
                        $data->Downloads=self::getDownloads($person);
                    }
                    if(in_array('Degrees',$request->input('actions'))){
                        $data->Degrees=$person->Degrees;
                    }
                    if(in_array('Seatings',$request->input('actions'))){
                        $data->printSeatings=$person->printSeatings;
                        $data->Seatings=self::getSeatings($person);
                    }
                }
                $data->StageList=$person['stageList'];
                $carbon = \Carbon\Carbon::now();
                $data->PrintDate=$carbon->toRfc7231String();
                if($data->Face->id == 30){
                    //dd($data->StageList->LastEntry);
                }
                
                $ret[$key]=$data;
        }
        self::$prePeople=$ret;
    }
    public static function getSeatings($person){
        if(in_array(self::$request->input('section'),['Seatings'])){
            $ret=[];
            //dd($person->Employment_Seatings);
            foreach($person->Employment_Seatings as $a=>$b){
                $data=new \stdClass;
                $data->Type=$b->Type;
                $data->Text=$b->Text;
                $data->Number=$b->Number;
                $data->Date=$b->Date;
                $data->Committee_Memebers=$b->Committee->Committee_Memebers;
                $data->Committee_Name=$b->Committee->Name;
                $data->Committee_Date=$b->Committee->Committee_Date;
                $ret[]=$data;
            }
            return $ret;
        }
    }
    /**
     * checkSeatingsRequest
     * <ul>check:<li>ids:array()</li><li>section:Seatings</li><li>type:'Table' or 'Ticket'</li><li>table:'tableForSign','tableForMembers' or 'tableForCollection'</li><li>date:datetime</li></ul>
     * return merge $request
     * @return void
     */
    public static function checkSeatingsRequest()
    {
        $request=self::$request;
        $atts=[
            'ids'=>'printForm.ids',
            'section'=>'SeatingForm.Sections',
            'type'=>'SeatingForm.type',
            'table'=>'SeatingForm.table',
            'printDate'=>'lastStage.Date',
            'stage'=>'SeatingForm.Stage'
        ];
        foreach ($atts as $key => $value) {
            $atts[$key]=trans("JOBLANG::Employment_Reports.".$value);
        }
        $roles=[ 
            'ids'=>'required',
            'section'=>'required|string|in:Seatings',
            'type'=>'required|in:Table,Ticket',
            'table'=>'required|in:tableForSign,tableForMembers,tableForCollection',
            'printDate'=>'required|date',
            'stage'=>'required|exists:Employment_Stages,id|filled|numeric'
        ];
        $vlruls=['required','string','in','date','exists','filled','numeric'];
        $vlruls2=[];
        foreach ($vlruls as $key => $value) {
            $vlruls2[$value]=trans("JOBLANG::Employment_Reports.errors.pleaseSelect").':attribute ';
        }
        $validator = \Validator::make($request->all(), $roles,$vlruls2,$atts);
        if($validator->fails()){
            $errors=implode('<br>',\Arr::map($validator->messages()->toArray(),function($v,$k){
                return $v[0];
            }));
            self::$error->message=$errors;self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        if(gettype($request->input('ids')) == 'string' && \AmerHelper::isJson($request->input('ids'))){
            $ids=json_decode($request->input('ids'),true);
        }elseif(gettype($request->input('ids')) == 'array'){
            $ids=$request->input('ids');
        }else{
            self::$error->message=trans("JOBLANG::Employment_Reports.errors.publicError",['name'=>trans("JOBLANG::Employment_Reports.ids")]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $data=[];
        $data['ids']=$ids;
        $data['stage']=$request->input('stage');
        $data['section']=$request->input('section');
        $data['type']=$request->input('type');
        $data['table']=$request->input('table');
        $data['actions']=['Seatings'];
        $data['printDate']=$request->input('printDate');
        self::$request->replace($data);
        return true;
    }
    public static function checkFilesRequest(){
        $request=self::$request;
        $actions=['Full','LastEntry','Downloads','CheckApplyData','GrievanceApply','GrievanceEditorial','GrievancePractical','Degrees','SeatingsTable'];
        $types=['file','faceWfile','face'];
        $has=['printDate'=>'lastStage.Date','actions'=>'printForm.action','type'=>'printForm.type','ids'=>'printForm.ids'];
        foreach($has as $a=>$b){if(!$request->has($a)){self::$error->message=trans("JOBLANG::Employment_Reports.errors.publicError",['name'=>trans("JOBLANG::Employment_Reports.".$b)]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}}
        foreach($has as $a=>$b){if(!$request->filled($a)){self::$error->message=trans("JOBLANG::Employment_Reports.errors.publicError",['name'=>trans("JOBLANG::Employment_Reports.".$b)]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}}
        if(gettype($request->input('ids')) == 'string' && \AmerHelper::isJson($request->input('ids'))){
            $ids=json_decode($request->input('ids'),true);
        }elseif(gettype($request->input('ids')) == 'array'){
            $ids=$request->input('ids');
        }else{
            self::$error->message=trans("JOBLANG::Employment_Reports.errors.publicError",['name'=>trans("JOBLANG::Employment_Reports.ids")]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        if(!is_array($request->input('actions'))){self::$error->message=trans("JOBLANG::Employment_Reports.errors.publicError",['name'=>trans("JOBLANG::Employment_Reports.action")]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
        foreach($request->input('actions') as $a=>$b){
            if(!in_array($b,$actions))
            {
                self::$error->message=$b;self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
            }
        }
        if(!in_array($request->input('type'),$types)){self::$error->message=trans("JOBLANG::Employment_Reports.errors.publicError",['name'=>trans("JOBLANG::Employment_Reports.type")]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
        $types=$request->input('type');
        $date=$request->input('date');
        if(!$request->has('current_page')){
            $current_page='1';
        }else{
            $current_page=$request->input('current_page');
        }
        
        $data=[];
        $data['ids']=$ids;
        $data['stage']=$request->input('stage');
        $data['section']=$request->input('section');
        $data['type']=$request->input('type');
        $data['table']=$request->input('table');
        $data['actions']=$request->input('actions');
        $data['printDate']=$request->input('printDate');
        self::$request->replace($data);
        return true;
    }
    /**
     * checkPrintForm
     * check the form requirements by checking self::$request
     * if request has Section it mean we will print Seatings else we will print others<br>
     * <ul><li>Seatings needs:<ul><li>ids:array()</li><li>stage: OR null</li><li>type:   Ticket or Table</li><li>table:'tableForSign','tableForMembers' or 'tableForCollection'</li><li>date: datetime</li></ul></li><li>Others:<ul><li>ids:array()</li><li>actions:array() choose multiple from ['Full','LastEntry','Downloads','CheckApplyData','GrievanceApply','GrievanceEditorial','GrievancePractical','Degrees','SeatingsTable']</li><li>types:'faceWfile' or 'face'</li><li>date: datetime</li></ul></li></ul>
     * checking useing checkSeatingsRequest(),checkFilesRequest()
     * @return void
     */
    public static function checkPrintForm(){
        $request=self::$request;
        //dd($request);
        if($request->has('section')){
            if($request->input('section') == 'Seatings'){
                return self::checkSeatingsRequest();
            }elseif($request->input('section') == 'file'){
                return self::checkFilesRequest();
            }
        }
        return true;
    }
    public static function pageFooter($config){
        $html='<hr>';
        $html.='
                    <table cellspacing="0" cellpadding="0" border="0" align="center">
                        <tr>
                            <td style="border-left:1px solid black;width:60mm">
                                    محطة مياه شمال سيناء المرشحة
                                    <br>
                                    القنطرة شرق - الاسماعيلية
                            </td>
                            <td style="border-left:1px solid black;width:60mm">
                                tel:0643751317 -0643751318 - 0643751345
                            </td>
                            <td style="border-left:1px solid black;width:60mm">Fax:0643751319</td>
                            <td style="width:10mm">%pageNumber%</td>
                        </tr>
                    </table>';
        return $html;
    }
    public static function pageHeader($config){
        $arco=[];
        $arco[]=config('Amer.amer.co_name');
        $arco[]=config('Amer.amer.hc_name');
        $arco[]=config('Amer.amer.min_name');
        $arco=join('<br>',$arco);
          $html="";
          $html.='
                    <table cellspacing="0" cellpadding="0" border="0" align="center">
                        <tr>
                            <td rowspan="3">'.$arco.'</td>
                            <td><img src="'.$config['pdfHeaderLogo']['Src'].'" width="60"></td>
                            <td rowspan="3">'.config('Amer.amer.co_name_english').'</td>
                        </tr>
                    </table><hr>';
          return $html;
    }
    public static function createTicket(){
        $config=config('Amer.tcpdf');
        $pdf = new \TCPDF($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->SetCreator($config['PDFCreator']);
        $pdf->SetAuthor(config('Amer.amer.co_name'));
        $pdf->SetTitle(trans('JOBLANG::Employment_seatings.Employment_seatings'));
        $pdf->SetSubject(trans('JOBLANG::Employment_seatings.Employment_seatings')) ;
        $pdf->SetKeywords(implode(',',explode(' ',trans('JOBLANG::Employment_seatings.Employment_seatings')." ".config('Amer.amer.co_name'))));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,10,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->SetAutoPageBreak(TRUE, $config['PdfMargin']['MarginBottom']);
        $pdf->setImageScale($config['ImageScaleRatio']);
        if (@file_exists($config['packagePath'].'lang/ara.php')) {
            require_once($config['packagePath'].'lang/ara.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('aealarabiya', '', 12, '', true);
        $pdf->setRTL(true);
        $pdf->AddPage();
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $html='';
        $html="";
        $html.=View::make("Employment::admin.seatings.ticketHeader")->render();
            $arr=self::$prePeople;
            //$html.= View::make("Employment::admin.seatings.ticket",['data'=>$arr[0],'pdf'=>$pdf])->render();
            foreach($arr as $a=>$b){
                $html.= View::make("Employment::admin.seatings.ticket",['data'=>$b,'pdf'=>$pdf])->render();
            }
            $tagvs = [
                'div' => [
                    ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
                ]
            ];
            $pdf->setHtmlVSpace($tagvs);
        $pdf->writeHTML($html, true, false, false, false, 'right');
        $filename="A.pdf";
        return $pdf->Output($filename, 'E');
    }
    public static function createTicketTableForSign(){
        $config=config('Amer.tcpdf');
        $pdf = new \Amerhendy\Pdf\Helpers\AmerPdf($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->SetCreator($config['PDFCreator']);
        $pdf->SetAuthor(config('Amer.amer.co_name'));
        $pdf->SetTitle(trans('JOBLANG::Employment_seatings.Employment_seatings'));
        $pdf->SetSubject(trans('JOBLANG::Employment_seatings.Employment_seatings')) ;
        $pdf->SetKeywords(implode(',',explode(' ',trans('JOBLANG::Employment_seatings.Employment_seatings')." ".config('Amer.amer.co_name'))));
        $pdf->setImageScale($config['ImageScaleRatio']);
        if (@file_exists($config['packagePath'].'lang/ara.php')) {
            require_once($config['packagePath'].'lang/ara.php');
            $pdf->setLanguageArray($l);
        }
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('aealarabiya', '', 12, '', true);
        $pdf->setRTL(true);
        //$pdf->setCustomFooterText('THIS IS CUSTOM FOOTER');
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=self::pageHeader($config), $tc=array(0,0,0), $lc=array(0,0,0));
        $pdf->setFooterHtml($font=['aealarabiya', 'B', 10],$hs=self::pageFooter($config), $tc=array(0,0,0), $lc=array(0,0,0),$line=true);
        $pdf->setFooterFont(Array($config['Font']['Date']['name'], '', $config['Font']['Date']['Size']));
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,20,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
        $pdf->SetAutoPageBreak(TRUE, $config['PdfMargin']['MarginBottom']);
        $pdf->AddPage();
        $html='';
        $html="";
        $html.=View::make("Employment::admin.seatings.TableForSignHeader")->render();
            $arr=self::$prePeople;
            $html.= View::make("Employment::admin.seatings.TableForSign",['data'=>$arr,'pdf'=>$pdf])->render();
            $tagvs = [
                'div' => [
                    ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
                ]
            ];
            $pdf->setHtmlVSpace($tagvs);
        $pdf->writeHTML($html, true, false, false, false, 'right');
        $filename="A.pdf";
        return $pdf->Output($filename, 'E');
    }
}
