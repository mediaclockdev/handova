<div id="basic-info" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label class="block text-sm text-neutral-900">Plan Name</label>
        <input type="text" id="plan_name" name="plan_name" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 form-control @error('plan_name') border-red-500 @enderror" placeholder="Enter plan name" value="{{ old('plan_name', $plan->plan_name ?? '') }}">
        @error('plan_name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="plan_type" class="block text-sm text-neutral-900">Plan Type</label>
        <select name="plan_type" id="plan_type" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_type') border-red-500 @enderror">
            <option value="">Select Plan Type</option>
            <option value="monthly" {{ old('plan_type', $plan->plan_type ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
            <option value="yearly" {{ old('plan_type', $plan->plan_type ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
            <option value="custom" {{ old('plan_type', $plan->plan_type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
        @error('plan_type')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

</div>

<div id="pricing-section" class="space-y-4">
    <h4 class="text-md text-neutral-900">Pricing Configuration</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Price</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-neutral-500">$</span>
                <input type="number" id="plan_price" name="plan_price" class="w-full pl-8 pr-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_price') border-red-500 @enderror" placeholder="0.00" value="{{ old('plan_price', $plan->plan_price ?? '') }}">
                @error('plan_price')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Duration</label>
            <input type="number" id="plan_duration" name="plan_duration" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_duration') border-red-500 @enderror" value="{{ old('plan_duration', $plan->plan_duration ?? '') }}" placeholder="30">
            @error('plan_duration')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Duration Unit</label>
            <select id="plan_duration_unit" name="plan_duration_unit" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_duration_unit') border-red-500 @enderror">
                <option value="">Select Duration Unit</option>
                <option value="days" {{ old('plan_duration_unit', $plan->plan_duration_unit ?? '') == 'days' ? 'selected' : '' }}>
                    Days
                </option>
                <option value="months" {{ old('plan_duration_unit', $plan->plan_duration_unit ?? '') == 'months' ? 'selected' : '' }}>Months</option>
                <option value="years" {{ old('plan_duration_unit', $plan->plan_duration_unit ?? '') == 'years' ? 'selected' : '' }}>Years</option>
            </select>
            @error('plan_duration_unit')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>


<div id="features-section" class="space-y-4">
    <h4 class="text-md text-neutral-900">Plan Features</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Allowed Listings</label>
            <input type="number" id="plan_allowed_listing" name="plan_allowed_listing" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_allowed_listing') border-red-500 @enderror" placeholder="10" value="{{ old('plan_allowed_listing', $plan->plan_allowed_listing ?? '') }}">
            @error('plan_allowed_listing')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Featured Properties</label>
            <input type="number" id="plan_featured_properties" name="plan_featured_properties" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_featured_properties') border-red-500 @enderror" placeholder="2" value="{{ old('plan_featured_properties', $plan->plan_featured_properties ?? '') }}">
            @error('plan_featured_properties')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Photo Upload Limit</label>
            <input type="number" id="plan_photo_upload_limit" name="plan_photo_upload_limit" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_photo_upload_limit') border-red-500 @enderror" placeholder="20" value="{{ old('plan_photo_upload_limit', $plan->plan_photo_upload_limit ?? '') }}">
            @error('plan_photo_upload_limit')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-sm text-neutral-900">Video Upload Limit</label>
            <input type="number" id="plan_video_upload_limit" name="plan_video_upload_limit" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_video_upload_limit') border-red-500 @enderror" placeholder="5" value="{{ old('plan_video_upload_limit', $plan->plan_video_upload_limit ?? '') }}">
            @error('plan_video_upload_limit')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div id="additional-features" class="space-y-4">
    <h4 class="text-md text-neutral-900">Additional Features</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="flex items-center">
            <input type="checkbox" name="plan_additional_feature[]" value="Priority Support"
                class="h-4 w-4 text-neutral-900 focus:ring-neutral-900 border-neutral-300 rounded"
                {{ in_array('Priority Support', old('plan_additional_feature', $plan->plan_additional_feature ?? [])) ? 'checked' : '' }}>
            <label class="ml-2 text-sm text-neutral-700">Priority Support</label>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="plan_additional_feature[]" value="Analytics Dashboard"
                class="h-4 w-4 text-neutral-900 focus:ring-neutral-900 border-neutral-300 rounded"
                {{ in_array('Analytics Dashboard', old('plan_additional_feature', $plan->plan_additional_feature ?? [])) ? 'checked' : '' }}>
            <label class="ml-2 text-sm text-neutral-700">Analytics Dashboard</label>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="plan_additional_feature[]" value="API Access"
                class="h-4 w-4 text-neutral-900 focus:ring-neutral-900 border-neutral-300 rounded"
                {{ in_array('API Access', old('plan_additional_feature', $plan->plan_additional_feature ?? [])) ? 'checked' : '' }}>
            <label class="ml-2 text-sm text-neutral-700">API Access</label>
        </div>

        <div class="flex items-center">
            <input type="checkbox" name="plan_additional_feature[]" value="Custom Branding"
                class="h-4 w-4 text-neutral-900 focus:ring-neutral-900 border-neutral-300 rounded"
                {{ in_array('Custom Branding', old('plan_additional_feature', $plan->plan_additional_feature ?? [])) ? 'checked' : '' }}>
            <label class="ml-2 text-sm text-neutral-700">Custom Branding</label>
        </div>

    </div>

    @error('plan_additional_feature')
    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>


<div id="description-section" class="space-y-2">
    <label class="block text-sm text-neutral-900">Plan Description</label>
    <textarea rows="4" id="plan_description" name="plan_description" class="w-full px-3 py-2 border border-neutral-300 rounded-md focus:outline-none focus:ring-2 focus:ring-neutral-900 @error('plan_description') border-red-500 @enderror" placeholder="Enter plan description..." value="{{ old('plan_description', $plan->plan_description ?? '') }}"></textarea>
     @error('plan_description')
    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>