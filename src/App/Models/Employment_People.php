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
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use \Amerhendy\Amer\App\Models\Cities;
use \Amerhendy\Amer\App\Models\Governorates;
class Employment_People extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,Sluggable, SluggableScopeHelpers;
    protected $table = 'Employment_People';
    protected $primaryKey = 'id';
    protected $fillable=[
                        'Annonce_id','Job_id','NID','Sex','Fname','Sname','Tname','Lname','LiveGov','LiveCity','LiveAddress','BornGov','BornCity','BirthDate',
                        'AgeYears','AgeMonths','AgeDays','ConnectLandline','ConnectMobile','ConnectEmail','Health_id','MaritalStatus_id','Arm_id','Ama_id',
                        'Tamin','Khebra','Education_id','EducationYear','Stage_id','Result','Message','DriverDegree','DriverStart','DriverEnd','FileName'
                    ];
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $cast=['Message'];
    public static $list=[];
    public static $fileds=[];
    public function sluggable(): array
        {
            return [
                'slug' => [
                    'source' => [],
                ],
            ];
        }
        public function Employment_StartAnnonces()
        {
            return $this->hasOne(Employment_StartAnnonces::class,'id', 'Annonce_id');
        }

    public function Employment_Stages()
        {
            return $this->hasOne(Employment_Stages::class,'id', 'Stage_id');
        }


    public function Employment_Job()
        {
            return $this->hasOne(Employment_Jobs::class,'id', 'Job_id');
        }


    public function BornGovernorates()
        {
            return $this->hasOne(Governorates::class,'id', 'BornGov');
        }


    public function BornCities()
        {
            return $this->hasOne(Cities::class,'id', 'BornCity');
        }


    public function LiveGovernorates()
        {
            return $this->hasOne(Governorates::class,'id', 'LiveGov');
        }

        function Employment_PeopleNewStage(){
            return $this->hasMany(Employment_PeopleNewStage::class,'People_id','id')->withTrashed();
        }
        function Employment_PeopleNewData(){
            return $this->hasOne(Employment_PeopleNewData::class,'People_id','id')->withTrashed();
        }
        function Employment_PeopleDegrees(){
            return $this->hasOne(Employment_PeopleDegrees::class,'People_id','id')->withTrashed();
        }
        function Employment_Grievance():HasMany
        {
            return $this->hasMany(Employment_Grievance::class,'People_id','id');
        }
    public function LiveCities()
        {
            return $this->hasOne(Cities::class,'id', 'LiveCity');
        }


    public function Employment_Health()
        {
            return $this->hasOne(Employment_Health::class,'id', 'Health_id');
        }


    public function Employment_MaritalStatus()
        {
            return $this->hasOne(Employment_MaritalStatus::class,'id', 'MaritalStatus_id');
        }


    public function Employment_Army()
        {
            return $this->hasOne(Employment_Army::class,'id', 'Arm_id');
        }


    public function Employment_Ama()
        {
            return $this->hasOne(Employment_Ama::class,'id', 'Ama_id');
        }


    public function Employment_Education()
        {
            return $this->hasOne(\Amerhendy\Employers\App\Models\Mosama_Educations::class,'id', 'Education_id');
        }


        public function Employment_Drivers()
        {
            return $this->hasOne(Employment_Drivers::class,'id', 'DriverDegree');
        }
        public function Employment_Status()
        {
            return $this->hasOne(Employment_Status::class,'id', 'Result');
        }
    public function Employment_Seatings(){
        return $this->hasMany(Employment_Seatings::class,"People_id");
    }
    public function FullName(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                return implode(' ',[$attributes['Fname'],$attributes['Sname'],$attributes['Tname'],$attributes['Lname']]);
            }
        );
    }
    public function LivePlace(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $livegov=Governorates::where('id',$attributes['LiveGov'])->get()->first();
                $LiveCity=Cities::where('id',$attributes['LiveCity'])->get()->first();
                return implode(' - ',[$livegov->Name,$LiveCity->Name,$attributes['LiveAddress']]);
            }
        );
    }
    public function BornPlace(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $BornGov=Governorates::where('id',$attributes['BornGov'])->get()->first();
                $BornCity=Cities::where('id',$attributes['BornCity'])->get()->first();
                return implode(' - ',[$BornGov->Name,$BornCity->Name]);
            }
        );
    }
    public function Connection(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $data=new \stdClass;
                $data->Landline=$attributes['ConnectLandline'];
                $data->Mobile=$attributes['ConnectMobile'];
                $data->Email=$attributes['ConnectEmail'];
                return $data;
            }
        );
    }
    public function khebraToStr(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $khebra=$attributes['Khebra'];
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
    public function HtmlMessage(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $data=$attributes['Message'];
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
