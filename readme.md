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

