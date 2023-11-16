<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Users;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/testef',function (Request $request){
    return 'ok';

});
Route::get('/users/{id}', function (string $id) {
    print "user";
    return 'User '.$id;
});

Route::get('allProducts', function (Request $request) {
   $Product = new App\Models\Products();
   /*
   $user->firstName = 'gabriel';
   $user->lastName = 'santos';
   $user->email = 'gabriel@gmail.com';
   $user->password = '36252245';
   $user->registrationDate='20/08/2022';
   $user->save();
   */
    $produts = $Product::all();
    $data = [];

    foreach ($produts as $key => $value){
        $value->url = asset("storage/productImg/$value->url");
        $data[$key] = $value;
    }

    return $data;
});

Route::post('verifylogin', function (Request $request) {
    $user = new App\Models\Users();

    $email = $request->get('email');
    $password = $request->get('password');

    $sql = "select userId, firstName from Users where email = '$email' and password = '$password'";
    if ($usersId = DB::select($sql)){
        $id = '';
        foreach ( $usersId as $key => $value){
            $id = $value;
        }
        return $id;
    }
    return ['error' => 'error'] ;
});

Route::post('userRegister', function (Request $request) {
    $data = [
        'firstname' => $request->get('firstname'),
        'lastName' => $request->get('lastname'),
        'email' => $request->get('email'),
        'password' => $request->get('password'),
        'address' => $request->get('address')
    ];

    $user = new Users();
    if($user->setUser($data)){
        return ['success' => true];
    }

    return ['success' => false];
});

Route::post('saveproduct', function (Request $request) {

    $data = [
        'sellerid' => $request->get('sellerid'),
        'productname' => $request->get('productname'),
        'description' => $request->get('description'),
        'price' => $request->get('price'),
        'stockquantity' => $request->get('stockquantity'),
        'category' => $request->get('category'),
        'productimg' => $request->file('productimg'),
    ];

    //return ['success' => $data['category']];

    $product = new \App\Models\Products();

    if($product->setProduct($data)){
        return ['success' => true];
    }

    return ['success' => false];

});

Route::get('/product/{id}', function (string $id) {
    $product = [];

    if(isset($id)){
        $product = \App\Models\Products::get_product_by_id($id);
    }

    $product->url = asset("storage/productImg/".$product->url);

    return [$product];
});

Route::post('saveComment', function (Request $request) {

    $data = [
        'userid' => $request->get('userid'),
        'productid' => $request->get('productid'),
        'comment' => $request->get('comment'),
    ];

    //return ['success' => $data['category']];

    $reviews = new \App\Models\Reviews();

    if($reviews->setComent($data)){
        return ['success' => true];
    }

    return ['success' => false];
});

Route::get('/getAllComment/{id}', function (string $id) {
    $comment =  \App\Models\Reviews::get_reviews_by_productid($id);
    $data = [];

    foreach ( $comment as $key => $value ) {
        $user = \App\Models\Users::get_user_by_id($value->UserID);

        $value->username = "$user->firstName $user->lastName";
        $data[$key] = $value;
    }

    return $data;
});

Route::post('/DeleteComment', function (Request $request) {
    $reviewid = $request->get('commentid');

    $review = new \App\Models\Reviews();

    if($review->deleteReview($reviewid)) {
        return ['success' => true];
    }

    return ['success' => false];
});

Route::post('/editComment', function (Request $request) {
    $reviewid = $request->get('commentid');
    $comment = $request->get('newcomment');

    $review = new \App\Models\Reviews();

    if($review->editReview($reviewid, $comment)) {
        return ['success' => true];
    }

    return ['success' => false];
});

Route::get('/getUserProduct/{id}', function (string $id) {
    $Product = new \App\Models\Products();

    $produts = $Product->get_product_by_userid($id);

    $data = [];

    foreach ($produts as $key => $value){
        $value->url = asset("storage/productImg/$value->url");
        $data[$key] = $value;
    }

    return $data;
});

Route::post('editproduct', function (Request $request) {

    $data = [
        'productid' => $request->get('productid'),
        'productname' => $request->get('productname'),
        'description' => $request->get('description'),
        'price' => $request->get('price'),
        'stockquantity' => $request->get('stockquantity'),
        'category' => $request->get('category'),
        'productimg' => $request->file('productimg'),
    ];

    $product = new \App\Models\Products();

    if($product->editProduct($data)){
        return ['success' => true];
    }

    return ['success' => false];
});

Route::post('/DeleteProduct', function (Request $request) {
    $prductid = $request->get('productid');

    $review = new \App\Models\Products();

    if($review->deleteProductById($prductid)) {
        return ['success' => true];
    }

    return ['success' => false];
});

Route::get('/getUser/{id}', function (string $id) {
    $user = \App\Models\Users::get_user_by_id($id);
    $userinfo = \App\Models\UserInfor::get_user_info_by_user_id($id);

    $user = array_merge((array)$user,(array)$userinfo);


    return [$user];
});

Route::post('/createOrder', function (Request $request) {
    $data = [
        'userid' => $request->get('userid'),
        'email' => $request->get('email'),
        'productid' => $request->get('productid'),
        'quantity' => $request->get('quantity'),
        'subtotal' => $request->get('subtotal'),
        'stock' => $request->get('stock'),
    ];

   $order = new \App\Models\Order();
    if ($order->setOrder($data)) {
        if($paymentinfo = \App\Models\Order::pix_qrcode_generete($data['email'], $data['subtotal'])) {
            return [$paymentinfo];
        }
    }

    return false;
});

Route::get('/getOrder/{id}', function (string $id) {
    $ordes = \App\Models\Order::get_order_by_userid($id);

    $Product = new \App\Models\Products();

    foreach ($ordes as $key => $order){
        $produts = $Product->get_product_by_id($order->ProductID);
        $order->productname = $produts->ProductName;
        $order->productimg =  asset("storage/productImg/$produts->url");
        $o[$key] = $order;
    }

    return $o;
});

Route::post('/Deleteorder', function (Request $request) {
    $orderid = $request->get('orderid');
    $orderitemid = $request->get('orderitemid');

    $order = new \App\Models\Order();

    if($order->delete_order_by_id($orderid,$orderitemid)) {
        return ['success' => true];
    }

    return ['success' => false];
});




