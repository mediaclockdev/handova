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
                return response()->json(['status' => false, 'message' => 'No house owners found for this user',], 404);
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

            // Get the appliance IDs from property
            $applianceIds = $property->appliance_id;

            if (is_null($applianceIds)) {
                $applianceIds = [];
            } elseif (!is_array($applianceIds)) {
                $decoded = json_decode($applianceIds, true);
                $applianceIds = is_array($decoded) ? $decoded : [$applianceIds];
            }

            // Fetch appliances
            $appliances = Appliance::whereIn('id', $applianceIds)->get();

            // Format appliances for API response
            $appliancesFormatted = $appliances->map(function ($appliance) {
                return [
                    'id' => $appliance->id,
                    'appliance_id' => $appliance->appliance_id,
                    'appliance_name' => $appliance->appliance_name,
                    'product_details' => $appliance->product_details,
                    'brand_name' => $appliance->brand_name,
                    'model' => $appliance->model,
                    'warranty_information' => $appliance->warranty_information,
                    'manuals' => json_decode($appliance->manuals) ?: [],
                    'appliances_images' => json_decode($appliance->appliances_images) ?: [],
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
                        'image' => json_decode($report->image) ?: [],
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

    public function getApplianceById(Request $request)
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

            // Validate appliance_id
            if (!$request->has('appliance_id') || empty($request->appliance_id)) {
                return response()->json(['status' => false, 'message' => 'Appliance ID is required',], 422);
            }

            // Fetch appliance
            $appliance = Appliance::find($request->appliance_id);

            if (!$appliance) {
                return response()->json(['status' => false, 'message' => 'Appliance not found',], 404);
            }

            // Decode manuals and images
            $manuals = collect(json_decode($appliance->manuals, true))
                ->map(function ($manual) {
                    $extension = pathinfo($manual, PATHINFO_EXTENSION);
                    return [
                        'format' => $extension,
                        'url' => $manual, // return DB value
                    ];
                })
                ->values();


            $applianceImages = collect(json_decode($appliance->appliances_images, true))
                ->map(fn($img) => $img) // return DB value only
                ->values();


            // Structure data for mobile
            $applianceData = [
                'product' => [
                    'appliance_id' => $appliance->id,
                    'appliance_name' => $appliance->appliance_name,
                    'product_details' => $appliance->product_details,
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

            // Return success response
            return response()->json(['status' => true, 'resoonse_code' => 200, 'data' => $applianceData,], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation failed', 'errors' => $e->errors(),], 422);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong', 'error' => $e->getMessage(),], 500);
        }
    }

    /* Request Appliance Service */
    public function requestApplianceService(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'properties_id' => 'required|exists:properties,id',
                'appliance_id' => 'required|exists:appliances,id',
                'issue_title' => 'required|string|max:255',
                'issue_category' => 'nullable|string|max:255',
                'issue_location' => 'nullable|string|max:255',
                'customer_contact' => 'nullable|string|max:50',
                'issue_details' => 'required|string',
                'reported_by' => 'required|exists:users,id',
                'reported_date' => 'required|date',
                'assigned_to_service_provider' => 'nullable|in:yes,no',
                'service_provider' => 'nullable|integer',
                'issue_status' => 'required|string|max:255',
                'issue_urgency_level' => 'required|string|max:255',
                'image.*' => 'nullable|file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png',
            ]);

            // Generate auto Issue Number
            $lastIssueNumber = IssueReport::select('issue_number')
                ->where('issue_number', 'LIKE', 'Issue-%')
                ->orderByRaw('CAST(SUBSTRING(issue_number, 7) AS UNSIGNED) DESC')
                ->first();

            $nextNumber = ($lastIssueNumber && preg_match('/Issue-(\d+)/', $lastIssueNumber->issue_number, $matches))
                ? (int)$matches[1] + 1
                : 1;

            $newIssueNumber = 'Issue-' . $nextNumber;

            // Upload Images
            $uploaded = [];

            if ($request->hasFile('image')) {
                foreach ($request->file('image') as $file) {
                    $path = $file->store('issue_report', 'public');
                    $uploaded[] = 'uploads/' . $path;
                }
            }

            // Create Issue
            $issue = IssueReport::create([
                'issue_number' => $newIssueNumber,
                'properties_id' => $validatedData['properties_id'],
                'appliance_id' => $validatedData['appliance_id'],
                'issue_title' => $validatedData['issue_title'],
                'issue_category' => $validatedData['issue_category'] ?? null,
                'issue_location' => $validatedData['issue_location'] ?? null,
                'customer_contact' => $validatedData['customer_contact'] ?? null,
                'issue_details' => $validatedData['issue_details'],
                'reported_by' => $validatedData['reported_by'],
                'reported_date' => $validatedData['reported_date'],
                'assigned_to_service_provider' => $validatedData['assigned_to_service_provider'] ?? 'no',
                'service_provider' => $validatedData['service_provider'] ?? null,
                'issue_status' => $validatedData['issue_status'],
                'issue_urgency_level' => $validatedData['issue_urgency_level'],
                'image' => json_encode($uploaded),
            ]);

            // Convert DB model to array and replace image with decoded JSON
            $issueArray = $issue->toArray();
            $issueArray['image'] = $uploaded; // decoded array

            return response()->json([
                'status' => true,
                'message' => 'Issue created successfully',
                'data' => $issueArray
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed in requestApplianceService', [
                'errors' => $e->errors(),
                'input'  => $request->all(),    // what user sent
                'files'  => $request->file(),   // uploaded files
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                $e->errors(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Exception in requestApplianceService', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getHousePlanByProperty(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|integer|exists:properties,id',
        ]);

        $propertyId = $validated['property_id'];

        // Fetch property
        $property = Property::find($propertyId);
       
        // Fetch all house plans for this property
        $housePlans = HousePlan::where('id', $property->house_plan_id)->get();

        if ($housePlans->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No house plans found for this property.',
            ], 404);
        }

        // Decode JSON fields for each plan
        $housePlans->transform(function ($plan) {
            $plan->floor_plan = json_decode($plan->floor_plan, true);
            $plan->appliance_id = json_decode($plan->appliance_id, true);
            return $plan;
        });

        // Get appliance IDs from property
        $applianceIds = $property->appliance_id;

        if (is_null($applianceIds)) {
            $applianceIds = [];
        } elseif (!is_array($applianceIds)) {
            $decoded = json_decode($applianceIds, true);
            $applianceIds = is_array($decoded) ? $decoded : [$applianceIds];
        }

        // Fetch appliances related to property
        $appliances = Appliance::whereIn('id', $applianceIds)->get();

        // Format each appliance’s images and manuals
        $appliances->transform(function ($appliance) {
            // Handle appliance images
            $applianceImages = [];
            if (!empty($appliance->appliances_images)) {
                $decodedImages = json_decode($appliance->appliances_images, true);
                if (is_array($decodedImages)) {
                    $applianceImages = array_map(function ($img) {
                        return 'uploads/appliances_images/' . basename($img);
                    }, $decodedImages);
                } else {
                    $applianceImages[] = 'uploads/appliances_images/' . basename($appliance->appliances_images);
                }
            }

            // Handle manuals
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

            $appliance->appliances_images = $applianceImages;
            $appliance->manuals = $manuals;

            return $appliance;
        });

        // Final response
        return response()->json([
            'success' => true,
            'response_code' => 200,
            'message' => 'House plans retrieved successfully.',
            'data' => [
                'house_plans' => $housePlans->isNotEmpty() ? $housePlans : [],
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

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'nullable|string|max:50',
            'last_name'  => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048'
        ]);

        // Update name fields
        if ($request->first_name) {
            $user->first_name = $request->first_name;
        }

        if ($request->last_name) {
            $user->last_name = $request->last_name;
        }

        $user->name = $request->first_name . ' ' . $request->last_name;

        // Image upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile', 'public');
            $user['profile_picture'] = 'uploads/' . $path;
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /* Service History API */
    public function appliancesServiceHistory(Request $request)
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

            // Validate property_id
            if (!$request->has('property_id') || empty($request->property_id)) {
                return response()->json(['status' => false, 'message' => 'Property ID is required'], 422);
            }

            // Fetch property
            $property = Property::find($request->property_id);
            if (!$property) {
                return response()->json(['status' => false, 'message' => 'Property not found'], 404);
            }

            // Fetch issue reports for this property
            $issues = IssueReport::where('properties_id', $property->id)
                ->latest()
                ->get();

            // Group by issue_status using ->groupBy()
            $grouped = $issues->map(function ($report) {
                // Decode image
                $images = json_decode($report->image, true);

                // Get only the first image (or null if empty)
                $firstImage = is_array($images) && count($images) > 0 ? $images[0] : null;

                $applianceName = $report->appliance->appliance_name ?? null;

                // Appliance image (stored as JSON array or single value depending on your DB)
                $applianceImages = json_decode($report->appliance->appliances_images ?? '[]', true);
                $applianceFirstImage = is_array($applianceImages) && count($applianceImages) > 0 ? $applianceImages[0] : null;

                return [
                    'id' => $report->id,
                    'appliance_name' => $applianceName,
                    'appliance_image' => $applianceFirstImage,
                    'issue_number' => $report->issue_number,
                    'issue_title' => $report->issue_title,
                    'issue_category' => $report->issue_category,
                    'issue_status' => $report->issue_status,
                    'issue_urgency_level' => $report->issue_urgency_level,
                    'reported_date' => $report->reported_date,
                    'image' => $firstImage,
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
                        'appliance_image' => $applianceFirstImage,
                        'issue_number' => $issue->issue_number,
                        'issue_title' => $issue->issue_title,
                        'issue_category' => $issue->issue_category,
                        'issue_details' => $issue->issue_details,
                        'issue_status' => $issue->issue_status,
                        'issue_urgency_level' => $issue->issue_urgency_level,
                        'reported_date' => $issue->reported_date,
                        'image' => $firstImage,
                        'property_title' => $issue->property->property_title ?? null,
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

    public function housePlanAmenities(Request $request)
    {
        // Check for bearer token
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['status' => false, 'message' => 'Token is required.',], 401);
        }

        // Validate user from token
        if (!$request->user()) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired token.',], 401);
        }

        // Validate request
        $validated = $request->validate([
            'house_plan_id' => 'required|integer',
        ]);

        $housePlanId = $validated['house_plan_id'];

        // Fetch house plan
        $housePlan = HousePlan::where('id', $housePlanId)->first();

        if (!$housePlan) {
            //return response()->json(['status' => true, 'response_code' => 200, 'message' => 'House plan not found.', 'appliances' => [],], 200);

            return response()->json([
                'status' => true,
                'response_code' => 200,
                'message' => 'House plan not found.',
                'data' => [
                    'appliances' =>  []
                ],
            ], 200);
        }

        // Decode JSON fields
        $housePlan->floor_plan = json_decode($housePlan->floor_plan, true);
        $housePlan->appliance_id = json_decode($housePlan->appliance_id, true);

        // Appliance IDs
        $applianceIds = $housePlan->appliance_id;

        if (is_null($applianceIds)) {
            $applianceIds = [];
        } elseif (!is_array($applianceIds)) {
            $decoded = json_decode($applianceIds, true);
            $applianceIds = is_array($decoded) ? $decoded : [$applianceIds];
        }

        // Fetch related appliances
        $appliances = Appliance::whereIn('id', $applianceIds)->get();

        // Format appliances
        $appliances->transform(function ($appliance) {

            // Format images
            $images = [];
            if (!empty($appliance->appliances_images)) {
                $decoded = json_decode($appliance->appliances_images, true);

                if (is_array($decoded)) {
                    $images = array_map(function ($img) {
                        return 'uploads/appliances_images/' . basename($img);
                    }, $decoded);
                } else {
                    $images[] = 'uploads/appliances_images/' . basename($appliance->appliances_images);
                }
            }

            // Format manuals
            $manuals = [];
            if (!empty($appliance->manuals)) {
                $decoded = json_decode($appliance->manuals, true);

                if (is_array($decoded)) {
                    $manuals = array_map(function ($file) {
                        return 'uploads/manuals/' . basename($file);
                    }, $decoded);
                } else {
                    $manuals[] = 'uploads/manuals/' . basename($appliance->manuals);
                }
            }

            $appliance->appliances_images = $images;
            $appliance->manuals = $manuals;

            return $appliance;
        });

        return response()->json([
            'status' => true,
            'response_code' => 200,
            'message' => 'Amenities fetched successfully.',
            'data' => [
                'appliances' => $appliances->isNotEmpty() ? $appliances : []
            ],
        ]);
    }

    /* Notification Listing */
    public function notificationListing(Request $request)
    {
        // Check for bearer token
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['response_code' => 401, 'status'  => false, 'message' => 'Token is required.'], 401);
        }

        // Validate user from token
        if (!$request->user()) {
            return response()->json(['response_code' => 401, 'status'  => false, 'message' => 'Invalid or expired token.'], 401);
        }

        // Validation
        $request->validate([
            'house_owner_id' => 'required|integer',
            'properties_id' => 'required|integer',
        ]);

        $houseOwnerId = $request->house_owner_id;
        $propertyId   = $request->properties_id;

        // Fetch notifications (filter by house_owner_id + property_id)
        $notifications = NotificationList::where('house_owner_id', $houseOwnerId)
            ->where('properties_id', $propertyId)
            ->orderBy('id', 'desc')
            ->get();

        // Count unread notifications
        $unreadCount = NotificationList::where('house_owner_id', $houseOwnerId)
            ->where('properties_id', $propertyId)
            ->where('is_read', 0)
            ->count();

        // If no notifications found
        if ($notifications->isEmpty()) {
            return response()->json([
                'response_code' => 200,
                'status' => true,
                'message' => 'No notifications found.',
                'unread_count' => 0,
                'data' => []
            ], 200);
        }

        // Success
        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => 'Notifications fetched successfully.',
            'unread_count' => $unreadCount,
            'data' => $notifications
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
}
