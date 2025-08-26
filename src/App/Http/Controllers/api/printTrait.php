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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
trait printTrait{
    /**
     * query
     * create the query for people
     * need ids:array(),section,
     * return self::$people
     * @return void
     */
    public static $forsearch=false;

    public static function pdfdod($data){
        $config=config('Amer.TCPDF');
        $config['ViewerPreferences']=[
            'HideToolbar' => true,
            'HideMenubar' => true,
            'HideWindowUI' => true,
            'FitWindow' => true,
            'CenterWindow' => true,
            'DisplayDocTitle' => true,
            'NonFullScreenPageMode' => 'UseNone', // UseNone, UseOutlines, UseThumbs, UseOC
            'ViewArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
            'ViewClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
            'PrintArea' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
            'PrintClip' => 'CropBox', // CropBox, BleedBox, TrimBox, ArtBox
            'PrintScaling' => 'AppDefault', // None, AppDefault
            'Duplex' => 'DuplexFlipLongEdge', // Simplex, DuplexFlipShortEdge, DuplexFlipLongEdge
            'PickTrayByPDFSize' => true,
            'PrintPageRange' => array(1,1,2,3),
            'NumCopies' => 2
        ];
        $pdf = new \Amerhendy\Pdf\Helpers\AmerPdf($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->SetAuthor(config('Amer.amer.co_name'));
        $pdf->SetTitle($data->headerTitle);
        $pdf->SetSubject($data->headerTitle) ;
        $pdf->SetKeywords(implode(',',explode(' ',$data->headerTitle." ".config('Amer.amer.co_name'))));
        $pageheader=View::make("Employment::admin.pdfheader",['config'=>$config])->render();
        $pageFooter=View::make("Employment::admin.pdfFooter",['config'=>$config])->render();
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=$pageheader, $tc=array(0,0,0), $lc=array(0,0,0));
        $pdf->setFooterHtml($font=['aealarabiya', 'B', 10],$hs=$pageFooter, $tc=array(0,0,0), $lc=array(0,0,0),$line=true);
        $pdf->setFooterFont(Array($config['Font']['Date']['name'], '', $config['Font']['Date']['Size']));
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,20,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
        $pdf->SetAutoPageBreak(TRUE, $config['PdfMargin']['MarginBottom']);
        $pdf->setImageScale($config['ImageScaleRatio']);
        $lngFile=$config['packagePath'].DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'ara.php';
        if (@file_exists($lngFile)) {
            require_once($lngFile);
            $pdf->setLanguageArray($l);
        }
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('aealarabiya', '', 12, '', true);
        $pdf->setRTL(true);
        $pdf->AddPage();
        $pdf->setViewerPreferences($config['ViewerPreferences']);
        return $pdf;
    }
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
            $peopleDB[$a]['annonce_id']=self::convertAnnonceToStdClass($person->Employment_StartAnnonces);
            $peopleDB[$a]['job_id']=self::convertJobToStdClass($person->Employment_Job);
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
            $peopleDB[$a]['Khebra']=$person->khebraToStr;
            $peopleDB[$a]['Education_id']=$person->Employment_Education->text;
            $peopleDB[$a]['EducationYear']=$person->EducationYear;
            if($person->Employment_Drivers == null){
                $peopleDB[$a]['DriverDegree']=null;$peopleDB[$a]['DriverStart']=null;$peopleDB[$a]['DriverEnd']=null;
            }else{
                $peopleDB[$a]['DriverDegree']=$person->Employment_Drivers->Text;
                $peopleDB[$a]['DriverStart']=$person->DriverStart;$peopleDB[$a]['DriverEnd']=$person->DriverEnd;
            }
            $stageList=new PeopleStagesController($person);
            $stageList::$forsearch=true;
            if(self::$request->has('actions')){
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
                    if(count($Seatings) == 1){
                        $Seatings=$Seatings[array_keys($Seatings)[0]];
                    }
                    $peopleDB[$a]['printSeatings']=$Seatings;
                }
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
//            dd()
            if($person->Employment_PeopleNewData !== null)
            {
                $person->Employment_PeopleNewData=AdminUpToDate::setEmploymentNewData($person);
            }
            $person->FullName=$person->FullName;
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
            $annonceinfo->Slug=$StartAnnonces->Slug;
            return $annonceinfo;
    }
    public static function convertJobToStdClass($Job){
        $job_id=new \stdClass();
            $job_id->Code=$Job->Code;
            $job_id->Slug=$Job->Slug;
            $job_id->Mosama_JobNames=$Job->Mosama_JobNames->text;
            $job_id->Mosama_Groups=$Job->Mosama_JobNames->Mosama_Groups->text;
            $job_id->Mosama_JobTitles=$Job->Mosama_JobNames->Mosama_JobTitles->text;
            $job_id->Mosama_Degrees=$Job->Mosama_JobNames->Mosama_Degrees->text;
            return $job_id;
    }
    public static function khebraToStr($khebra,$type=null){
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
            $type=$khebra[$keys[0]];
            if(isset($keys[1])){
                $time=$khebra[$keys[1]];
            }else{
                $time=$khebra[$keys[0]];
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

                    $replaced = \Str::replaceArray('?', [$type, $time], trans('JOBLANG::Employment_Reports.printForm.khebra'));
            }
        return $khebra;

    }
    public static function setFace($person){
        $face=new \stdClass();
        $face->id=$person->id;
        $face->NID=$person->NID;
        $face->annonce_id=$person->annonce_id;
        //last stage
        $face->lastStage=$person->stageList->Last;
        //fullname
        if($person->Employment_PeopleNewData !== null){
            $face->FullName=$person->Employment_PeopleNewData->FullName;
            $face->job_id=$person->Employment_PeopleNewData->job_id;
            $face->ConnectLandline=$person->Employment_PeopleNewData->ConnectLandline;
            $face->ConnectMobile=$person->Employment_PeopleNewData->ConnectMobile;
            $face->ConnectEmail=$person->Employment_PeopleNewData->ConnectEmail;
            $face->Health_id=$person->Employment_PeopleNewData->Health_id;
        }else{
            $face->job_id=$person->job_id;
            $face->FullName=$person->FullName;
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
                //$data->PrintDate=$carbon->toRfc7231String();
                $data->PrintDate=$carbon->format(config('Amer.amer.Carbon_dateTimeFormat') ?? 'Y-m-d H:i:s');
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
                $ret[]=$b;
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
        }else{
            self::$error->message=trans('JOBLANG::Employment_Reports.errors.NoIdsSent');self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
    }
    public static function FileSection(){
        $config=config('Amer.TCPDF');
        $pdf = new \Amerhendy\Pdf\Helpers\AmerPdf($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->setViewerPreferences($config['ViewerPreferences']);
        $pdf->SetCreator($config['PDFCreator']);
        $pdf->SetAuthor(config('Amer.amer.co_name'));
        $pdf->SetTitle(trans('JOBLANG::Employment_Reports.Employment_Reports'));
        $pdf->SetSubject(trans('JOBLANG::Employment_Reports.Employment_Reports')) ;
        $pdf->SetKeywords(implode(',',explode(' ',trans('JOBLANG::Employment_Reports.Employment_Reports')." ".config('Amer.amer.co_name'))));
        $pdf->setImageScale($config['ImageScaleRatio']);
        if (@file_exists($config['packagePath'].'lang/ara.php')) {
            require_once($config['packagePath'].'lang/ara.php');
            $pdf->setLanguageArray($l);
        }
        $pageFooter=View::make("Employment::admin.pdfFooter",['config'=>$config])->render();
        $pdf->setFooterHtml($font=['aealarabiya', 'B', 10],$hs=$pageFooter, $tc=array(0,0,0), $lc=array(0,0,0),$line=true);
        $pdf->setFooterFont(Array($config['Font']['Date']['name'], '', $config['Font']['Date']['Size']));
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,33,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
        $pdf->SetAutoPageBreak(TRUE, $config['PdfMargin']['MarginBottom']);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('aealarabiya', '', 11, '', true);
        $pdf->setRTL(true);
        $pdf->startPageGroup();
        $tablewidth=190;
        $css=View::make("Employment::admin.seatings.CssTable",['data'=>['tablewidth'=>$tablewidth]])->render();
        ///Committee_Number,Committee_Name
            $arr=self::$prePeople;
            self::WantedpagesFN();
            foreach (self::$pages as $key => $value) {
                $pagetitle=explode('_',$value);
                $uid=$pagetitle[1];
                $title=$pagetitle[0];
                $heads='';
                $user=\Arr::where($arr,function($v,$k)use($uid){
                    return $v->Face->id == $uid;
                });
                $user=$user[array_keys($user)[0]];
                $user=json_decode(json_encode($user),true);
                $user['PageName']=trans('JOBLANG::Employment_Reports.printForm.fileTite.'.$title);
                $pageheader1=View::make("Employment::admin.pdfheader",['config'=>$config])->render();
                $pageheader2=View::make("Employment::admin.printreports.header",['config'=>$config,'value'=>$user])->render();
                $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=$pageheader1.$pageheader2, $tc=array(0,0,0), $lc=array(0,0,0));
                $pdf->AddPage();
                $config['request']['actions']=self::$request['actions'];
                if($title=='face'){
                    $html=View::make("Employment::admin.printreports.Face",['config'=>$config,'value'=>$user])->render();
                    $pdf->writeHTML($css.$html, true, false, false, false, 'right');
                }elseif(in_array($title,['CheckApplyData','LastEntry',''])){
                    $config['page']=$title;
                    $html=View::make("Employment::admin.printreports.LastEntry",['config'=>$config,'value'=>$user])->render();
                    $pdf->writeHTML($css.$html, true, false, false, false, 'right');
                }elseif($title == 'collect'){
                    $config['page']=$title;
                    $html=View::make("Employment::admin.printreports.collect",['config'=>$config,'value'=>$user])->render();
                    $pdf->writeHTML($css.$html, true, false, false, false, 'right');
                }
                $tagvs = [
                    'div' => [
                        ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
                    ]
                ];
                $pdf->setHtmlVSpace($tagvs);
            }

        $filename="A.pdf";
        return $pdf->Output($filename, 'E');
    }
    public static function WantedpagesFN(){
        $pages=[];
        $insideFace=[];
        if(in_array('Full',self::$request->input('actions'))){
            $old=self::$request->all();
            $old['actions']=[ "LastEntry", "Downloads", "CheckApplyData", "GrievanceApply", "GrievanceEditorial", "GrievancePractical", "Degrees" ];
            self::$request->replace($old);
        }
        if(self::$request->input('type') == 'faceWfile'){
            $pages[]='face';
        }
        foreach (self::$request->input('actions') as $key => $value) {
            $pages[]=$value;
        }
        $insideFaceArr=['Degrees','GrievancePractical','GrievanceEditorial','GrievanceApply','Downloads'];
        if(self::$request->input('type') == 'faceWfile'){
            foreach($pages as $k=>$v){
                if(in_array($v,$insideFaceArr)){
                    \Arr::forget($pages, $k);
                }
            }
        }else{
            foreach($pages as $k=>$v){
                if(in_array($v,$insideFaceArr)){
                    \Arr::forget($pages, $k);
                }
            }
            $pages[]='collect';
        }
        $pages=array_unique($pages);
        foreach (self::$prePeople as $key => $value) {
            foreach ($pages as $page) {
                self::$pages[]=$page."_".$value->Face->id;
            }
        }
    }
    public static function createTicket(){
        $config=config('Amer.TCPDF');
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
        $pdf->setViewerPreferences($config['ViewerPreferences']);
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $html='';
        $html="";
        $html.=View::make("Employment::admin.seatings.Cssticket")->render();
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
        $config=config('Amer.TCPDF');
        $pdf = new \Amerhendy\Pdf\Helpers\AmerPdf($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->setViewerPreferences($config['ViewerPreferences']);
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
        $pageheader=View::make("Employment::admin.pdfheader",['config'=>$config])->render();
        $pageFooter=View::make("Employment::admin.pdfFooter",['config'=>$config])->render();
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=$pageheader, $tc=array(0,0,0), $lc=array(0,0,0));
        $pdf->setFooterHtml($font=['aealarabiya', 'B', 10],$hs=$pageFooter, $tc=array(0,0,0), $lc=array(0,0,0),$line=true);
        $pdf->setFooterFont(Array($config['Font']['Date']['name'], '', $config['Font']['Date']['Size']));
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,20,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
        $pdf->SetAutoPageBreak(TRUE, $config['PdfMargin']['MarginBottom']);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('aealarabiya', '', 11, '', true);
        $pdf->setRTL(true);
        $html='';
        $html="";
        if (self::$request->input('table') == 'tableForCollection') {
            $pdf->setPageOrientation('L');
            $tablewidth=255;
        }else{
            $tablewidth=190;
        }
        $html.=View::make("Employment::admin.seatings.CssTable",['data'=>['tablewidth'=>$tablewidth]])->render();
        ///Committee_Number,Committee_Name
            $arr=self::$prePeople;
            if(self::$request->input('table') == 'tableForSign'){
                $pdf->AddPage();
                    $tagvs = [
                        'div' => [
                            ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
                        ]
                    ];
                    $pdf->setHtmlVSpace($tagvs);
                $html.= View::make("Employment::admin.seatings.TableForSign",['data'=>$arr,'pdf'=>$pdf])->render();
                $pdf->writeHTML($html, true, false, false, false, 'right');
            }else{


                $pdf->startPageGroup();
                $annonceInfo=$arr[0]->Face->annonce_id;
                $Split=self::splitbycommitte($arr);
                $Split=$Split->toArray();
                    $pdf->AddPage();
                    $tagvs = [
                        'div' => [
                            ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
                        ]
                    ];
                    $pdf->setHtmlVSpace($tagvs);
                if(self::$request->input('table') == 'tableForMembers'){
                    $html.= View::make("Employment::admin.seatings.TableFormembers",['data'=>$Split,'pdf'=>$pdf])->render();
                }elseif (self::$request->input('table') == 'tableForCollection') {
                    $html.= View::make("Employment::admin.seatings.TableForCollection",['data'=>$Split,'tablewidth'=>$tablewidth,'pdf'=>$pdf])->render();
                }
                $pdf->writeHTML($html, true, false, false, false, 'right');
            }
        $filename="A.pdf";

        return $pdf->Output($filename, 'E');
    }
    static function sortForTableMembers($data) {
        //$data=json_encode($data);
        //$data=json_decode($data,true);
        $groupByCommitteeName=collect($data)->groupBy(function($item,$key){
            return $item->printSeatings->Committee_Name;
        });
        $lazyCollection =collect($data)->sortBy(
            ['printSeatings.Committee_Date','asc'],
            ['printSeatings.Committee_Number','asc'],
            ['printSeatings.Number','asc']
        );
            return $lazyCollection->all();
    }
    static function splitbycommitte($data){
        $annonceInfo=$data[0]->Face->annonce_id;
        ///groupbyjob
        $data=collect($data);
        //remove annonceinfo
        foreach($data as $k=>$v){
            unset($data[$k]->Face->annonce_id);
            unset($data[$k]->Face->lastStage);
            unset($data[$k]->Face->ConnectLandline);unset($data[$k]->Face->ConnectMobile);unset($data[$k]->Face->ConnectEmail);
            unset($data[$k]->Grievance);unset($data[$k]->Seatings);unset($data[$k]->StageList);
        }
        //if stage !== tahriry groupbyjob
        /**
         * if(self::$request->input('stage') == 7){
                    $Seatings=$stageList::get('HtmlSeatingsPractical',null,'array');
                }elseif(self::$request->input('stage') == 14){
                    $Seatings=$stageList::get('HtmlSeatingsEditorial',null,'array');
                }elseif(self::$request->input('stage') == 13){
                    $Seatings=$stageList::get('HtmlSeatingsapply',null,'array');
                }else{
                    $Seatings=null;
                }
         */
        if(self::$request->input('stage') == 14){
            return self::GroupByJob($data);
        }
        return self::GroupByJob($data);
    }
    static function GroupByJob($data) {
        $collection=$data->groupBy(function($item,$key){
            return $item->Face->job_id->Code;
        });

        $collection=$collection->map(function ($v,$k){
            return $v->groupBy(function($item,$key){
                return $item->printSeatings->Committee_Date;
            });
        });
        $collection=$collection->map(function ($v,$k){
            return $v->sortBy(
                ['printSeatings.Committee_Date','asc'],
                ['printSeatings.Committee_Number','asc'],
                ['printSeatings.Number','asc']
            );
        });
        return $collection;
    }
    public static function printSearchResult(){
        $config=config('Amer.TCPDF');
        $pdf = new \Amerhendy\Pdf\Helpers\AmerPdf($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->SetCreator($config['PDFCreator']);
        $pdf->SetAuthor(config('Amer.amer.co_name'));
        $pdf->SetTitle(trans('JOBLANG::Employment_seatings.Employment_seatings'));
        $pdf->SetSubject(trans('JOBLANG::Employment_seatings.Employment_seatings')) ;
        $pdf->SetKeywords(implode(',',explode(' ',trans('JOBLANG::Employment_seatings.Employment_seatings')." ".config('Amer.amer.co_name'))));
        $pageheader=View::make("Employment::admin.pdfheader",['config'=>$config])->render();
        $pageFooter=View::make("Employment::admin.pdfFooter",['config'=>$config])->render();
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=$pageheader, $tc=array(0,0,0), $lc=array(0,0,0));
        $pdf->setFooterHtml($font=['aealarabiya', 'B', 10],$hs=$pageFooter, $tc=array(0,0,0), $lc=array(0,0,0),$line=true);
        $pdf->setFooterFont(Array($config['Font']['Date']['name'], '', $config['Font']['Date']['Size']));
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,20,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
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
        $pdf->setViewerPreferences($config['ViewerPreferences']);
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
        $html='';
        $html="";
        if (self::$request->input('table') == 'tableForCollection') {
            $pdf->setPageOrientation('L');
            $tablewidth=255;
        }else{
            $tablewidth=190;
        }
        $html.=View::make("Employment::admin.seatings.CssTable",['data'=>['tablewidth'=>$tablewidth]])->render();
            $arr=self::$prePeople[0];
            $lastSatge=$arr->stageList->Last;
                $html.= View::make("Employment::searchPDF",['data'=>$arr,'pdf'=>$pdf])->render();
                if(property_exists($lastSatge,'Statics')){
                    return [$html];
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
    public static function showJobPrint($job){
        $job->headerTitle=trans('JOBLANG::Employment_Jobs.Employment_Jobs');
        $pdf=self::pdfdod($job);
        $html="";
        $tablewidth=190;
        $html.=View::make("Employment::admin.seatings.CssTable",['data'=>['tablewidth'=>$tablewidth]])->render();
        $html.=View::make("Employment::PDFshowjobs",['data'=>['tablewidth'=>$tablewidth,'job'=>$job]])->render();
        $tagvs = [
            'div' => [
                ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
            ]
        ];
        $pdf->setHtmlVSpace($tagvs);
        $pdf->writeHTML($html, true, false, false, false, 'right');
        $filename=$job->headerTitle.".pdf";
        $ret=[];
        $ret['pdf']=$pdf->Output($filename, 'E');
        $ret['Stage']=$job->Employment_StartAnnonces->Employment_Stages;
        //dd($job->Employment_StartAnnonces->Employment_Stages,$job);
        return $ret;
    }
    public static function applyReviewPrint($data){
        $pdf=self::pdfdod($data);
        $html='';
        $html="";
        $tablewidth=190;
        $html.=View::make("Employment::admin.seatings.CssTable",['data'=>['tablewidth'=>$tablewidth]])->render();
        if(!property_exists($data,"OldData")){
            $html.=View::make("Employment::PDFapplyReviewJob",['data'=>['tablewidth'=>$tablewidth,'job'=>$data]])->render();
        }else{
            $qr = QrCode::format('png')->size(200)
            ->generate($data->QR);
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qr);
            $data->QR=$qrBase64;
            $html.=View::make("Employment::PDFCompleteReview",['data'=>['tablewidth'=>$tablewidth,'job'=>$data]])->render();
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
    public static function AdminUpToDateResult($result){
        $config=config('Amer.TCPDF');
        $config['PageOrientation']='L';
        $pdf = new \Amerhendy\Pdf\Helpers\AmerPdf($config['PageOrientation'],$config['PDFUnit'],$config['PageSize'], true, 'UTF-8', false);
        $pdf->SetCreator($config['PDFCreator']);
        $pdf->SetAuthor(config('Amer.amer.co_name'));
        $pdf->SetTitle(trans('JOBLANG::Employment_seatings.Employment_seatings'));
        $pdf->SetSubject(trans('JOBLANG::Employment_seatings.Employment_seatings')) ;
        $pdf->SetKeywords(implode(',',explode(' ',trans('JOBLANG::Employment_seatings.Employment_seatings')." ".config('Amer.amer.co_name'))));
        $pageheader=View::make("Employment::admin.pdfheader",['config'=>$config,'pdf'])->render();
        $pageFooter=View::make("Employment::admin.pdfFooter",['config'=>$config])->render();
        $pdf->setHeaderData($ln='', $lw=0, $ht='', $hs=$pageheader, $tc=array(0,0,0), $lc=array(0,0,0));
        $pdf->setFooterHtml($font=['aealarabiya', 'B', 10],$hs=$pageFooter, $tc=array(0,0,0), $lc=array(0,0,0),$line=true);
        $pdf->setFooterFont(Array($config['Font']['Date']['name'], '', $config['Font']['Date']['Size']));
        $pdf->SetDefaultMonospacedFont($config['Font']['MONOSPACED']);
        $pdf->SetMargins(10,20,10);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(20);
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
        $pdf->setViewerPreferences($config['ViewerPreferences']);
        $html='';
        $html="";

        $tablewidth=255;
        $html.=View::make("Employment::admin.seatings.CssTable",['data'=>['tablewidth'=>$tablewidth]])->render();
        $html.=View::make("Employment::admin.printreports.adminUpToDate",['data'=>['tablewidth'=>$tablewidth,'data'=>$result]])->render();
        $tagvs = [
            'div' => [
                ['h' => 0.5, 'n' => 0.01],['h' => 0.5, 'n' => 0.01]
            ]
        ];
        $pdf->setHtmlVSpace($tagvs);
        $pdf->writeHTML($html, true, false, false, false, 'right');
        $filename="A.pdf";
        //dd($job->Employment_StartAnnonces->Employment_Stages,$job);
        return $pdf->Output($filename, 'E');
    }
}
