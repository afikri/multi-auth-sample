## Steps for creating multi authentication 
### 1. Add new row "is_admin" in users table and model
````php
public function up(){
    Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_admin')->nullable(); 
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
}
````
and in **User** model
````php
protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];
````
then do **migrate**, after that do not forget to issue make:auth command for scaffolding authentication <br/>
### 2.Create admin middleware
Run command `php artisan make:middleware Admin` then head to **Admin** class in the Middleware folder. Modify the _handle function_ so it looks like 
````php
public function handle($request, Closure $next){
        if(auth()->user()->is_admin == 1){
            return $next($request);
        }
        return redirect('home')->with('error','You do not have admin access');
}
````
and in the **Kernel** file add the line as below
````php
 protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'admin' => \App\Http\Middleware\Admin::class,

    ];
````
### 3. Create route
In the routes folder, open the web file and add
````php
Route::get('/admin/home', 'HomeController@admin')->name('admin.home')->middleware('admin');
````

### 4. Modify controller function
Add the admin function at the very end of the block, it looks like as
````php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function admin()
    {
        return view('admin');
    }
}

````

### 5. Create blade for normal user and admin
Inside the views folder, add files
1. Normal user or home.blade.php
````php 
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in as Normal User!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

````
2. Admin 
````php 
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in as Administrator!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

````
Those file are almost similar, just differ line normal user and admin  
### 6. Update login controller
Modify LoginController by add extra **login function**,  so it looks like as
````php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){   
        $input = $request->all();
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            if (auth()->user()->is_admin == 1) {
                return redirect()->route('admin.home');
            }else{
                return redirect()->route('home');
            }
        }else{
            return redirect()->route('login')
                ->with('error','Email-Address And Password Are Wrong.');
        }
    }
          
}

````
### 7. Create seeder
To create user seeder, issue `php artisan make:seeder UsersTableSeeder` command, and populate data in the **run function**
````php
<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        DB::table('users')->insert([
			[
			'name'=>'alex',
            'email'=>'alex@jung.de',
            'is_admin' => '1',
			'password'=>bcrypt('alex@jung.de'),
            'remember_token'=> str_random(25),
            'created_at'=> date(now()),
            'updated_at'=>date(now())
            ],
            [
                'name'=>'chloe',
                'email'=> 'chloe@gmx.de',
                'is_admin' => '0',
                'password'=>bcrypt('chloe@gmx.de'),
                'remember_token'=> str_random(25),
                'created_at'=>date(now()),
                'updated_at'=>date(now())
            ]
		
		]);
    }
}

````
Run the app by `php artisan serve` and type `http://localhost:8000/` to see the result.

