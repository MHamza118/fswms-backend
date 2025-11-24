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
            'name' => 'Mr Admin',
            'email' => 'admin@fsprogrammers.com',
            'password' => Hash::make('Admin1234*'),  // Use a secure password for production
            'role' => 'admin',  // Assuming 'admin' is a valid role
            'verified' => true,  // Assuming you want the user to be verified
            'dob' => '1990-01-01',  // Example date of birth
            'gender' => 'male',  // Example gender
            'image_url' => '',  // Example image URL
        ]);
        User::create([
            'name' => 'Mr Manager',
            'email' => 'manager@fsprogrammers.com',
            'password' => Hash::make('Manager1234*'),  // Use a secure password for production
            'role' => 'manager',  // Assuming 'admin' is a valid role
            'verified' => true,  // Assuming you want the user to be verified
            'dob' => '1992-12-04',  // Example date of birth
            'gender' => 'male',  // Example gender
            'image_url' => '',  // Example image URL
        ]);
        User::create([
            'name' => 'Salesman User',
            'email' => 'salesman@fsprogrammers.com',
            'password' => Hash::make('salesman1234*'),  // Use a secure password for production
            'role' => 'salesman',  // Assuming 'admin' is a valid role
            'verified' => true,  // Assuming you want the user to be verified
            'dob' => '1992-12-04',  // Example date of birth
            'gender' => 'male',  // Example gender
            'image_url' => '',  // Example image URL
        ]);
    }
}
