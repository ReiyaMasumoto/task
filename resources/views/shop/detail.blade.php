@extends('layouts.app')
@section('title', 'ブログ一覧')
@section('content')
<div class="row">
  <div class="col-md-10 col-md-offset-2">
      <h2>{{ $product->product_name }}</h2>
      <p><span class="mr-4">商品番号</span>{{ $product->id }}</p>
      <p><img src="{{ asset('/storage/' .$product->image) }}" alt="{{ $product->image }}" width="400px" height="250px"></p>
      <p>{{ $product->price }}<span class="m-4">円</span></p>
      <p><span class="mr-4">在庫</span>{{ $product->stock }}</p>
      <p><span class="mr-4">メーカー</span>{{ $product->company->company_name }}</p>
      <p><span class="mr-4">コメント</span>{{ $product->comment }}</p>
      <p><button type="button" class="btn btn-primary" 
              onclick="location.href='/task/public/product/edit/{{ $product->id }}'">編集</button></p>
      <p><button type="button" class="btn btn-primary" 
          onclick="location.href='/task/public/home'">戻る</button></p>
      @if (session('err_msg'))
        <p class="text-danger">
          {{ session('err_msg') }}
      @endif    
        </p>
      
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