<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all bookings that should have payments
        $bookings = Booking::whereIn('status', ['completed', 'checked_in'])->get();

        foreach ($bookings as $booking) {
            // Check if payment already exists for this booking
            if (!$booking->payments()->exists()) {
                // Both completed and checked_in bookings should have completed payments
                // since they represent actual revenue
                $paymentStatus = in_array($booking->status, ['completed', 'checked_in']) ? 'completed' : 'failed';
                
                // Create payment record
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_method' => $this->getRandomPaymentMethod(),
                    'amount' => $booking->total_amount,
                    'currency' => 'MYR',
                    'transaction_id' => $this->generateTransactionId(),
                    'payment_status' => $paymentStatus,
                    'payment_gateway' => $this->getRandomGateway(),
                    'gateway_response' => $this->generateGatewayResponse($paymentStatus),
                    'processed_at' => $paymentStatus === 'completed' ? $booking->created_at : null,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ]);

                $this->command->info("Created payment for booking {$booking->id} - Amount: \${$booking->total_amount} - Status: {$paymentStatus}");
            }
        }

        $this->command->info('Payment seeding completed!');
    }

    /**
     * Get a random payment method.
     */
    private function getRandomPaymentMethod(): string
    {
        $methods = ['credit_card', 'debit_card', 'bank_transfer', 'cash'];
        return $methods[array_rand($methods)];
    }

    /**
     * Get a random payment gateway.
     */
    private function getRandomGateway(): string
    {
        $gateways = ['stripe', 'paypal', 'square', 'authorize_net'];
        return $gateways[array_rand($gateways)];
    }

    /**
     * Generate a realistic transaction ID.
     */
    private function generateTransactionId(): string
    {
        return 'txn_' . strtoupper(bin2hex(random_bytes(8)));
    }

    /**
     * Generate a gateway response based on payment status.
     */
    private function generateGatewayResponse(string $status): array
    {
        if ($status === 'completed') {
            return [
                'status' => 'succeeded',
                'message' => 'Payment processed successfully',
                'gateway_transaction_id' => 'gw_' . strtoupper(bin2hex(random_bytes(6))),
                'processed_at' => now()->toISOString(),
            ];
        }

        return [
            'status' => 'failed',
            'message' => 'Payment processing failed',
        ];
    }
}
