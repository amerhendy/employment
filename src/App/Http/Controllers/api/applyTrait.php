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
use \Amerhendy\Employers\App\Models\Mosama_Experiences;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
trait applyTrait
{
    static function local($annid,$jobid,$process,Request $request){
        if((!isset($request->annonce_id)) || (!isset($request->job_id)) || (!isset($request->actiontype))){
            return response()->json(['message' => __LINE__], 405);
        }
        $annonce=Employment_StartAnnonces::where('Slug',$request->annonce_id)->first();
        if(!$annonce){
            return response()->json(['error'=>['annonce_id'=>[trans('JOBLANG::Employment_StartAnnonces.singular')]]]);
        }
        self::$annonce=$annonce;
        $job=Employment_Jobs::with([
            'Employment_Ama',
            'Employment_Army',
            'Cities',
            'Mosama_Educations',
            'Employment_Health',
            'Employment_MaritalStatus',
            'Employment_Drivers',
            'Mosama_Educations',
            'Mosama_JobNames.Mosama_Experiences',
            'Employment_StartAnnonces.Governorates',
            'Employment_StartAnnonces.Employment_Qualifications',
            'Employment_StartAnnonces.Employment_Stages'
            ])->where('Slug',$request->job_id)->get()->first();
            if(!$job){
                return response()->json(['error'=>['job_id'=>[trans('JOBLANG::Employment_Jobs.singular')]]]);
            }
        self::$job=$job;
        if($request['actiontype'] == 'complete'){
            ///////// check if he can complete
            ///////// check if he completed before////////
            $getlaststage=self::getlaststage($request['uid']);
            if($getlaststage['result'] == false){
                return response()->json(['error'=>['annonce_id'=>['غير متاح التقديم']]]);
            }
            $dsdsd=Employment_PeopleNewData::where('Stage_id',$getlaststage['stage'])->get()->count();
            if($dsdsd !== 0){
                return response()->json(['error'=>['annonce_id'=>['تم تقديم البيانات من قبل ... يرجى متابعة الصفحة الرسمية']]]);
            }
                $alluserstages=$getlaststage['allstages'];
                $newfilename=$request['uinid'] . '_'.$alluserstages.'.pdf';
                $request['new_file_name']=$newfilename;
                $request['Stage_id']=$getlaststage['stage'];
        }
        $validator=self::check_validate($request);
        if(count($validator->errors())){
            return response()->json(['error'=>$validator->errors()]);
        }
        return self::store($request,$validator);
    }
    public static function check_validate(Request $request){
        /*dd(
            $request->host(),
        $request->httpHost(),
        $request->schemeAndHttpHost(),
        $request->method(),
        $request->bearerToken(),
        $request->ip(),
        $request->getAcceptableContentTypes(),
        $request->input(),
        $query = $request->query());
        $request->merge(['votes' => 0]);
        //dd($request->input('Fname'));
        dd($request->all());*/
        $rules=[
            '_token'=>'required',
            '_method'=>'required',
            'annonce_id' => 'required|exists:Employment_StartAnnonces,Slug',
            'job_id' => 'required_if:actiontype,==,apply|exists:Employment_Jobs,Slug',
            'apply_date'=>'required|date|size:19',
            'Fname'=>'required|string|max:255|min:3',
            'Sname'=>'required|string|max:255|min:3',
            'Tname'=>'required|string|max:255|min:3',
            'Lname'=>'required|string|max:255|min:3',
            'NID'=>'required_if:actiontype,==,apply|digits:14',
            'BirthDate'=>'required_if:actiontype,==,apply|date',
            'Sex'=>'required_if:actiontype,==,apply|in:0,1',
            'AgeYears'=>'required_if:actiontype,==,apply|integer',
            'AgeMonths'=>'required_if:actiontype,==,apply|integer',
            'AgeDays'=>'required_if:actiontype,==,apply|integer',
            'BornGov'=>'required|exists:Governorates,id',
            'BornCity'=>'required|exists:Cities,id',
            'LiveGov'=>'required|exists:Governorates,id',
            'LiveCity'=>'required|exists:Cities,id',
            'LiveAddress'=>'required|max:255|min:10',
            'ConnectLandline'=>'required|max:255|min:3',
            'ConnectMobile'=>'required|max:255|min:7',
            //'ConnectEmail'=>'required|max:255|email:rfc,strict,dns,spoof,filter,filter_unicode',
            'ConnectEmail'=>'required|max:255|email:rfc,strict,spoof,filter,filter_unicode',
            'Health_id'=>'integer|required|exists:Employment_Health,id',
            'MaritalStatus_id'=>'integer|required|exists:Employment_MaritalStatus,id',
            'Arm_id'=>'integer|required|exists:Employment_Army,id',
            'Ama_id'=>'integer|required|exists:Employment_Ama,id',
            'Education_id'=>'integer|required|exists:Mosama_Educations,id',
            'EducationYear'=>'required|integer|min:4',
            'Khebra'=>'required|integer',
            'Tamin'=>'required|min:1',
            'acceptall'=>'required|accepted',
            'actiontype'=>'required',
            'uinid'=>'required_if:actiontype,==,complete|digits:14|exists:Employment_People,NID',
            'uid'=>'required_if:actiontype,==,complete|exists:Employment_People,id',
            'uploades'=>'required|file|mimetypes:application/pdf'
        ];
        $attributes=[
            'Fname'=> trans('JOBLANG::Employment_People.Fname'),
            'Sname'=> trans('JOBLANG::Employment_People.Sname'),
            'Tname'=> trans('JOBLANG::Employment_People.Tname'),
            'Lname'=> trans('JOBLANG::Employment_People.Lname'),
            'NID'=>trans('JOBLANG::Employment_People.NID'),
            'BirthDate'=>trans('JOBLANG::Employment_People.BirthDate'),
            'Sex'=>trans('JOBLANG::Employment_People.Sex.Sex'),
            'AgeYears'=>trans('JOBLANG::Employment_People.Age.AgeYears'),
            'AgeMonths'=>trans('JOBLANG::Employment_People.Age.AgeMonths'),
            'AgeDays'=>trans('JOBLANG::Employment_People.Age.AgeDays'),
            'BornGov'=>trans('JOBLANG::Employment_People.bornPlace.Governorate'),
            'BornCity'=>trans('JOBLANG::Employment_People.bornPlace.City'),
            'LiveGov'=>trans('JOBLANG::Employment_People.LivePlace.Governorator'),
            'LiveCity'=>trans('JOBLANG::Employment_People.LivePlace.City'),
            'LiveAddress'=>trans('JOBLANG::Employment_People.LivePlace.Address'),
            'ConnectLandline'=>trans('JOBLANG::Employment_People.Connection.LandLine'),
            'ConnectMobile'=>trans('JOBLANG::Employment_People.Connection.Mobile'),
            'ConnectEmail'=>trans('JOBLANG::Employment_People.Connection.Email'), //'required|max:255|email:rfc,strict,dns,spoof,filter,filter_unicode',
            'Health_id'=>trans('JOBLANG::Employment_Health.singular'),
            'MaritalStatus_id'=>trans('JOBLANG::Employment_MaritalStatus.singular'),
            'Arm_id'=>trans('JOBLANG::Employment_Army.singular'),
            'Ama_id'=>trans('JOBLANG::Employment_Ama.singular'),
            'Education_id'=>trans('JOBLANG::Employment_People.Mosama_Educations.Mosama_Educations'),
            'EducationYear'=>trans('JOBLANG::Employment_People.Mosama_Educations.year'),
            'Khebra'=>trans('JOBLANG::Employment_People.Khebra.years'),
            'Tamin'=>trans('JOBLANG::Employment_People.Tamin.Tamin'),
            'acceptall'=>trans('JOBLANG::Employment_People.acceptall'),
            'uinid'=>trans('JOBLANG::Employment_People.NID'),
            'uid'=>trans('JOBLANG::Employment_People.uid'),
            'uploades'=>trans('JOBLANG::Employment_People.Uploaded_files'),
            //'uploades'=>'required|file|mimetypes:application/pdf'
        ];
        $messages=[
            'required' => trans('JOBLANG::apply.errors.required',[':attribute']),
            'string' => trans('JOBLANG::apply.errors.string',[':attribute']),
            'digits'=> trans('JOBLANG::apply.errors.digits',[':attribute']),
            'integer'=> trans('JOBLANG::apply.errors.digits',[':attribute']),
            'required_if'=>trans('JOBLANG::apply.errors.required',[':attribute']),
            'date'=> trans('JOBLANG::apply.errors.date',[':attribute']),
            'in'=> trans('JOBLANG::apply.errors.in',[':attribute']),
            'exists'=>trans('JOBLANG::apply.errors.exists',[':attribute']),
            'max'=>trans('JOBLANG::apply.errors.max',[':attribute',':max']),
            'email'=>trans('JOBLANG::apply.errors.email',[':attribute']),
            'file'=>trans('JOBLANG::apply.errors.file',[':attribute']),
            'mimetypes'=>trans('JOBLANG::apply.errors.mimetypes',[':attribute']),
        ];
        $validator = Validator::make($request->all(), $rules,$messages,$attributes);
        if($request['actiontype'] == 'apply'){
            $annonce_id=self::$annonce->id;
            $people=Employment_People::where('Annonce_id',self::$annonce->id)->where('NID',$request->NID)->first();
            if($people){$validator->errors()->add('NID',trans('JOBLANG::apply.nid_Already_Exists'));}
        }
        if(self::$job->Driver == 0){
            if($request['DriverDegree'] == ''){$validator->errors()->add('degree',trans('JOBLANG::Employment_People.DriverDegree.DriverDegreeri_req'));}
            if($request['DriverStart'] == ''){$validator->errors()->add('DriverStart',trans('JOBLANG::Employment_People.DriverDegree.DriverStart'));}
            if($request['DriverEnd'] == ''){$validator->errors()->add('DriverEnd',trans('JOBLANG::Employment_People.DriverDegree.DriverEnd'));}
        }
return $validator;
}
    public static function store(Request $request,$validator)
    {
        $requirements=self::check_job_employer_requirements($request);
        $request['Message']=$requirements;
        $store_file=self::store_file($request);
        if($store_file === false){
            $validator->errors()->add('uploades',trans('JOBLANG::Employment_People.Uploaded_files'));
            return response()->json(['error'=>$validator->errors()]);
        }
        $request->merge(['FileName'=>$store_file]);
        $data=[];
        $Employment_Jobs=self::$job;
        $data['user']=\Amerhendy\Employment\App\Http\Controllers\apply::sort_request_to_review($request->all(),self::$annonce,self::$job);
        $job_annonce=self::getjob_by_job_slug($request->annonce_id,$request->job_id);
        $job_annonce=json_decode($job_annonce)->data;
        $data['job']= $job_annonce;
        $data['annonce']=$job_annonce->Employment_StartAnnonces;
        if($request->actiontype == 'apply' || $request->actiontype == 'complete')
        {
            ///////////////////////////////
            //$success=['success'=>[100,100]];
            //return response()->json($success);
            ///////////////////////////////
            $submit=self::apply_submit($request);
            if($submit[1] == 'bad'){
                return response()->json(['error'=>$submit[2]]);
            }else{
                $success=['success'=>$submit];
                return response()->json($success);
            }
        }
    }
    public static function check_job_employer_requirements(Request $request)
    {
        $errors=[];
        $job=self::$job;
        if(!$job){
            return 'error';
        }
        if(self::check_age($job->Age,$request['AgeYears'],$request['AgeMonths'],$request['AgeDays']) == false){
            $errors['Age']='JOBLANG::Employment_People.Age.Age';
        }
        if(self::check_condetions($job,'Employment_Ama',$request['Ama_id']) == false){
            $errors['Ama_id']='JOBLANG::Employment_Ama.singular';
        }
        if(self::check_condetions($job,'Employment_Army',$request['Arm_id']) == false){
            $errors['Arm_id']='JOBLANG::Employment_Army.singular';
        }
        if(self::check_condetions($job,'Mosama_Educations',$request['Education_id']) == false){
            $errors['Education_id']='EMPLANG::Mosama_Educations.singular';
        }
        if(self::check_condetions($job,'Employment_Health',$request['Health_id']) == false){
            $errors['Health_id']='JOBLANG::Employment_Health.singular';
        }
        if(self::check_condetions($job,'Employment_MaritalStatus',$request['MaritalStatus_id']) == false){
            $errors['MaritalStatus_id']='JOBLANG::Employment_MaritalStatus.singular';
        }
        if(self::check_khebra($job,'Mosama_Experiences',$request['Khebra'],$request['Khebra_type']) == false){
            $errors['Khebra']='EMPLANG::Mosama_Experiences.singular';
        }
        if((int) $job->Driver == 1){
            $request['DriverDegree']=null;
            $request['DriverStart']=null;
            $request['DriverEnd']=null;
        }else{
            $a=self::check_driver_degree($job->Employment_Drivers,$request['DriverDegree'],$request['DriverStart'],$request['DriverEnd']);
            if($a == 'error'){
                return 'error';
            }
            if($a !== 1){
                $errors['Driver']=$a;
            }
        }
        $placescheck=self::check_places($request,$job);
        if($placescheck !== true){$errors[]=$placescheck;}
        //$errors['Driver']=[['DriverDegree'=>'JOBLANG::Employment_People.Employment_Drivers.DriverDegree'],['DriverEnd'=>'JOBLANG::Employment_People.Employment_Drivers.DriverEnd']];
        $errors=self::flatten_requirements($errors);
        return $errors;
    }
    public static function check_age($target,$years,$months,$days)
    {
        if($years == $target){
            if(($months> 0) || ($days> 0)){
                return false;
            }
            return true;
        }
        if($years > $target){
            return false;
        }
        return true;
    }
    public static function check_khebra($target,$table,$time,$type)
    {
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
    public static function check_condetions($job,$table,$people)
    {
        $con=$job->{$table};
        
        $ids=[];
        foreach($con as $a) {
            $ids[]=$a['id'];
        }
        if(!in_array($people,$ids)){
            return false;
        }
        return true;
    }
    public static function check_driver_degree($target,$degree,$start,$end)
    {
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
    public static function check_places($request,$job)
    {
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
    private static function apply_submit(Request $request)
    {
        if($request->actiontype == 'apply'){
            $request->merge(['Stage_id' => self::$annonce->Stage_id]);
            $request->merge(['Annonce_id' => self::$annonce->id]);
        }
        if($request->actiontype == 'complete'){
            $request->merge(['People_id' => $request->uid]);
        }
        $request->merge(['Job_id' => self::$job->id]);
        if(self::$job->Driver == '1'){
            $request->merge(['DriverDegree' => null]);
            $request->merge(['DriverStart' => null]);
            $request->merge(['DriverEnd' => null]);
        }
        if(count($request->Message)){
            $request->merge(['Result' => 2]);
        }else {
            $request->merge(['Result' => 1]);
        }
        if(!$request->has('ConnectLandline')){$request->merge(['ConnectLandline' => null]);}
        if(!$request->has('ConnectMobile')){$request->merge(['ConnectMobile' => null]);}
        if(!$request->has('ConnectEmail')){$request->merge(['ConnectEmail' => null]);}
        if(!$request->has('Arm_id')){$request->merge(['Arm_id' => null]);}
        if(!$request->has('Ama_id')){$request->merge(['Employment_Ama' => null]);}
        if(!$request->has('Tamin')){$request->merge(['Tamin' => null]);}
        if(!$request->has('Khebra')){$request->merge(['Khebra' => null]);}else{
            $request->merge(['Khebra' => json_encode([$request->input('Khebra_type'),$request->input('Khebra')])]);
        }
        if(!$request->has('Message')){$request->merge(['Message' => null]);}else{
            if(is_array($request->input('Message'))){
                if(!count($request->input('Message'))){
                    $request->merge(['Message' =>null]);
                }else{
                    $messages=\Arr::map($request->input('Message'),function($v,$k){
                        return trans($v);
                    });
                    $request->merge(['Message' => json_encode([$request->input('Message')])]);
                }
            }
        }
        if(!$request->has('DriverDegree')){$request->merge(['DriverDegree' => null]);}
        if(!$request->has('DriverStart')){$request->merge(['DriverStart' => null]);}
        if(!$request->has('DriverEnd')){$request->merge(['DriverEnd' => null]);}
        $request->merge(['created_at' => $request->input('apply_date')]);
        $request->merge(['updated_at' => null]);
        $request->merge(['deleted_at' => null]);
        $request->merge(['deleted_at' => null]);
        $arrRequest=$request->toArray();
        $arrRequest['id']=self::newid($request->actiontype);
            $unset=['_token','annonce_id','job_id','_method','apply_date','Khebra_type','actiontype','uploades','acceptall'];
        if($request->actiontype == 'complete'){
            $unset=array_merge($unset,['uid','NID','BirthDate','Sex','AgeYears','AgeMonths','AgeDays','uinid','new_file_name','select_job']);
        }
        foreach ($unset as $key => $value) {
            unset($arrRequest[$value]);
        }
        $request->merge(['uploades'=>$request['uploades']->getClientOriginalName()]);

        if($request->actiontype == 'apply'){
            $insert=Employment_people::create($arrRequest);
        }elseif($request->actiontype == 'complete'){
            $insert=Employment_PeopleNewData::create($arrRequest);
        }
        if(!$insert){
            return ['bad','inserting People Data'];
        }
        $request->merge(['id'=>$insert->id]);
        //insert New Stage
        $los=[];
        if($request->actiontype == 'apply'){$los['People_id']=$request->input('id');}else{$los['People_id']=$request->input('People_id');}
        if(!is_null($request->input('Message'))){
            $los['Message']=$request->input('Message');
            if($request->actiontype == 'apply'){$los['Status_id']=2;$los['Stage_id']=5;}else{$los['Stage_id']=3;$los['Status_id']=3;}
            $los['created_at']=now();
        }else{
            $los['Status_id']=4;
            $los['Message']=null;
            if($request->actiontype == 'apply'){$los['Stage_id']=3;}else{$los['Stage_id']=14;}
            $los['Stage_id']=3;
            $los['created_at']=now();
        }
        if($request->actiontype == 'apply'){
            /*$newstage=Employment_PeopleNewStage::create($los);
            if(!$newstage){
                return ['bad','inserting NewStage'];
            }
            */
        }
        $testid=self::insertLogDB($request);
        $request->merge(['test'=>$testid]);
        return [$request->input('test'),$request->input('id')];
    }
    public static function insertLogDB(Request $request){
        $id=AmerHelper::LstTableID('Employment_ApplyLog');
        $dbcode=json_encode($request->all());
        $testid=\DB::table('Employment_ApplyLog')->insertGetId(['id'=>$id,'userData' => $dbcode]);
        return $testid;
    }
    public static function store_file(Request $request){
        if($request->actiontype == 'apply'){
            $newfile=$request['NID'] . '.pdf';
        }else{
            $newfile=$request->input('new_file_name');
        }
        $path = $request->file('uploades')->storeAs(config('Amer.employment.root_disk_name'), $newfile);
        if($path){
            return $path;
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
        private static function newid($req){
            if($req == 'apply'){
                return AmerHelper::LstTableID('Employment_People');
            }else{
                return AmerHelper::LstTableID('Employment_PeopleNewData');
            }
        }
}