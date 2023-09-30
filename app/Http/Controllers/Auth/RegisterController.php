<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $db_name = 'test_' . Str::random(10);
        $this->createDynamicDB($db_name);
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'db_name'  => $db_name,
        ]);
    }


    public function createDynamicDB($db_name)
    {
        // Create a database for the User
        $createDbQuery = "CREATE DATABASE IF NOT EXISTS $db_name";
        DB::statement($createDbQuery);

        // Switch to the newly created database
        $useDbQuery = "USE $db_name";
        DB::statement($useDbQuery);

        // Import the SQL file into the database
        $sql_path = base_path('database/sql/structure.sql');

        if (file_exists($sql_path)) {
            $sql = file_get_contents($sql_path);

            // Use DB::unprepared to execute the SQL queries from the file
            DB::unprepared($sql);
            // use dfault database
            $useDbQuery = "USE " . config('database.connections.mysql.database');
            DB::statement($useDbQuery);
        }
        else {
            // Handle the case where the SQL file doesn't exist
            // You can log an error or take other appropriate action
        }
    }

}