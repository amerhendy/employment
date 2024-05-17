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
class Employment_StartAnnonces extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,Sluggable, SluggableScopeHelpers;
    protected $table = 'Employment_StartAnnonces';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['Number', 'Year', 'Description', 'Employment_Qualifications','Stage_id', "Slug", 'Status','Employment_Stages','Governorates'];
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];
public function sluggable(): array
    {
        return [
            'Slug' => [
                'source' => ['Governorates','Number', 'Year', 'Status','Employment_Stages'],
            ],
        ];
    }
    
    //Employment_StartAnnonces
    public function Employment_StartAnnonces_Qualifications()
    {
        return $this->belongsToMany(Employment_Qualifications::class, 'Employment_StartAnnonces','id','id');
    }
    public function Employment_Qualifications()

    {
        return $this->belongsToMany(Employment_Qualifications::class, 'Employment_StartAnnonces_Qualifications','Annonce_id','Qualification_id')->withTrashed();

    }

//Employment_StartAnnonces
    public function Employment_StartAnnonces_Governorates()
    {
        return $this->belongsToMany(\Amerhendy\Amer\App\Models\Governorates::class, 'Employment_StartAnnonces','id','id');
    }
    public function Governorates()

    {
        return $this->belongsToMany(\Amerhendy\Amer\App\Models\Governorates::class, 'Employment_StartAnnonces_Governorates','Annonce_id','Governorate_id')->withTrashed();

    }
    public function Employment_Stages()
    {
        return $this->hasOne(Employment_Stages::class,'id', 'Stage_id');
    }
    public function Employment_Committee()
    {
        return $this->hasOne(Employment_Committee::class,'id', 'Stage_id');
    }
    public function Employment_Jobs()
    {
        return $this->hasMany(Employment_Jobs::class, 'Annonce_id','id');
    }
}
