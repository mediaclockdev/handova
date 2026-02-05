<?php

namespace App\Http\Controllers\Admin;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use App\Services\FirebaseService;
use App\Http\Controllers\Controller;
use App\Models\HouseOwner;
use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\HouseOwnerAssignedMail;
use App\Models\User;
use App\Models\NotificationList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HouseOwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $houseOwners = HouseOwner::with('property')->where('user_id', auth()->id())->latest()->paginate(10);
        $properties = Property::select('id', 'property_title')->where('user_id', Auth::id())->get();
        return view('admin.house_owners.index', compact('houseOwners', 'properties'));
    }

    public function getOwners($propertyId)
    {
        $owners = HouseOwner::with('property')->where('properties_id', $propertyId)->get()
            ->map(function ($owner) {
                $owner->property_title = $owner->property->property_title ?? '';
                return $owner;
            });

        return response()->json($owners);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $properties = Property::select('id', 'property_title')
            ->where('user_id', Auth::id())
            ->get();

        $formTitle = 'New House Owner';

        // Generate unique House Owner ID safely
        $prefix = 'HO-';
        $nextNumber = 1;

        do {
            $houseOwnerId = $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $exists = \App\Models\HouseOwner::where('house_owner_id', $houseOwnerId)->exists();
            $nextNumber++;
        } while ($exists);
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';

        return view('admin.house_owners.create', compact(
            'properties',
            'formTitle',
            'houseOwnerId',
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }

    public function sendPushToUser($user, $title, $body, $extraData = [])
    {
        $fcm = new FirebaseService();

        $token = $user->fcm_token;
        $deviceType = strtolower($user->device_type ?? '');

        if (!$token) {
            Log::warning("Push notification not sent. User {$user->id} has no FCM token.");
            return ['error' => 'User has no FCM token'];
        }

        if (!in_array($deviceType, ['android', 'ios'])) {
            Log::warning("Push notification not sent. Unknown device type '{$user->device_type}' for user {$user->id}");
            return ['error' => 'Unknown device type'];
        }

        if ($deviceType === 'android') {
            $data = array_merge($extraData, [
                'title' => $title,
                'body'  => $body,
                'channelId' => 'default'
            ]);

            // Convert all values to strings for FCM
            $data = array_map(fn($value) => (string)$value, $data);

            $result = $fcm->sendAndroidPush($token, $data, $title, $body);
            Log::info("Android push notification sent to user {$user->id}{$token}", ['result' => $result]);
            return $result;
        }

        if ($deviceType === 'ios') {
            $result = $fcm->sendIosPush($token, $extraData, $title, $body);
            Log::info("iOS push notification sent to user {$user->id}", ['result' => $result]);
            return $result;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ================= VALIDATION (outside try) =================
        $validated = $request->validate([
            'house_owner_id'        => 'required|unique:house_owners,house_owner_id',
            'properties_id'         => 'required|exists:properties,id',
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'email_address'         => 'required|email',
            'country_code'          => 'required|string|min:2|max:6',
            'phone_number'          => 'required|string|min:6|max:20',
            'address_of_property'   => 'required|string',
            'house_plan_name'       => 'nullable|string|max:255',
            'build_completion_date' => 'nullable|date',
            'assigned_builder_site_manager' => 'nullable|string|max:255',
            'number_of_bedrooms'    => 'nullable|integer',
            'number_of_bathrooms'   => 'nullable|integer',
            'parking'               => 'nullable|string|max:255',
            'handover_documents'    => 'nullable|array',
            'handover_documents.*'  => 'file|mimes:jpg,jpeg,png,pdf,csv,doc,docx|max:2048',
            'floor_plan_upload'     => 'nullable|array',
            'floor_plan_upload.*'   => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'property_status'       => 'required|string|max:255',
            'tags'                  => 'nullable|string',
            'internal_notes'        => 'nullable|string',
        ]);

        try {
            $data = $request->except(['handover_documents', 'floor_plan_upload']);

            // ================= PHONE VALIDATION =================
            $phoneUtil   = PhoneNumberUtil::getInstance();
            $rawPhone    = trim($validated['phone_number']);
            $countryCode = trim($validated['country_code']);
            $cleanPhone  = preg_replace('/\D+/', '', $rawPhone);

            try {
                if (str_starts_with($rawPhone, '+')) {
                    $number = $phoneUtil->parse($rawPhone, null);
                } else {
                    $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
                }

                if (! $phoneUtil->isValidNumber($number)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'phone_number' => 'Invalid phone number for selected country.',
                        ]);
                }

                $data['phone_number'] = $phoneUtil->format(
                    $number,
                    PhoneNumberFormat::E164
                );
            } catch (NumberParseException $e) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'phone_number' => 'Invalid phone number format.',
                    ]);
            }

            if ($request->hasFile('handover_documents')) {
                $handoverDocs = [];
                foreach ($request->file('handover_documents') as $file) {
                    $handoverDocs[] = $file->store('handover_docs', 'public');
                }
                $data['handover_documents'] = json_encode($handoverDocs);
            }

            if ($request->hasFile('floor_plan_upload')) {
                $floorPlans = [];
                foreach ($request->file('floor_plan_upload') as $file) {
                    $floorPlans[] = $file->store('floor_plans', 'public');
                }
                $data['floor_plan_upload'] = json_encode($floorPlans);
            }

            $data['user_id']       = Auth::id();
            $data['properties_id'] = (int) $validated['properties_id'];

            $alreadyAssigned = HouseOwner::where('properties_id', $data['properties_id'])->first();
            if ($alreadyAssigned) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'properties_id' => 'This property is already assigned to another house owner.',
                    ]);
            }

            HouseOwner::create($data);

            $houseOwner = HouseOwner::create($validated);

            // Send email to owner
            $builderId = 'Handova-' . Auth::id();
            Mail::to($houseOwner->email_address)->send(new HouseOwnerAssignedMail($houseOwner, $builderId));

            // Send push notification
            $user = User::where('email', $houseOwner->email_address)->first();
            if ($user) {
                $pushResult = $this->sendPushToUser(
                    $user,
                    "New Assignment",
                    "You have been assigned a new house by {$builderId}",
                    [
                        "type" => "assignment",
                        "house_owner_id" => (string)$user->id, // convert to string
                        "builder_id" => $builderId
                    ],
                    "high"
                );

                Log::info("Push notification result for user {$user->id}", ['pushResult' => $pushResult]);


                NotificationList::create([
                    'properties_id'   => $validated['properties_id'],
                    'house_owner_id'  => $user->id,
                    'title'           => "New Assignment",
                    'body'            => "You have been assigned a new house by {$builderId}",
                    'is_read'         => false,
                ]);
            }

            return redirect()
                ->route('admin.house_owners.index')
                ->with('success', 'House Owner added successfully.');
        } catch (\Throwable $e) {
            Log::error('HouseOwner store failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $owner = HouseOwner::findOrFail($id);
        $phoneUtil = PhoneNumberUtil::getInstance();

        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';

        if (!empty($owner->phone_number)) {
            $number = $phoneUtil->parse($owner->phone_number, 'AU');

            $countryCode = '+' . $number->getCountryCode();
            $nationalNumber = $number->getNationalNumber();
            $countryIso = strtolower(
                $phoneUtil->getRegionCodeForNumber($number)
            );
        }

        $properties = Property::select('id', 'property_title')->get();
        $formTitle = 'Update House Owner';
        $houseOwnerId = $owner->house_owner_id;

        return view('admin.house_owners.edit', compact(
            'owner',
            'properties',
            'formTitle',
            'houseOwnerId',
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $houseOwner = HouseOwner::findOrFail($id);

        // ================= VALIDATION =================
        $validated = $request->validate([
            'properties_id'             => 'required|exists:properties,id',
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'email_address'             => 'required|email',

            'country_code'              => 'nullable|string|max:5',
            'phone_number'              => 'required|string|min:6|max:20',

            'address_of_property'       => 'required|string',
            'house_plan_name'           => 'nullable|string|max:255',
            'build_completion_date'     => 'nullable|date',
            'assigned_builder_site_manager' => 'nullable|string|max:255',
            'number_of_bedrooms'        => 'nullable|integer',
            'number_of_bathrooms'       => 'nullable|integer',
            'parking'                   => 'nullable|string|max:255',
            'property_status'           => 'required|string|max:255',
            'tags'                      => 'nullable|string',
            'internal_notes'            => 'nullable|string',

            'handover_documents'        => 'nullable|array',
            'handover_documents.*'      => 'file|mimes:jpg,jpeg,png,pdf,csv,doc,docx|max:2048',

            'floor_plan_upload'         => 'nullable|array',
            'floor_plan_upload.*'       => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'existing_handover_documents' => 'nullable|array',
            'existing_handover_documents.*' => 'string',

            'existing_floor_plan_upload' => 'nullable|array',
            'existing_floor_plan_upload.*' => 'string',

            'remove_handover_documents' => 'nullable|array',
            'remove_handover_documents.*' => 'string',

            'remove_floor_plan_upload'  => 'nullable|array',
            'remove_floor_plan_upload.*' => 'string',
        ]);

        // ================= DATA PREP =================
        $data = $request->except([
            'handover_documents',
            'floor_plan_upload',
            'existing_handover_documents',
            'existing_floor_plan_upload',
            'remove_handover_documents',
            'remove_floor_plan_upload',
        ]);

        $data['properties_id'] = (int) $validated['properties_id'];

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($validated['phone_number']);
        $countryCode = trim($validated['country_code'] ?? '');
        $cleanPhone  = preg_replace('/\D+/', '', $rawPhone);

        try {
            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse($rawPhone, null);
            } else {
                if (empty($countryCode)) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'country_code' => 'Country code is required.',
                        ]);
                }

                $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
            }

            if (! $phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'phone_number' => 'Invalid phone number for selected country.',
                    ]);
            }

            $data['phone_number'] = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'phone_number' => 'Invalid phone number format.',
                ]);
        }

        // ================= FILE HANDLING =================
        $handoverDocs = $request->input('existing_handover_documents', []);
        $floorPlans   = $request->input('existing_floor_plan_upload', []);

        if ($request->filled('remove_handover_documents')) {
            foreach ($request->remove_handover_documents as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if ($request->filled('remove_floor_plan_upload')) {
            foreach ($request->remove_floor_plan_upload as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if ($request->hasFile('handover_documents')) {
            foreach ($request->file('handover_documents') as $file) {
                $handoverDocs[] = $file->store('handover_docs', 'public');
            }
        }

        if ($request->hasFile('floor_plan_upload')) {
            foreach ($request->file('floor_plan_upload') as $file) {
                $floorPlans[] = $file->store('floor_plans', 'public');
            }
        }

        $data['handover_documents'] = json_encode($handoverDocs);
        $data['floor_plan_upload']  = json_encode($floorPlans);
        $data['user_id']            = Auth::id();

        // ================= UPDATE =================
        $houseOwner->update($data);

        // Send email to owner
        $builderId = 'Handova-' . Auth::id();
        Mail::to($houseOwner->email_address)->send(new HouseOwnerAssignedMail($houseOwner, $builderId));

        // Send push notification
        $user = User::where('email', $houseOwner->email_address)->first();
        if ($user) {
            $pushResult = $this->sendPushToUser(
                $user,
                "New Assignment",
                "You have been assigned a new house by {$builderId}",
                [
                    "type" => "assignment",
                    "house_owner_id" => (string)$user->id, // convert to string
                    "builder_id" => $builderId
                ],
                "high"
            );

            Log::info("Push notification result for user {$user->id}", ['pushResult' => $pushResult]);


            NotificationList::create([
                'user_id'         => $user->id,
                'properties_id'   => $validated['properties_id'],
                'house_owner_id'  => $user->id,
                'title'           => "New Assignment",
                'body'            => "You have been assigned a new house by {$builderId}",
                'is_read'         => false,
            ]);
        }

        return redirect()
            ->route('admin.house_owners.index')
            ->with('success', 'House Owner updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $houseOwner = HouseOwner::findOrFail($id);
        $houseOwner->delete();
        return redirect()->route('admin.house_owners.index')
            ->with('success', 'House Owner deleted successfully.');
    }

    public function deleteImage(HouseOwner $houseOwner, $type, $index)
    {
        if (in_array($type, ['handover_documents', 'floor_plan_upload'])) {
            $files = $houseOwner->$type;
            if (isset($files[$index])) {
                Storage::disk('public')->delete($files[$index]);
                unset($files[$index]);
                $houseOwner->$type = array_values($files);
                $houseOwner->save();
            }
        }
        return back()->with('success', 'Image deleted.');
    }
}
