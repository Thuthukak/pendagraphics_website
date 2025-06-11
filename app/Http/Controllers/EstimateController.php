<?php
namespace App\Http\Controllers;

use App\Mail\EstimateRequest;
use App\Models\Estimate;
use App\Models\EstimateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;

class EstimateController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'selectedServices' => 'required|array|min:1',
            'selectedServices.*.id' => 'required|integer|exists:services,id',
            'totalEstimate' => 'required|numeric|min:0',
            'additionalDetails' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create estimate record
            $estimate = Estimate::create([
                'name' => $request->name,
                'email' => $request->email,
                'total_amount' => $request->totalEstimate,
                'notes' => $request->additionalDetails,
                'status' => 'pending', // Default status
            ]);

            // Store selected services
            foreach ($request->selectedServices as $service) {
                EstimateService::create([
                    'estimate_id' => $estimate->id,
                    'service_id' => $service['id'],
                    'price' => $service['price'],
                ]);
            }

            // Try to send email notification
            $emailSent = false;
            $emailError = null;
            
            try {
                Mail::to($request->email)
                    ->cc(config('mail.admin_address'))
                    ->bcc(config('mail.from.address'))
                    ->send(new EstimateRequest($estimate));
                    
                $emailSent = true;
                
                // Update estimate status to indicate email was sent
                $estimate->update(['status' => 'emailed']);
                
            } catch (Exception $mailException) {
                // Log the email error but don't fail the entire request
                Log::error('Failed to send estimate email', [
                    'estimate_id' => $estimate->id,
                    'email' => $request->email,
                    'error' => $mailException->getMessage()
                ]);
                
                $emailError = $mailException->getMessage();
                
                // Update estimate status to indicate email failed
                $estimate->update(['status' => 'email_failed']);
            }

            // Return success response with email status
            $response = [
                'message' => 'Estimate request received successfully',
                'estimate_id' => $estimate->id,
                'email_sent' => $emailSent
            ];

            // If email failed, inform the user
            if (!$emailSent) {
                $response['email_warning'] = 'Your request was saved, but we encountered an issue sending the confirmation email. We will contact you directly within 24 hours.';
            }

            return response()->json($response, 201);

        } catch (Exception $e) {
            // Log the general error
            Log::error('Failed to create estimate', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again or contact us directly.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}