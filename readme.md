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

### 6. Update login controller

### 7. Create seeder

Run the app by `php artisan serve` and type `http://localhost:8000/` to see the result.

