<?php
namespace Amerhendy\Employment\App\Http\Controllers\Admin\Main;
use Illuminate\Support\Facades\DB;
use \Amerhendy\Amer\App\Http\Controllers\Base\AmerController;
use \Amerhendy\Amer\App\Helpers\Library\AmerPanel\AmerPanelFacade as AMER;
use \Amerhendy\Employment\App\Models\Employment_People;
use Amerhendy\Employment\App\Models\Employment_StartAnnonces;
use Amerhendy\Employment\App\Models\Employment_PeopleNewStage;
use \Amerhendy\Amer\App\Models\Cities;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\View;
class Employment_ReportsAmerController extends AmerController
{
    public function __construct(Amer $amer){
        $this->middleware("Amer");

    }
    private static function annonce($id){
        return Employment_StartAnnonces::with('Employment_Jobs')->where('id',$id)->first();
    }
    static function jobs($jobs){
        $jobArr=$jobs->toArray();
        $alo=\Arr::map($jobArr,function($v,$k){
            return($v['id']);
        });
        return $jobs;
    }
    static function generatenid($birthdate){
        $nid='';
        if($birthdate->format("Y")>=2000){
            $nid.=3;
        }else{
            $nid.=2;
        }
        $nid.=$birthdate->format("ymd");
        $GovsArr=[1=>'01',2=>'21',3=>'02',4=>'12',5=>'31',6=>'18',7=>'23',8=>'16',9=>'19',10=>'17',11=>'24',12=>'14',13=>'32',15=>'28',16=>'25',14=>'04',17=>'22',18=>'03',19=>'11',20=>'13',21=>'35',22=>'15',23=>'33',24=>'29',25=>'27',26=>'34',27=>'26'];
        $govid=array_rand($GovsArr);
        $borncity=Cities::where('gov_id',$govid)->inRandomOrder()->get()->first();
        $livegov=$govid=array_rand($GovsArr);
        $livecity=Cities::where('gov_id',$livegov)->inRandomOrder()->get()->first();
        $nid.=$GovsArr[$govid];
        $fourNumbers=rand(1000,9999);
        if ($fourNumbers % 2 == 0) {
            $sex=0;
          }else{
            $sex= 1;
          }
        $nid.=$fourNumbers;
        $nid.=rand(1,9);
        return ['nid'=>$nid,'sex'=>$sex,'borngov'=>$govid,'borncity'=>$borncity->id,'livegov'=>$livegov,'livecity'=>$livecity->id];
    }
    static function generatename($sex){
        $male=['محمد','احمد','محمود','حامد','حمد','حمدان','حماد','حميد','سليم','سالم','سلمان','سالمان','ممدوح','معتز','مصطفى','طه','يس','عبدالله','عبدالجواد','عبدالمنعم','عامر','عبدالرازق','عبدالعليم','مروان','مازن','يوسف','كريم','كرم','كارم','سعفان','عادل','خليل','ابراهيم','اسلام','يونس','ادم','ادريس','اسماعيل','معتصم','مؤمن','زيد','زياد','زايد'];
            $female=['علا','عبير','سمحة','سحر','نجوى','نجلاء','نهلة','نهال','نرجس','ايمان','عزة','عزيزة','حنان','مى','مروة','ايه','اسماء','سلوى','سالى','رشا','رحاب','رحمة','امينة','جميلة','جملات','جمانة','فاطمة','بدوية','أسما','ثرية'];
        if($sex==1){            
            $Fname=$male[array_rand($male)];
        }else{
            $Fname=$female[array_rand($female)];
        }
        $Sname=$male[array_rand($male)];
        $Tname=$male[array_rand($male)];
        $Lname=$male[array_rand($male)];
        $street=array_rand($male)." شارع ".$male[array_rand($male)] ." بن ". $male[array_rand($male)];
        return[$Fname,$Sname,$Tname,$Lname,$street];
    }
    static function createConnections(){
        $numbers=[1,2,3,4,5,6,7,8,9];
        $letters=['q','Q','w','e','r','t','y','u','i','o','p','a','s','d','f','g','h','j','k','l','z','x','c','v','b','n','m','W','E','R','T','Y','U','I','O','P','A','S','D','F','G','H','J','K','L','Z','X','C','V','B','N','M'];
        $landline='0'.rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9);
        $mobile='01'.rand(0,2).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9).rand(1,9);
        $email='';
        for($i=0;$i<10;$i++){
            $email.=$letters[array_rand($letters)];
        }
        $email.="@";
        for($i=0;$i<4;$i++){
            $email.=$letters[array_rand($letters)];
        }
        $at=['com','org','net','i'];
        $email.=".".$at[array_rand($at)];
        return[$landline,$mobile,$email];
    }
    public static function test(){
        $annids=[1,2,3];
        $rand=array_rand($annids);
        $annonce=self::annonce($annids[$rand]);
        $jobs=$annonce->Employment_Jobs;
        $jobArr=$jobs->toArray();
        $jobids=\Arr::map($jobArr,function($v,$k){
            return($v['id']);
        });
        $jobid=$jobids[array_rand($jobids)];
        $birthdate=new \DateTime(rand(01,31)."-".rand(01,12)."-".rand(1970,2003)."");
        $howOldAmI =\AmerHelper::findage($birthdate);
        $howOldAmI=explode("-",$howOldAmI);
        $nidarr=self::generatenid($birthdate);
        $names=self::generatename($nidarr['sex']);
        $connections=self::createConnections();
        $MaritalStatus=[1,2,4,6];
        $khebra=[];
        $khebra[rand(0,2)]=rand(0,10);
        $result=rand(1,2);
        if($result == 1){
            $message=null;
        }else{
            $message=[];
            $errors=[];
            $errors[]=['CityBornLive'=>'JOBLANG::Employment_jobs.CityBornLive'];
            $errors[]=['DriverDegree'=>'JOBLANG::Employment_People.Employment_Drivers.DriverDegree'];
            $errors[]=['DriverEnd'=>'JOBLANG::Employment_People.Employment_Drivers.DriverEnd'];
            $errors[]=['Khebra'=>'EMPLANG::Mosama_Experiences.singular'];
            $errors[]=['MaritalStatus_id'=>'JOBLANG::Employment_MaritalStatus.singular'];
            $errors[]=['Health_id'=>'JOBLANG::Employment_Health.singular'];
            $errors[]=['Education_id'=>'EMPLANG::Mosama_Educations.singular'];
            $errors[]=['Arm_id'=>'JOBLANG::Employment_Army.singular'];
            $errors[]=['Ama_id'=>'JOBLANG::Employment_Ama.singular'];
            $errors[]=['Age'=>'JOBLANG::Employment_People.Age.Age'];
            $errlen=rand(1,5);
            $message=[];
            for($i=0;$i<=$errlen;$i++){
                $message[]=($errors[array_rand($errors)]);
            }
            $message=json_encode($message);
        }
        $drivers=[null,1,2,3,4];
        $drivers=$drivers[array_rand($drivers)];
        if($drivers == null){
            $start=null;
            $end=null;
        }else{
            $start= date("m-d-Y",mt_rand(strtotime("1/1/2021"),strtotime("1/1/2022")));
            $end= date("m-d-Y",mt_rand(strtotime("1/1/2023"),strtotime("1/1/2024")));
        }
        
        $arr=[
            'id'=>'',
            'annonce_id'=>$annonce->id,
            'job_id'=>$jobid,
            'NID'=>$nidarr['nid'],
            'Sex'=>$nidarr['sex'],
            'Fname'=>$names[0],
            'Lname'=>$names[3],
            'Sname'=>$names[1],
            'Tname'=>$names[2],
            'LiveGov'=>$nidarr['livegov'],
            'LiveCity'=>$nidarr['livecity'],
            'LiveAddress'=>$names[4],
            'BornGov'=>$nidarr['borngov'],
            'BornCity'=>$nidarr['borncity'],
            'BirthDate'=>$birthdate->format('m-d-Y'),
            'AgeYears'=>(int) $howOldAmI[0],
            'AgeMonths'=>(int) $howOldAmI[1],
            'AgeDays'=> (int) $howOldAmI[2],
            'ConnectLandline'=> $connections[0],
            'ConnectMobile'=>$connections[1],
            'ConnectEmail'=>$connections[2],
            'Health_id'=>rand(1,18),
            'MaritalStatus_id'=>$MaritalStatus[array_rand($MaritalStatus)],
            'Arm_id'=>rand(1,4),
            'Ama_id'=>rand(5,7),
            'Tamin'=>rand(100000,999999),
            'Khebra'=>json_encode([$khebra]),
            'Education_id'=>rand(1,32),
            'EducationYear'=>rand(1995,2021),
            'Stage_id'=>1,
            'Result'=>$result,
            'Message'=>$message,
            'DriverDegree'=>$drivers,
            'DriverStart'=>$start,
            'DriverEnd'=>$end,
            'FileName'=>$nidarr['nid'].".php",
            'created_at'=>now(),
        ];
        return $arr;
    }
    public static function genNewStatus(){
        //annonce_cities
        $ans=[];$sq=[];
        $jobs=\Amerhendy\Employment\App\Models\Employment_Jobs::where('Driver',1)->get('id');
        foreach($jobs as $job){
            $people=Employment_People::where('job_id',$job->id)->get();
            foreach($people as $a=>$b){
                DB::table('Employment_People')->where('id',$b->id)->update(['DriverDegree'=>null,'DriverStart'=>null,'DriverEnd'=>null]);
            }
        }
        foreach($sq as $a=>$b){
            //DB::table('Employment_People')->where('id',$a)->update(['Khebra'=>$b]);
        }
        dd($sq);
        //DB::table('Employment_Jobs_City')->insert($sq);
    }
    public static function index()
    {
        //return self::genNewStatus();
        //dd(\Route::currentRouteName());
        return view("Employment::admin.index");
        $data=[];
        for($i=1;$i<=1000;$i++){
            $tes=self::test();
            $tes['id']=$i;
            $data[]=$tes;
        }
        foreach($data as $a=> $b){
            Employment_People::create($b);
        }
        
    }
    public static function printForm(Request $request)
    {
        $data=new \stdClass();
        $data->section='';
        $data->ids='';
        $data->stage='';
        $data->type='';
        $data->table='';
        $data->actions='';
        $data->printDate='';
        if($request->has('printSectionForm')){
            $printSectionForm=['files','Seatings'];
            if(!in_array($request->input('printSectionForm'),$printSectionForm=['files','Seatings'])){return view('errors.layout',['error_number'=>500,'file'=>__FILE__,'error_message'=>__LINE__]);}
        }
        $print ='files';
        if($request->input('printSectionForm') == 'Seatings'){
            $print='Seatings';
        }
        if($print =='Seatings'){
            if(!in_array($request->input('SeatingPrintType'),['Table','Ticket'])){return view('errors.layout',['error_number'=>500,'file'=>__FILE__,'error_message'=>__LINE__]);}
            if(!\AmerHelper::isJson($request->input('SeatingidsTextarea'))){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__]);}
            if($request->input('SeatingPrintType') == 'Table'){
                if(!$request->has('SeatingPrintTable')){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__]);}
                if(!in_array($request->input('SeatingPrintTable'),['tableForSign','tableForMembers','tableForCollection'])){return view('errors.layout',['error_number'=>500,'file'=>__FILE__,'error_message'=>__LINE__]);}
            }
            $ids=json_decode($request->input('SeatingidsTextarea'),true);
            $data->section=$request->input('printSectionForm');
            $data->ids=$ids;
            $data->stage=$request->input('SeatingPrintStage');
            $data->type=$request->input('SeatingPrintType');
            $data->table=$request->input('SeatingPrintTable');
            $data->printDate=$request->input('print_date');
            return view("Employment::admin.searchform.printData",['data'=>$data]);
        }
        
        $actions=['Full','LastEntry','Downloads','CheckApplyData','GrievanceApply','GrievanceEditorial','GrievancePractical','Degrees','SeatingsTable'];
        $types=['file','faceWfile','face'];
        if(!$request->has(['print_date','actions','types','PrintidsTextArea'])){return view('errors.layout',['error_number'=>500,'file'=>__FILE__,'error_message'=>__LINE__]);}
        
        if(!$request->filled(['print_date','actions','types','PrintidsTextArea'])){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__]);}
        
        if(!\AmerHelper::isJson($request->input('PrintidsTextArea'))){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__]);}
        if(!is_array($request->input('actions'))){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__]);}
        foreach($request->input('actions') as $a=>$b){
            if(!in_array($b,$actions)){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__."<br>".$b]);}
        }
        if(!in_array($request->input('types'),$types)){return view('errors.layout',['error_number'=>500,'error_message'=>__LINE__]);}
        $ids=json_decode($request->input('PrintidsTextArea'),true);
        $data->section='file';
        $data->ids=$ids;
        $data->type=$request->input('types');
        $data->actions=$request->input('actions');
        $data->printDate=$request->input('print_date');
        return view("Employment::admin.searchform.printData",['data'=>$data]);
    }
}