<?php

namespace App\Http\Controllers;

use App\Image;
use App\Product;
use App\Tag;
use App\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ProductController extends Controller{
    public function render($id){
        $product = Product::getByID($id);
        if($product == null) {
            return response('No such product was found',404);
        }
        $feedback = Review::getByProductID($id);
        $reviews = $feedback == null? [] : $feedback->reviews;
        $score = $feedback == null? 0 : $feedback->score;
        return view('pages.product', ['img' => $product->img, 'description' =>  $product->description, 
                                        'price' => $product->price, 'score' => $score, 'name' => $product->name, 
                                        'related' => [['id' => 1, 'img' => 'img/supreme_vase.jpg', 'name' => 'Supreme Bonsai Pot', 'price' => '40€'], ['id' => 1, 'img' => 'img/gloves_tool.jpg', 'name' => 'Blue Garden Gloves', 'price' => '9€'], ['id' => 1, 'img' => 'img/pondlilies_outdoor.jpg', 'name' => 'Pond White Lilies', 'price' => '40€']], 
                                        'reviews' => $reviews]);
    }

    public function add()
    {
        return view('pages.product-form');
    }

    public function create(Request $request)
    {

        $request->validate(['img' => ['required'], 'name' => ['required'],
             'price' => ['required', 'numeric', 'min:1'], 'description' => ['required'], 'stock' => ['required', 'numeric', 'min:1'],
             'tags' => ['required', 'string']]);

        $product = new Product;
        $product->stock = $request->input('stock');
        $product->price = $request->input('price');
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->views = 0;
        $product->save();

        $file = Input::file('img');
        $path = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move('img/', $path);


        $img = new Image;
        $img->img_name = $path;
        $img->description = $request->input('name');
        $img->save();
        
        $product->images()->attach($img->id);

        $tags = preg_split('/,/', $request->input('tags'));
        foreach ($tags as $tag){
            $db_tag = Tag::where('name', '=', $tag)->first();
            if($db_tag == null){
                $db_tag = new Tag;
                $db_tag->name = $tag;
                $db_tag->save();
            }

            $product->tags()->attach($db_tag->id);
        }

        return redirect('/home');
    }

    public function buyNow(Request $request)
    {
        $request->session()->put('items', [1]); //TODO: Add the actual id of the product
        return redirect('checkout-details');
    }
}
