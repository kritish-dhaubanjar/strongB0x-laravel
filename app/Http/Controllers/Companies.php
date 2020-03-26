<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;


class Companies extends Controller
{
    public function index(){
        return Company::first();
        // return Storage::disk('public')->download("images/$company->logo");
    }

    public function update(Request $request){
        $request->validate([
            'name'=>'required',
            'phone_number'=>'required',
            'address'=>'required',
            'logo' => 'max:10000|mimes:jpeg,bmp,png,jpeg'
        ]);


        $company = Company::first();
        $company->name = $request->name;
        $company->phone_number = $request->phone_number;
        $company->email = $request->email;
        $company->address = $request->address;
        $company->tax_number = $request->tax_number;
        
        // return $request->file;  //undefined, null, File

        if($request->has('file') && $request->file !='undefined' && $request->file != 'null'){
            if(!is_null($company->logo)){
                Storage::disk('public')->delete("logo/$company->logo");
            }

            $basename = $request->file->getClientOriginalName();
            $extension =pathinfo($basename, PATHINFO_EXTENSION);
            $filename =  pathinfo($basename, PATHINFO_FILENAME);
            $logo = $filename.'_'.time().'.'.$extension;
            Storage::disk('public')->putFileAs("logo", $request->file, $logo);
            $company->logo = $logo;
        }
        else if ($request->has('file') && $request->file == 'null' ){
            if(!is_null($company->logo)){
                $company->logo = null;
                Storage::disk('public')->delete("logo/$company->logo");
            }
        }

        if($company->save()){
            return $company;
        };
    }
}
