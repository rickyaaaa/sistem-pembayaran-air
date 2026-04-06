<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Resident;
use App\Models\User;
use App\Services\FinancialReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_financial_summary_calculation_is_correct()
    {
        $year = 2026;

        // 1. Create Confirmed Payment (Income)
        $resident = Resident::factory()->create();
        $bill = Bill::factory()->create(['resident_id' => $resident->id, 'year' => $year, 'amount' => 50000]);
        
        Payment::factory()->create([
            'bill_id' => $bill->id,
            'resident_id' => $resident->id,
            'amount_paid' => 50000,
            'status' => PaymentStatus::Confirmed,
            'payment_date' => "$year-01-10"
        ]);

        // 2. Create Registration (Income)
        Registration::factory()->create([
            'amount' => 100000,
            'payment_date' => "$year-02-15"
        ]);

        // 3. Create Expense (Expense)
        Expense::factory()->create([
            'amount' => 30000,
            'date' => "$year-03-20"
        ]);

        $service = new FinancialReportService();
        $summary = $service->getSummary($year);

        // Calculation: (50,000 + 100,000) - 30,000 = 120,000
        $this->assertEquals(150000, $summary['total_income'] + $summary['total_registrations']);
        $this->assertEquals(30000, $summary['total_expenses']);
        $this->assertEquals(120000, $summary['current_balance']);
    }

    public function test_unconfirmed_payments_are_not_counted_in_income()
    {
        $year = 2026;
        $resident = Resident::factory()->create();
        $bill = Bill::factory()->create(['resident_id' => $resident->id, 'year' => $year, 'amount' => 50000]);

        // Pending payment
        Payment::factory()->create([
            'bill_id' => $bill->id,
            'resident_id' => $resident->id,
            'amount_paid' => 50000,
            'status' => PaymentStatus::Pending,
            'payment_date' => "$year-01-10"
        ]);

        $service = new FinancialReportService();
        $summary = $service->getSummary($year);

        $this->assertEquals(0, $summary['total_income']);
    }
}
