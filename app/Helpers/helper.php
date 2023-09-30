<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

function createDynamicDB($user, $db_name = null, $role = "Administrator")
{
    if ($db_name == null) {
        $db_name = 'test_' . Str::random(10);
    }

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
        // create user
        DB::table('users')->insert([
            'id'       => $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
            'password' => $user['password'],
        ]);
        $u = User::find($user['id']);
        // assign role
        $u->assignRole($role);
        // use default database
        $useDbQuery = "USE " . config('database.connections.mysql.database');
        DB::statement($useDbQuery);
    }
    else {
        throw new Exception("SQL file does not exist");
    }
}