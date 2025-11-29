<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Mr Admin",
            "email" => "admin@fsprogrammers.com",
            "password" => Hash::make("Admin1234*"), // Use a secure password for production
            "role" => "admin", // Assuming 'admin' is a valid role
            "verified" => true, // Assuming you want the user to be verified
            "dob" => "1990-01-01", // Example date of birth
            "gender" => "male", // Example gender
            "image_url" => "", // Example image URL
        ]);
        User::create([
            "name" => "Mr Manager",
            "email" => "manager@fsprogrammers.com",
            "password" => Hash::make("Manager1234*"), // Use a secure password for production
            "role" => "manager", // Assuming 'admin' is a valid role
            "verified" => true, // Assuming you want the user to be verified
            "dob" => "1992-12-04", // Example date of birth
            "gender" => "male", // Example gender
            "image_url" => "", // Example image URL
        ]);
        User::create([
            "name" => "Salesman User",
            "email" => "salesman@fsprogrammers.com",
            "password" => Hash::make("salesman1234*"), // Use a secure password for production
            "role" => "salesman", // Assuming 'admin' is a valid role
            "verified" => true, // Assuming you want the user to be verified
            "dob" => "1992-12-04", // Example date of birth
            "gender" => "male", // Example gender
            "image_url" => "", // Example image URL
        ]);
        User::create([
            "name" => "Sales Rep",
            "email" => "salesrep@fsprogrammers.com",
            "password" => Hash::make("SalesRep1234*"), // Use a secure password for production
            "role" => "salesman",
            "verified" => true,
            "dob" => "1995-03-15",
            "gender" => "male",
            "image_url" => "",
        ]);
        User::create([
            "name" => "Sales Agent",
            "email" => "salesagent@fsprogrammers.com",
            "password" => Hash::make("SalesAgent1234*"), // Use a secure password for production
            "role" => "salesman",
            "verified" => true,
            "dob" => "1994-07-22",
            "gender" => "female",
            "image_url" => "",
        ]);
        User::create([
            "name" => "Sales Manager",
            "email" => "salesmanager@fsprogrammers.com",
            "password" => Hash::make("SalesManager1234*"), // Use a secure password for production
            "role" => "salesman",
            "verified" => true,
            "dob" => "1993-11-08",
            "gender" => "male",
            "image_url" => "",
        ]);
        User::create([
            "name" => "Sales North",
            "email" => "salesnorth@fsprogrammers.com",
            "password" => Hash::make("SalesNorth1234*"), // Use a secure password for production
            "role" => "salesman",
            "verified" => true,
            "dob" => "1996-05-30",
            "gender" => "female",
            "image_url" => "",
        ]);
    }
}
