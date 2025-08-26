<?php
namespace Amerhendy\Employment\App\Http\Controllers\api;
use Amerhendy\Amer\App\Helpers\AmerHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \Amerhendy\Employment\App\Http\resources\JobResource;
use \Amerhendy\Employment\App\Models\{Employment_StartAnnonces};
use \Amerhendy\Employment\App\Models\Employment_Jobs as jobs;
use Amerhendy\Employment\App\Models\{
    Employment_Stages,
    Employment_Status,
    Employment_Jobs,
    Employment_People,
    Employment_PeopleNewStage,
    Employment_PeopleNewData,
    Employment_DinamicPages,
    Employment_Health,
    Employment_Ama,
    Employment_Army,
    Employment_MaritalStatus,
    Employment_Drivers,
    employment_apply_logs
};

use \Amerhendy\Employers\App\Models\Mosama_Educations;
use \Amerhendy\Amer\App\Models\{Governorates, Cities, ShortUrls};
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use Illuminate\Support\Facades\DB;
use stdClass;
use Illuminate\Http\UploadedFile;
trait applyTrait
{
    //request
    //toPrint
    //toStore
    public static $reqmerge=[];
    public static $dataToStore,$newStageToStore,$newLogDataToStore;
    public static $action,$json;
    //set data
    //set validator
    //set action
    public static function store()
    {
        self::$json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json(self::$request);
        $validator=Validator::make(self::$request->all(),[]);
        self::$action=self::$request->actiontype;
        //set newData
        self::$dataToStore['id']=(string) \Str::uuid();
        $insert=self::getInserModel();
        self::prepareSotreData();
        self::check_job_employer_requirements();
        $store_file=self::store_file();
        if($store_file === false){
            $validator->errors()->add('uploades',trans('JOBLANG::Employment_People.Uploaded_files'));
            self::$error->message=$validator->errors();self::$error->line=__LINE__;return \AmerHelper::responseError(self::$error,self::$error->number);
        }
        self::insertNewStage();
        self::apply_submit();
    }
    public static function getInserModel(){
        switch (self::$action) {
            case 'create':
                //set cols
                $insert=new Employment_people;
                break;
            case 'complete':
                $insert=new Employment_PeopleNewData;
                break;
            
            default:
                dd("ds");
                break;
        }
        $columns = \Schema::getColumnListing($insert->getTable());
        foreach ($columns as $key => $value) {
            self::$dataToStore[$value]=null;
        }
        self::$dataToStore['id']=\Str::uuid()->toString();
        //$dataToStore,$newStageToStore,$newLogDataToStore
        $newStageModel=new Employment_PeopleNewStage;
        $columns = \Schema::getColumnListing($newStageModel->getTable());
        foreach ($columns as $key => $value) {
            self::$newStageToStore[$value]=null;
        }
        self::$newStageToStore['id']=\Str::uuid()->toString();
        //employment_apply_logs
        $employmentapplylogs=new employment_apply_logs;
        $columns = \Schema::getColumnListing($employmentapplylogs->getTable());
        foreach ($columns as $key => $value) {
            self::$newLogDataToStore[$value]=null;
        }
        self::$newLogDataToStore['id']=\Str::uuid()->toString();
        return $insert;
    }
    public static function check_job_employer_requirements()
    {
        $job=self::$job;
        if(!$job){
            return 'error';
        }
        if(self::check_age() == false){
            self::$dataToStore['message']['Age']='JOBLANG::Employment_People.Age.Age';
        }
        if(self::check_condetions('Employment_Ama',self::$request->input('ama_id')) == false){
            self::$dataToStore['message']['ama_id']='JOBLANG::Employment_Ama.singular';
        }
        if(self::check_condetions('Employment_Army',self::$request->input('arm_id')) == false){
            self::$dataToStore['message']['arm_id']='JOBLANG::Employment_Army.singular';
        }
        if(self::check_condetions('Mosama_Educations',self::$request->input('education_id')) == false){
            self::$dataToStore['message']['education_id']='EMPLANG::Mosama_Educations.singular';
        }
        if(self::check_condetions('Employment_Health',self::$request->input('health_id')) == false){
            self::$dataToStore['message']['health_id']='JOBLANG::Employment_Health.singular';
        }
        if(self::check_condetions('Employment_MaritalStatus',self::$request->input('marital_status_id')) == false){
            self::$dataToStore['message']['marital_status_id']='JOBLANG::Employment_MaritalStatus.singular';
        }
        if(self::check_khebra() == false){
            self::$dataToStore['message']['khebra']='EMPLANG::Mosama_Experiences.singular';
        }
        if($job->driver == false){
            $request['DriverDegree']=null;
            $request['DriverStart']=null;
            $request['DriverEnd']=null;
        }else{
            $a=self::check_driver_degree();
            if($a == 'error'){
                return 'error';
            }
            if($a !== 1){
                self::$dataToStore['message']['Driver']=$a;
            }
        }
        $placescheck=self::check_places();
        if($placescheck !== true){self::$dataToStore['message'][]=$placescheck;}
        //$errors['Driver']=[['DriverDegree'=>'JOBLANG::Employment_People.Employment_Drivers.DriverDegree'],['DriverEnd'=>'JOBLANG::Employment_People.Employment_Drivers.DriverEnd']];
        /*
        */
        self::$dataToStore['message']=self::flatten_requirements(self::$dataToStore['message']);
        if(is_array(self::$dataToStore['message'])){
            self::$newStageToStore['message']=self::$dataToStore['message']=json_encode(self::$dataToStore['message']);
        }
    }
    public static function insertNewStage(){
    
        $insert=[];
        $message=self::$dataToStore['message'];
        $isMessageNull = ($message === null);
        $statusCode = null;
        $stageCode = null;
        switch (self::$action) {
            case 'complete':
                if ($isMessageNull) {
                    $statusCode = 'review';      // تحت المراجعة
                    $stageCode = 'searching';         // ينتقل للمرحلة التالية
                } else {
                    $statusCode = 'refused';     // مرفوض
                    $stageCode = 'searching';         // يتوقف
                }
                break;
            case 'create':
                if ($isMessageNull) {
                    $statusCode = 'review';      // تحت المراجعة
                    $stageCode = 'searching';         // ينتقل للمرحلة التالية
                } else {
                    $statusCode = 'complete';    // مكتمل
                    $stageCode = 'complete';     // حالة الاكتمال
                }
                break;
        }
        $status = Employment_Status::where('code', $statusCode)->first();
        if ($status) {
            self::$dataToStore['result_id']=self::$newStageToStore['status_id'] = $status->id;
        } else {
            throw new \Exception("Employment status not found for code: {$statusCode}");
        }

        // جلب المرحلة من جدول employment_stages
        $stage = Employment_Stages::where('code', $stageCode)->first();
        if ($stage) {
            self::$newStageToStore['stage_id'] = $stage->id;
        } else {
            throw new \Exception("Employment stage not found for code: {$stageCode}");
        }
    }
    public static function check_age()
    {
        $months=(int)self::$request->input('age_months');
        $years=(int)self::$request->input('age_years');
        $days=(int)self::$request->input('age_days');
        if($years == (int)self::$job->age){
            if(($months> 0) || ($days> 0)){
                return false;
            }
            return true;
        }
        if($years > (int)self::$job->age){
            return false;
        }
        return true;
    }
    public static function check_khebra()
    {
        $target=self::$job;$table="Mosama_Experiences";$time=self::$request['khebra_years'];$type=self::$request['khebra_type'];
        $times=[];
        $exp=$target->Mosama_JobNames->$table;
        $expA=$exp->toArray();
        if(!in_array($type,['Interval','Work_experience'])){
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
        $Cities=$job->City;
        $BornCity=$request->input('born_city');
        $LiveCity=$request->input('live_city');
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
        self::$request->merge(['file_name'=>self::$request['file_name']->getClientOriginalName()]);
        //$dataToStore,$newStageToStore,$newLogDataToStore
        self::$dataToStore['id']=self::$dataToStore['id'] ?? \Str::uuid()->toString();
        self::$newStageToStore['id']=self::$newStageToStore['id'] ?? \Str::uuid()->toString();
        self::$newLogDataToStore['id']=self::$newLogDataToStore['id'] ?? \Str::uuid()->toString();
        if (Employment_PeopleNewStage::find(self::$newStageToStore['id'])) {
                throw new \Exception("الشخص موجود مسبقًا بالمعرف: ".self::$dataToStore['id']);
        }
        if (employment_apply_logs::find(self::$newLogDataToStore['id'])) {
                throw new \Exception("الشخص موجود مسبقًا بالمعرف: ".self::$dataToStore['id']);
        }
        self::$newLogDataToStore['user_data']=json_encode(['request'=>self::$request,'store'=>self::$dataToStore]);
        $personData=self::$dataToStore;
        $stageData=self::$newStageToStore;
        $finalData=self::$newLogDataToStore;
        DB::transaction(function () use ($personData, $stageData, $finalData) {
            $person = Employment_PeopleNewData::create($personData);
            $stage = Employment_PeopleNewStage::create($stageData);
            $final = employment_apply_logs::create($finalData);
            //DB::rollBack();
            //return [$person, $stage, $final]; // لو حابب ترجعهم
        });
    }
    public static function store_file(){
        if(self::$request->actiontype == 'create'){
            $newfile=self::$request['nid'].'-'.md5(time() . rand() . \Str::random(40)). '.pdf';
        }else{
            //$newfile=self::$request->input('new_file_name');
            $newfile=self::$request['nid'].'-'.md5(time() . rand() . \Str::random(40)). '.pdf';
        }
        $path = self::$request->file('file_name')->storeAs(config('Amer.Employment.root_disk_name'), $newfile);
        if($path){
            self::$dataToStore['file_name']=$path;
            //self::$request->merge(['file_name'=>$path]);
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
                
        /**
         * sort_request_to_review
         * set the request Data To html array
         * @return void
         */
        public static function sort_request_to_review(){
            //check request
            $json=\Amerhendy\Amer\App\Helpers\AmerHelper::is_Json(self::$request);
            $request=self::$request;$annonce=self::$annonce;$job=self::$job;
            $data=new \stdClass;
            if($request->has('id')){
                self::$request->merge(['uid',$request->input('id')]);
                $request=self::$request;
                $data->id=$request->input('uid');
            }elseif($request->has('uid')){
                $data->id=$request->input('uid');
            }
            $job=new \stdClass;
            $job->Text=self::$job->Mosama_JobNames->text;
            $job->Code=self::$job->code;
            $job->Driver=self::$job->driver;
            $data->job_id=$job;
            $data->NID=$request->nid;
            $data->AgeYears=$request->age_days;
            $data->AgeMonths=$request->age_months;
            $data->AgeDays=$request->age_years;
            $data->BirthDate=$request->birth_date;
            $sex = $json 
                ? 'employment.Employment_People.Sex.' . ($request->sex == '1' ? 'Male' : 'Female') 
                : trans('JOBLANG::Employment_People.Sex.' . ($request->sex == '1' ? 'Male' : 'Female'));
            $data->Sex = $sex;
            $data->FullName=implode(' ',[$request->input('fname'),$request->input('sname'),$request->input('tname'),$request->input('lname')]);
            $data->LivePlace = (object)[
                'LiveGov'     => optional(optional(Cities::find(self::$request->input('live_place')))->Governorates)->name,
                'LiveCity'    => optional(Cities::find(self::$request->input('live_place')))->name,
                'liveaddress' => self::$request->input('live_address'),
            ];
            $data->BornPlace = (object)[
                'BornGov' => optional(optional(Cities::find(self::$request->input('born_place')))->Governorates)->name,
                'BornCity' => optional(Cities::find(self::$request->input('born_place')))->name,
            ];
            $data->Connection = (object)[
                'Landline' => $request->input('connect_landline'),
                'Mobile'   => $request->input('connect_mobile'),
                'Email'    => $request->input('connect_email'),
            ];
            $data->Health_id = Employment_Health::withTrashed()->find($request->input('health_id'))?->text;
            $data->MaritalStatus_id = Employment_MaritalStatus::withTrashed()->find($request->input('marital_status_id'))?->text;
            $data->Arm_id = Employment_Army::withTrashed()->find($request->input('arm_id'))?->text;
            $data->Ama_id = Employment_Ama::withTrashed()->find($request->input('ama_id'))?->text;
            $data->Tamin=$request->input('tamin');
            $data->Khebra=self::khebraToStr([$request->input('khebra_type'),$request->input('khebra_years')]);
            $data->Education_id = Mosama_Educations::withTrashed()->find($request->input('education_id'))?->text;
            $data->EducationYear=$request->input('education_year');
            if ($request->has('accept_driver')) {
                $data->accept_driver = $request->input('accept_driver');

                if ($data->accept_driver && ($driver = Employment_Drivers::withTrashed()->find(self::$request['driverdegree']))) {
                    $data->DriverDegree = $driver->text;
                    $data->DriverStart  = $request->input('driverstart');
                    $data->DriverEnd    = $request->input('DriverEnd');
                } else {
                    $data->DriverDegree = $data->DriverStart = $data->DriverEnd = null;
                }
            }


            $data->created_at=$request->input('apply_date');
            if($request->input('actiontype') === 'create'){
                if($json){$data->actiontype='apply.apply_buttom_apply';}else{$data->actiontype=__('JOBLANG::apply_buttom_apply');}
            }else{
                if($json){$data->actiontype='employment.apply.Complete_buttom_apply';}else{$data->actiontype=__('JOBLANG::apply.Complete_buttom_apply');}
            }
            if($request->input('file_name')){
                
            }
            $file_name=$request->file('file_name');
            if ($file_name instanceof UploadedFile) {
                if(gettype($request->file('file_name')) == 'string'){
                    $data->uploades=$request->file('file_name');
                }else{
                    $data->uploades=$request->file('file_name')->getClientOriginalName();
                }
            }else{
                if(is_null($file_name)){
                    $file_name=$request->input('file_name');
                    $data->uploades=$file_name;
                }else{
                    $data->uploades=null;
                }
            }
            return $data;
        }
        public static function getjobInfo($Object=null,$show='one'){
            $newresult=[];
            $request=self::$request;
            if(is_null($Object)){
                if($request->has('job_id')){
                    if(!is_array($request->input('job_id'))){
                        $jobid=[$request->input('job_id')];
                    }else{
                        $jobid=$request->input('job_id');
                    }
                    $Object=jobs::where('id',$jobid);
                }
                $Object=$Object->get();
            }
            $Object = JobResource::collection($Object);
            $Object=$Object->map(fn($data) => (new JobResource($data))->toObject(request()));
if ($show == 'one') {
    $result = $Object->first(); // يجيب أول عنصر
    if ($request->view == 'pdf') {
        return \AmerHelper::responsedata(self::showJobPrint($result), 1, 1, '');
    } elseif ($request->view == 'json') {
        $result->Stage = $result->Employment_StartAnnonces->Employment_Stages;
        return \AmerHelper::responsedata($result, 1, 1, '');
    }
    return $result;
} else {
    $result = $newresult;
    return \AmerHelper::responsedata($result, 1, 1, '');
}
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
        }else{
            $testid=self::insertLogDB();
            $qr->test=$testid;
            $qr->requestType='review';
        }
        $qrKey='type=employment&action='.$qr->requestType.'&testid='.$qr->test;
        $amerhelper=new \AmerHelper();
        $qrKey=$amerhelper::encryptData($qrKey);
        $qrRoute=route('qrcode',['element'=>'pck?'.$qrKey]);
        $shortUrl=$amerhelper::createShortUrl($qrRoute,1,'year');
        return $shortUrl;
    }

    public static function insertLogDB(){
        $request=self::$request;
        $testid=\DB::table('employment_apply_logs')->insertGetId(['id'=>\Str::uuid(),'user_data' => json_encode($request)]);
        return $testid;
    }
    public static function setBasicJobInfo(){
        self::$job=self::getjobInfo();
        foreach(self::$job->Mosama_JobNames->Mosama_Experiences as $a=>$b){
            if($b[1] == 0){
                self::$job->Mosama_JobNames->Mosama_Experiences[$a]=trans("EMPLANG::Mosama_Experiences.year0");
            }else{
                if($b[0] == 'Work_experience'){
                    self::$job->Mosama_JobNames->Mosama_Experiences[$a][0]=trans("EMPLANG::Mosama_Experiences.enum_1");
                }elseif($b[0] == 'Interval'){
                    self::$job->Mosama_JobNames->Mosama_Experiences[$a][0]=trans("EMPLANG::Mosama_Experiences.enum_0");
                }
                $translate=trans("EMPLANG::Mosama_Experiences.translate");
                self::$job->Mosama_JobNames->Mosama_Experiences[$a] =(string) \Str::of($translate)->replaceArray('?', self::$job->Mosama_JobNames->Mosama_Experiences[$a]);
            }
        }
    }
    public static function setRequestData(){
        self::SetReqactiontypeData();
        if(!self::$request->has('khebra')){
            self::$reqmerge['khebra'] = json_encode([self::$request->input('khebra_type'),self::$request->input('khebra_years')]);
            self::$request->merge(['khebra',json_encode([self::$request->input('khebra_type'),self::$request->input('khebra_years')])]);
        }
        $education_year = self::$request->education_year;
        [$month, $year] = explode('/', $education_year);
        $education_year = $year . str_pad($month, 2, '0', STR_PAD_LEFT);
        self::$reqmerge['education_year']=$education_year;
        self::$reqmerge['created_at']=self::$request->input('apply_date');
        self::$reqmerge['updated_at']=null;
        self::$reqmerge['deleted_at']=null;
        self::$request->merge(self::$reqmerge);
    }
    public static function prepareSotreData(){
        if(array_key_exists('people_id',self::$dataToStore)){self::$newStageToStore['people_id']=self::$dataToStore['people_id']=self::$request->input('uid');}else{self::$newStageToStore['people_id']=self::$dataToStore['id'];}
        if(array_key_exists('job_id',self::$dataToStore)){self::$dataToStore['job_id']=self::$request->input('job_id');}
        if(array_key_exists('fname',self::$dataToStore)){self::$dataToStore['fname']=self::$request->input('fname');}
        if(array_key_exists('sname',self::$dataToStore)){self::$dataToStore['sname']=self::$request->input('sname');}
        if(array_key_exists('tname',self::$dataToStore)){self::$dataToStore['tname']=self::$request->input('tname');}
        if(array_key_exists('lname',self::$dataToStore)){self::$dataToStore['lname']=self::$request->input('lname');}
        if(array_key_exists('live_place',self::$dataToStore)){self::$dataToStore['live_place']=self::$request->input('live_place');}
        if(array_key_exists('live_address',self::$dataToStore)){self::$dataToStore['live_address']=self::$request->input('live_address');}
        if(array_key_exists('born_place',self::$dataToStore)){self::$dataToStore['born_place']=self::$request->input('born_place');}
        if(array_key_exists('connect_landline',self::$dataToStore)){self::$dataToStore['connect_landline']=self::$request->input('connect_landline');}
        if(array_key_exists('connect_mobile',self::$dataToStore)){self::$dataToStore['connect_mobile']=self::$request->input('connect_mobile');}
        if(array_key_exists('connect_email',self::$dataToStore)){self::$dataToStore['connect_email']=self::$request->input('connect_email');}
        if(array_key_exists('health_id',self::$dataToStore)){self::$dataToStore['health_id']=self::$request->input('health_id');}
        if(array_key_exists('marital_status_id',self::$dataToStore)){self::$dataToStore['marital_status_id']=self::$request->input('marital_status_id');}
        if(array_key_exists('arm_id',self::$dataToStore)){self::$dataToStore['arm_id']=self::$request->input('arm_id');}
        if(array_key_exists('ama_id',self::$dataToStore)){self::$dataToStore['ama_id']=self::$request->input('ama_id');}
        if(array_key_exists('tamin',self::$dataToStore)){self::$dataToStore['tamin']=self::$request->input('tamin');}
        if(array_key_exists('khebra',self::$dataToStore)){self::$dataToStore['khebra']=json_encode([self::$request->input('khebra_type'),self::$request->input('khebra_years')]);}
        if(array_key_exists('education_id',self::$dataToStore)){self::$dataToStore['education_id']=self::$request->input('education_id');}
        if(array_key_exists('education_year',self::$dataToStore)){
            if(\Str::contains( self::$request->input('education_year'),'/')){
                [$month, $year] = explode('/', self::$request->input('education_year'));
                $education_year = $year . str_pad($month, 2, '0', STR_PAD_LEFT);
            }else{
                $education_year=(int) self::$request->input('education_year');
            }
            self::$dataToStore['education_year']=$education_year;
        }
        if(array_key_exists('driver_degree',self::$dataToStore)){self::$dataToStore['driver_degree']=self::$request->input('driver_degree');}
        if(array_key_exists('driver_start',self::$dataToStore)){self::$dataToStore['driver_start']=self::$request->input('driver_start');}
        if(array_key_exists('driver_end',self::$dataToStore)){self::$dataToStore['driver_end']=self::$request->input('driver_end');}
        if(array_key_exists('nid',self::$dataToStore)){self::$dataToStore['nid']=self::$request->input('nid');}
        if(array_key_exists('sex',self::$dataToStore)){self::$dataToStore['sex']=self::$request->input('sex');}
        if(array_key_exists('birth_date',self::$dataToStore)){self::$dataToStore['birth_date']=self::$request->input('birth_date');}
        if(array_key_exists('age_years',self::$dataToStore)){self::$dataToStore['age_years']=self::$request->input('age_years');}
        if(array_key_exists('age_months',self::$dataToStore)){self::$dataToStore['age_months']=self::$request->input('age_months');}
        if(array_key_exists('age_days',self::$dataToStore)){self::$dataToStore['age_days']=self::$request->input('age_days');}
        if(array_key_exists('created_at',self::$dataToStore)){self::$dataToStore['created_at']=self::$request->input('apply_date');}
        
        self::SetRequsetActionTypeData();
    }
    private static function SetRequsetActionTypeData(){
        switch (self::$request->input('actiontype')) {
            case 'create':
                self::$dataToStore['stage_id']=self::$annonce->stage_id;
                self::$dataToStore['annonce_id']=self::$annonce->id;
                break;

            case 'complete':
                self::$dataToStore['stage_id']=self::$request->input('peoplenewstageid');
                break;

            default:
            dd("SD");
            break;
        }
    }
    }
