<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewEnquiryNotification;

class ContactController extends Controller
{
    /**
     * Store a new contact enquiry
     */
    public function contactForm(Request $request)
{
    Log::info('Processing contact form submission');
    
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'service' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'status' => 'nullable|string|in:new,in_progress,waiting_for_response,resolved,spam,closed',
        ]);
        
        Log::info('Validated contact data', ['data' => $validated]);
        
        // Set default status if not provided
        if (!isset($validated['status'])) {
            $validated['status'] = 'new';
        }

        $logo = config('app.logo');
        
        $contact = Contact::create($validated);
        
        Log::info('Contact created successfully', ['id' => $contact->id]);
        
        // Send email notification to admin
        try {
            $adminEmail = config('mail.from.admin_address');
            Mail::to($adminEmail)->send(new NewEnquiryNotification($contact));
            Log::info('Admin notification email sent', ['admin_email' => $adminEmail]);
        } catch (\Exception $emailException) {
            // Log email error but don't fail the request
            Log::error('Failed to send admin notification email', [
                'error' => $emailException->getMessage()
            ]);
        }
        
        return response()->json([
            'message' => 'Enquiry submitted successfully',
            'contact' => $contact,
            'logo' => $logo
        ], 201);
        
    } catch (ValidationException $e) {
        Log::warning('Validation failed', ['errors' => $e->errors()]);
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('Error creating contact', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'message' => 'An error occurred while processing your enquiry'
        ], 500);
    }
}
    
    /**
     * Get paginated list of contacts with search and filters
     */
    public function index(Request $request)
    {
        try {
            $query = Contact::query();
            
            // Search by name, email, or phone
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }
            
            // Filter by status
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }
            
            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            // Validate sort fields to prevent SQL injection
            $allowedSortFields = ['created_at', 'updated_at', 'name', 'service', 'status'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }
            
            // Paginate
            $perPage = $request->get('per_page', 10);
            $perPage = min(max($perPage, 10), 100); // Limit between 10 and 100
            
            $contacts = $query->paginate($perPage);
            
            return response()->json($contacts);
            
        } catch (\Exception $e) {
            Log::error('Error fetching contacts', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'An error occurred while fetching enquiries'
            ], 500);
        }
    }
    
    /**
     * Get a single contact by ID
     */
    public function show($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            return response()->json($contact);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enquiry not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching contact', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'An error occurred while fetching the enquiry'
            ], 500);
        }
    }
    
    /**
     * Update an existing contact
     */
    public function update(Request $request, $id)
    {
        Log::info('Updating contact', ['id' => $id]);
        try {
            $contact = Contact::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'email' => 'nullable|email|max:255',
                'service' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string|max:2000',
                'status' => 'sometimes|string|in:new,in_progress,waiting_for_response,resolved,spam,closed',
            ]);
            
            $contact->update($validated);
            
            Log::info('Contact updated successfully', ['id' => $contact->id]);
            
            return response()->json([
                'message' => 'Enquiry updated successfully',
                'contact' => $contact->fresh()
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enquiry not found'
            ], 404);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error updating contact', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'An error occurred while updating the enquiry'
            ], 500);
        }
    }
    
    /**
     * Delete a contact
     */
    public function destroy($id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();
            
            Log::info('Contact deleted successfully', ['id' => $id]);
            
            return response()->json([
                'message' => 'Enquiry deleted successfully'
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enquiry not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error deleting contact', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'An error occurred while deleting the enquiry'
            ], 500);
        }
    }
}