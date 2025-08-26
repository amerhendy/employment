<?php

namespace Amerhendy\Employment\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class Employment_JobsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow creates if the user is logged in
        return amer_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'Annonce_id'=>'required|exists:Employment_StartAnnonces,id',
            'Cities'=>'required|exists:Cities,id',
            'Group_id'=>'required|exists:Mosama_Groups,id',
            'JobTitle_id'=>'required|exists:Mosama_JobTitles,id',
            'Job_id'=>'required|exists:Mosama_JobNames,id',
            'Code'=>'required',
            'Employment_Ama'=>'required|exists:Employment_Ama,id',
            'Employment_Army'=>'required|exists:Employment_Army,id',
            'Employment_MaritalStatus'=>'required|exists:Employment_MaritalStatus,id',
            'Employment_Health'=>'required|exists:Employment_Health,id',
            'Mosama_Educations'=>'required|exists:Mosama_Educations,id',
            'Employment_Qualifications'=>'required|exists:Employment_Qualifications,id',
            'Employment_IncludedFiles'=>'required|exists:Employment_IncludedFiles,id',
            'Employment_Instructions'=>'required|exists:Employment_Instructions,id',
            'Count'=>'required|numeric',
            'Age'=>'required|numeric',
            'AgeIn'=>'required|date',
            'Driver'=>'required',
            'Employment_Drivers'=>'requiredIf:Driver,0|exists:Employment_Drivers,id',
            'Status'=>'required',
        ];
    }

    // OPTIONAL OVERRIDE
    // public function forbiddenResponse()
    // {
        // Optionally, send a custom response on authorize failure
        // (default is to just redirect to initial page with errors)
        //
        // Can return a response, a view, a redirect, or whatever else
        // return Response::make('Permission denied foo!', 403);
    // }

    // OPTIONAL OVERRIDE
    // public function response()
    // {
        // If you want to customize what happens on a failed validation,
        // override this method.
        // See what it does natively here:
        // https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/Http/FormRequest.php
    // }
}
