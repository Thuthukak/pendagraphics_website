<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Service::query();

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $query->search($request->search);
            }

            // Filter by status
            if ($request->has('status') && in_array($request->status, ['active', 'inactive'])) {
                $query->where('is_active', $request->status === 'active');
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['name', 'base_price', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
            }

            // Pagination or get all
            if ($request->has('per_page')) {
                $services = $query->paginate($request->get('per_page', 15));
            } else {
                $services = $query->get();
            }

            return response()->json($services);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching services',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function indexWebDesign()
    {
        return view('services.web-design');
    }

    public function indexGraphicDesign()
    {
        return view('services.graphic-design');
    }

    public function indexProductDesign()
    {
        return view('services.product-design');
    }

    public function indexIdentityDesign()
    {
        return view('services.identity-design');
    }

    public function indexECommerce()
    {
        return view('services.e-commerce');
    }

    public function indexDigitalMarketing()
    {
        return view('services.digital-marketing');
    }

/**
 * Store a newly created service.
 */
public function store(Request $request)
{
    Log::info('we are in the store method', ['request_data' => $request->all()]);
    
    try {
        // Convert string boolean to actual boolean before validation
        $requestData = $request->all();
        if (isset($requestData['is_active'])) {
            $requestData['is_active'] = filter_var($requestData['is_active'], FILTER_VALIDATE_BOOLEAN);
        }
        
        $validatedData = Validator::make($requestData, [
            'name' => 'required|string|max:255|unique:services,name',
            'description' => 'required|string|max:1000',
            'base_price' => 'required|numeric|min:0|max:999999.99',
            'is_active' => 'required|boolean',
        ])->validate();

        Log::info('validatedData', $validatedData);

        $service = Service::create($validatedData);

        Log::info('Service created', ['service' => $service->toArray()]);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ], 201);

    } catch (ValidationException $e) {
        Log::error('Validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Error creating service', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'message' => 'Error creating service',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Display the specified service.
 */
public function show(Service $service)
{
    try {
        return response()->json($service);

    } catch (\Exception $e) {
        Log::error('Error fetching service', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Error fetching service',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Update the specified service.
 */
public function update(Request $request, Service $service)
{
    Log::info('Updating service', ['service_id' => $service->id, 'request_data' => $request->all()]);
    
    try {
        // Convert string boolean to actual boolean before validation
        $requestData = $request->all();
        if (isset($requestData['is_active'])) {
            $requestData['is_active'] = filter_var($requestData['is_active'], FILTER_VALIDATE_BOOLEAN);
        }
        
        $validatedData = Validator::make($requestData, [
            'name' => 'required|string|max:255|unique:services,name,' . $service->id,
            'description' => 'required|string|max:1000',
            'base_price' => 'required|numeric|min:0|max:999999.99',
            'is_active' => 'required|boolean',
        ])->validate();

        Log::info('Update validation passed', $validatedData);

        $service->update($validatedData);

        Log::info('Service updated successfully', ['service' => $service->fresh()->toArray()]);

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service->fresh()
        ]);

    } catch (ValidationException $e) {
        Log::error('Update validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Error updating service', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'message' => 'Error updating service',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Remove the specified service.
 */
public function destroy(Service $service)
{
    try {
        // Optional: Check if service is being used before deletion
        // You might want to add relationships checks here
        
        Log::info('Deleting service', ['service_id' => $service->id]);
        
        $service->delete();

        Log::info('Service deleted successfully', ['service_id' => $service->id]);

        return response()->json([
            'message' => 'Service deleted successfully'
        ]);

    } catch (\Exception $e) {
        Log::error('Error deleting service', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'message' => 'Error deleting service',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Toggle service active status.
 */
public function toggleStatus(Service $service)
{
    try {
        $oldStatus = $service->is_active;
        
        $service->update([
            'is_active' => !$service->is_active
        ]);

        Log::info('Service status toggled', [
            'service_id' => $service->id,
            'old_status' => $oldStatus,
            'new_status' => $service->fresh()->is_active
        ]);

        return response()->json([
            'message' => 'Service status updated successfully',
            'service' => $service->fresh()
        ]);

    } catch (\Exception $e) {
        Log::error('Error updating service status', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'message' => 'Error updating service status',
            'error' => $e->getMessage()
        ], 500);
    }
}
}


