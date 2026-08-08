@php
    use App\Enums\BloodGroup;
    use App\Enums\Gender;

    $pilgrim = $pilgrim ?? null;
@endphp

@push('styles')
    <link href="{{ asset('css/pilgrim-form.css') }}" rel="stylesheet">
@endpush

<div class="pilgrim-form">
    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Registration</h5>
        <div class="row compact g-2">
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="hajj_year">Hajj Year <span class="text-danger">*</span></label>
                <input type="number" name="hajj_year" id="hajj_year" min="2000" max="2100"
                       value="{{ old('hajj_year', $pilgrim->hajj_year ?? now()->year) }}"
                       class="form-control @error('hajj_year') is-invalid @enderror" required>
                @error('hajj_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="booking_date">Booking Date <span class="text-danger">*</span></label>
                <input type="date" name="booking_date" id="booking_date"
                       value="{{ old('booking_date', optional($pilgrim?->booking_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                       class="form-control @error('booking_date') is-invalid @enderror" required>
                @error('booking_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="form_owner_id">Form Owner <span class="text-danger">*</span></label>
                <select name="form_owner_id" id="form_owner_id" class="form-control js-searchable-select @error('form_owner_id') is-invalid @enderror" data-placeholder="Select form owner" required>
                    <option value="" disabled {{ old('form_owner_id', $pilgrim->form_owner_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($formOwners as $formOwner)
                        <option value="{{ $formOwner->id }}" {{ old('form_owner_id', $pilgrim->form_owner_id ?? '') == $formOwner->id ? 'selected' : '' }}>
                            {{ $formOwner->name }}
                        </option>
                    @endforeach
                </select>
                @error('form_owner_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="company_id">Company <span class="text-danger">*</span></label>
                <select name="company_id" id="company_id" class="form-control js-searchable-select @error('company_id') is-invalid @enderror" data-placeholder="Select company" required>
                    <option value="" disabled {{ old('company_id', $pilgrim->company_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $pilgrim->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}@if($company->code) ({{ $company->code }})@endif
                        </option>
                    @endforeach
                </select>
                @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="maktab_category_id">Maktab <span class="text-danger">*</span></label>
                <select name="maktab_category_id" id="maktab_category_id" class="form-control js-searchable-select @error('maktab_category_id') is-invalid @enderror" data-placeholder="Select maktab" required>
                    <option value="" disabled {{ old('maktab_category_id', $pilgrim->maktab_category_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($maktabCategories as $category)
                        <option value="{{ $category->id }}" {{ old('maktab_category_id', $pilgrim->maktab_category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->zone }})
                        </option>
                    @endforeach
                </select>
                @error('maktab_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="package_id">Package <span class="text-danger">*</span></label>
                <select name="package_id" id="package_id" class="form-control js-searchable-select @error('package_id') is-invalid @enderror" data-placeholder="Select package" required>
                    <option value="" disabled {{ old('package_id', $pilgrim->package_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}" {{ old('package_id', $pilgrim->package_id ?? '') == $package->id ? 'selected' : '' }}>
                            {{ $package->number }} — {{ $package->name }}
                        </option>
                    @endforeach
                </select>
                @error('package_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label" for="care_off_id">Care Off <span class="text-danger">*</span></label>
                <select name="care_off_id" id="care_off_id" class="form-control js-searchable-select @error('care_off_id') is-invalid @enderror" data-placeholder="Select care off" required>
                    <option value="" disabled {{ old('care_off_id', $pilgrim->care_off_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($careOffs as $careOff)
                        <option value="{{ $careOff->id }}" {{ old('care_off_id', $pilgrim->care_off_id ?? '') == $careOff->id ? 'selected' : '' }}>
                            {{ $careOff->name }}
                        </option>
                    @endforeach
                </select>
                @error('care_off_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label" for="pod_city_id">POD <span class="text-danger">*</span></label>
                <select name="pod_city_id" id="pod_city_id" class="form-control js-searchable-select @error('pod_city_id') is-invalid @enderror" data-placeholder="Select city" required>
                    <option value="" disabled {{ old('pod_city_id', $pilgrim->pod_city_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" {{ old('pod_city_id', $pilgrim->pod_city_id ?? '') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('pod_city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label" for="room_type_id">Room <span class="text-danger">*</span></label>
                <select name="room_type_id" id="room_type_id" class="form-control js-searchable-select @error('room_type_id') is-invalid @enderror" data-placeholder="Select room" required>
                    <option value="" disabled {{ old('room_type_id', $pilgrim->room_type_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($roomTypes as $roomType)
                        <option value="{{ $roomType->id }}" {{ old('room_type_id', $pilgrim->room_type_id ?? '') == $roomType->id ? 'selected' : '' }}>
                            {{ $roomType->name }}
                        </option>
                    @endforeach
                </select>
                @error('room_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Personal Details</h5>
        <div class="row compact g-2">
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="gender">Gender <span class="text-danger">*</span></label>
                <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                    <option value="" disabled {{ old('gender', $pilgrim?->gender?->value) ? '' : 'selected' }}>Select</option>
                    @foreach (Gender::cases() as $gender)
                        <option value="{{ $gender->value }}"
                            {{ old('gender', $pilgrim?->gender?->value) === $gender->value ? 'selected' : '' }}>
                            {{ $gender->label() }}
                        </option>
                    @endforeach
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="surname">Surname <span class="text-danger">*</span></label>
                <input type="text" name="surname" id="surname" value="{{ old('surname', $pilgrim->surname ?? '') }}"
                       class="form-control @error('surname') is-invalid @enderror" required>
                @error('surname') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="given_name">Given Name <span class="text-danger">*</span></label>
                <input type="text" name="given_name" id="given_name" value="{{ old('given_name', $pilgrim->given_name ?? '') }}"
                       class="form-control @error('given_name') is-invalid @enderror" required>
                @error('given_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <label class="form-label" for="full_name_display">Haji Full Name</label>
                <input type="text" id="full_name_display" class="form-control" readonly tabindex="-1"
                       value="{{ old('full_name_display', $pilgrim->full_name ?? '') }}">
                <span class="form-hint">Built from given name and surname.</span>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="blood_group">Blood <span class="text-danger">*</span></label>
                <select name="blood_group" id="blood_group" class="form-control @error('blood_group') is-invalid @enderror" required>
                    <option value="" disabled {{ old('blood_group', $pilgrim?->blood_group?->value) ? '' : 'selected' }}>Select</option>
                    @foreach (BloodGroup::cases() as $bloodGroup)
                        <option value="{{ $bloodGroup->value }}"
                            {{ old('blood_group', $pilgrim?->blood_group?->value) === $bloodGroup->value ? 'selected' : '' }}>
                            {{ $bloodGroup->label() }}
                        </option>
                    @endforeach
                </select>
                @error('blood_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="father_husband_name">Father / Husband <span class="text-danger">*</span></label>
                <input type="text" name="father_husband_name" id="father_husband_name"
                       value="{{ old('father_husband_name', $pilgrim->father_husband_name ?? '') }}"
                       class="form-control @error('father_husband_name') is-invalid @enderror" required>
                @error('father_husband_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="date_of_birth">DOB <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth" id="date_of_birth"
                       value="{{ old('date_of_birth', optional($pilgrim?->date_of_birth)->format('Y-m-d')) }}"
                       class="form-control @error('date_of_birth') is-invalid @enderror" required>
                @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-1 col-md-2 col-4">
                <label class="form-label" for="age_display">Age</label>
                <input type="text" id="age_display" class="form-control" readonly tabindex="-1"
                       value="{{ old('age_display', isset($pilgrim) ? (string) $pilgrim->age : '') }}">
                <span class="form-hint">Hajj year − DOB.</span>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="birth_place">Birth Place <span class="text-danger">*</span></label>
                <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $pilgrim->birth_place ?? '') }}"
                       class="form-control @error('birth_place') is-invalid @enderror" required>
                @error('birth_place') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Passport & Contact</h5>
        <div class="row compact g-2">
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="passport_no">Passport <span class="text-danger">*</span></label>
                <input type="text" name="passport_no" id="passport_no" maxlength="9"
                       placeholder="AB1234567" inputmode="text" autocomplete="off"
                       value="{{ old('passport_no', $pilgrim->passport_no ?? '') }}"
                       class="form-control js-passport-input text-uppercase @error('passport_no') is-invalid @enderror" required>
                @error('passport_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="passport_expiry">Expiry <span class="text-danger">*</span></label>
                <input type="date" name="passport_expiry" id="passport_expiry"
                       value="{{ old('passport_expiry', optional($pilgrim?->passport_expiry)->format('Y-m-d')) }}"
                       class="form-control @error('passport_expiry') is-invalid @enderror" required>
                @error('passport_expiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <label class="form-label" for="cnic">CNIC <span class="text-danger">*</span></label>
                <input type="text" name="cnic" id="cnic" placeholder="12345-1234567-1"
                       maxlength="15" inputmode="numeric" autocomplete="off"
                       value="{{ old('cnic', $pilgrim->cnic ?? '') }}"
                       class="form-control js-cnic-input @error('cnic') is-invalid @enderror" required>
                @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="mobile">Mobile <span class="text-danger">*</span></label>
                <input type="text" name="mobile" id="mobile" placeholder="0300-1234567"
                       value="{{ old('mobile', $pilgrim->mobile ?? '') }}"
                       class="form-control @error('mobile') is-invalid @enderror" required>
                @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-12">
                <label class="form-label" for="address">Address <span class="text-danger">*</span></label>
                <input type="text" name="address" id="address"
                       placeholder="Full address"
                       value="{{ old('address', $pilgrim->address ?? '') }}"
                       class="form-control @error('address') is-invalid @enderror" required>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Mehram & Waris</h5>
        <div class="row compact g-2">
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="mehram_name">Mehram Name <span class="text-danger">*</span></label>
                <input type="text" name="mehram_name" id="mehram_name" value="{{ old('mehram_name', $pilgrim->mehram_name ?? '') }}"
                       class="form-control @error('mehram_name') is-invalid @enderror" required>
                @error('mehram_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="mehram_relation_id">Mehram Relation <span class="text-danger">*</span></label>
                <select name="mehram_relation_id" id="mehram_relation_id" class="form-control js-searchable-select @error('mehram_relation_id') is-invalid @enderror" data-placeholder="Select relation" required>
                    <option value="" disabled {{ old('mehram_relation_id', $pilgrim->mehram_relation_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($mehramRelations as $relation)
                        <option value="{{ $relation->id }}" {{ old('mehram_relation_id', $pilgrim->mehram_relation_id ?? '') == $relation->id ? 'selected' : '' }}>
                            {{ $relation->name }}
                        </option>
                    @endforeach
                </select>
                @error('mehram_relation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_name">Waris Name <span class="text-danger">*</span></label>
                <input type="text" name="waris_name" id="waris_name" value="{{ old('waris_name', $pilgrim->waris_name ?? '') }}"
                       class="form-control @error('waris_name') is-invalid @enderror" required>
                @error('waris_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_relation_id">Waris Relation <span class="text-danger">*</span></label>
                <select name="waris_relation_id" id="waris_relation_id" class="form-control js-searchable-select @error('waris_relation_id') is-invalid @enderror" data-placeholder="Select relation" required>
                    <option value="" disabled {{ old('waris_relation_id', $pilgrim->waris_relation_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($warisRelations as $relation)
                        <option value="{{ $relation->id }}" {{ old('waris_relation_id', $pilgrim->waris_relation_id ?? '') == $relation->id ? 'selected' : '' }}>
                            {{ $relation->name }}
                        </option>
                    @endforeach
                </select>
                @error('waris_relation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_cnic">Waris CNIC <span class="text-danger">*</span></label>
                <input type="text" name="waris_cnic" id="waris_cnic" placeholder="12345-1234567-1"
                       maxlength="15" inputmode="numeric" autocomplete="off"
                       value="{{ old('waris_cnic', $pilgrim->waris_cnic ?? '') }}"
                       class="form-control js-cnic-input @error('waris_cnic') is-invalid @enderror" required>
                @error('waris_cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_mobile">Waris Mobile <span class="text-danger">*</span></label>
                <input type="text" name="waris_mobile" id="waris_mobile" placeholder="0300-1234567"
                       value="{{ old('waris_mobile', $pilgrim->waris_mobile ?? '') }}"
                       class="form-control @error('waris_mobile') is-invalid @enderror" required>
                @error('waris_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Family & Photo</h5>
        <div class="row compact g-2">
            @if (! $pilgrim)
                <div class="col-lg-5 col-md-6">
                    <label class="form-label" for="existing_family_number">Link to Family <span class="text-muted">(optional)</span></label>
                    <select name="existing_family_number" id="existing_family_number" class="form-control js-searchable-select js-family-select @error('existing_family_number') is-invalid @enderror" data-placeholder="Search family">
                        <option value="">New — single (S)</option>
                    </select>
                    <span class="form-hint js-family-members-hint"></span>
                    @error('existing_family_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endif
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="family_code">Family Code</label>
                <input type="text" id="family_code" readonly
                       value="{{ old('family_code', $pilgrim->family_code ?? '') }}"
                       class="form-control js-family-code @error('family_code') is-invalid @enderror"
                       data-preview-url="{{ route('admin.pilgrims.preview-family-code') }}"
                       data-families-url="{{ route('admin.pilgrims.families') }}"
                       data-pilgrim-id="{{ $pilgrim->id ?? '' }}">
                @error('family_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-{{ $pilgrim ? '5' : '5' }} col-md-{{ $pilgrim ? '6' : '6' }}">
                <label class="form-label">Photo (JPEG)</label>
                <div class="pilgrim-photo-field @error('photo') is-invalid @enderror"
                     @if ($pilgrim?->photo_path) data-existing-url="{{ asset('storage/'.$pilgrim->photo_path) }}" @endif>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,.jpg"
                           class="pilgrim-photo-field__input @error('photo') is-invalid @enderror">
                    <input type="hidden" name="remove_photo" value="0" class="js-remove-photo">

                    <div class="pilgrim-photo-field__empty js-photo-empty">
                        <button type="button" class="btn btn-sm btn-outline-secondary js-photo-upload">
                            Upload photo
                        </button>
                        <span class="pilgrim-photo-field__hint">JPEG only, max 2MB</span>
                    </div>

                    <div class="pilgrim-photo-field__preview js-photo-preview" hidden>
                        <img src="" alt="Photo preview" class="pilgrim-photo-field__image js-photo-image">
                        <div class="pilgrim-photo-field__actions">
                            <button type="button" class="btn btn-sm btn-outline-primary js-photo-change">Change</button>
                            <button type="button" class="btn btn-sm btn-outline-danger js-photo-remove">Remove</button>
                        </div>
                    </div>
                </div>
                @error('photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 js-family-notice-wrap" hidden>
                <div class="family-notice js-family-notice"></div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    (function () {
        function formatPassportInput(input) {
            const raw = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            let letters = '';
            let digits = '';

            for (const char of raw) {
                if (letters.length < 2) {
                    if (/[A-Z]/.test(char)) {
                        letters += char;
                    }
                } else if (/\d/.test(char) && digits.length < 7) {
                    digits += char;
                }
            }

            input.value = letters + digits;
        }

        function formatCnicInput(input) {
            const digits = input.value.replace(/\D/g, '').slice(0, 13);

            if (digits.length <= 5) {
                input.value = digits;
            } else if (digits.length <= 12) {
                input.value = digits.slice(0, 5) + '-' + digits.slice(5);
            } else {
                input.value = digits.slice(0, 5) + '-' + digits.slice(5, 12) + '-' + digits.slice(12);
            }
        }

        document.querySelectorAll('.js-passport-input').forEach(function (input) {
            input.addEventListener('input', function () { formatPassportInput(input); });
            input.addEventListener('paste', function () { setTimeout(function () { formatPassportInput(input); }, 0); });
        });

        document.querySelectorAll('.js-cnic-input').forEach(function (input) {
            input.addEventListener('input', function () { formatCnicInput(input); });
            input.addEventListener('paste', function () { setTimeout(function () { formatCnicInput(input); }, 0); });
        });

        const surnameInput = document.getElementById('surname');
        const givenNameInput = document.getElementById('given_name');
        const fullNameDisplay = document.getElementById('full_name_display');
        const hajjYearInput = document.getElementById('hajj_year');
        const dateOfBirthInput = document.getElementById('date_of_birth');
        const ageDisplay = document.getElementById('age_display');

        function updateFullNameDisplay() {
            if (!fullNameDisplay) {
                return;
            }

            const givenName = (givenNameInput?.value || '').trim();
            const surname = (surnameInput?.value || '').trim();
            fullNameDisplay.value = [givenName, surname].filter(Boolean).join(' ');
        }

        function updateAgeDisplay() {
            if (!ageDisplay) {
                return;
            }

            const hajjYear = parseInt(hajjYearInput?.value || '', 10);
            const dobValue = dateOfBirthInput?.value || '';

            if (!hajjYear || !dobValue) {
                ageDisplay.value = '';
                return;
            }

            const dobYear = parseInt(dobValue.slice(0, 4), 10);

            if (Number.isNaN(dobYear)) {
                ageDisplay.value = '';
                return;
            }

            ageDisplay.value = String(Math.max(0, hajjYear - dobYear));
        }

        [surnameInput, givenNameInput].forEach(function (input) {
            input?.addEventListener('input', updateFullNameDisplay);
            input?.addEventListener('change', updateFullNameDisplay);
        });

        [hajjYearInput, dateOfBirthInput].forEach(function (input) {
            input?.addEventListener('input', updateAgeDisplay);
            input?.addEventListener('change', updateAgeDisplay);
        });

        updateFullNameDisplay();
        updateAgeDisplay();

        const familyCodeInput = document.getElementById('family_code');
        const companySelect = document.getElementById('company_id');
        const existingFamilySelect = document.getElementById('existing_family_number');
        const familyMembersHint = document.querySelector('.js-family-members-hint');
        const familyNoticeWrap = document.querySelector('.js-family-notice-wrap');
        const familyNotice = document.querySelector('.js-family-notice');
        const isEditForm = Boolean(familyCodeInput?.dataset.pilgrimId);
        let loadedFamilies = [];

        async function loadFamilies() {
            if (!existingFamilySelect || !companySelect?.value || !familyCodeInput?.dataset.familiesUrl) {
                return;
            }

            const currentValue = existingFamilySelect.value;
            existingFamilySelect.innerHTML = '<option value="">New — single (S)</option>';
            loadedFamilies = [];

            try {
                const params = new URLSearchParams({
                    company_id: companySelect.value,
                    hajj_year: hajjYearInput?.value || '',
                });
                const response = await fetch(familyCodeInput.dataset.familiesUrl + '?' + params.toString(), {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                loadedFamilies = data.families ?? [];

                loadedFamilies.forEach(function (family) {
                    const option = document.createElement('option');
                    option.value = family.family_number;
                    option.textContent = family.label;
                    existingFamilySelect.appendChild(option);
                });

                const oldValue = @json(old('existing_family_number'));
                existingFamilySelect.value = oldValue || currentValue || '';
                updateFamilyHint();

                if (window.AdminForm) {
                    window.AdminForm.syncTomSelect(existingFamilySelect);
                }
            } catch (error) {
                loadedFamilies = [];
            }
        }

        function selectedFamily() {
            if (!existingFamilySelect?.value) {
                return null;
            }

            return loadedFamilies.find(function (item) {
                return String(item.family_number) === existingFamilySelect.value;
            }) ?? null;
        }

        function updateFamilyHint() {
            const family = selectedFamily();

            if (familyMembersHint) {
                if (!family) {
                    familyMembersHint.textContent = '';
                } else {
                    familyMembersHint.textContent = 'Members: ' + family.members.map(function (m) {
                        return m.suffix + ' ' + m.name;
                    }).join(', ');
                }
            }
        }

        async function refreshFamilyCode() {
            if (!familyCodeInput || !companySelect?.value) {
                return;
            }

            const params = new URLSearchParams({
                company_id: companySelect.value,
                hajj_year: hajjYearInput?.value || '',
            });

            if (familyCodeInput.dataset.pilgrimId) {
                params.append('pilgrim_id', familyCodeInput.dataset.pilgrimId);
            } else if (existingFamilySelect?.value) {
                params.append('family_number', existingFamilySelect.value);
            }

            try {
                const response = await fetch(familyCodeInput.dataset.previewUrl + '?' + params.toString(), {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (!response.ok) {
                    familyCodeInput.value = '';
                    return;
                }

                const data = await response.json();
                familyCodeInput.value = data.family_code ?? '';

                if (familyNoticeWrap && familyNotice) {
                    if (data.promote_single) {
                        familyNotice.textContent = 'Existing single member will update to A; this person will be B.';
                        familyNoticeWrap.hidden = false;
                    } else {
                        familyNoticeWrap.hidden = true;
                        familyNotice.textContent = '';
                    }
                }
            } catch (error) {
                familyCodeInput.value = '';
            }
        }

        companySelect?.addEventListener('change', function () {
            loadFamilies().then(refreshFamilyCode);
        });

        hajjYearInput?.addEventListener('change', function () {
            updateAgeDisplay();
            loadFamilies().then(refreshFamilyCode);
        });

        existingFamilySelect?.addEventListener('change', function () {
            updateFamilyHint();
            refreshFamilyCode();
        });

        if (!isEditForm) {
            loadFamilies().then(refreshFamilyCode);
        } else {
            refreshFamilyCode();
        }

        document.querySelectorAll('.pilgrim-photo-field').forEach(function (field) {
            const input = field.querySelector('.pilgrim-photo-field__input');
            const removeInput = field.querySelector('.js-remove-photo');
            const emptyState = field.querySelector('.js-photo-empty');
            const previewState = field.querySelector('.js-photo-preview');
            const image = field.querySelector('.js-photo-image');
            const existingUrl = field.dataset.existingUrl || '';
            let objectUrl = null;

            function revokeObjectUrl() {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            }

            function showEmpty() {
                emptyState.hidden = false;
                previewState.hidden = true;
                image.removeAttribute('src');
            }

            function showPreview(url) {
                image.src = url;
                emptyState.hidden = true;
                previewState.hidden = false;
            }

            function openFilePicker() {
                input.click();
            }

            field.querySelector('.js-photo-upload')?.addEventListener('click', openFilePicker);
            field.querySelector('.js-photo-change')?.addEventListener('click', openFilePicker);

            field.querySelector('.js-photo-remove')?.addEventListener('click', function () {
                input.value = '';
                removeInput.value = '1';
                revokeObjectUrl();
                showEmpty();
            });

            input.addEventListener('change', function () {
                const file = input.files?.[0];

                if (!file) {
                    if (existingUrl && removeInput.value !== '1') {
                        showPreview(existingUrl);
                    } else {
                        showEmpty();
                    }

                    return;
                }

                removeInput.value = '0';
                revokeObjectUrl();
                objectUrl = URL.createObjectURL(file);
                showPreview(objectUrl);
            });

            if (existingUrl) {
                removeInput.value = '0';
                showPreview(existingUrl);
            }
        });
    })();
</script>
@endpush
