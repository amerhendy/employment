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
    protected $table = 'employment_start_annonces';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['number', 'year', 'description','stage_id', "slug", 'status'];
    protected $dates = ['deleted_at'];
    //$table->enum('status', ['publish', 'draft'])->default('draft')->nullable();

    //employment_start_annonces
    public function employment_start_annonce_qualification()
    {
        return $this->belongsToMany(employment_qualifications::class, 'employment_start_annonces','id','id');
    }
    public function employment_qualifications()
    {
        return $this->belongsToMany(Employment_Qualifications::class, 'employment_start_annonce_qualification','annonce_id','qualification_id')->withTrashed();

    }

//employment_start_annonces
    public function employment_start_annonce_gov()
    {
        return $this->belongsToMany(\Amerhendy\Amer\App\Models\Governorates::class, 'employment_start_annonces','id','id');
    }
    public function governorate()
    {
        return $this->belongsToMany(\Amerhendy\Amer\App\Models\Governorates::class,'employment_start_annonce_gov','annonce_id','governorate_id')->withTrashed();

    }
    public function Employment_Stages()
    {
        return $this->hasOne(Employment_Stages::class,'id', 'stage_id');
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
