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
class Employment_Stages extends Model
{
    use HasFactory,SoftDeletes,AmerTrait,HasRoles,HasApiTokens,Sluggable, SluggableScopeHelpers;
    protected $table = 'Employment_Stages';
    protected $primaryKey = 'id';
    protected $fillable=['Text','Days','Page','Front'];
    public $incrementing = true;
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    public static $list=[];
    
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => [],
            ],
        ];
    }
    public static function addpages($ODS=null){
        $staticpages=Employment_StaticPages::all();
        $dinamicpages=Employment_DinamicPages::all();
        $final=[];
        foreach($staticpages as $k=>$v){
            $final['S:'.$v->id]=trans('JOBLANG::Employment_StaticPages.Employment_StaticPages').':'.$v->Name;
        }
        foreach($dinamicpages as $k=>$v){
            $final['D:'.$v->id]=trans('JOBLANG::Employment_DinamicPages.Employment_DinamicPages').':'.$v->Text;
        }
        if(is_null($ODS)){return $final;}else{
            return $ODS;
        }
    }
    public function getSlugWithLink($Amer) {
        $page_type=self::check_page_type($this->Page);
        if($page_type == ''){return '';}
        $a='';
        $a.='<a href="';
        $a.=url("admin/".$page_type[0],"show");
        $a.='" target="_blank">';
        $a.=$page_type[1];
        $a.='</a>';
        print $a;
    }
    public static function check_page_type($page)
    {
        $routename=config('Amer.employment.routeName_prefix','Employment');
        if (substr($page, 0, 2) === 'S:') {
            $m=substr($page, 2, strlen ($page));
            $pages=Employment_StaticPages::all()->where('id','=',$m);
            if (count($pages) == '0') {
                return '';
            }
            foreach($pages as $k=>$v){
                $name=$v->Name;
            }
            return [Route($routename.'.Employment_StaticPages.show',$m),$name];
        }
        if (substr($page, 0, 2) === 'D:') {
            $m=substr($page, 2, strlen ($page));

            $pages=Employment_DinamicPages::all()->where('id','=',$m);
            if(count($pages) == 0){return'';}
            foreach($pages as $k=>$v){
                $name=$v->Name;
            }
            return [Route($routename.'.Employment_DinamicPages.show',$m),$name];
        }
}
}