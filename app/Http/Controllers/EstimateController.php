<?php

namespace App\Http\Controllers;

use App\Mail\EstimateRequest;
use App\Models\Estimate;
use App\Models\EstimateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create estimate record
        $estimate = Estimate::create([
            'name' => $request->name,
            'email' => $request->email,
            'total_amount' => $request->totalEstimate,
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

        // Send email notification
        Mail::to($request->email)
            ->cc(config('mail.admin_address'))
            ->send(new EstimateRequest($estimate));

        return response()->json([
            'message' => 'Estimate request received successfully',
            'estimate_id' => $estimate->id
        ], 201);
    }
}
