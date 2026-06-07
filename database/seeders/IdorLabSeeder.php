<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class IdorLabSeeder extends Seeder
{
    public function run(): void
    {
        $firstCustomer = Customer::updateOrCreate(
            ['email' => 'customer1@example.com'],
            [
                'name' => 'Customer One',
                'password' => Hash::make('password'),
            ],
        );

        $secondCustomer = Customer::updateOrCreate(
            ['email' => 'customer2@example.com'],
            [
                'name' => 'Customer Two',
                'password' => Hash::make('password'),
            ],
        );

        Invoice::updateOrCreate(
            ['invoice_number' => 'IDOR-LAB-001'],
            [
                'customer_id' => $firstCustomer->id,
                'amount' => 125.00,
                'issued_at' => '2026-06-08',
            ],
        );

        Invoice::updateOrCreate(
            ['invoice_number' => 'IDOR-LAB-002'],
            [
                'customer_id' => $secondCustomer->id,
                'amount' => 250.00,
                'issued_at' => '2026-06-08',
            ],
        );
    }
}
