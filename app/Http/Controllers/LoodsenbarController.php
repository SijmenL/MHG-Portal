<?php

namespace App\Http\Controllers;

use App\Models\Loodsenbar_categories as loodsenbar_categories; 
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoodsenbarController extends Controller
{

    public function viewHome()
    {
        $user = Auth::user();
        $categories = loodsenbar_categories::all();
        
        return view('speltakken.loodsen.loodsenbar.home', ['categories' => $categories]);
    }

    public function viewMenageProducts()
    {
        $user = Auth::user();
        $categories = loodsenbar_categories::all();
        
        return view('speltakken.loodsen.loodsenbar.menageProducts', ['categories' => $categories]);
    }


    public function viewAddProduct()
    {
        $user = Auth::user();
        
        return view('speltakken.loodsen.loodsenbar.addProduct');
    }

    public function viewAddCategory()
    {
        $user = Auth::user();
        
        return view('speltakken.loodsen.loodsenbar.addCategory');
    }


    public function addCategory(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category = new loodsenbar_categories();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->c_user_id = $user->id;
        $category->u_user_id = $user->id;
        $category->save();

        return redirect()->route('loodsenbar.menage.products')->with('message', 'Categorie toegevoegd');
    }
}