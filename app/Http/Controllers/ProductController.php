<?php

namespace App\Http\Controllers;

use App\Image;
use App\Product;
use App\Tag;
use App\Review;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ProductController extends Controller
{
    public function render($id)
    {
        $product = Product::getByID($id);
        if ($product == null) {
            return abort(404);
        }
        $feedback = Review::getByProductID($id);
        $reviews = $feedback == null ? [] : $feedback->reviews;
        $score = $feedback == null ? 0 : $feedback->score;
        return view('pages.product', ['id'=>$id,
            'img' => $product->img, 'description' =>  $product->description,
            'price' => $product->price, 'score' => $score, 'name' => $product->name,
            'related' => [['id' => 1, 'img' => 'img/supreme_vase.jpg', 'name' => 'Supreme Bonsai Pot', 'price' => '40€'], ['id' => 1, 'img' => 'img/gloves_tool.jpg', 'name' => 'Blue Garden Gloves', 'price' => '9€'], ['id' => 1, 'img' => 'img/pondlilies_outdoor.jpg', 'name' => 'Pond White Lilies', 'price' => '40€']],
            'reviews' => $reviews
        ]);
    }

    public function add()
    {
        if (User::checkUser() != User::$MANAGER) 
            return back();
        return view('pages.product-form');
    }

    public function create(Request $request)
    {
        $request->validate([
            'img' => ['required'], 'name' => ['required'],
            'price' => ['required', 'numeric', 'min:1'], 'description' => ['required'], 'stock' => ['required', 'numeric', 'min:1'],
            'tags' => ['required', 'string']
        ]);


        DB::beginTransaction();
        $product = new Product;
        $this->authorize('create', $product);
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
        foreach ($tags as $tag) {
            $db_tag = Tag::where('name', '=', $tag)->first();
            if ($db_tag == null) {
                $db_tag = new Tag;
                $db_tag->name = $tag;
                $db_tag->save();
            }

            $product->tags()->attach($db_tag->id);
        }


        DB::commit();
        return redirect('/product/' . $product->id);
    }

    public function addShoppingCart(Request $request ,$id)
    {
        $request->validate(['quantity' => ['required' , 'min:1']]);

        $role = User::checkUser();
        if ($role == User::$GUEST)
        {
            return response('Need to login', 401);
        }

        if ($role == User::$MANAGER)
            return response('Manager', 403);

        $user =  Auth::id();

        $quantity = $request->get('quantity');

        DB::insert('insert into shopping_cart(id_user,id_product,quantity) values (?, ?,?)', [$user, $id, $quantity]);

        return redirect('/product/' . $id);
    }

    public function buyNow($id)
    {
        if(User::validateCustomer())
            return redirect('/login');
        request()->session()->put('items', [$id => 1]);
        request()->session()->put('buynow', true);
        return redirect('checkout-details');
    }

    public function delete($id)
    {
        $product = Product::find($id);
        if(!$product) {
            return response()->json(['message' => 'The product does not exist.'], 404);
        }

        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'The product was deleted succesfully.'], 200);
    }

    
    public function buy()
    {
        $user = Auth::id();
        $cart = Product::getShoppingCartIds($user);

        $array_items = [];
        foreach($cart as $value){
            $array_items[$value->id] = $value->qty;
        }
      
        request()->session()->put('items', $array_items);

        if(count(request()->session()->get('items', [])) == 0){
            return response('No products in cart!', 400);
        }

        return redirect('checkout-details');
    }
}
