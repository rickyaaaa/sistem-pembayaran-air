<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'username' => 'admin',
            'name' => 'Administrator',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Residents
        $residentsData = [
            ['block' => 'A', 'house' => '1', 'name' => 'Budi Santoso', 'phone' => '081234567890'],
            ['block' => 'A', 'house' => '2', 'name' => 'Siti Rahayu', 'phone' => '081234567891'],
            ['block' => 'A', 'house' => '3', 'name' => 'Ahmad Wijaya', 'phone' => '081234567892'],
            ['block' => 'A', 'house' => '4', 'name' => 'Dewi Lestari', 'phone' => null],
            ['block' => 'A', 'house' => '5', 'name' => 'Eko Prasetyo', 'phone' => '081234567894'],
            ['block' => 'B', 'house' => '1', 'name' => 'Fatimah Zahra', 'phone' => '081234567895'],
            ['block' => 'B', 'house' => '2', 'name' => 'Gunawan Hadi', 'phone' => '081234567896'],
            ['block' => 'B', 'house' => '3', 'name' => 'Hendra Kusuma', 'phone' => null],
            ['block' => 'B', 'house' => '4', 'name' => 'Indah Permata', 'phone' => '081234567898'],
            ['block' => 'B', 'house' => '5', 'name' => 'Joko Widodo', 'phone' => '081234567899'],
        ];

        $residents = [];
        foreach ($residentsData as $data) {
            $blockNumber = strtolower($data['block'] . $data['house']);

            $user = User::create([
                'username' => $blockNumber,
                'name' => $data['name'],
                'password' => Hash::make($blockNumber),
                'role' => 'resident',
            ]);

            $residents[] = Resident::create([
                'user_id' => $user->id,
                'block_number' => $blockNumber,
                'block' => $data['block'],
                'house_number' => $data['house'],
                'phone_number' => $data['phone'],
                'is_active' => true,
            ]);
        }

        // Create bills for the last 3 months
        $currentYear = now()->year;
        $currentMonth = now()->month;
        $billAmount = 50000;

        for ($m = max(1, $currentMonth - 2); $m <= $currentMonth; $m++) {
            foreach ($residents as $resident) {
                $bill = Bill::create([
                    'resident_id' => $resident->id,
                    'month' => $m,
                    'year' => $currentYear,
                    'amount' => $billAmount,
                    'status' => 'unpaid',
                ]);

                // Some paid, some pending
                if ($m < $currentMonth) {
                    // Previous months: most paid
                    if (rand(1, 10) <= 8) {
                        $bill->update(['status' => 'paid']);
                        Payment::create([
                            'bill_id' => $bill->id,
                            'resident_id' => $resident->id,
                            'payment_date' => now()->setMonth($m)->setDay(rand(5, 25)),
                            'amount_paid' => $billAmount,
                            'proof_file' => 'payments/sample.jpg',
                            'status' => 'confirmed',
                            'confirmed_by' => $admin->id,
                            'confirmed_at' => now()->setMonth($m)->setDay(rand(5, 28)),
                        ]);
                    }
                } elseif ($m == $currentMonth) {
                    // Current month: some pending
                    if (rand(1, 10) <= 3) {
                        $bill->update(['status' => 'pending']);
                        Payment::create([
                            'bill_id' => $bill->id,
                            'resident_id' => $resident->id,
                            'payment_date' => now()->setDay(rand(1, min(now()->day, 28))),
                            'amount_paid' => $billAmount,
                            'proof_file' => 'payments/sample.jpg',
                            'status' => 'pending',
                        ]);
                    }
                }
            }
        }

        // Create some registrations
        foreach (array_slice($residents, 0, 5) as $resident) {
            Registration::create([
                'resident_id' => $resident->id,
                'payment_date' => now()->subMonths(rand(1, 6)),
                'amount' => 200000,
                'notes' => 'Biaya pendaftaran awal',
                'created_by' => $admin->id,
            ]);
        }

        // Create some expenses
        $expenseCategories = ['Perawatan', 'Operasional', 'Perbaikan', 'Material'];
        $expenseDescs = [
            'Perbaikan pipa utama', 'Biaya listrik pompa', 'Pembelian chlorine',
            'Ganti kran bocor', 'Service pompa air', 'Biaya admin bulanan',
        ];

        for ($i = 0; $i < 6; $i++) {
            Expense::create([
                'date' => now()->subDays(rand(1, 90)),
                'amount' => rand(1, 10) * 50000,
                'description' => $expenseDescs[$i],
                'category' => $expenseCategories[array_rand($expenseCategories)],
                'created_by' => $admin->id,
            ]);
        }
    }
}
