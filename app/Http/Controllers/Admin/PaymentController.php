<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.room.roomType.translations', 'booking.user']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking.room.roomType.translations', 'booking.user']);
        
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }

    /**
     * Get payment details for AJAX requests.
     */
    public function details(Payment $payment)
    {
        return response()->json([
            'transaction_id' => $payment->transaction_id,
            'gateway_transaction_id' => $payment->gateway_transaction_id,
            'amount' => number_format($payment->amount, 2),
            'payment_method' => ucfirst(str_replace('_', ' ', $payment->payment_method)),
            'status' => ucfirst($payment->status),
            'processed_at' => $payment->processed_at ? $payment->processed_at->format('M d, Y H:i') : null,
            'notes' => $payment->notes,
        ]);
    }

    /**
     * Mark a payment as completed.
     */
    public function complete(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Only pending payments can be marked as completed.');
        }

        DB::beginTransaction();
        try {
            $payment->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            // Update booking status if needed
            if ($payment->booking->status === 'pending') {
                $payment->booking->update(['status' => 'confirmed']);
            }

            DB::commit();

            return back()->with('success', 'Payment marked as completed successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating payment: ' . $e->getMessage());
        }
    }

    /**
     * Process a refund.
     */
    public function refund(Request $request, Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return back()->with('error', 'Only completed payments can be refunded.');
        }

        $this->validate($request, [
            'amount' => 'nullable|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|in:guest_request,cancellation,overbooking,service_issue,other',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $refundAmount = $request->amount ?: $payment->amount;

            // Create refund record
            $refund = Payment::create([
                'booking_id' => $payment->booking_id,
                'transaction_id' => 'REF-' . time() . rand(100, 999),
                'amount' => -$refundAmount, // Negative amount for refund
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
                'status' => 'completed',
                'processed_at' => now(),
                'notes' => 'Refund: ' . $request->reason . ($request->notes ? ' - ' . $request->notes : ''),
            ]);

            // Update original payment status if full refund
            if ($refundAmount >= $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }

            DB::commit();

            return back()->with('success', 'Refund processed successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download receipt.
     */
    public function receipt(Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return back()->with('error', 'Receipt can only be generated for completed payments.');
        }

        $payment->load(['booking.room.roomType.translations', 'booking.user']);

        // You can implement PDF generation here using a package like DomPDF
        // For now, return a view that can be printed
        return view('admin.payments.receipt', compact('payment'));
    }
}
