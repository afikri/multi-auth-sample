## Steps for creating multi authentication 
1. Add new row "is_admin" in users table and model
````php
public function up()
    {
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
```
and in **User** model
````php
protected $fillable = [
        'name', 'email', 'password', 'is_admin'
    ];
````
    
