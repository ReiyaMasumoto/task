<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     *
     * 
     * @return view
     */
    public function show()
    {
        $products = Product::all();
        // dd($products);
        $company = Company::find(1)->products()->where('company_id')->first();

        return view('shop.list',
        ['products'=> $products],['company' => $company]);
    }

    public function exeSearch(ProductRequest $request)
    {        
        
        $keyword = $request->input('keyword');
        $companyId = $request->input('company_id');
        $query = Product::query();
        // dd($request->company_id);
        
        if(!empty($keyword))
        {
            $query->where('product_name', 'LIKE',"%{$request->keyword}%");
        }
        // dd($keyword);
        if(!empty($companyId))
        {
            $query->where('company_id', $companyId);
        }

        $products = $query->get();

        return view('shop.list', compact('products', 'keyword', 'companyId'));
    }


    public function showDetail($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            \Session::flash('err_msg', ' データがありません');
            return redirect(route('products'));
        }
        return view('shop.detail',
        ['product'=> $product]);
    }

    public function showCreate() {
        $product = Product::all();
        $companies = Company::all();
        return view('shop.form',['product' => $product],['companies' => $companies]);
    }

    public function exeStore(ProductRequest $request) {
        \DB::beginTransaction();
           
            try{
                $image = $request->file('image');
                if(isset($image))
                {
                    $path = $image->store('images','public');
                    // dd($path);
                    if($path){
                        Product::create([
                            'product_name' => $request->product_name,
                            'company_id' => $request->company_id,
                            'price' => $request->price,
                            'stock' => $request->stock,
                            'comment' => $request->comment,
                            'image' => $path,
                        ]);
                    }else{
                        Product::create([
                            'product_name' => $request->product_name,
                            'company_id' => $request->company_id,
                            'price' => $request->price,
                            'stock' => $request->stock,
                            'comment' => $request->comment,
                        ]);
                    }
                }
                
            \DB::commit();
            }catch(\Exception $e){
                DB::rollback();
            }
            
    
            \Session::flash('err_msg', ' 商品を登録しました');

        
            return redirect(route('products'));
    }

    public function showEdit($id)
    {
        $product = Product::find($id);
        $companies = Company::all();
        
        if (is_null($product)) {
            \Session::flash('err_msg', ' データがありません');
            return redirect(route('products'));
        }
        
        return view('shop.edit',
        ['product'=> $product],['companies' => $companies]);
    }

    public function exeUpdate(ProductRequest $request) {
        
        $inputs = $request->all();
        $product = Product::all();
        $img = Product::find($product->image);
            if($image){
                \Storage::disk('public')->delete($img);
                $path = $image->store('images', 'public');
            }else{
                $image = null;
            }

            $products = Product::find($inputs['id']);
            if(!isset($image)){
                $products->fill([
                    'product_name' => $inputs['product_name'],
                    'comment' => $inputs['comment'],
                    'price' => $inputs['price'],
                    'stock' => $inputs['stock'],
                    'company_id' => $inputs['company_id'],
                ]);
            }else{
                fill([
                'product_name' => $inputs['product_name'],
                'comment' => $inputs['comment'],
                'price' => $inputs['price'],
                'stock' => $inputs['stock'],
                'company_id' => $inputs['company_id'],
                'image' => $image,
            ]);
        }

            $products->save();

        \DB::beginTransaction();
            try {
            // 商品を登録
            $products = Product::find($inputs['id']);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            abort(500);
        }
        \Session::flash('err_msg', ' 商品を更新しました');
            return redirect(route('products'));
    }

    public function exeDelete($id)
    {
        if(empty($id)){
            \Session::flash('err_msg', ' データがありません');
            return redirect(route('products'));
        }

        try {
            $product = Product::destroy($id);
        } catch(\Throwable $e){
            abort(500);
        }

        \Session::flash('err_msg', ' 削除しました');
        return redirect(route('products'));
    }
}
