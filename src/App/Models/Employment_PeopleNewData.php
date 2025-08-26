<?php

namespace Amerhendy\Employment\App\Models;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Amerhendy\Amer\App\Models\Traits\AmerTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Casts\Attribute;

use \Amerhendy\Amer\App\Models\Cities;
use \Amerhendy\Amer\App\Models\governorates;
class Employment_PeopleNewData extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = "employment_people_new_data";
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable=[
        'id', 'people_id','job_id','fname','sname','tname','lname','live_place', 'live_address','born_place',
        'connect_landline','connect_mobile','connect_email','health_id','marital_status_id','arm_id','ama_id',
        'tamin','khebra','education_id','education_year','stage_id','result_id','message','driver_degree','driver_start','driver_end','file_name',
        'nid','sex','birth_date','age_years','age_months','age_days'
    ];
    protected $dates = ['deleted_at'];
    public function Employment_People()
    {
        return $this->hasOne(Employment_People::class,'id', 'people_id');
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
        return $this->hasOne(governorates::class,'id', 'born_gov');
    }


public function BornCities()
    {
        return $this->hasOne(Cities::class,'id', 'born_city');
    }


public function livegovernorates()
    {
        return $this->hasOne(governorates::class,'id', 'live_gov');
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
    function Employment_Grievance()
    {
        return $this->hasOne(Employment_Grievance::class,'people_id','id')->withTrashed();
    }
public function LiveCities()
    {
        return $this->hasOne(Cities::class,'id', 'live_city');
    }


public function Employment_Health()
    {
        return $this->hasOne(Employment_Health::class,'id', 'health_id');
    }


public function Employment_Maritalstatus()
    {
        return $this->hasOne(Employment_Maritalstatus::class,'id', 'marital_status_id');
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


    public function employment_status()
    {
        return $this->hasOne(employment_status::class,'id', 'result_id');
    }
public function Employment_Drivers()
    {
        return $this->hasOne(Employment_Drivers::class,'id', 'driver_degree');
    }

    public function Fullname(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                return implode(' ',[$attributes['fname'],$attributes['sname'],$attributes['tname'],$attributes['lname']]);
            }
        );
    }
    public function livePlaceFormatted(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $livecity=Cities::where('id',$attributes['live_place'])->get()->first();
                if (!$livecity) return null;
                $livegov=$livecity->Governorates;
                return implode(' - ',[$livegov->name,$livecity->name,$attributes['live_address']]);
            }
        );
    }
    public function BornPlaceFormatted(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $borncity=Cities::where('id',$attributes['born_place'])->get()->first();
                if (!$borncity) return null;
                $borngov=$borncity->Governorates;
                
                return implode(' - ',[$borngov->name,$borncity->name]);
            }
        );
    }
    public function Connection(): Attribute
    {
        return Attribute::make(
            get:function (mixed $value, array $attributes){
                $data=new \stdClass;
                $data->Landline=$attributes['connect_landline'];
                $data->Mobile=$attributes['connect_mobile'];
                $data->Email=$attributes['connect_email'];
                return $data;
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
