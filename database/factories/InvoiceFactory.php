<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'invoice_number' => 'INV-'.fake()->unique()->numerify('######'),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'issued_at' => fake()->date(),
        ];
    }
}
