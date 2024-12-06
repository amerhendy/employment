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
class Employment_StartAnnonces extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,HasUuids;
    protected $table = 'employment_startannonces';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['number', 'year', 'description', 'employment_qualifications','stage_id', "slug", 'status','employment_stages','governorates'];
    protected $dates = ['deleted_at'];
    public static $list=[];
    public static $fileds=[];

    //Employment_StartAnnonces
    public function employment_startannonces_qualifications()
    {
        return $this->belongsToMany(employment_qualifications::class, 'employment_startannonces','id','id');
    }
    public function employment_qualifications()

    {
        return $this->belongsToMany(employment_qualifications::class, 'employment_startannonces_qualifications','annonce_id','qualification_id')->withTrashed();

    }

//employment_startannonces
    public function employment_startannonces_governorates()
    {
        return $this->belongsToMany(\Amerhendy\Amer\App\Models\governorates::class, 'employment_startannonces','id','id');
    }
    public function governorate()

    {
        return $this->belongsToMany(\Amerhendy\Amer\App\Models\governorates::class, 'employment_startannonces_governorates','annonce_id','gov_id')->withTrashed();

    }
    public function employment_stages()
    {
        return $this->hasOne(employment_stages::class,'id', 'stage_id');
    }
    public function Employment_Committee()
    {
        return $this->hasOne(Employment_Committee::class,'id', 'stage_id');
    }
    public function Employment_Jobs()
    {
        return $this->hasMany(Employment_Jobs::class, 'annonce_id','id');
    }
}
