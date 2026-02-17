<?php

namespace App\Http\Controllers\Api;

use App\Services\FirebaseService;
use App\Http\Controllers\Controller;
use App\Models\HouseOwner;
use App\Models\Property;
use App\Models\Appliance;
use App\Models\HousePlan;
use App\Models\ComplianceCertificate;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\PageContent;
use App\Models\NotificationList;
use App\Models\ServiceTicket;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceTicketMail;
use Illuminate\Support\Facades\Log;
use App\Models\Specialization;
use Illuminate\Support\Facades\Storage;
use App\Models\IssueOtp;
use App\Mail\IssueCompletionOtpMail;
use App\Mail\IssueCompletedMail;

class HouseOwnerApiController extends Controller
{
    public function getOwnerWithProperties(Request $request)
    {
        try {
            // Check for bearer token
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.',], 401);
            }

            // Validate user from token
            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.',], 401);
            }

            // Validate input
            $validatedData = $request->validate([
                'user_id'  => 'required|integer',
                'page'     => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            // Fetch user
            $user = User::find($validatedData['user_id']);
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found',], 404);
            }

            // Fetch house owners with same email
            $houseOwners = HouseOwner::where('email_address', $user->email)->get();
            if ($houseOwners->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'House Allocation Is In Progress',], 404);
            }

            // Collect all property IDs
            $allPropertyIds = [];
            foreach ($houseOwners as $owner) {
                $propertyIds = $owner->properties_id;

                if (is_null($propertyIds)) continue;
                if (!is_array($propertyIds)) {
                    $decoded = json_decode($propertyIds, true);
                    $propertyIds = is_array($decoded) ? $decoded : [$propertyIds];
                }

                $allPropertyIds = array_merge($allPropertyIds, $propertyIds);
            }

            $allPropertyIds = array_unique($allPropertyIds);

            if (empty($allPropertyIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'No properties found for these house owners',
                ], 404);
            }

            // Pagination setup
            $perPage = $validatedData['per_page'] ?? 10;
            $page = $validatedData['page'] ?? 1;
            $offset = ($page - 1) * $perPage;

            // Fetch paginated properties
            $query = Property::whereIn('id', $allPropertyIds);
            $total = $query->count();

            $properties = $query
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Step 9: Attach appliances and decode images/manuals
            $properties->transform(function ($property) {
                $applianceIds = $property->appliance_id;

                if (is_null($applianceIds)) {
                    $applianceIds = [];
                } elseif (!is_array($applianceIds)) {
                    $decoded = json_decode($applianceIds, true);
                    $applianceIds = is_array($decoded) ? $decoded : [$applianceIds];
                }

                $appliances = Appliance::whereIn('id', $applianceIds)->get();

                // Decode manuals and appliances_images for mobile-friendly format
                $appliancesFormatted = $appliances->map(function ($appliance) {
                    return [
                        'id' => $appliance->id,
                        'appliance_id' => $appliance->appliance_id,
                        'appliance_name' => $appliance->appliance_name,
                        'product_details' => $appliance->product_details,
                        'category' => $appliance->category,
                        'place_of_location' => $appliance->place_of_location,
                        'brand_name' => $appliance->brand_name,
                        'model' => $appliance->model,
                        'warranty_information' => $appliance->warranty_information,
                        'manuals' => json_decode($appliance->manuals) ?: [],
                        'appliances_images' => json_decode($appliance->appliances_images) ?: [],
                        'created_at' => $appliance->created_at,
                        'updated_at' => $appliance->updated_at,
                    ];
                });

                $property->appliances = $appliancesFormatted;
                return $property;
            });

            // Step 10: Return paginated response
            return response()->json([
                'status'       => true,
                'properties'   => $properties,
                'total'        => $total,
                'per_page'     => (int)$perPage,
                'current_page' => (int)$page,
                'total_pages'  => ceil($total / $perPage),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getAppliancesByProperty(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.',], 401);
            }

            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.',], 401);
            }

            if (!$request->has('property_id') || empty($request->property_id)) {
                return response()->json(['status' => false, 'message' => 'Property ID is required',], 422);
            }

            $property = Property::find($request->property_id);
            if (!$property) {
                return response()->json(['status' => false, 'message' => 'Property not found',], 404);
            }

            $applianceIds = $property->appliance_id;

            if (is_null($applianceIds)) {
                $applianceIds = [];
            } elseif (!is_array($applianceIds)) {
                $decoded = json_decode($applianceIds, true);
                $applianceIds = is_array($decoded) ? $decoded : [$applianceIds];
            }

            $appliances = Appliance::whereIn('id', $applianceIds)->get();
            $appliancesFormatted = $appliances->map(function ($appliance) {
                return [
                    'id' => $appliance->id,
                    'appliance_id' => $appliance->appliance_id,
                    'appliance_name' => $appliance->appliance_name,
                    'product_details' => $appliance->product_details,
                    'category' => $appliance->category,
                    'place_of_location' => $appliance->place_of_location,
                    'brand_name' => $appliance->brand_name,
                    'model' => $appliance->model,
                    'warranty_information' => $appliance->warranty_information,
                    'manuals' => collect(
                        is_array($appliance->manuals)
                            ? $appliance->manuals
                            : (json_decode($appliance->manuals, true) ?? [])
                    )->map(fn($m) => 'storage/' . ltrim($m, '/'))->values(),

                    'appliances_images' => collect(
                        is_array($appliance->appliances_images)
                            ? $appliance->appliances_images
                            : (json_decode($appliance->appliances_images, true) ?? [])
                    )->map(fn($i) => 'storage/' . ltrim($i, '/'))->values(),
                    'created_at' => $appliance->created_at,
                    'updated_at' => $appliance->updated_at,
                ];
            });

            // Fetch recent issue reports for this property
            $recentServices = IssueReport::where('properties_id', $property->id)
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'issue_number' => $report->issue_number,
                        'issue_title' => $report->issue_title,
                        'issue_category' => $report->issue_category,
                        'issue_status' => $report->issue_status,
                        'issue_urgency_level' => $report->issue_urgency_level,
                        'reported_date' => $report->reported_date,
                        'service_provider' => $report->service_provider,
                        'image' => collect(
                            is_array($report->image)
                                ? $report->image
                                : (json_decode($report->image, true) ?? [])
                        )->map(fn($i) => 'storage/' . ltrim($i, '/'))->values(),
                        'property_title' => $report->property->property_title ?? null,
                    ];
                });


            // Final response structure
            return response()->json([
                'status' => true,
                'response_code' => 200,
                'data' => [
                    'property_id' => $property->id,
                    'recent_services' => $recentServices->isNotEmpty()
                        ? $recentServices
                        : [],
                    'all_appliances' => $appliancesFormatted->isNotEmpty()
                        ? $appliancesFormatted
                        : [],
                ],
                // 'total' => $appliancesFormatted->count(),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors(),], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage(),], 500);
        }
    }

    /* Id Wise Fetch Appliance's Data */
    public function getApplianceById(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.',], 401);
            }

            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.',], 401);
            }

            if (!$request->has('appliance_id') || empty($request->appliance_id)) {
                return response()->json(['status' => false, 'message' => 'Appliance ID is required',], 422);
            }

            $appliance = Appliance::find($request->appliance_id);
            if (!$appliance) {
                return response()->json(['status' => false, 'message' => 'Appliance not found',], 404);
            }

            $manualsData = is_array($appliance->manuals) ? $appliance->manuals : json_decode($appliance->manuals, true);
            $manuals = collect($manualsData)->map(function ($manual) {
                return [
                    'format' => pathinfo($manual, PATHINFO_EXTENSION),
                    'url'    => 'storage/' . $manual,
                ];
            })->values();

            $imagesRaw = $appliance->appliances_images;

            if (is_string($imagesRaw)) {
                $imagesRaw = json_decode($imagesRaw, true);
            }

            if (is_array($imagesRaw) && count($imagesRaw) === 1 && is_string($imagesRaw[0])) {
                $decoded = json_decode($imagesRaw[0], true);
                if (is_array($decoded)) {
                    $imagesRaw = $decoded;
                }
            }

            $applianceImages = collect($imagesRaw)->map(function ($img) {
                return 'storage/' . ltrim($img, '/');
            })->values();

            $applianceData = [
                'product' => [
                    'appliance_id' => $appliance->id,
                    'appliance_name' => $appliance->appliance_name,
                    'product_details' => $appliance->product_details,
                    'category' => $appliance->category,
                    'place_of_location' => $appliance->place_of_location,
                    'name' => $appliance->brand_name,
                    'brand' => $appliance->brand_name,
                    'model' => $appliance->model,
                    'warranty' => $appliance->warranty_information,
                    'manuals' => $manuals,
                    'appliances_images' => $applianceImages,
                ],
                'service' => [
                    "last_service_date" => "24-11-2025",
                    "next_service_date" => "25-12-2025",
                    "service_engineer" => [
                        "name" => "John",
                        "profile_url" => "uploads/profile/aMrnFGQs8kqWaDg29f34m2kzwZZKPOaw45f0jsPe.jpg",
                    ]
                ],
            ];

            return response()->json(['status' => true, 'resoonse_code' => 200, 'data' => $applianceData,], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors(),], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage(),], 500);
        }
    }

    public function requestApplianceService(Request $request)
    {
        try {
            // Normalize empty service provider
            if ($request->input('service_provider') === '' || $request->input('service_provider') == 0) {
                $request->merge(['service_provider' => null]);
            }

            $validatedData = $request->validate([
                'properties_id' => 'required|exists:properties,id',
                'appliance_id'  => 'required|exists:appliances,id',
                'issue_title'    => 'required|string|max:255',
                'issue_category' => 'nullable|string|max:255',
                'issue_location' => 'nullable|string|max:255',
                'customer_contact' => 'nullable|string|max:50',
                'issue_details'  => 'required|string',
                'reported_by'   => 'required|exists:users,id',
                'reported_date' => 'required|date',
                'assigned_to_service_provider' => 'nullable|in:yes,no',
                'service_provider' => [
                    'nullable',
                    'sometimes',
                    'exists:users,id',
                ],
                'issue_status'        => 'required|string|max:255',
                'issue_urgency_level' => 'required|string|max:255',

                // Same as other modules
                'image'   => 'nullable|array',
                'image.*' => 'file|mimes:jpg,jpeg,png,pdf,csv,xlsx,xls|max:5120',
            ]);

            // Validate service provider role
            if (!empty($validatedData['service_provider'])) {
                $isServiceProvider = \App\Models\User::where('id', $validatedData['service_provider'])
                    ->where('role', 'service_provider')
                    ->exists();

                if (!$isServiceProvider) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Selected user is not a valid service provider',
                    ], 422);
                }
            }

            if (($validatedData['assigned_to_service_provider'] ?? 'no') !== 'yes') {
                $validatedData['service_provider'] = null;
            }

            // Generate issue number
            $lastIssueNumber = IssueReport::where('issue_number', 'LIKE', 'Issue-%')
                ->orderByRaw('CAST(SUBSTRING(issue_number, 7) AS UNSIGNED) DESC')
                ->first();

            $nextNumber = ($lastIssueNumber && preg_match('/Issue-(\d+)/', $lastIssueNumber->issue_number, $m))
                ? (int) $m[1] + 1
                : 1;

            $newIssueNumber = 'Issue-' . $nextNumber;

            // tore images (same logic as Property)
            $images = [];

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $images[] = $file->store('issue_reports', 'public');
                }
            }

            $issue = IssueReport::create([
                'issue_number'   => $newIssueNumber,
                'properties_id'  => $validatedData['properties_id'],
                'appliance_id'   => $validatedData['appliance_id'],
                'issue_title'    => $validatedData['issue_title'],
                'issue_category' => $validatedData['issue_category'] ?? null,
                'issue_location' => $validatedData['issue_location'] ?? null,
                'customer_contact' => $validatedData['customer_contact'] ?? null,
                'issue_details'  => $validatedData['issue_details'],
                'reported_by'    => $validatedData['reported_by'],
                'reported_date'  => $validatedData['reported_date'],
                'assigned_to_service_provider' => $validatedData['assigned_to_service_provider'] ?? 'no',
                'service_provider' => $validatedData['service_provider'],
                'issue_status'   => $validatedData['issue_status'],
                'issue_urgency_level' => $validatedData['issue_urgency_level'],
                'image'          => json_encode($images),
            ]);

            // Decode images for API response
            $responseData = $issue->toArray();
            $responseData['image'] = $images;

            return response()->json([
                'status'  => true,
                'message' => 'Issue created successfully',
                'data'    => $responseData,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function getHousePlanByProperty(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
        ]);

        $propertyId = $validated['property_id'];
        $property = Property::find($propertyId);

        $housePlans = HousePlan::where('id', $property->house_plan_id)->get();

        if ($housePlans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No house plans found for this property.',
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | Fetch ALL appliances once & key by ID (for floor mapping)
    |--------------------------------------------------------------------------
    */
        $allAppliances = Appliance::all()->keyBy('id');

        /*
    |--------------------------------------------------------------------------
    | Transform house plans & floor details
    |--------------------------------------------------------------------------
    */
        $housePlans->transform(function ($plan) use ($allAppliances) {

            $floors = $plan->floor_details;

            if (is_string($floors)) {
                $floors = json_decode($floors, true);
            } elseif (is_object($floors)) {
                $floors = json_decode(json_encode($floors), true);
            }

            $floors = $floors ?? [];
            $formattedFloors = [];

            foreach ($floors as $floorName => $floorData) {

                /*
            |--------------------------------------------------
            | Replace appliance IDs with appliance details
            |--------------------------------------------------
            */
                $floorAppliances = [];

                if (!empty($floorData['appliances']) && is_array($floorData['appliances'])) {
                    foreach ($floorData['appliances'] as $applianceId) {
                        if (isset($allAppliances[$applianceId])) {
                            $appliance = $allAppliances[$applianceId];

                            // Format appliance images
                            $images = [];
                            if (!empty($appliance->appliances_images)) {
                                $decodedImages = json_decode($appliance->appliances_images, true);
                                if (is_array($decodedImages)) {
                                    $images = array_map(
                                        fn($img) =>
                                        'storage/appliances_images/' . basename($img),
                                        $decodedImages
                                    );
                                }
                            }

                            // Format manuals
                            $manuals = [];
                            if (!empty($appliance->manuals)) {
                                $decodedManuals = json_decode($appliance->manuals, true);
                                if (is_array($decodedManuals)) {
                                    $manuals = array_map(
                                        fn($file) =>
                                        'storage/manuals/' . basename($file),
                                        $decodedManuals
                                    );
                                }
                            }

                            $floorAppliances[] = [
                                'id' => $appliance->id,
                                'name' => $appliance->appliance_name ?? null,
                                'appliances_images' => $images,
                                'manuals' => $manuals,
                            ];
                        }
                    }
                }

                $floorData['appliances'] = $floorAppliances;

                /*
            |--------------------------------------------------
            | Floor plan images
            |--------------------------------------------------
            */
                if (isset($floorData['floor_plan']) && is_array($floorData['floor_plan'])) {
                    $floorData['floor_plan'] = array_map(
                        fn($img) => 'storage/' . ltrim($img, '/'),
                        $floorData['floor_plan']
                    );
                }

                $floorData['floor_name'] = $floorName;
                $formattedFloors[] = $floorData;
            }

            $plan->floor_details = $formattedFloors;

            return $plan;
        });

        /*
    |--------------------------------------------------------------------------
    | Property-level appliances (existing logic untouched)
    |--------------------------------------------------------------------------
    */
        $applianceIds = $property->appliance_id;

        if (is_null($applianceIds)) {
            $applianceIds = [];
        } elseif (!is_array($applianceIds)) {
            $decoded = json_decode($applianceIds, true);
            $applianceIds = is_array($decoded) ? $decoded : [$applianceIds];
        }

        $appliances = Appliance::whereIn('id', $applianceIds)->get();

        $appliances->transform(function ($appliance) {

            $images = [];
            if (!empty($appliance->appliances_images)) {
                $decodedImages = json_decode($appliance->appliances_images, true);
                if (is_array($decodedImages)) {
                    $images = array_map(
                        fn($img) =>
                        'storage/appliances_images/' . basename($img),
                        $decodedImages
                    );
                }
            }

            $manuals = [];
            if (!empty($appliance->manuals)) {
                $decodedManuals = json_decode($appliance->manuals, true);
                if (is_array($decodedManuals)) {
                    $manuals = array_map(
                        fn($file) =>
                        'storage/manuals/' . basename($file),
                        $decodedManuals
                    );
                }
            }

            $appliance->appliances_images = $images;
            $appliance->manuals = $manuals;

            return $appliance;
        });

        /*
    |--------------------------------------------------------------------------
    | Final Response
    |--------------------------------------------------------------------------
    */
        return response()->json([
            'success' => true,
            'response_code' => 200,
            'message' => 'House plans retrieved successfully.',
            'data' => [
                'house_plans' => $housePlans->first() ?? new \stdClass(), // ✅ object
                'appliances' => $appliances->isNotEmpty() ? $appliances : [],
            ],
        ], 200);
    }


    public function getComplianceCertificatesByProperty(Request $request)
    {
        try {
            // Check for bearer token
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.',], 401);
            }

            // Validate user from token
            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.',], 401);
            }

            // Validate property_id
            if (!$request->has('property_id') || empty($request->property_id)) {
                return response()->json(['status' => false, 'message' => 'Property ID is required',], 422);
            }

            // Fetch the property
            $property = Property::find($request->property_id);

            if (!$property) {
                return response()->json(['status' => false, 'message' => 'Property not found',], 404);
            }

            // Fetch all certificates
            $certificates = ComplianceCertificate::where('property_id', $property->id)->get();

            // Fetch recent certificates (last 1 month)
            $oneMonthAgo = Carbon::now()->subMonth();
            $recentCertificates = ComplianceCertificate::where('property_id', $property->id)
                ->where('created_at', '>=', $oneMonthAgo)
                ->get();

            // Format function
            $format = function ($certificate) {
                $attachment = isset($certificate->attachments[0]) ? $certificate->attachments[0] : null;
                return [
                    'id' => $certificate->id,
                    'property_id' => $certificate->property_id,
                    'certification_title' => $certificate->certification_title,
                    'compliance_type' => $certificate->compliance_type,
                    'certificate_number' => $certificate->certificate_number,
                    'issuing_authority' => $certificate->issuing_authority,
                    'date_of_issue' => optional($certificate->date_of_issue)->format('Y-m-d'),
                    'expiry_date' => optional($certificate->expiry_date)->format('Y-m-d'),
                    'property_area' => $certificate->property_area,
                    'attachments' => isset($certificate->attachments[0]) ? $certificate->attachments[0] : null,
                    'attachment_type' => $attachment ? strtolower(pathinfo($attachment, PATHINFO_EXTENSION)) : null,
                    'notes' => $certificate->notes,
                    'created_at' => $certificate->created_at,
                    'updated_at' => $certificate->updated_at,
                ];
            };

            // Final API Response
            return response()->json([
                'status' => true,
                'property_id' => $property->id,
                'response_code' => 200,
                'data' => [
                    'recent_certificates' => $recentCertificates->map($format),
                    'all_certificates' => $certificates->map($format),
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function applianceFeedbackForm(Request $request)
    {
        // Check token
        if (!$request->bearerToken()) {
            return response()->json([
                'status' => false,
                'message' => 'Token is required.',
            ], 401);
        }

        if (!$request->user()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        $validated = $request->validate([
            'appliance_id' => 'nullable|exists:appliances,id',
            'property_id' => 'required|exists:properties,id',
            'message' => 'required|string',

            // SAME FORMAT AS SERVICE API
            'image.*' => 'nullable|file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png',
            'video.*' => 'nullable|mimetypes:video/mp4,video/mpeg|max:200000',
        ]);

        $uploadedImages = [];
        $uploadedVideos = [];

        // MULTIPLE IMAGES
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $path = $file->store('feedback/images', 'public');
                $uploadedImages[] = 'uploads/' . $path;
            }
        }

        // MULTIPLE VIDEOS
        if ($request->hasFile('video')) {
            foreach ($request->file('video') as $file) {
                $path = $file->store('feedback/videos', 'public');
                $uploadedVideos[] = 'uploads/' . $path;
            }
        }

        $feedback = \App\Models\ApplianceFeedback::create([
            'appliance_id' => $validated['appliance_id'] ?? null,
            'property_id' => $validated['property_id'],
            'message' => $validated['message'],
            'image' => json_encode($uploadedImages),
            'video' => json_encode($uploadedVideos),
        ]);

        // Response same as your Issue API
        $response = $feedback->toArray();
        $response['image'] = $uploadedImages;
        $response['video'] = $uploadedVideos;

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'data' => $response
        ], 201);
    }

    /* Profile Update */
    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name'       => 'nullable|string|max:50',
            'last_name'        => 'nullable|string|max:50',
            'company_name'        => 'nullable|string',
            'profile_picture'  => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'service_specialisation' => 'nullable|exists:specializations,id',
            'latitude'  => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address'   => 'nullable|string',
        ]);

        // Update name fields
        if ($request->filled('first_name')) {
            $user->first_name = $request->first_name;
        }

        if ($request->filled('last_name')) {
            $user->last_name = $request->last_name;
        }

        if ($request->filled('first_name') || $request->filled('last_name')) {
            $user->name = trim($request->first_name . ' ' . $request->last_name);
        }

        if ($request->role === 'service_provider') {
            $user->service_specialisation = $request->service_specialisation;
            $user->company_name = $request->company_name;
        } else {
            $user->service_specialisation = null;
            $user->company_name = null;
        }

        $profilePicture = $user->profile_picture;

        if ($request->hasFile('profile_picture')) {

            if ($profilePicture) {
                Storage::disk('public')->delete(str_replace('storage/', '', $profilePicture));
            }

            $path = $request->file('profile_picture')->store('profile', 'public');
            $profilePicture = 'storage/' . $path;
        }

        if ($request->filled('latitude')) {
            $user->latitude = $request->latitude;
        }

        if ($request->filled('longitude')) {
            $user->longitude = $request->longitude;
        }

        if ($request->filled('address')) {
            $user->address = $request->address;
        }


        $user->profile_picture = $profilePicture;
        $user->save();
        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => $user,
        ]);
    }

    /* Service History API */
    public function appliancesServiceHistory(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.'], 401);
            }

            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.'], 401);
            }

            if (!$request->has('property_id') || empty($request->property_id)) {
                return response()->json(['status' => false, 'message' => 'Property ID is required'], 422);
            }

            $property = Property::find($request->property_id);
            if (!$property) {
                return response()->json(['status' => false, 'message' => 'Property not found'], 404);
            }

            $issues = IssueReport::where('properties_id', $property->id)
                ->latest()
                ->get();

            $grouped = $issues->map(function ($report) {
                $images = json_decode($report->image, true);
                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;
                $applianceName = $report->appliance->appliance_name ?? null;
                $applianceImages = json_decode($report->appliance->appliances_images ?? '[]', true);
                $applianceFirstImage = is_array($applianceImages) && count($applianceImages) > 0 ? $applianceImages[0] : null;

                return [
                    'id' => $report->id,
                    'appliance_name' => $applianceName,
                    'appliance_image' => 'storage/' . $applianceFirstImage,
                    'issue_number' => $report->issue_number,
                    'issue_title' => $report->issue_title,
                    'issue_category' => $report->issue_category,
                    'issue_status' => $report->issue_status,
                    'issue_urgency_level' => $report->issue_urgency_level,
                    'reported_date' => $report->reported_date,
                    'image' => 'storage/' . $firstImage,
                    'property_title' => $report->property->property_title ?? null,
                ];
            })->groupBy('issue_status');
            return response()->json([
                'status' => true,
                'response_code' => 200,
                'data' => [
                    'property_id' => $property->id,
                    'service_history' => $grouped,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* Service History API Id Wise */
    public function getAppliancesServiceHistoryId(Request $request)
    {
        try {
            // Check bearer token
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.'], 401);
            }

            // Validate user from token
            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.'], 401);
            }

            // If issue_id passed → return single issue details
            if ($request->has('issue_id')) {
                $issue = IssueReport::with(['appliance', 'property'])
                    ->where('id', $request->issue_id)
                    ->first();

                if (!$issue) {
                    return response()->json(['status' => false, 'message' => 'Issue not found'], 404);
                }

                // Parse images
                $images = json_decode($issue->image, true);
                $firstImage = is_array($images) && count($images) ? $images[0] : null;

                $applianceImages = json_decode($issue->appliance->appliances_images ?? '[]', true);
                $applianceFirstImage = is_array($applianceImages) && count($applianceImages) ? $applianceImages[0] : null;

                return response()->json([
                    'status' => true,
                    'response_code' => 200,
                    'data' => [
                        'id' => $issue->id,
                        'appliance_name' => $issue->appliance->appliance_name ?? null,
                        'appliance_image' => 'storage/' . $applianceFirstImage,
                        'issue_number' => $issue->issue_number,
                        'issue_title' => $issue->issue_title,
                        'issue_category' => $issue->issue_category,
                        'issue_details' => $issue->issue_details,
                        'issue_status' => $issue->issue_status,
                        'issue_urgency_level' => $issue->issue_urgency_level,
                        'reported_date' => $issue->reported_date,
                        'image' => $firstImage,
                        'property_title' => $issue->property->property_title ?? null,
                        'service_provider' => $issue->assignedServiceProvider,
                    ]
                ], 200);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /* Page Content API */
    public function getPageContent(Request $request)
    {
        try {

            // Check for bearer token
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.'], 401);
            }

            // Validate user from token
            if (!$request->user()) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.'], 401);
            }

            // Validate incoming request
            $request->validate([
                'type' => 'required|string'
            ]);

            // Fetch data based on 'type'
            $pageContent = PageContent::where('type', $request->type)->get();

            if ($pageContent->isEmpty()) {
                return response()->json(['status' => false, 'response_code' => 200, 'message' => 'No page content found for this type.', 'data' => []], 404);
            }

            return response()->json(['status' => true, 'response_code' => 200, 'message' => 'Page content fetched successfully.', 'data' => $pageContent], 200);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'response_code' => 200, 'message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }

    /* House Plan ID wise fetched the appliances  */
    public function housePlanAmenities(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token || !$request->user()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        $validated = $request->validate([
            'house_plan_id' => 'required|integer|exists:house_plans,id',
        ]);

        $housePlan = HousePlan::find($validated['house_plan_id']);

        if (!$housePlan || empty($housePlan->floor_details)) {
            return response()->json([
                'status'        => true,
                'response_code' => 200,
                'message'       => 'No floor details found.',
                'data'          => []
            ]);
        }

        $floorDetails = $housePlan->floor_details;
        $response     = [];

        foreach ($floorDetails as $floorName => $floorData) {
            $applianceIds = $floorData['appliances'] ?? [];
            if (!is_array($applianceIds)) {
                $applianceIds = [];
            }
            $appliances = Appliance::whereIn('id', $applianceIds)->get();
            $appliances->transform(function ($appliance) {
                $imagesRaw = $appliance->appliances_images;

                if (is_string($imagesRaw)) {
                    $imagesRaw = json_decode($imagesRaw, true);
                }

                if (!is_array($imagesRaw)) {
                    $imagesRaw = [];
                }

                $appliance->appliances_images = array_map(
                    fn($img) => 'uploads/appliances_images/' . basename($img),
                    $imagesRaw
                );

                $manualsRaw = $appliance->manuals;

                if (is_string($manualsRaw)) {
                    $manualsRaw = json_decode($manualsRaw, true);
                }

                if (!is_array($manualsRaw)) {
                    $manualsRaw = [];
                }

                $appliance->manuals = array_map(
                    fn($file) => 'uploads/manuals/' . basename($file),
                    $manualsRaw
                );

                return $appliance;
            });

            $response[$floorName] = [
                'bedrooms'      => $floorData['bedrooms'] ?? null,
                'bathrooms'     => $floorData['bathrooms'] ?? null,
                'parking'       => $floorData['parking'] ?? null,
                'swimming_pool' => $floorData['swimming_pool'] ?? null,
                'floor_plan'    => $floorData['floor_plan'] ?? [],
                'appliances'    => $appliances
            ];
        }

        return response()->json([
            'status'        => true,
            'response_code' => 200,
            'message'       => 'Amenities fetched floor-wise successfully.',
            'data'          => $response
        ]);
    }

    /* Notification Listing */
    public function notificationListing(Request $request)
    {
        $user = $request->user(); // 🔐 FROM SANCTUM TOKEN

        if (!$user) {
            return response()->json([
                'response_code' => 401,
                'status' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $notifications = NotificationList::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $unreadCount = NotificationList::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => 'Notifications fetched successfully.',
            'unread_count' => $unreadCount,
            'data' => $notifications,
        ], 200);
    }


    /* Notification Read */
    public function markNotificationsAsRead(Request $request)
    {
        // Check for bearer token
        if (!$request->bearerToken()) {
            return response()->json([
                'response_code' => 401,
                'status'  => false,
                'message' => 'Token is required.'
            ], 401);
        }

        // Validate user from token
        if (!$request->user()) {
            return response()->json([
                'response_code' => 401,
                'status'  => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        // Validation
        $request->validate([
            'house_owner_id' => 'required|integer',
            'properties_id'  => 'required|integer'
        ]);


        $houseOwnerId = $request->house_owner_id;
        $propertyId   = $request->properties_id;


        // Update only unread notifications for this owner + property
        $updated = NotificationList::where('house_owner_id', $houseOwnerId)
            ->where('properties_id', $propertyId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => $updated > 0
                ? 'Notifications marked as read successfully.'
                : 'No unread notifications found.',
            'updated_count' => $updated
        ], 200);
    }

    public function searchAppliance(Request $request)
    {
        // Check for bearer token
        if (!$request->bearerToken()) {
            return response()->json([
                'response_code' => 401,
                'status'  => false,
                'message' => 'Token is required.'
            ], 401);
        }

        // Validate user from token
        if (!$request->user()) {
            return response()->json([
                'response_code' => 401,
                'status'  => false,
                'message' => 'Invalid or expired token.'
            ], 401);
        }

        // Validate inputs
        $request->validate([
            'property_id' => 'required|integer',
            'appliance_name' => 'nullable|string',
        ]);

        // Fetch property
        $property = Property::find($request->property_id);

        if (!$property) {
            return response()->json([
                'response_code' => 200,
                'status'  => false,
                'message' => 'Property not found.',
                'data' => []
            ], 200);
        }

        // Handle JSON string OR array input
        $applianceIds = $property->appliance_id;

        if (is_string($applianceIds)) {
            $applianceIds = json_decode($applianceIds, true);
        }

        if (!is_array($applianceIds)) {
            $applianceIds = [$applianceIds];
        }

        if (empty($applianceIds)) {
            return response()->json([
                'response_code' => 200,
                'status' => true,
                'message' => 'No appliances found for this property.',
                'data' => []
            ]);
        }

        // Fetch appliances with optional search
        $query = Appliance::whereIn('id', $applianceIds);

        if (!empty($request->appliance_name)) {
            $query->where('appliance_name', 'LIKE', '%' . $request->appliance_name . '%');
        }

        $appliances = $query->get();

        // If searching by appliance_name AND no results found
        if (!empty($request->appliance_name) && $appliances->isEmpty()) {
            return response()->json([
                'response_code' => 200,
                'status' => false,
                'message' => 'No appliances matched this name for the selected property.',
                'data' => []
            ], 200);
        }

        // Map appliances to include images (same as getApplianceById format)
        $appliancesData = $appliances->map(function ($appliance) {
            $images = collect(json_decode($appliance->appliances_images, true) ?? [])
                ->map(fn($img) => $img)
                ->values();

            $manuals = [];
            if (!empty($appliance->manuals)) {
                $decodedManuals = json_decode($appliance->manuals, true);
                if (is_array($decodedManuals)) {
                    $manuals = array_map(function ($file) {
                        return 'uploads/manuals/' . basename($file);
                    }, $decodedManuals);
                } else {
                    $manuals[] = 'uploads/manuals/' . basename($appliance->manuals);
                }
            }

            return [
                'appliance_id' => $appliance->id,
                'appliance_name' => $appliance->appliance_name,
                'brand_name' => $appliance->brand_name,
                'model' => $appliance->model,
                'warranty_information' => $appliance->warranty_information,
                'appliances_images' => $images,
                'manuals' => $manuals,
            ];
        });

        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => 'Appliances fetched successfully.',
            'data' => $appliancesData
        ]);
    }

    public function emailUs(Request $request)
    {

        $request->validate([
            'house_owner_id' => 'required|exists:house_owners,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);


        // Store in database
        $ticket = ServiceTicket::create([
            'house_owner_id' => $request->house_owner_id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Send email to admin
        $adminEmail = 'mediaclockdev@gmail.com'; // Replace with your admin email
        Mail::to($adminEmail)->send(new ServiceTicketMail($ticket));

        return response()->json([
            'status' => true,
            'message' => 'Service ticket submitted successfully.',
            'data' => $ticket
        ]);
    }

    /* Service Proiver Issue Listing */
    public function getIssuesByUser(Request $request)
    {
        $request->validate([
            'user_id'      => 'required|exists:users,id',
            'issue_status' => 'nullable|string'
        ]);

        $issues = IssueReport::with(['property', 'appliance', 'reporter'])
            ->where('service_provider', $request->user_id)
            ->when($request->issue_status, function ($query) use ($request) {
                $query->where('issue_status', $request->issue_status);
            })
            // If issue_status is empty, exclude accepted & declined
            ->when(!$request->filled('status'), function ($query) {
                $query->whereNotIn('status', ['accepted', 'declined']);
            })
            ->latest()
            ->get()
            ->map(function ($issue) {

                $images = [];

                if (!empty($issue->image)) {

                    // Decode JSON if stored as string
                    $decodedImages = is_string($issue->image)
                        ? json_decode($issue->image, true)
                        : $issue->image;

                    if (is_array($decodedImages)) {
                        $images = array_map(function ($img) {
                            return 'storage/' . ltrim($img, '/');
                        }, $decodedImages);
                    }
                }

                // Assign formatted images
                $issue->image = array_values($images);

                return $issue;
            });

        return response()->json([
            'status'  => true,
            'message' => 'Issue reports fetched successfully',
            'data'    => $issues
        ], 200);
    }

    public function getIssuesAcceptedList(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status'  => 'nullable|string'
        ]);
        $issues = IssueReport::with(['property', 'appliance', 'reporter'])
            ->where('service_provider', $request->user_id)
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->get()
            ->map(function ($issue) {

                $images = [];

                if (!empty($issue->image)) {

                    // Decode JSON if stored as string
                    $decodedImages = is_string($issue->image)
                        ? json_decode($issue->image, true)
                        : $issue->image;

                    if (is_array($decodedImages)) {
                        $images = array_map(function ($img) {
                            return 'storage/' . ltrim($img, '/');
                        }, $decodedImages);
                    }
                }

                // Assign formatted images
                $issue->image = array_values($images);

                return $issue;
            });

        return response()->json([
            'status'  => true,
            'message' => 'Issue reports fetched successfully',
            'data'    => $issues
        ], 200);
    }

    /* Accept/Declined Issue Report By Service Provider */
    public function updateIssueReportByServiceProvider(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'issue_report_id' => 'required|exists:issue_reports,id',
            'status'          => 'required|in:accepted,declined',
        ]);

        // Fetch issue report
        $issueReport = IssueReport::find($validated['issue_report_id']);

        // Update status
        $issueReport->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Issue status updated successfully.',
            'data'    => [
                'issue_report_id' => $issueReport->id,
                'status'          => $issueReport->status,
            ],
        ], 200);
    }

    /* Update Availability Preferences By Service Provider */
    public function updateServiceProviderAvailabilityPreferences(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'days' => 'sometimes|array',
            'days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'time_from' => 'required_with:days|date_format:H:i',
            'time_to' => 'required_with:days|date_format:H:i|after:time_from',
            'availability' => 'sometimes|array'
        ]);

        $user = User::find($request->user_id);
        if ($user->role !== 'service_provider') {
            return response()->json([
                'status' => true,
                'message' => 'User is not a service provider'
            ], 200);
        }

        if ($request->has('days')) {
            $availabilityPreferences = [
                'days' => $request->days,
                'time' => [
                    'from' => $request->time_from,
                    'to'   => $request->time_to,
                ]
            ];
        }

        if ($request->has('availability')) {
            $availabilityPreferences = $request->availability;
        }

        $user->availability_preferences = $availabilityPreferences;
        $user->save();
        return response()->json([
            'status' => true,
            'message' => 'Availability preferences updated successfully',
            'data' => $user
        ]);
    }

    public function fetchServiceSpecialization()
    {
        $specializations = Specialization::where('status', 'active')
            ->select('id', 'specialization', 'status')
            ->orderBy('specialization', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Service specializations fetched successfully',
            'data' => $specializations
        ], 200);
    }

    public function updateServiceProvidersCoverage(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'coverage' => 'required|integer|min:1|max:100'
            ]);

            $user = User::find($request->user_id);
            if (!$user) {
                Log::warning('User not found', [
                    'user_id' => $request->user_id
                ]);
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }
            if ($user->role !== 'service_provider') {
                return response()->json([
                    'status' => false,
                    'message' => 'User is not a service provider'
                ], 400);
            }
            $user->coverage = $request->coverage;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'Coverage areas updated successfully',
                'data' => $user,

            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /* Update Issue Statue by Service Provider */

    public function requestIssueCompletion(Request $request)
    {
        $validated = $request->validate([
            'issue_report_id' => 'required|exists:issue_reports,id',
            'issue_status'    => 'required|string|max:255',
        ]);

        $issueReport = IssueReport::findOrFail($validated['issue_report_id']);

        // Generate OTP
        $otp = rand(100000, 999999);

        IssueOtp::create([
            'issue_report_id' => $issueReport->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5), // ✅ REQUIRED
        ]);


        // Get house owners
        $houseOwners = User::where('id', $issueReport->reported_by)->get();

        foreach ($houseOwners as $owner) {
            Mail::to($owner->email)->send(
                new IssueCompletionOtpMail($otp, $issueReport)
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to house owner email for verification.',
        ]);
    }


    public function completeIssueReportByServiceProvider(Request $request)
    {
        $issueReportId = $request->input('issue_report_id');
        $issueStatus   = $request->input('issue_status');
        $otp           = $request->input('otp');

        if (!$issueReportId || !$issueStatus || !$otp) {
            return response()->json([
                'status' => false,
                'message' => 'Required fields are missing.',
            ], 422);
        }

        $otpRecord = IssueOtp::where('issue_report_id', $issueReportId)
            ->where('otp', $otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $issueReport = IssueReport::find($issueReportId);

        if (!$issueReport) {
            return response()->json([
                'status' => false,
                'message' => 'Issue report not found.',
            ], 404);
        }

        $issueReport->update([
            'issue_status' => $issueStatus,
        ]);
        $otpRecord->delete();
        $houseOwners = User::where('id', $issueReport->reported_by)->get();
        foreach ($houseOwners as $owner) {
            Mail::to($owner->email)->send(
                new IssueCompletedMail($issueReport)
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Issue status updated successfully.',
            'data'    => [
                'issue_report_id' => $issueReport->id,
                'issue_status'    => $issueReport->issue_status,
            ],
        ], 200);
    }



    public function getServiceHistoryByUser(Request $request)
    {
        $user = $request->user();
        $issues = IssueReport::with(['property', 'appliance', 'reporter'])
            ->where('service_provider', $user->id)
            ->latest()
            ->get()
            ->map(function ($issue) {

                $images = [];

                if (!empty($issue->image)) {
                    $decodedImages = is_string($issue->image)
                        ? json_decode($issue->image, true)
                        : $issue->image;

                    if (is_array($decodedImages)) {
                        $images = array_map(function ($img) {
                            return asset('storage/' . ltrim($img, '/'));
                        }, $decodedImages);
                    }
                }

                $issue->image = $images;

                return $issue;
            });

        $groupedIssues = $issues->groupBy(function ($item) {
            return strtolower($item->issue_status);
        });

        return response()->json([
            'status' => true,
            'response_code' => 200,
            'data' => [
                'service_history' => [
                    'inprogress' => $groupedIssues->get('inprogress', []),
                    'pending'   => $groupedIssues->get('pending', []),
                    'completed' => $groupedIssues->get('completed', []),
                    'cancelled' => $groupedIssues->get('cancelled', []),
                ]
            ]
        ], 200);
    }
}
