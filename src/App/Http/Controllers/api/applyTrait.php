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
    public static $reqmerge=[];
    public static function store()
    {
        $validator=Validator::make(self::$request->all(),[]);
        $store_file=self::store_file();
        if($store_file === false){
            $validator->errors()->add('uploades',trans('JOBLANG::Employment_People.Uploaded_files'));
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        /*setMessage and result*/
        self::check_job_employer_requirements();
        self::setBasicJobInfo();
        $data=self::sort_request_to_review();
        if($data === 0){
            return '';
        }
        $data->job=self::$job;
        if(self::$request->has('id') && self::$request->has('test')){
            $data->headerTitle=trans('JOBLANG::apply.pageHeaderTitle.successApply');
        }else{
            $data->headerTitle=trans('JOBLANG::apply.pageHeaderTitle.preview_data');
            $data->headerTitleNote=trans('JOBLANG::apply.pageHeaderTitle.preview_dataNote');
        }
        if(self::$request->actiontype == 'create' || self::$request->actiontype == 'complete')
        {
            self::setRequestData();
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
        return false;
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
        if((int) $job->driver == 1){
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
        self::$reqmerge['message']=json_encode($errors);
        if(count($errors)){self::$reqmerge['result']='60821f0e-bff3-4b05-81b7-d7401c97808e';}else{self::$reqmerge['result']='c3fc3ced-2bf3-41ee-a411-9bfb4b004a62';}
    }
    public static function check_age()
    {
        if((int)self::$request->ageyears == (int)self::$job->age){
            if(((int)self::$request->agemonths> 0) || ((int)self::$request->agedays> 0)){
                return false;
            }
            return true;
        }
        if((int)self::$request->ageyears > (int)self::$job->age){
            return false;
        }
        return true;
    }
    public static function check_khebra()
    {
        $target=self::$job;$table="Mosama_Experiences";$time=self::$request['Khebra'];$type=self::$request['Khebra_type'];
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
        $target=self::$job->Employment_Drivers;$degree=self::$request['driverdegree'];$start=self::$request['driverstart'];$end=self::$request['driverend'];
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
        $arrRequest=self::$request->toArray();
        $arrRequest['id']=(string) \str::uuid();
        $unset=['_token','_method','apply_date','Khebra_type','acceptall','accept_driver','actiontype','annonce','job','uploades'];
        if(self::$request->input('actiontype') == 'create'){
            $unset[]='annonceid';
            $unset[]='jobid';
            $unset[]='select_job';
        }
        if(self::$request->actiontype == 'complete'){
            $unset=array_merge($unset,['uid','NID','BirthDate','Sex','AgeYears','AgeMonths','AgeDays','uinid','new_file_name','select_job']);
        }
        foreach ($unset as $key => $value) {
            unset($arrRequest[$value]);
        }
        self::$request->merge(['uploades'=>self::$request['uploades']->getClientOriginalName()]);
        if(self::$request->actiontype == 'create'){
            $insert=new Employment_people;
            $columns = \Schema::getColumnListing($insert->getTable());
            $nwe=[];
            foreach ($columns as $key => $value) {
                $vl=$arrRequest[$value];
                if(\Str::isUuid($vl)){
                    //$vl="'".$vl."'";
                }
                $insert->{$value}=$vl;
            }
            $insert->saveOrFail();
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
        $testid=self::insertLogDB(self::$request);
        self::$request->merge(['test'=>$testid]);
        return [self::$request->input('test'),self::$request->input('id')];
    }
    public static function store_file(){
        if(self::$request->actiontype == 'create'){
            $newfile=self::$request['nid'].'-'.md5(time() . rand() . \Str::random(40)). '.pdf';
        }else{
            $newfile=self::$request->input('new_file_name');
            $newfile=self::$request['nid'].'-'.md5(time() . rand() . \Str::random(40)). '.pdf';
        }
        $path = self::$request->file('uploades')->storeAs(config('Amer.Employment.root_disk_name'), $newfile);
        if($path){
            self::$request['filename']=$path;
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
        if($di[0]['text'] !== 'complete'){return ['result'=>false];}
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
            $request=self::$request;$annonce=self::$annonce;$job=self::$job;
            $data=new \stdClass;
            if($request->has('id')){
                self::$request->merge(['uid',$request->input('id')]);
                $request=self::$request;
                $data->uid=$request->input('uid');
            }elseif($request->has('uid')){

                $data->uid=$request->input('uid');
            }
            $data->apply_date=$request->input('apply_date');
            if($request->input('actiontype') === 'create'){
                $data->actiontype=__('JOBLANG::apply.apply_buttom_apply');
            }else{
                $data->actiontype=__('JOBLANG::apply.Complete_buttom_apply');
            }
            $data->fullname=implode(' ',[$request->input('fname'),$request->input('sname'),$request->input('tname'),$request->input('lname')]);
            if($request->has('uid')){
                $peo=Employment_People::where('id',$request->input('uid'))->first();
                if(!$peo){return 0;}
                $data->NID=$peo->nid;
                $data->BirthDate=$peo->birthdate;
                if($peo->sex == '1'){$peo->sex=trans('JOBLANG::Employment_People.Sex.Male');}else{$peo->sex=trans('JOBLANG::Employment_People.Sex.Female');}
                $data->Sex=$peo->sex;
                $data->age=$peo->Age;
            }else{
                $data->NID=$request->nid;
                $data->BirthDate=$request->birthdate;
                if($request->sex == '1'){$sex=trans('JOBLANG::Employment_People.Sex.Male');}else{$sex=trans('JOBLANG::Employment_People.Sex.Female');}
                $data->Sex=$sex;
                $Age=new \stdClass();
                $Age->ageyears=$request->ageyears;
                $Age->agemonths=$request->agemonths;
                $Age->agedays=$request->agedays;
                $data->age=$Age;
            }

            $BornGov=Governorates::where('id',self::$request->input('borngov'))->first();
            $BornCity=Cities::where('id',self::$request->input('borncity'))->first();
            $birth_place=new \stdClass();
            $birth_place->BornGov=$BornGov->name;
            $birth_place->BornCity=$BornCity->name;
            $data->birth_place=$birth_place;
            $LiveGov=Governorates::where('id',self::$request->input('livegov'))->first();
            $LiveCity=Cities::where('id',self::$request->input('livecity'))->first();
            $live_place=new \stdClass();
            $live_place->LiveGov=$LiveGov->name;
            $live_place->LiveCity=$LiveCity->name;
            $live_place->liveaddress=self::$request->input('liveaddress');
            $data->live_place=$live_place;
            $data->ConnectLandline=$request->input('connectlandline');
            $data->ConnectMobile=$request->input('connectmobile');
            $data->ConnectEmail=$request->input('connectemail');
            //
            $Employment_Health=Employment_Health::withTrashed()->where('id',$request->input('health_id'))->first();
            $data->Health_id=$Employment_Health->text ?? null;
            $Employment_MaritalStatus=Employment_MaritalStatus::withTrashed()->where('id',$request->input('maritalstatus_id'))->first();
            $data->MaritalStatus_id=$Employment_MaritalStatus->text ?? null;
            $Employment_Army=Employment_Army::withTrashed()->where('id',$request->input('arm_id'))->first();
            $data->Employment_Army=$Employment_Army->text ?? null;
            $Employment_Ama=Employment_Ama::withTrashed()->where('id',$request->input('ama_id'))->first();
            $data->Employment_Ama=$Employment_Ama->text ?? null;
            $Mosama_Educations=Mosama_Educations::withTrashed()->where('id',$request->input('education_id'))->first();
            $data->Education_id=$Mosama_Educations->text ?? null;
            $data->EducationYear=$request->input('educationyear');
            if($request->has('accept_driver')){
                $data->accept_driver=(int) $request->input('accept_driver');
                if((int) $request->input('accept_driver') !== 1){
                    $Employment_Drivers=Employment_Drivers::withTrashed()->where('id',self::$request['driverdegree'])->first();
                    if($Employment_Drivers){
                        $data->DriverDegree=$Employment_Drivers->text;$data->DriverStart=$request->input('driverstart');$data->DriverEnd=$request->input('DriverEnd');
                    }else{
                        $data->DriverDegree=$data->DriverStart=$data->DriverEnd=null;
                    }
                }else{
                    $data->DriverDegree=$data->DriverStart=$data->DriverEnd=null;
                }
            }
            if($request->has('Khebra_type')){
                $khebs=[2=>trans("EMPLANG::Mosama_Experiences.enum_2"),0=>trans("EMPLANG::Mosama_Experiences.enum_0"),1=>trans("EMPLANG::Mosama_Experiences.enum_1")];
                $data->Khebra_type=$khebs[$request->input('Khebra_type')];
            }
            if(\Str::isJson($request->input('Khebra'))){
                $data->Khebra=json_decode($request->input('Khebra'),true);
                if(is_array($data->Khebra)){
                    $data->Khebra=$data->Khebra[1];
                }else{
                    $data->Khebra=$data->Khebra;
                }
            }else{
                $data->Khebra=$request->input('Khebra');
            }
            $data->Tamin=$request->input('tamin');
            if(!empty($request->file('uploades'))){
                if(gettype($request->file('uploades')) == 'string'){
                    $data->uploades=$request->file('uploades');
                }else{
                    $data->uploades=$request->file('uploades')->getClientOriginalName();
                }
            }else{
                $data->uploades=null;
            }
            return $data;
        }
        public static function getjobInfo($Object=null,$show='one'){
            $newresult=[];
            $request=self::$request;
            if(is_null($Object)){
                if($request->has('jobid')){
                    if(!is_array($request->input('jobid'))){
                        $jobid=[$request->input('jobid')];
                    }else{
                        $jobid=$request->input('jobid');
                    }
                    $Object=jobs::where('id',$jobid);
                }
                $Object=$Object->get();
            }
            foreach ($Object as $key => $data) {
                $result=new \stdClass;
                $result->id=$data->id;
                $result->code=$data->code;
                if($data->description == 'null' || $data->description == null || $data->description == ''){
                    $data->description=null;
                }
                $result->Description=$data->description;
                $result->Count=$data->count;
                $result->AgeIn=$data->ageformat;
                $result->Driver=$data->driver;
                $result->Mosama_Groups=$data->Mosama_JobNames->Mosama_Groups->text;
                $result->Mosama_JobTitles=$data->Mosama_JobNames->Mosama_JobTitles->text;
                $result->Mosama_JobNames=new \stdClass;
                $result->Mosama_JobNames->Text=$data->Mosama_JobNames->text;
                $result->Mosama_JobNames->Mosama_Degrees=$data->Mosama_JobNames->Mosama_Degrees->text;
                $result->Mosama_JobNames->Mosama_Tasks=\Arr::map($data->Mosama_JobNames->Mosama_Tasks->toArray(),function($v,$k){return $v['text'];});
                $result->Mosama_JobNames->Mosama_Skills=\Arr::map($data->Mosama_JobNames->Mosama_Skills->toArray(),function($v,$k){return $v['text'];});
                $result->Mosama_JobNames->Mosama_Goals=\Arr::map($data->Mosama_JobNames->Mosama_Goals->toArray(),function($v,$k){return $v['text'];});
                $result->Mosama_JobNames->Mosama_Experiences=\Arr::map($data->Mosama_JobNames->Mosama_Experiences->toArray(),function($v,$k){return [$v['type'],$v['time']];});
                $result->Mosama_JobNames->Mosama_Competencies=\Arr::map($data->Mosama_JobNames->Mosama_Competencies->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_StartAnnonces=new \stdClass;
                $result->Employment_StartAnnonces->Number=$data->Employment_StartAnnonces->number;
                $result->Employment_StartAnnonces->Year=$data->Employment_StartAnnonces->year;
                $result->Employment_StartAnnonces->Description=$data->Employment_StartAnnonces->description;
                $result->Employment_StartAnnonces->Employment_Stages=[$data->Employment_StartAnnonces->Employment_Stages->text,(int)$data->Employment_StartAnnonces->Employment_Stages->front,$data->Employment_StartAnnonces->Employment_Stages->page,$data->Employment_StartAnnonces->Employment_Stages->functionName];
                $result->Employment_StartAnnonces->Governorates=\Arr::map($data->Employment_StartAnnonces->Governorates->toArray(),function($v,$k){return $v['name'];});
                $result->Employment_StartAnnonces->Employment_Qualifications=\Arr::map($data->Employment_StartAnnonces->Employment_Qualifications->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_Ama=\Arr::map($data->Employment_Ama->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_Army=\Arr::map($data->Employment_Army->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_Health=\Arr::map($data->Employment_Health->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_Instructions=\Arr::map($data->Employment_Instructions->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_MaritalStatus=\Arr::map($data->Employment_MaritalStatus->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_Qualifications=\Arr::map($data->Employment_Qualifications->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_Drivers=\Arr::map($data->Employment_Drivers->toArray(),function($v,$k){return $v['text'];});
                $result->Employment_IncludedFiles=\Arr::map($data->Employment_IncludedFiles->toArray(),function($v,$k){return $v['filename'];});
                $result->Mosama_Educations=\Arr::map($data->Mosama_Educations->toArray(),function($v,$k){return $v['text'];});
                $result->Cities=\Arr::map($data->Cities->toArray(),function($v,$k){return $v["name"];});
                $newresult[]=$result;
            }

            if($show == 'one'){
                $result=$newresult[0];
                if($request->view == 'pdf'){
                    return \AmerHelper::responsedata(self::showJobPrint($result),1,1,'');
                }elseif($request->view == 'json'){
                    return \AmerHelper::responsedata($result,1,1,'');
                }
                return $result;
            }else{
                $result=$newresult;
                return \AmerHelper::responsedata($result,1,1,'');
            }
            dd($result);
            if($request->view == 'pdf'){
                return \AmerHelper::responsedata(self::showJobPrint($result),1,1,'');
            }elseif($request->view == 'json'){
                return \AmerHelper::responsedata($result,1,1,'');
            }
            return \AmerHelper::responsedata(self::showJobPrint($result),1,1,'');
            dd($request->view);
        }

    public static function create_review_qrcode($data,$req){
        $qr=new \stdClass();
        if(self::$request->input('actiontype') == 'create'){
            if(self::$request->has(['id','test'])){
                $qr->requestType='apply-review';
                $qr->id=$req['id'];
                $qr->test=$req['test'];
            }else{
                $testid=self::insertLogDB();
                $qr->test=$testid;
                $qr->requestType='review';
            }
        }
        /*$qr->annonce=self::$annonce->id;
        $qr->job=self::$job->id;
        $qr->date=$data->apply_date;
        $qr->NID=$data->NID;
        $qr->actiontype=$req['actiontype'];*/

        $qr=route('qrcode',['element'=>'pck?type=employment&action='.$qr->requestType.'&testid='.$qr->test]);
        return $qr;
        $amerhelper=new \AmerHelper();
        dd($amerhelper::tokenencrypt($qr));
        return $amerhelper::tokenencrypt($qr);
    }

    public static function insertLogDB(){
        $request=self::$request;
        $testid=\DB::table('employment_applylogs')->insertGetId(['id'=>\Str::uuid(),'userdata' => json_encode($request)]);
        return $testid;
    }
    public static function setBasicJobInfo(){
        self::$job=self::getjobInfo();
        foreach(self::$job->Mosama_JobNames->Mosama_Experiences as $a=>$b){
            if($b[1] == 0){
                self::$job->Mosama_JobNames->Mosama_Experiences[$a]=trans("EMPLANG::Mosama_Experiences.year0");
            }else{
                if($b[0] == '1'){
                    self::$job->Mosama_JobNames->Mosama_Experiences[$a][0]=trans("EMPLANG::Mosama_Experiences.enum_1");
                }elseif($b[0] == '0'){
                    self::$job->Mosama_JobNames->Mosama_Experiences[$a][0]=trans("EMPLANG::Mosama_Experiences.enum_0");
                }
                $translate=trans("EMPLANG::Mosama_Experiences.translate");
                self::$job->Mosama_JobNames->Mosama_Experiences[$a] =(string) \Str::of($translate)->replaceArray('?', self::$job->Mosama_JobNames->Mosama_Experiences[$a]);
            }
        }

    }
    public static function setRequestData(){
        $reqmerge=[];

        self::SetReqactiontypeData();
        if((int)self::$job->Driver == 1){
            self::$reqmerge['driverdegree']=null;
            self::$reqmerge['driverstart']=null;
            self::$reqmerge['driverend']=null;
        }
        if(!self::$request->has('Khebra')){self::$reqmerge['Khebra'] = null;}else{
            self::$reqmerge['khebra'] = json_encode([self::$request->input('Khebra_type'),self::$request->input('Khebra')]);
        }
        self::$reqmerge['created_at']=self::$request->input('apply_date');
        self::$reqmerge['updated_at']=null;
        self::$reqmerge['deleted_at']=null;
        self::$request->merge(self::$reqmerge);
    }
    private static function SetReqactiontypeData(){
        switch (self::$request->input('actiontype')) {
            case 'create':
                self::$reqmerge['stage_id']=self::$annonce->stage_id;
                self::$reqmerge['annonce_id']=self::$annonce->id;
                break;

            case 'complete':
                self::$reqmerge['people_id']=self::$request->input('uid');
                self::$reqmerge['stage_id']=self::$request->input('PeopleNewStageId');
                break;

            default:
            dd("SD");
            break;
        }
        self::$reqmerge['job_id']=self::$job->id;
    }
    }
