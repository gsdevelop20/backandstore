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



