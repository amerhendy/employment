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
use Illuminate\Database\Eloquent\Casts\Attribute;
class Employment_Committee extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens;
    protected $table ="Employment_Committee";
    protected $guarded = ['id'];
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [];
    protected $dates = ['deleted_at'];
    protected $casts=[
        'Committee_Memebers'=>'array'
    ];
    public static $fileds=[];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    protected function Committee_Memebers(): Attribute
{
    return Attribute::make(
        get: fn (string $value) => json_decode($value, true),
        set: fn (array $value) => json_encode($value),
    );
}
    public static function remove_force($id){
        $data=self::withTrashed()->find($id);
            if(!$data){return 0;}
        return $data::forceDelete();
        return 1;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function Employment_StartAnnonces()
    {
        return $this->belongsTo(Employment_StartAnnonces::class, 'Annonce_id','id');
    }
}
