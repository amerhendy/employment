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
public function Employment_Seatings(){
    return $this->hasMany(Employment_Seatings::class,"People_id");
}
}
