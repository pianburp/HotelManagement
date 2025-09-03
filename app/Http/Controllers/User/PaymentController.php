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
            $user = auth()->user();
            
            // Get booking data from session
            $roomId = session('booking.room_id');
            if (!$roomId) {
                return redirect()->route('user.rooms.index')
                    ->with('error', 'Booking session expired. Please start over.');
            }

            $room = Room::with('roomType')->find($roomId);
            if (!$room) {
                return redirect()->route('user.rooms.index')
                    ->with('error', 'Room not found. Please select another room.');
            }

            // Create booking with session data
            $booking = Booking::create([
                'booking_reference' => 'HMS-' . strtoupper(Str::random(6)),
                'user_id' => $user->id,
                'room_id' => $roomId,
                'guest_name' => session('booking.guest_name', $user->name),
                'guest_email' => session('booking.guest_email', $user->email),
                'guest_phone' => session('booking.guest_phone', $user->phone),
                'check_in_date' => session('booking.check_in'),
                'check_out_date' => session('booking.check_out'),
                'guests_count' => session('booking.guests'),
                'total_amount' => session('booking.total_amount'),
                'status' => 'confirmed',
                'special_requests' => session('booking.special_requests'),
                'booking_source' => 'website',
            ]);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => session('booking.payment_method'),
                'amount' => session('booking.total_amount'),
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

            // Update session data for success page
            session([
                'booking.confirmation_number' => $booking->booking_reference,
                'booking.transaction_id' => $payment->transaction_id,
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
