<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Show payment demo page.
     */
    public function demo()
    {
        return view('user.payments.demo');
    }

    /**
     * Process payment demo - create actual booking and payment records.
     */
    public function processDemo(Request $request)
    {
        $status = $request->input('status');
        
        if ($status === 'success') {
            return $this->processSuccessPayment($request);
        } elseif ($status === 'failed') {
            return $this->processFailedPayment($request);
        }
        
        return redirect()->route('user.payments.demo');
    }

    /**
     * Process successful payment and create booking/payment records.
     */
    private function processSuccessPayment(Request $request)
    {
        try {
            // Get booking data from session or request
            $bookingData = [
                'room_type' => session('booking.room_type', 'Deluxe Suite'),
                'check_in' => session('booking.check_in', date('Y-m-d')),
                'check_out' => session('booking.check_out', date('Y-m-d', strtotime('+3 days'))),
                'guests' => session('booking.guests', '2'),
                'payment_method' => session('booking.payment_method', 'credit_card'),
                'total' => str_replace(['RM ', 'RM'], '', session('booking.total', '660.00')),
            ];

            // Find an available room (for demo purposes, use the first available room)
            $room = Room::with('roomType')
                ->whereHas('roomType', function ($query) {
                    $query->where('is_active', true);
                })
                ->where('status', 'available')
                ->first();

            if (!$room) {
                // Create a demo room if none exists
                $room = $this->createDemoRoom();
            }

            // Calculate nights and total
            $checkIn = Carbon::parse($bookingData['check_in']);
            $checkOut = Carbon::parse($bookingData['check_out']);
            $nights = $checkIn->diffInDays($checkOut);
            $totalAmount = $room->roomType->base_price * $nights;

            // Create booking
            $booking = Booking::create([
                'booking_reference' => 'HMS-' . strtoupper(Str::random(6)),
                'user_id' => auth()->id(),
                'room_id' => $room->id,
                'check_in_date' => $bookingData['check_in'],
                'check_out_date' => $bookingData['check_out'],
                'guests_count' => $bookingData['guests'],
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'special_requests' => 'Demo booking from payment processor',
                'booking_source' => 'website',
            ]);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $bookingData['payment_method'],
                'amount' => $totalAmount,
                'currency' => 'MYR',
                'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                'payment_status' => 'completed',
                'payment_gateway' => 'demo_gateway',
                'gateway_response' => [
                    'status' => 'success',
                    'message' => 'Payment processed successfully',
                    'demo' => true,
                ],
                'processed_at' => now(),
            ]);

            // Update room status to reserved (since booking is confirmed)
            $room->update(['status' => 'reserved']);

            // Store real booking data in session for the success page
            session([
                'booking.confirmation_number' => $booking->booking_reference,
                'booking.transaction_id' => $payment->transaction_id,
                'booking.room_type' => $room->roomType->name ?? $bookingData['room_type'],
                'booking.total' => 'RM ' . number_format($totalAmount, 2),
                'booking.booking_id' => $booking->id,
            ]);

            return redirect()->route('user.payments.success');

        } catch (\Exception $e) {
            \Log::error('Payment processing error: ' . $e->getMessage());
            return redirect()->route('user.payments.failed')
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process failed payment.
     */
    private function processFailedPayment(Request $request)
    {
        // For failed payments, we could still create a booking record with failed status
        // but for this demo, we'll just redirect to the failed page
        return redirect()->route('user.payments.failed');
    }

    /**
     * Create a demo room for testing purposes.
     */
    private function createDemoRoom()
    {
        // This is a fallback method if no rooms exist
        // In production, you would handle this differently
        
        // First, ensure we have a room type
        $roomType = \App\Models\RoomType::first();
        if (!$roomType) {
            $roomType = \App\Models\RoomType::create([
                'code' => 'DEMO',
                'base_price' => 220.00,
                'max_occupancy' => 2,
                'amenities' => json_encode(['wifi', 'tv', 'ac']),
                'is_active' => true,
            ]);

            // Create translation
            \App\Models\RoomTypeTranslation::create([
                'room_type_id' => $roomType->id,
                'locale' => 'en',
                'name' => 'Demo Suite',
                'description' => 'A demo room for testing purposes',
            ]);
        }

        return Room::create([
            'room_number' => 'DEMO-' . rand(100, 999),
            'room_type_id' => $roomType->id,
            'floor_number' => 1,
            'size' => 25.0,
            'smoking_allowed' => false,
            'status' => 'available',
            'notes' => 'Demo room created for payment testing',
        ]);
    }

    /**
     * Show payment success page.
     */
    public function success()
    {
        return view('user.payments.success');
    }

    /**
     * Show payment failed page.
     */
    public function failed()
    {
        return view('user.payments.failed');
    }
}
