<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Address::create([
            'zipcode' => '12345-678',
            'street' => 'Rua teste',
            'number' => '123',
            'city' => 'Cidade teste',
            'state' => 'Estado teste',
            'country' => 'Brasil',
            'complement' => 'Apt teste',
            'user_id' => 1
        ]);
    }
}
