@extends('layouts.app')
@section('title', '商品一覧')
@section('content')
<div class="row">
  <div  class="col-md-8 col-md-offset-4">
    <form action="{{ route('search') }}" method="GET">
    @csrf  
      <div class="search_word">
        <input type="text" name="keyword">
      </div>
      <div class="search_box">
        <select data-toggle="select" name="company_id">
          <option value="">未選択</option>
          @foreach($products as $product)
            <option value="{{ $product->company->id }}">{{ $product->company->company_name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-primary">
          検索する
        </button>
      </div>
    </form>
  </div>
  <div class="col-md-10 col-md-offset-2">
      <h2>商品記事一覧</h2>
      @if (session('err_msg'))
        <p class="text-danger">
          {{ session('err_msg') }}
      @endif    
        </p>
      <table class="table table-striped">
          <tr>
              <th>id</th>
              <th>商品画像</th>
              <th>商品名</th>
              <th>価格</th>
              <th>在庫数</th>
              <th>メーカー名</th>
          </tr>
          @foreach($products as $product)
          <tr>
              <td>{{ $product->id }}</td>
              <td><img src="{{ asset('/storage/' .$product->image) }}" alt="{{ $product->image }}" width="200px" height="100px"></td>
              <td>{{ $product->product_name }}</td>
              <td>{{ $product->price }}</td>
              <td>{{ $product->stock }}</td>
              <td>{{ $product->company->company_name }}</td>
              <td><button type="button" class="btn btn-primary" 
              onclick="location.href='/task/public/product/{{ $product->id }}'">詳細</button></td>
              <form method="POST" action="{{ route('destroy', $product->id) }}" onSubmit="return checkDelete()">
              @csrf
              <td><button type="submit" class="btn btn-primary" 
              onclick=>削除</button></td>
              </form>
          </tr>
          @endforeach
      </table>
  </div>
</div>
<script>
function checkDelete(){
  if(window.confirm('削除してよろしいですか？')){
      return true;
  } else {
      return false;
  }
}
</script>
@endsection