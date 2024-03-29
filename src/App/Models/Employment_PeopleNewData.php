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
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

use \Amerhendy\Amer\App\Models\Cities;
use \Amerhendy\Amer\App\Models\Governorates;
class Employment_PeopleNewData extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,Sluggable, SluggableScopeHelpers;
    protected $table = 'Employment_PeopleNewData';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable=[
        'People_id','Job_id','Fname','Sname','Tname','Lname','LiveGov','LiveCity','LiveAddress','BornGov','BornCity','BirthDate',
        'ConnectLandline','ConnectMobile','ConnectEmail','Health_id','MaritalStatus_id','Arm_id','Ama_id',
        'Tamin','Khebra','Education_id','EducationYear','Stage_id','Result','Message','DriverDegree','DriverStart','DriverEnd','FileName'
    ];
    protected $dates = ['deleted_at'];
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
    public function Employment_People()
    {
        return $this->hasOne(Employment_People::class,'id', 'People_id');
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
    function Employment_Grievance()
    {
        return $this->hasOne(Employment_Grievance::class,'People_id','id')->withTrashed();
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
}
