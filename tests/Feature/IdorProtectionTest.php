<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IdorProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_access_own_invoice(): void
    {
        $customer = Customer::factory()->create();
        $invoice = Invoice::factory()->for($customer)->create();

        $response = $this
            ->actingAs($customer, 'customer')
            ->getJson("/labs/idor/secure/invoices/{$invoice->id}");

        $response
            ->assertOk()
            ->assertJsonPath('invoice_number', $invoice->invoice_number)
            ->assertJsonPath('customer_id', $customer->id);
    }

    public function test_customer_cannot_access_another_customer_invoice(): void
    {
        $customer = Customer::factory()->create();
        $otherCustomerInvoice = Invoice::factory()->create();

        $response = $this
            ->actingAs($customer, 'customer')
            ->getJson("/labs/idor/secure/invoices/{$otherCustomerInvoice->id}");

        $response->assertNotFound();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $invoice = Invoice::factory()->create();

        $response = $this->get("/labs/idor/secure/invoices/{$invoice->id}");

        $response->assertRedirect('/login');
    }
}
