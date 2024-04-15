<?php

namespace App\Http\Controllers;

use App\Models\Loodsenbar_categories as loodsenbar_categories; 
use App\Models\Loodsenbar_products as loodsenbar_products;
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
        $products = loodsenbar_products::all();
        
        return view('speltakken.loodsen.loodsenbar.home', ['categories' => $categories, 'products' => $products]);
    }

    public function viewProductsOfCategory(Request $request)
    {
        $user = Auth::user();
        $category = loodsenbar_categories::find($request->id);
        if(!$category){
            return redirect()->route('loodsen.loodsenbar')->with('error', 'Categorie niet gevonden');
        }
        $products = loodsenbar_products::where('category_id', $request->id)->get();

        return view('speltakken.loodsen.loodsenbar.products', ['category' => $category, 'products' => $products]);
    }

    public function viewMenageProducts()
    {
        $user = Auth::user();
        $categories = loodsenbar_categories::all();
        $products = loodsenbar_products::all();
        
        return view('speltakken.loodsen.loodsenbar.menageProducts', ['categories' => $categories, 'products' => $products]);
    }


    public function viewAddProduct()
    {
        $user = Auth::user();
        $categories = loodsenbar_categories::all();

        return view('speltakken.loodsen.loodsenbar.addProduct', ['categories' => $categories]);
    }

    public function viewAddCategory()
    {
        $user = Auth::user();
        
        return view('speltakken.loodsen.loodsenbar.addCategory');
    }

    public function viewEditProduct(Request $request)
    {
        $user = Auth::user();
        $product = loodsenbar_products::find($request->id);
        $categories = loodsenbar_categories::all();

        return view('speltakken.loodsen.loodsenbar.editProduct', ['product' => $product, 'categories' => $categories]);
    }

    public function viewEditCategory(Request $request)
    {
        $user = Auth::user();
        $category = loodsenbar_categories::find($request->id);

        return view('speltakken.loodsen.loodsenbar.editCategory', ['category' => $category]);
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

    public function addProduct(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = new loodsenbar_products();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category;
        $product->c_user_id = $user->id;
        $product->u_user_id = $user->id;

        // $image = $request->file('image');
        // $imageName = time().'.'.$image->extension();
        // $image->move(public_path('images'), $imageName);
        // $product->image_route = $imageName;

        $product->save();

        return redirect()->route('loodsenbar.menage.products')->with('message', 'Product toegevoegd');
    }

    public function editProduct(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = loodsenbar_products::find($request->id);
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category;
        $product->u_user_id = $user->id;

        // $image = $request->file('image');
        // $imageName = time().'.'.$image->extension();
        // $image->move(public_path('images'), $imageName);
        // $product->image_route = $imageName;

        $product->save();

        return redirect()->route('loodsenbar.menage.products')->with('message', 'Product aangepast');
    }

    public function editCategory(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $category = loodsenbar_categories::find($request->id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->u_user_id = $user->id;
        $category->save();

        return redirect()->route('loodsenbar.menage.products')->with('message', 'Categorie aangepast');
    }

    public function deleteProduct(Request $request)
    {
        $user = Auth::user();
        $product = loodsenbar_products::find($request->id);
        $product->delete();

        return redirect()->route('loodsenbar.menage.products')->with('message', 'Product verwijderd');
    }

    public function deleteCategory(Request $request)
    {
        $user = Auth::user();
        // check if category has products
        $products = loodsenbar_products::where('category_id', $request->id)->get();
        if(count($products) > 0){
            return redirect()->route('loodsenbar.menage.products')->with('error', 'Categorie heeft nog producten!');
        }
        $category = loodsenbar_categories::find($request->id);
        $category->delete();

        return redirect()->route('loodsenbar.menage.products')->with('message', 'Categorie verwijderd');
    }
}