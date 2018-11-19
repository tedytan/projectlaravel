<?php

namespace App\Http\Controllers\Web;


use DB;
use Response;
use Storage;
use App\Models\Product;
use App\Models\ImagesProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('product','asc')->paginate(15);

        return view('admin.product.index', compact('products'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'product' => 'required|max:60',
            'price' => 'required|min:0',
            'stock' => 'required| min:0',
            'image.*' => 'required|mimes:jpg,jpeg,png'
        ]);

        DB::beginTransaction();
        try{

            $product = product::create([
                'product' => $request->product,
                'price' => $request->price,
                'stock' => $request->stock,
                'description' => $request->description,
            ]);

            if( $request->hasFile('images') ){

                $array = [];

                foreach($request->images as $key => $value) {

                    $path = $value->store('products');

                    $image = [
                        'product_id' => $product->id,
                        'image' => $path,
                    ];

                    array_push( $array,$image);
                }

                ImagesProduct::insert($array);
            }
            
            DB::commit();
        }catch(\Exeption $e){

            DB::rollback();
            dd($e);
        }

        return redirect()->back();
    }

    public function show($id)
    {
        $product = Product::with('imageRelation')->where('id',$id)->first();

        // dd($product);
        return view('admin.product.detail',compact('product'));
    }

    public function update(Request $request)
    {
        $product = Product::where('id',$request->id)->first();

        $oldimagesProduct = ImagesProduct::where('product_id',$request->id)->get();

        DB::beginTransaction();
        try{

            $product->update([
                'product' => $request->product,
                'price' => $request->price,
                'stock' => $request->stock,
                'description' => $request->description,
            ]);

            if( $request->hasFile("images") ){
                // hapus data gambar lama
                if( count( $oldimagesProduct) >= 0 ){
                    foreach ($oldimagesProduct as $old) {
                        Storage::delete($old->image);
                    }

                    ImagesProduct::where('product_id',$request->id)->delete();
                }

                // insert data gambar baru
                $array = [];
                foreach($request->images as $key => $value) {
                    $path = $value->store('products');
                    $image = [
                        'product_id' => $request->id,
                        'image' => $path,
                    ];

                    array_push( $array,$image);
                }

                ImagesProduct::insert($array);

            }

            DB::commit();
        }catch(\Exeption $e){
            dd($e);
            DB::rollback();
        }

        return redirect()->back();
    }
}
