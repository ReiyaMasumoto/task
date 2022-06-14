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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * 商品一覧画面
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        // dd($products);
        $company = Company::find(1)->products()->where('company_id')->first();

        return view('home',
        ['products'=> $products],['company' => $company]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * 商品登録画面
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = Product::all();
        $companies = Company::all();
        return view('shop.form',['product' => $product],['companies' => $companies]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * 商品登録処理
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        \DB::beginTransaction();
           
            try{
                $image = $request->file('image');
                if(isset($image))
                {
                    $path = $image->store('images','public');
                    // dd($path);
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
                
                
            \DB::commit();
            }catch(\Exception $e){
                DB::rollback();
            }
            
    
            \Session::flash('err_msg', ' 商品を登録しました');

        
            return redirect(route('home'));
    }

    /**
     * Display the specified resource.
     *
     * 商品詳細画面
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (is_null($product)) {
            \Session::flash('err_msg', ' データがありません');
            return redirect(route('home'));
        }
        return view('shop.detail',
        ['product'=> $product]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * 商品編集画面
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $companies = Company::all();
        
        if (is_null($product)) {
            \Session::flash('err_msg', ' データがありません');
            return redirect(route('home'));
        }
        
        return view('shop.edit',
        ['product'=> $product],['companies' => $companies]);
    }

    /**
     * Update the specified resource in storage.
     *
     * 商品編集処理
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $inputs = $request->all();
        $img = Product::first('image');
        $image = $inputs['image'];
        // dd($image);
            if($image){
                \Storage::disk('public')->delete($img);
                $path = $image->store('images', 'public');
                // dd($path);
            }else{
                $image = null;
            }

            $product = Product::find($inputs['id']);
            if(!isset($image)){
                $product->fill([
                    'product_name' => $inputs['product_name'],
                    'comment' => $inputs['comment'],
                    'price' => $inputs['price'],
                    'stock' => $inputs['stock'],
                    'company_id' => $inputs['company_id'],
                ]);
            }else{
                $product->fill([
                'product_name' => $inputs['product_name'],
                'comment' => $inputs['comment'],
                'price' => $inputs['price'],
                'stock' => $inputs['stock'],
                'company_id' => $inputs['company_id'],
                'image' => $path,
            ]);
        }

            $product->save();

        \DB::beginTransaction();
            try {
            // 商品を登録
            $product = Product::find($inputs['id']);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            abort(500);
        }
        \Session::flash('err_msg', ' 商品を更新しました');
            return redirect(route('home'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * 削除処理
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(empty($id)){
            \Session::flash('err_msg', ' データがありません');
            return redirect(route('home'));
        }

        try {
            $product = Product::destroy($id);
        } catch(\Throwable $e){
            abort(500);
        }

        \Session::flash('err_msg', ' 削除しました');
        return redirect(route('home'));
    }

    public function search(Request $request)
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
}
