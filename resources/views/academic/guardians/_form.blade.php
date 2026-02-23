<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Guardian Type *</label>
        <select class="form-select" name="guardian_type" required>
            <option value="">Select Type</option>
            <option value="father" {{ old('guardian_type', $guardian->guardian_type ?? '') == 'father' ? 'selected' : '' }}>Father</option>
            <option value="mother" {{ old('guardian_type', $guardian->guardian_type ?? '') == 'mother' ? 'selected' : '' }}>Mother</option>
            <option value="guardian" {{ old('guardian_type', $guardian->guardian_type ?? '') == 'guardian' ? 'selected' : '' }}>Guardian</option>
        </select>
        @error('guardian_type') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Full Name *</label>
        <input type="text" class="form-control" name="full_name" value="{{ old('full_name', $guardian->full_name ?? '') }}" required>
        @error('full_name') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Occupation</label>
        <input type="text" class="form-control" name="occupation" value="{{ old('occupation', $guardian->occupation ?? '') }}">
        @error('occupation') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Annual Income</label>
        <input type="number" class="form-control" name="annual_income" value="{{ old('annual_income', $guardian->annual_income ?? '') }}" step="0.01" min="0">
        @error('annual_income') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Mobile Number</label>
        <div class="input-group">
            <span class="input-group-text">+91</span>
            <input type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number', $guardian->mobile_number ?? '') }}" placeholder="e.g., 9035466787">
        </div>
        @error('mobile_number') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="{{ old('email', $guardian->email ?? '') }}">
        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Relation</label>
        <input type="text" class="form-control" name="relation" value="{{ old('relation', $guardian->relation ?? '') }}" placeholder="e.g., Uncle, Grandfather">
        @error('relation') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="3">{{ old('address', $guardian->address ?? '') }}</textarea>
        @error('address') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Photo</label>
        <input type="file" class="form-control" name="photo" accept="image/*">
        @if(!empty($guardian->photo_path))
            <div class="mt-2 text-center">
                <img src="{{ asset('storage/' . $guardian->photo_path) }}" alt="Guardian Photo" class="rounded-circle" style="width:80px; height:80px; object-fit:cover;">
            </div>
        @endif
        @error('photo') <div class="text-danger small">{{ $message }}</div> @enderror
        <div class="form-check mt-3">
            <input class="form-check-input" type="checkbox" name="is_primary_contact" id="is_primary_contact" {{ old('is_primary_contact', $guardian->is_primary_contact ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_primary_contact">Primary Contact</label>
        </div>
    </div>
</div>
