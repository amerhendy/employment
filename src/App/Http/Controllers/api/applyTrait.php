<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use \Amerhendy\Employment\App\Models\Employment_Jobs as jobs;
use \Amerhendy\Employment\App\Models\Employment_Stages;
use \Amerhendy\Employment\App\Models\Employment_Jobs;
use \Amerhendy\Employment\App\Models\Employment_People;
use \Amerhendy\Employment\App\Models\Employment_PeopleNewStage;
use \Amerhendy\Employment\App\Models\Employment_PeopleNewData;
use \Amerhendy\Employment\App\Models\Employment_DinamicPages;
use \Amerhendy\Employment\App\Models\Employment_Health;
use \Amerhendy\Employment\App\Models\Employment_Ama;
use \Amerhendy\Employment\App\Models\Employment_Army;
use \Amerhendy\Employment\App\Models\Employment_MaritalStatus;
use \Amerhendy\Employment\App\Models\Employment_Drivers;
use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Amer\App\Models\Governorates;
use \Amerhendy\Amer\App\Models\Cities;
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
trait applyTrait
{
    public static function store()
    {
        $validator=Validator::make(self::$request->all(),[]);
        $requirements=self::check_job_employer_requirements();
        self::$request['Message']=$requirements;
        $store_file=self::store_file();
        if($store_file === false){
            $validator->errors()->add('uploades',trans('JOBLANG::Employment_People.Uploaded_files'));
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        $data=[];
        $Employment_Jobs=self::$job;
        $data['user']=self::sort_request_to_review();
        self::$request['view']='json';
        $job_annonce=self::getjobinfo();
        $job_annonce=$job_annonce->getOriginalContent()['data'];
        $data['job']= $job_annonce;
        $data['annonce']=$job_annonce->Employment_StartAnnonces;
        if(self::$request->actiontype == 'create' || self::$request->actiontype == 'complete')
        {
            ///////////////////////////////
            //$success=['success'=>[100,100]];
            //return response()->json($success);
            ///////////////////////////////
            $submit=self::apply_submit();
            if($submit[1] == 'bad'){
                return response()->json(['error'=>$submit[2]]);
            }else{
                $success=['success'=>$submit];
                return response()->json($success);
            }
        }
    }
    public static function check_job_employer_requirements()
    {
        $errors=[];
        $job=self::$job;
        if(!$job){
            return 'error';
        }
        if(self::check_age() == false){
            $errors['Age']='JOBLANG::Employment_People.Age.Age';
        }
        
        if(self::check_condetions('Employment_Ama',self::$request->Ama_id) == false){
            $errors['Ama_id']='JOBLANG::Employment_Ama.singular';
        }
        if(self::check_condetions('Employment_Army',self::$request['Arm_id']) == false){
            $errors['Arm_id']='JOBLANG::Employment_Army.singular';
        }
        if(self::check_condetions('Mosama_Educations',self::$request['Education_id']) == false){
            $errors['Education_id']='EMPLANG::Mosama_Educations.singular';
        }
        if(self::check_condetions('Employment_Health',self::$request['Health_id']) == false){
            $errors['Health_id']='JOBLANG::Employment_Health.singular';
        }
        if(self::check_condetions('Employment_MaritalStatus',self::$request['MaritalStatus_id']) == false){
            $errors['MaritalStatus_id']='JOBLANG::Employment_MaritalStatus.singular';
        }
        if(self::check_khebra() == false){
            $errors['Khebra']='EMPLANG::Mosama_Experiences.singular';
        }
        if((int) $job->Driver == 1){
            $request['DriverDegree']=null;
            $request['DriverStart']=null;
            $request['DriverEnd']=null;
        }else{
            $a=self::check_driver_degree();
            if($a == 'error'){
                return 'error';
            }
            if($a !== 1){
                $errors['Driver']=$a;
            }
        }
        $placescheck=self::check_places();
        if($placescheck !== true){$errors[]=$placescheck;}
        //$errors['Driver']=[['DriverDegree'=>'JOBLANG::Employment_People.Employment_Drivers.DriverDegree'],['DriverEnd'=>'JOBLANG::Employment_People.Employment_Drivers.DriverEnd']];
        $errors=self::flatten_requirements($errors);
        return $errors;
    }
    public static function check_age()
    {
        if(self::$request->AgeYears == self::$job->Age){
            if((self::$request->AgeMonths> 0) || (self::$request->AgeDays> 0)){
                return false;
            }
            return true;
        }
        if(self::$request->AgeYears > self::$job->Age){
            return false;
        }
        return true;
    }
    public static function check_khebra()
    {
        $target=self::$job;$table="Mosama_Experiences";$time=self::$request['Khebra'];$type=self::$request['Khebra_type'];
        //'Mosama_Experiences',self::$request['Khebra'],self::$request['Khebra_type']
        $times=[];
        $exp=$target->Mosama_JobNames->$table;
        $expA=$exp->toArray();
        if(!in_array($type,[0,1])){
            ///check if no experinces
            if(!count(\Arr::where($expA,function($v,$k){return $v['time'] == 0;}))){
                return false;
            }else{
                return true;
            }
        }
        $selected=\Arr::where($expA,function($v,$k)use($type){return $v['type'] == $type;});
        $times=[];
        foreach ($selected as $key => $value) {
            $times[]=(int)$value['time'];
        }
        if(!count($times)){
            return false;
        }
        $max=max($times);
        $time=(int) $time;
        if($time >= $max){
            return true;
        }else{
            return false;
        }
        return false;
    }
    public static function check_condetions($table,$people)
    {
        $con=self::$job->{$table};
        
        $ids=[];
        foreach($con as $a) {
            $ids[]=$a['id'];
        }
        if(!in_array($people,$ids)){
            return false;
        }
        return true;
    }
    public static function check_driver_degree()
    {
        $target=self::$job->Employment_Drivers;$degree=$request['DriverDegree'];$start=$request['DriverStart'];$end=$request['DriverEnd'];
        if(!count($target)){return 'error';}
        $ids=[];
        foreach($target as $a=>$b){
            $ids[]=$b->id;
        }
        if(!in_array($degree,$ids)){return ['DriverDegree'=>'JOBLANG::Employment_People.Employment_Drivers.DriverDegree'];}
        if($end<now()){
            return ['DriverEnd'=>'JOBLANG::Employment_People.Employment_Drivers.DriverEnd'];
        }
        return 1;
    }
    public static function check_places()
    {
        $request=self::$request;$job=self::$job;
        $errors=[];$right=[];
        $Cities=$job->Cities;
        $BornCity=$request->BornCity;
        $LiveCity=$request->LiveCity;
        $Cities_id=[];
        foreach ($Cities as $key => $value) {
            $Cities_id[]=$value->id;
        }
        //JOBLANG::Employment_jobs.CityBornLive
        if(!in_array($BornCity,$Cities_id)){
            $errors[]='JOBLANG::Employment_jobs.CityBornLive';
        }
        if(!in_array($LiveCity,$Cities_id)){
            $errors[]='JOBLANG::Employment_jobs.CityBornLive';
        }
        if(count($errors) !== 2){
            return true;
        }else{
            return ['CityBornLive'=>'JOBLANG::Employment_jobs.CityBornLive'];
            return $errors;
        }
    }
    private static function flatten_requirements($errors)
    {
        $all=[];
        if(!count($errors)){return[];}
        foreach($errors as $a=>$b){
            if(!is_array($b)){
                $all[$a]=$b;
            }else{
                $b=self::flatten_requirements($b);
                foreach($b as $k=>$d){
                    $all[$k]=$d;
                }
            }
        }
        return $all;
    }
    private static function apply_submit()
    {
        $request=self::$request;
        if(self::$request->actiontype == 'create'){
            self::$request['Stage_id']=self::$annonce->Stage_id;
            self::$request['Annonce_id']=self::$annonce->id;
        }
        if(self::$request->actiontype == 'complete'){
            self::$request['People_id']=self::$request->uid;
            self::$request['Stage_id']=self::$request['PeopleNewStageId'];
        }
        self::$request['Job_id']=self::$job->id;
        if(self::$job->Driver == '1'){
            self::$request['DriverDegree']=null;
            self::$request['DriverStart']=null;
            self::$request['DriverEnd']=null;
        }
        if(count($request->Message)){self::$request['Result']=2;}else {self::$request['Result']=1;}

        //if(!$request->has('ConnectLandline')){$request->merge(['ConnectLandline' => null]);}
        if(!self::$request->has('ConnectLandline')){self::$request['ConnectLandline'] = null;}
        if(!self::$request->has('ConnectMobile')){self::$request['ConnectMobile'] = null;}
        if(!self::$request->has('ConnectEmail')){self::$request['ConnectEmail'] = null;}
        if(!self::$request->has('Arm_id')){self::$request['Arm_id'] = null;}
        if(!self::$request->has('Ama_id')){self::$request['Employment_Ama'] = null;}
        if(!self::$request->has('Tamin')){self::$request['Tamin'] = null;}
        if(!self::$request->has('Khebra')){self::$request['Khebra'] = null;}else{
            self::$request['Khebra'] = json_encode([self::$request->input('Khebra_type'),self::$request->input('Khebra')]);
        }
        if(!self::$request->has('Message')){self::$request['Message'] = null;}else{
            if(is_array(self::$request->input('Message'))){
                if(!count(self::$request->input('Message'))){
                    self::$request['Message'] =null;
                }else{
                    $messages=\Arr::map(self::$request->input('Message'),function($v,$k){
                        return trans($v);
                    });
                    self::$request['Message'] = json_encode([$request->input('Message')]);
                }
            }
        }
        if(!self::$request->has('DriverDegree')){self::$request->merge(['DriverDegree' => null]);}
        if(!self::$request->has('DriverStart')){self::$request->merge(['DriverStart' => null]);}
        if(!self::$request->has('DriverEnd')){self::$request->merge(['DriverEnd' => null]);}
        self::$request->merge(['created_at' => self::$request->input('apply_date')]);
        self::$request->merge(['updated_at' => null]);
        self::$request->merge(['deleted_at' => null]);
        self::$request->merge(['deleted_at' => null]);
        $arrRequest=self::$request->toArray();
        $arrRequest['id']=self::newid();
            $unset=['_token','annonce_id','job_id','_method','apply_date','Khebra_type','actiontype','uploades','acceptall'];
        if(self::$request->actiontype == 'complete'){
            $unset=array_merge($unset,['uid','NID','BirthDate','Sex','AgeYears','AgeMonths','AgeDays','uinid','new_file_name','select_job']);
        }
        foreach ($unset as $key => $value) {
            unset($arrRequest[$value]);
        }
        self::$request->merge(['uploades'=>$request['uploades']->getClientOriginalName()]);
        if(self::$request->actiontype == 'create'){
            $insert=Employment_people::create($arrRequest);
        }elseif(self::$request->actiontype == 'complete'){
            $insert=Employment_PeopleNewData::create($arrRequest);
        }
        if(!$insert){
            return ['bad','inserting People Data'];
        }
        self::$request->merge(['id'=>$insert->id]);
        //insert New Stage
        $los=[];
        if(self::$request->actiontype == 'create'){$los['People_id']=self::$request->input('id');}else{$los['People_id']=self::$request->input('People_id');}
        if(!is_null(self::$request->input('Message'))){
            $los['Message']=self::$request->input('Message');
            if(self::$request->actiontype == 'create'){$los['Status_id']=2;$los['Stage_id']=5;}else{$los['Stage_id']=3;$los['Status_id']=3;}
            $los['created_at']=now();
        }else{
            $los['Status_id']=4;
            $los['Message']=null;
            if(self::$request->actiontype == 'create'){$los['Stage_id']=3;}else{$los['Stage_id']=14;}
            $los['Stage_id']=3;
            $los['created_at']=now();
        }
        
        if(self::$request->actiontype == 'create'){
            /*$newstage=Employment_PeopleNewStage::create($los);
            if(!$newstage){
                return ['bad','inserting NewStage'];
            }
            */
        }
        $testid=self::insertLogDB($request);
        self::$request->merge(['test'=>$testid]);
        return [self::$request->input('test'),$request->input('id')];
    }
    public static function insertLogDB(Request $request){
        $id=AmerHelper::LstTableID('Employment_ApplyLog');
        $dbcode=json_encode($request->all());
        $testid=\DB::table('Employment_ApplyLog')->insertGetId(['id'=>$id,'userData' => $dbcode]);
        return $testid;
    }
    public static function store_file(){
        if(self::$request->actiontype == 'create'){
            $newfile=self::$request['NID']. '.pdf';
        }else{
            $newfile=self::$request->input('new_file_name');
            $newfile=self::$request['NID'].'-'.md5(time() . rand() . \Str::random(40)). '.pdf';
        }
        $path = self::$request->file('uploades')->storeAs(config('Amer.employment.root_disk_name'), $newfile);
        if($path){
            self::$request['FileName']=$path;
            return true;
        }else{
            return false;
        }
    }
    public static function getlaststage($id){
        ///////// check if he can complete
        ///////// check if he completed before////////
        $result=[];
        $ls=Employment_PeopleNewStage::with('Employment_Stages')->where('People_id',$id)->get()->toArray();
        if(sizeof($ls) == 0){
        return ['result'=>false];
        }
        $lss=$ls[sizeof($ls)-1];
        $stage_id=$lss['Stage_id'];
        if(
            ($stage_id == '0') || ($stage_id == '0') || ($stage_id=='')
            ){
            return ['result'=>false];
            }
        $stage=Employment_stages::where('id',$stage_id)->get('Page')->toArray();
        if(!sizeof($stage)){
            return ['result'=>false];
        }
        $page_complex=$stage[0]['Page'];
        $pd=explode(':',$page_complex);
        if(sizeof($pd) !== 2){return ['result'=>false];}
        if(($pd[0] !== 'D') AND ($pd[0] !== 'd')){return ['result'=>false];}
        $di=Employment_DinamicPages::where('id',$pd[1])->get()->toArray();
        if(!sizeof($di)){return ['result'=>false];}
        if($di[0]['Text'] !== 'complete'){return ['result'=>false];}
        $result['result']=true;
        $result['stage']=$lss['id'];
        $data=Employment_People::where('id',$id)->get()->toArray();
        if(!sizeof($data)){$result['allstages']=0;}else{$result['allstages']=sizeof($data);}
        return $result;
        ////////////////////////////////////?////////////////////////////////////?////////////////////////////////////?
        if(sizeof($ls) == 0){
            return ['result'=>false];
        }elseif(sizeof($ls) == 1){
        $ls_len=sizeof($ls);
            if(count($ls[0]['employment_stages']) == 0){
                return ['result'=>false];
            }
            $pd=explode(':',$ls[0]['employment_stages']['page']);
            if($pd[0] !== "D"){return ['result'=>false];}
            $di=Employment_pages_dinamic::where('id',$pd[1])->get()->toArray();
            if(!count($di)){return false;}
            if($di[0]['name'] == 'complete'){$result['result']=true;$result['stage']=$ls[$ls_len-1]['id'];}
            $las=[];
            $Work_jobs_people_results=Employment_people::where('id',$id)->get()->toArray();
            $result['last_message']=$Work_jobs_people_results[0]['message'];
            $result['allstages']=$ls_len;
            return $result;
        }else{
            $ls_len=sizeof($ls);
            //checkstage
            $stage=Employment_stages::where('id',$ls[$ls_len-1]['stage'])->get('page')->toArray();
            if(!count($stage)){return ['result'=>false];}
            $pd=explode(':',$stage[0]['page']);
            if($pd[0] == "D"){
                $di=Employment_pages_dinamic::where('id',$pd[1])->get()->toArray();
                if(!count($di)){return false;}
                if($di[0]['name'] == 'complete'){$result['result']=true;$result['stage']=$ls[$ls_len-1]['id'];}
            }else{
                return ['result'=>false];
            }
            //get before last
            $las=[];
            for($i=0;$i <= $ls_len-1; $i++){
                $a=Employment_people_new_data::where('uid',$id)->where('stage',$ls[$i])->get()->toArray();
                if(count($a) !== 0){
                    $las[]=$a[$i];
                    continue;
                }
            }
            if(empty($las)){
                $data=employment_people::where('id',$id)->get()->toArray();
                $result['last_message']=$data[0]['result'];
                $result['allstages']=$ls_len;
            }
            return $result;
        }
        }   
        private static function newid(){
            if(self::$request->actiontype == 'create'){
                return AmerHelper::LstTableID('Employment_People');
            }else{
                return AmerHelper::LstTableID('Employment_PeopleNewData');
            }
        }
        public static function sort_request_to_review(){
            $request=self::$request->all();$annonce=self::$annonce;$job=self::$job;
            $data=[];
            if(isset($request['id'])){
                $data['uid']=$request['id'];
            }elseif(isset($request['uid'])){
                $data['uid']=$request['uid'];
            }
            $data['apply_date']=self::$request['apply_date'];
            if(self::$request['actiontype'] == 'create'){$data['actiontype']=__('JOBLANG::apply.apply_buttom_apply');}else{$data['actiontype']=__('JOBLANG::apply.Complete_buttom_apply');}
            $data['fullname']=implode(" ",[self::$request['Fname'],self::$request['Sname'],self::$request['Tname'],self::$request['Lname']]);
            if(isset($request['uid'])){
                $peo=Employment_People::where('id',$request['uid'])->first();
                if(!$peo){return 0;}
                $data['NID']=$peo->NID;
                $data['BirthDate']=$peo->BirthDate;
                if($peo->Sex == '1'){$peo->Sex=trans('JOBLANG::Employment_People.Sex.Male');}else{$peo->Sex=trans('JOBLANG::Employment_People.Sex.Female');}
                $data['Sex']=$peo->Sex;
                $data['age']=$peo->AgeYears.'/'.$peo->AgeMonths.'/'.$peo->AgeDays;
            }else{
                $data['NID']=$request['NID'];
                $data['BirthDate']=$request['BirthDate'];
                if(self::$request['Sex'] == '1'){$data['Sex']=trans('JOBLANG::Employment_People.Sex.Male');}else{$data['Sex']=trans('JOBLANG::Employment_People.Sex.Female');}
                $data['age']=implode("/",[self::$request['AgeYears'],self::$request['AgeMonths'],self::$request['AgeDays']]);
            }
            $BornGov=Governorates::where('id',self::$request['BornGov'])->first();
            $BornCity=Cities::where('id',self::$request['BornCity'])->first();
            $data['birth_blace']=implode(' - ',[$BornGov->Name,$BornCity->Name]);
            $LiveGov=Governorates::where('id',$request['LiveGov'])->first();
            $LiveCity=Cities::where('id',$request['LiveCity'])->first();
            $data['live_place']=implode(' - ',[$LiveGov->Name,$BornCity->LiveCity,self::$request['LiveAddress']]);
            $data['ConnectLandline']=self::$request['ConnectLandline'];
            $data['ConnectMobile']=self::$request['ConnectMobile'];
            $data['ConnectEmail']=self::$request['ConnectEmail'];
            $Employment_Health=Employment_Health::withTrashed()->where('id',self::$request['Health_id'])->first();
            $data['Health_id']=$Employment_Health->Text;
            $Employment_MaritalStatus=Employment_MaritalStatus::withTrashed()->where('id',self::$request['MaritalStatus_id'])->first();
            $data['MaritalStatus_id']=$Employment_MaritalStatus->Text;
            $Employment_Army=Employment_Army::withTrashed()->where('id',self::$request['Arm_id'])->first();
            $data['Employment_Army']=$Employment_Army->Text;
            $Employment_Ama=Employment_Ama::withTrashed()->where('id',self::$request['Ama_id'])->first();
            $data['Employment_Ama']=$Employment_Ama->Text;
            $Mosama_Educations=Mosama_Educations::withTrashed()->where('id',self::$request['Education_id'])->first();
            $data['Education_id']=$Mosama_Educations->text;
            $data['EducationYear']=$request['EducationYear'];
            if(self::$request->has('accept_driver')){
                $data['accept_driver']=(int) self::$request->input('accept_driver');
                if((int) self::$request->input('accept_driver') !== 1){
                    $Employment_Drivers=Employment_Drivers::withTrashed()->where('id',self::$request['DriverDegree'])->first();
                    if($Employment_Drivers){$data['DriverDegree']=$Employment_Drivers->Text;$data['DriverStart']=self::$request['DriverStart'];$data['DriverEnd']=self::$request['DriverEnd'];}
                }else{
                    $data['DriverDegree']=$data['DriverStart']=$data['DriverEnd']=null;
                }
            }
            
            if(isset(self::$request['Khebra_type'])){
                $khebs=[2=>trans("EMPLANG::Mosama_Experiences.enum_2"),0=>trans("EMPLANG::Mosama_Experiences.enum_0"),1=>trans("EMPLANG::Mosama_Experiences.enum_1")];
                $data['Khebra_type']=$khebs[self::$request['Khebra_type']];
            }
            if(\Str::isJson(self::$request['Khebra'])){
                $data['Khebra']=json_decode(self::$request['Khebra'],true);
                if(is_array($data['Khebra'])){
                    $data['Khebra']=$data['Khebra'][1];
                }else{
                    $data['Khebra']=$data['Khebra'];
                }
                
            }else{
                $data['Khebra']=self::$request['Khebra'];
            }
            $data['Tamin']=self::$request['Tamin'];
            if(!empty(self::$request['uploades'])){
                if(gettype(self::$request['uploades']) == 'string'){
                    $data['uploades']=self::$request['uploades'];
                }else{
                    $data['uploades']=self::$request['uploades']->getClientOriginalName();
                }
            }else{
                $data['uploades']=null;
            }
            //$reqtype
            return $data;
        }
        public static function getjobInfo(){
            $request=self::$request;
            //check request
            //jobSlug,annonceSlug,page
            if(!isset(self::$job) || self::$job == null){
                $check=self::ShowJobReq();
                if(!$check){
                    return $check;
                }
                $annonceSlug=self::$annonce->Slug ?? self::$request->input('annonceSlug');
                $data=jobs::with('Employment_StartAnnonces')
                    ->with('Employment_StartAnnonces.Employment_Stages')
                    ->with('Employment_StartAnnonces.Governorates')
                    ->with('Employment_StartAnnonces.Employment_Qualifications')
                    ->with('Mosama_JobTitles')
                    ->with(['Mosama_JobNames','Mosama_JobNames.Mosama_Competencies','Mosama_JobNames.Mosama_Experiences','Mosama_JobNames.Mosama_Goals','Mosama_JobNames.Mosama_Skills','Mosama_JobNames.Mosama_Tasks','Mosama_JobNames.Mosama_Degrees'])
                    ->with('Mosama_Groups')
                    ->with('Employment_Ama')
                    ->with('Employment_Army')
                    ->with('Cities')
                    ->with('Mosama_Educations')
                    ->with('Employment_Health')
                    ->with('Employment_IncludedFiles')
                    ->with('Employment_Instructions')
                    ->with('Employment_MaritalStatus')
                    ->with('Employment_Qualifications')
                    ->with('Employment_Drivers')
                    ->where('Slug',$request->input('jobSlug'))
                    ->whereHas('Employment_StartAnnonces',function($query)use($annonceSlug){
                    return $query->where('Employment_StartAnnonces.Slug',$annonceSlug);
                    })
                    ->first();
                    if(!$data){self::$error->message=trans('JOBLANG::Employment_Reports.errors.publicError',['name'=>trans('JOBLANG::Employment_Reports.errors.informations')]);self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);}
            }else{
                $data=self::$job;
            }
            
            $result=new \stdClass;
            $result->code=$data->Code;
            if($data->Description == 'null' || $data->Description == null || $data->Description == ''){
                $data->Description=null;
            }
            $result->Description=$data->Description;
            $result->Slug=$data->Slug;
            $result->Count=$data->Count;
            $AgeIn=new \stdClass;
            $AgeIn->Age=$data->Age;
            $AgeIn->Day=\Carbon\Carbon::parse($data->AgeIn)->format('d');
            $AgeIn->Month=\Carbon\Carbon::parse($data->AgeIn)->format('m');
            $AgeIn->Year=\Carbon\Carbon::parse($data->AgeIn)->format('Y');
            $result->AgeIn=$AgeIn;
            $result->Driver=$data->Driver;
            $result->Mosama_JobNames=new \stdClass;
            $result->Mosama_JobNames->Text=$data->Mosama_JobNames->text;
            $result->Mosama_JobNames->Mosama_Degrees=$data->Mosama_JobNames->Mosama_Degrees->text;
            $result->Mosama_JobNames->Mosama_Tasks=\Arr::map($data->Mosama_JobNames->Mosama_Tasks->toArray(),function($v,$k){return $v['text'];});
            $result->Mosama_JobNames->Mosama_Skills=\Arr::map($data->Mosama_JobNames->Mosama_Skills->toArray(),function($v,$k){return $v['text'];});
            $result->Mosama_JobNames->Mosama_Goals=\Arr::map($data->Mosama_JobNames->Mosama_Goals->toArray(),function($v,$k){return $v['text'];});
            $result->Mosama_JobNames->Mosama_Experiences=\Arr::map($data->Mosama_JobNames->Mosama_Experiences->toArray(),function($v,$k){return [$v['type'],$v['time']];});
            $result->Mosama_JobNames->Mosama_Competencies=\Arr::map($data->Mosama_JobNames->Mosama_Competencies->toArray(),function($v,$k){return $v['text'];});
            
            $result->Mosama_JobTitles=$data->Mosama_JobTitles->text;
            $result->Employment_StartAnnonces=new \stdClass;
            $result->Employment_StartAnnonces->Number=$data->Employment_StartAnnonces->Number;
            $result->Employment_StartAnnonces->Year=$data->Employment_StartAnnonces->Year;
            $result->Employment_StartAnnonces->Description=$data->Employment_StartAnnonces->Description;
            $result->Employment_StartAnnonces->Employment_Stages=[$data->Employment_StartAnnonces->Employment_Stages->Text,(int)$data->Employment_StartAnnonces->Employment_Stages->Front,$data->Employment_StartAnnonces->Employment_Stages->Page];
            $result->Employment_StartAnnonces->Governorates=\Arr::map($data->Employment_StartAnnonces->Governorates->toArray(),function($v,$k){return $v['Name'];});
            $result->Employment_StartAnnonces->Employment_Qualifications=\Arr::map($data->Employment_StartAnnonces->Employment_Qualifications->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_Ama=\Arr::map($data->Employment_Ama->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_Army=\Arr::map($data->Employment_Army->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_Health=\Arr::map($data->Employment_Health->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_Instructions=\Arr::map($data->Employment_Instructions->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_MaritalStatus=\Arr::map($data->Employment_MaritalStatus->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_Qualifications=\Arr::map($data->Employment_Qualifications->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_Drivers=\Arr::map($data->Employment_Drivers->toArray(),function($v,$k){return $v['Text'];});
            $result->Employment_IncludedFiles=\Arr::map($data->Employment_IncludedFiles->toArray(),function($v,$k){return $v['FileName'];});
            $result->Mosama_Educations=\Arr::map($data->Mosama_Educations->toArray(),function($v,$k){return $v['text'];});
            $result->Cities=\Arr::map($data->Cities->toArray(),function($v,$k){return $v['Name'];});
            $result->Mosama_Groups=$data->Mosama_Groups->text;
            if($request->view == 'pdf'){
                return \AmerHelper::responsedata(self::showJobPrint($result),1,1,'');
            }elseif($request->view == 'json'){
                return \AmerHelper::responsedata($result,1,1,'');
            }
            return \AmerHelper::responsedata(self::showJobPrint($result),1,1,'');
            dd($request->view);
            
        }
}