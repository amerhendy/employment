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
class Employment_Seatings extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens;
    protected $table = 'Employment_Seatings';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;
    //public $cast=['Seatings'=>'json'];

    protected $fillable = ['Stage_id','Seatings'];
    protected $dates = ['deleted_at'];
        public function Employment_Stages()
        {
            return $this->belongsTo(Employment_Stages::class, 'Stage_id');
        }
        public function setSeatings(){
            return json_decode($this->Seatings,true);
        }
        protected function Seatings(): Attribute

        {
    
            return Attribute::make(
    
                get: fn ($value) => json_decode($value, true),
    
                set: fn ($value) => json_encode($value),
    
            );
    
        } 
        public function Employment_Committee(){
            return $this->belongsTo(Employment_Committee::class,'Committee_number','id');
        }
    }
