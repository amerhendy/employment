<?php

namespace Amerhendy\Employment\App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Amerhendy\Amer\App\Models\Traits\AmerTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use \Amerhendy\Amer\App\Models\Cities;
use \Amerhendy\Amer\App\Models\governorates;
class Employment_People extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = "employment_people";
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';
    protected $fillable=[
                        'annonce_id','job_id','nid','sex','fname','sname','tname','lname','livegov','livecity','liveaddress','borngov','borncity','birthdate',
                        'ageyears','agemonths','agedays','connectlandline','connectmobile','connectemail','health_id','maritalstatus_id','arm_id','ama_id',
                        'tamin','khebra','education_id','educationyear','stage_id','result','message','driverdegree','driverstart','driverend','filename'
                    ];
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $cast=['id'=>'uuid'];
        public function employment_startannonces()
        {
            return $this->hasOne(Employment_StartAnnonces::class,'id', 'annonce_id');
        }

    public function employment_stages()
        {
            return $this->hasOne(employment_stages::class,'id', 'stage_id');
        }


    public function Employment_Job()
        {
            return $this->hasOne(Employment_Jobs::class,'id', 'job_id');
        }


    public function borngovernorates()
        {
            return $this->hasOne(governorates::class,'id', 'borngov');
        }


    public function BornCities()
        {
            return $this->hasOne(Cities::class,'id', 'borncity');
        }


    public function livegovernorates()
        {
            return $this->hasOne(governorates::class,'id', 'livegov');
        }

        function Employment_PeopleNewStage(){
            return $this->hasMany(Employment_PeopleNewStage::class,'people_id','id')->withTrashed();
        }
        function Employment_PeopleNewData(){
            return $this->hasOne(Employment_PeopleNewData::class,'people_id','id')->withTrashed();
        }
        function Employment_PeopleDegrees(){
            return $this->hasOne(Employment_PeopleDegrees::class,'people_id','id')->withTrashed();
        }
        function Employment_Grievance():HasMany
        {
            return $this->hasMany(Employment_Grievance::class,'people_id','id');
        }
    public function LiveCities()
        {
            return $this->hasOne(Cities::class,'id', 'livecity');
        }


    public function Employment_Health()
        {
            return $this->hasOne(Employment_Health::class,'id', 'health_id');
        }


    public function Employment_Maritalstatus()
        {
            return $this->hasOne(Employment_Maritalstatus::class,'id', 'maritalstatus_id');
        }


    public function Employment_Army()
        {
            return $this->hasOne(Employment_Army::class,'id', 'arm_id');
        }


    public function Employment_Ama()
        {
            return $this->hasOne(Employment_Ama::class,'id', 'ama_id');
        }


    public function Employment_Education()
        {
            return $this->hasOne(\Amerhendy\Employers\App\Models\Mosama_Educations::class,'id', 'education_id');
        }


        public function Employment_Drivers()
        {
            return $this->hasOne(Employment_Drivers::class,'id', 'driverdegree');
        }
        public function employment_status()
        {
            return $this->hasOne(employment_status::class,'id', 'result');
        }
    public function Employment_Seatings(){
        return $this->hasMany(Employment_Seatings::class,"people_id");
    }
    public function Fullname(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                return implode(' ',[$attributes['fname'],$attributes['sname'],$attributes['tname'],$attributes['lname']]);
            }
        );
    }
    public function Age(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $data=new \stdClass();
                $data->ageyears=$attributes['ageyears'];
                $data->agemonths=$attributes['agemonths'];
                $data->agedays=$attributes['agedays'];
                return $data;
            }
        );
    }
    public function LivePlace(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $livegov=governorates::where('id',$attributes['livegov'])->get()->first();
                $livecity=Cities::where('id',$attributes['livecity'])->get()->first();
                return implode(' - ',[$livegov->Name,$livecity->Name,$attributes['liveaddress']]);
            }
        );
    }
    public function BornPlace(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $borngov=governorates::where('id',$attributes['borngov'])->get()->first();
                $borncity=Cities::where('id',$attributes['borncity'])->get()->first();
                return implode(' - ',[$borngov->Name,$borncity->Name]);
            }
        );
    }
    public function Connection(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $data=new \stdClass;
                $data->Landline=$attributes['connectlandline'];
                $data->Mobile=$attributes['connectmobile'];
                $data->Email=$attributes['connectemail'];
                return $data;
            }
        );
    }
    public function khebraToStr(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $khebra=$attributes['khebra'];
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
                    $time=$khebra[$keys[0]];

                    if(isset($keys[1])){
                        $type=$khebra[$keys[1]];
                    }else{
                        $type=$keys[0];
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
                    }
                return $khebra;
            }
        );
    }
    public function Htmlmessage(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $data=$attributes['message'];
                if(is_null($data)){return null;}
                $re=[];
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
                return $re;
            }
        );
    }
}
