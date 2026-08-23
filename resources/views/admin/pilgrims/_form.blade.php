@php
    use App\Enums\BloodGroup;
    use App\Enums\Gender;

    $pilgrim = $pilgrim ?? null;
    $activeHajjYear = $activeHajjYear ?? (int) now()->year;
    $entryDate = old('entry_date', optional($pilgrim?->entry_date)->format('Y-m-d') ?? now()->format('Y-m-d'));
    $selectedPackageId = old('package_id', $pilgrim->package_id ?? '');
    $selectedPackage = $packages->firstWhere('id', (int) $selectedPackageId);
    $qurbaniIncluded = (bool) old('qurbani_included', $pilgrim->qurbani_included ?? $selectedPackage?->qurbani_included ?? false);
@endphp

@push('styles')
    <link href="{{ asset('css/pilgrim-form.css') }}" rel="stylesheet">
@endpush

<div class="pilgrim-form">
    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Registration</h5>
        <div class="row compact g-2">
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="hajj_year_display">Hajj Year</label>
                <input type="text" id="hajj_year_display" readonly
                       value="{{ $activeHajjYear }}"
                       class="form-control">
                <input type="hidden" name="hajj_year" id="hajj_year" value="{{ $activeHajjYear }}">
                @error('hajj_year') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="entry_date_display">Entry Date</label>
                <input type="text" id="entry_date_display" readonly
                       value="{{ \Carbon\Carbon::parse($entryDate)->format('d/m/Y') }}"
                       class="form-control">
                <input type="hidden" name="entry_date" id="entry_date" value="{{ $entryDate }}">
                @error('entry_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="form_owner_id">Form Owner</label>
                <select name="form_owner_id" id="form_owner_id" class="form-control js-searchable-select @error('form_owner_id') is-invalid @enderror" data-placeholder="Select form owner">
                    <option value="" {{ old('form_owner_id', $pilgrim->form_owner_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($formOwners as $formOwner)
                        <option value="{{ $formOwner->id }}" {{ old('form_owner_id', $pilgrim->form_owner_id ?? '') == $formOwner->id ? 'selected' : '' }}>
                            {{ $formOwner->name }}
                        </option>
                    @endforeach
                </select>
                @error('form_owner_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="company_id">Company</label>
                <select name="company_id" id="company_id" class="form-control js-searchable-select @error('company_id') is-invalid @enderror" data-placeholder="Select company">
                    <option value="" {{ old('company_id', $pilgrim->company_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $pilgrim->company_id ?? '') == $company->id ? 'selected' : '' }}>
                            {{ $company->registrationOptionLabel() }}
                        </option>
                    @endforeach
                </select>
                @error('company_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label" for="maktab_category_id">Maktab</label>
                <select name="maktab_category_id" id="maktab_category_id" class="form-control js-searchable-select @error('maktab_category_id') is-invalid @enderror" data-placeholder="Select maktab">
                    <option value="" {{ old('maktab_category_id', $pilgrim->maktab_category_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($maktabCategories as $category)
                        <option value="{{ $category->id }}" {{ old('maktab_category_id', $pilgrim->maktab_category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->zone }})
                        </option>
                    @endforeach
                </select>
                @error('maktab_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <x-admin.package-select
                :packages="$packages"
                :selected="old('package_id', $pilgrim->package_id ?? '')"
                qurbani-data
                class="@error('package_id') is-invalid @enderror"
            >
                @error('package_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </x-admin.package-select>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="qurbani_included">Qurbani</label>
                <div class="pilgrim-form-switch @error('qurbani_included') is-invalid @enderror">
                    <input type="hidden" name="qurbani_included" value="0">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="qurbani_included" id="qurbani_included" value="1"
                               @checked($qurbaniIncluded)>
                        <label class="form-check-label" for="qurbani_included">
                            <span class="js-qurbani-label">{{ $qurbaniIncluded ? 'Yes' : 'No' }}</span>
                        </label>
                    </div>
                </div>
                @error('qurbani_included') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label" for="care_off_id">Care Off</label>
                <select name="care_off_id" id="care_off_id" class="form-control js-searchable-select @error('care_off_id') is-invalid @enderror" data-placeholder="Select care off">
                    <option value="" {{ old('care_off_id', $pilgrim->care_off_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($careOffs as $careOff)
                        <option value="{{ $careOff->id }}" {{ old('care_off_id', $pilgrim->care_off_id ?? '') == $careOff->id ? 'selected' : '' }}>
                            {{ $careOff->name }}
                        </option>
                    @endforeach
                </select>
                @error('care_off_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label" for="pod_city_id">POD</label>
                <select name="pod_city_id" id="pod_city_id" class="form-control js-searchable-select @error('pod_city_id') is-invalid @enderror" data-placeholder="Select city">
                    <option value="" {{ old('pod_city_id', $pilgrim->pod_city_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" {{ old('pod_city_id', $pilgrim->pod_city_id ?? '') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
                @error('pod_city_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-4 col-md-4">
                <label class="form-label" for="room_type_id">Room</label>
                <select name="room_type_id" id="room_type_id" class="form-control js-searchable-select @error('room_type_id') is-invalid @enderror" data-placeholder="Select room">
                    <option value="" {{ old('room_type_id', $pilgrim->room_type_id ?? '') ? '' : 'selected' }}>Select</option>
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
                <label class="form-label" for="gender">Gender</label>
                <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                    <option value="" {{ old('gender', $pilgrim?->gender?->value) ? '' : 'selected' }}>Select</option>
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
                <label class="form-label" for="surname">Surname</label>
                <input type="text" name="surname" id="surname" value="{{ old('surname', $pilgrim->surname ?? '') }}"
                       class="form-control @error('surname') is-invalid @enderror">
                @error('surname') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="given_name">Given Name</label>
                <input type="text" name="given_name" id="given_name" value="{{ old('given_name', $pilgrim->given_name ?? '') }}"
                       class="form-control @error('given_name') is-invalid @enderror">
                @error('given_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <label class="form-label" for="full_name_display">Haji Full Name</label>
                <input type="text" id="full_name_display" class="form-control" readonly tabindex="-1"
                       value="{{ old('full_name_display', $pilgrim->full_name ?? '') }}">
                <span class="form-hint">Built from given name and surname.</span>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="blood_group">Blood</label>
                <select name="blood_group" id="blood_group" class="form-control @error('blood_group') is-invalid @enderror">
                    <option value="" {{ old('blood_group', $pilgrim?->blood_group?->value) ? '' : 'selected' }}>Select</option>
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
                <label class="form-label" for="father_husband_name">Father / Husband</label>
                <input type="text" name="father_husband_name" id="father_husband_name"
                       value="{{ old('father_husband_name', $pilgrim->father_husband_name ?? '') }}"
                       class="form-control @error('father_husband_name') is-invalid @enderror">
                @error('father_husband_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="date_of_birth">DOB</label>
                <x-admin.date-input
                    name="date_of_birth"
                    id="date_of_birth"
                    :value="optional($pilgrim?->date_of_birth)->format('Y-m-d')"
                    max="{{ now()->format('Y-m-d') }}"
                />
            </div>
            <div class="col-lg-1 col-md-2 col-4">
                <label class="form-label" for="age_display">Age</label>
                <input type="text" id="age_display" class="form-control" readonly tabindex="-1"
                       value="{{ old('age_display', isset($pilgrim) ? (string) $pilgrim->age : '') }}">
                <span class="form-hint">Hajj year − DOB.</span>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="birth_place">Birth Place</label>
                <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $pilgrim->birth_place ?? '') }}"
                       class="form-control @error('birth_place') is-invalid @enderror">
                @error('birth_place') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Passport & Contact</h5>
        <div class="row compact g-2">
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="passport_no">Passport</label>
                <input type="text" name="passport_no" id="passport_no" maxlength="9"
                       placeholder="AB1234567" inputmode="text" autocomplete="off"
                       value="{{ old('passport_no', $pilgrim->passport_no ?? '') }}"
                       class="form-control js-passport-input text-uppercase @error('passport_no') is-invalid @enderror">
                @error('passport_no') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="passport_expiry">Expiry</label>
                <x-admin.date-input
                    name="passport_expiry"
                    id="passport_expiry"
                    :value="optional($pilgrim?->passport_expiry)->format('Y-m-d')"
                    min="{{ now()->format('Y-m-d') }}"
                />
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <label class="form-label" for="cnic">CNIC</label>
                <input type="text" name="cnic" id="cnic" placeholder="12345-1234567-1"
                       maxlength="15" inputmode="numeric" autocomplete="off"
                       value="{{ old('cnic', $pilgrim->cnic ?? '') }}"
                       class="form-control js-cnic-input @error('cnic') is-invalid @enderror">
                @error('cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <label class="form-label" for="mobile">Mobile</label>
                <input type="text" name="mobile" id="mobile" placeholder="0300-1234567"
                       value="{{ old('mobile', $pilgrim->mobile ?? '') }}"
                       class="form-control @error('mobile') is-invalid @enderror">
                @error('mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-12">
                <label class="form-label" for="address">Address</label>
                <input type="text" name="address" id="address"
                       placeholder="Full address"
                       value="{{ old('address', $pilgrim->address ?? '') }}"
                       class="form-control @error('address') is-invalid @enderror">
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Mehram & Waris</h5>
        <div class="row compact g-2">
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="mehram_name">Mehram Name</label>
                <input type="text" name="mehram_name" id="mehram_name" value="{{ old('mehram_name', $pilgrim->mehram_name ?? '') }}"
                       class="form-control @error('mehram_name') is-invalid @enderror">
                @error('mehram_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="mehram_relation_id">Mehram Relation</label>
                <select name="mehram_relation_id" id="mehram_relation_id" class="form-control js-searchable-select @error('mehram_relation_id') is-invalid @enderror" data-placeholder="Select relation">
                    <option value="" {{ old('mehram_relation_id', $pilgrim->mehram_relation_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($mehramRelations as $relation)
                        <option value="{{ $relation->id }}" {{ old('mehram_relation_id', $pilgrim->mehram_relation_id ?? '') == $relation->id ? 'selected' : '' }}>
                            {{ $relation->name }}
                        </option>
                    @endforeach
                </select>
                @error('mehram_relation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_name">Waris Name</label>
                <input type="text" name="waris_name" id="waris_name" value="{{ old('waris_name', $pilgrim->waris_name ?? '') }}"
                       class="form-control @error('waris_name') is-invalid @enderror">
                @error('waris_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_relation_id">Waris Relation</label>
                <select name="waris_relation_id" id="waris_relation_id" class="form-control js-searchable-select @error('waris_relation_id') is-invalid @enderror" data-placeholder="Select relation">
                    <option value="" {{ old('waris_relation_id', $pilgrim->waris_relation_id ?? '') ? '' : 'selected' }}>Select</option>
                    @foreach ($warisRelations as $relation)
                        <option value="{{ $relation->id }}" {{ old('waris_relation_id', $pilgrim->waris_relation_id ?? '') == $relation->id ? 'selected' : '' }}>
                            {{ $relation->name }}
                        </option>
                    @endforeach
                </select>
                @error('waris_relation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_cnic">Waris CNIC</label>
                <input type="text" name="waris_cnic" id="waris_cnic" placeholder="12345-1234567-1"
                       maxlength="15" inputmode="numeric" autocomplete="off"
                       value="{{ old('waris_cnic', $pilgrim->waris_cnic ?? '') }}"
                       class="form-control js-cnic-input @error('waris_cnic') is-invalid @enderror">
                @error('waris_cnic') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="form-label" for="waris_mobile">Waris Mobile</label>
                <input type="text" name="waris_mobile" id="waris_mobile" placeholder="0300-1234567"
                       value="{{ old('waris_mobile', $pilgrim->waris_mobile ?? '') }}"
                       class="form-control @error('waris_mobile') is-invalid @enderror">
                @error('waris_mobile') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Family & Association</h5>
        <div class="row compact g-2">
            @if ($pilgrim)
                <div class="col-lg-6 col-md-8 js-within-company-family-field">
                    <label class="form-label" for="family_move_to">Family Assignment</label>
                    <select name="family_move_to" id="family_move_to" class="form-control js-searchable-select js-family-move-select @error('family_move_to') is-invalid @enderror" data-placeholder="Select family assignment">
                        <option value="keep" @selected(old('family_move_to', 'keep') === 'keep')>
                            Keep current{{ $pilgrim->family_code ? ' — '.$pilgrim->family_code : '' }}
                        </option>
                        <option value="new" @selected(old('family_move_to') === 'new')>New — single (S)</option>
                    </select>
                    <span class="form-hint js-family-move-hint"></span>
                    @error('family_move_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @else
                <div class="col-lg-6 col-md-8">
                    <label class="form-label" for="existing_family_number">Link to Family <span class="text-muted">(optional)</span></label>
                    <select name="existing_family_number" id="existing_family_number" class="form-control js-searchable-select js-family-select @error('existing_family_number') is-invalid @enderror" data-placeholder="Search family">
                        <option value="">New — single (S)</option>
                    </select>
                    <span class="form-hint js-family-members-hint"></span>
                    @error('existing_family_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endif
            <div class="col-lg-6 col-md-8 js-transfer-family-field" hidden>
                <label class="form-label" for="existing_family_number">Link to Family <span class="text-muted">(optional)</span></label>
                <select name="" id="existing_family_number_transfer" class="form-control js-searchable-select js-family-select-transfer @error('existing_family_number') is-invalid @enderror" data-placeholder="Search family" data-defer-tom-select>
                    <option value="">New — single (S)</option>
                </select>
                <span class="form-hint js-family-transfer-hint">Shown when company is changed. Leave as new single unless joining an existing family.</span>
                @error('existing_family_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <label class="form-label" for="family_code">Family Code</label>
                <input type="text" id="family_code" readonly
                       value="{{ old('family_code', $pilgrim->family_code ?? '') }}"
                       class="form-control js-family-code @error('family_code') is-invalid @enderror"
                       data-preview-url="{{ route('admin.pilgrims.preview-family-code') }}"
                       data-families-url="{{ route('admin.pilgrims.families') }}"
                       data-pilgrim-id="{{ $pilgrim->id ?? '' }}"
                       data-original-company-id="{{ $pilgrim->company_id ?? '' }}"
                       data-original-family-number="{{ $pilgrim->family_number ?? '' }}"
                       data-original-family-code="{{ $pilgrim->family_code ?? '' }}">
                @error('family_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 js-family-notice-wrap" hidden>
                <div class="family-notice js-family-notice"></div>
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Documents</h5>
        <div class="row compact g-2">
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Photo</label>
                <x-admin.image-upload
                    name="photo"
                    remove-name="remove_photo"
                    :existing-url="$pilgrim?->photo_url"
                    :existing-filename="$pilgrim?->photo_path ? basename($pilgrim->photo_path) : null"
                    upload-label="Upload photo"
                    preview-alt="Photo preview"
                />
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Passport</label>
                <x-admin.image-upload
                    name="passport"
                    remove-name="remove_passport"
                    :existing-url="$pilgrim?->passport_url"
                    :existing-filename="$pilgrim?->passport_path ? basename($pilgrim->passport_path) : null"
                    upload-label="Upload passport"
                    preview-alt="Passport preview"
                />
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Visa</label>
                <x-admin.image-upload
                    name="visa"
                    remove-name="remove_visa"
                    :existing-url="$pilgrim?->visa_url"
                    :existing-filename="$pilgrim?->visa_path ? basename($pilgrim->visa_path) : null"
                    upload-label="Upload visa"
                    preview-alt="Visa preview"
                />
            </div>
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Ticket</label>
                <x-admin.image-upload
                    name="ticket"
                    remove-name="remove_ticket"
                    :existing-url="$pilgrim?->ticket_url"
                    :existing-filename="$pilgrim?->ticket_path ? basename($pilgrim->ticket_path) : null"
                    upload-label="Upload ticket"
                    preview-alt="Ticket preview"
                />
            </div>
        </div>
    </section>

    <section class="pilgrim-form-section">
        <h5 class="pilgrim-form-section-title">Comments</h5>
        <div class="row compact g-2">
            <div class="col-12">
                <label class="form-label" for="comments">Comments <span class="text-muted">(optional)</span></label>
                <textarea name="comments" id="comments" rows="5"
                          placeholder="Any notes or remarks about this registration"
                          class="form-control @error('comments') is-invalid @enderror">{{ old('comments', $pilgrim->comments ?? '') }}</textarea>
                @error('comments') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

        const packageSelect = document.getElementById('package_id');
        const qurbaniToggle = document.getElementById('qurbani_included');
        const qurbaniLabel = document.querySelector('.js-qurbani-label');

        function updateQurbaniLabel() {
            if (qurbaniLabel && qurbaniToggle) {
                qurbaniLabel.textContent = qurbaniToggle.checked ? 'Yes' : 'No';
            }
        }

        function syncQurbaniFromPackage() {
            if (!qurbaniToggle || !packageSelect) {
                return;
            }

            const option = packageSelect.selectedOptions[0];

            if (!option || !option.value) {
                qurbaniToggle.checked = false;
                updateQurbaniLabel();

                return;
            }

            qurbaniToggle.checked = option.dataset.qurbani === '1';
            updateQurbaniLabel();
        }

        packageSelect?.addEventListener('change', syncQurbaniFromPackage);
        qurbaniToggle?.addEventListener('change', updateQurbaniLabel);
        updateQurbaniLabel();

        const familyCodeInput = document.getElementById('family_code');
        const companySelect = document.getElementById('company_id');
        const createFamilySelect = document.getElementById('existing_family_number');
        const transferFamilySelect = document.getElementById('existing_family_number_transfer');
        const familyMoveSelect = document.getElementById('family_move_to');
        const familyMembersHint = document.querySelector('.js-family-members-hint');
        const familyMoveHint = document.querySelector('.js-family-move-hint');
        const familyNoticeWrap = document.querySelector('.js-family-notice-wrap');
        const familyNotice = document.querySelector('.js-family-notice');
        const transferFamilyField = document.querySelector('.js-transfer-family-field');
        const withinCompanyFamilyField = document.querySelector('.js-within-company-family-field');
        const originalCompanyId = familyCodeInput?.dataset.originalCompanyId || '';
        const originalFamilyNumber = familyCodeInput?.dataset.originalFamilyNumber || '';
        let loadedFamilies = [];

        function getSelectValue(select) {
            if (!select) {
                return '';
            }

            if (select.tomselect) {
                return select.tomselect.getValue() || '';
            }

            return select.value || '';
        }

        function setSelectValue(select, value) {
            if (!select) {
                return;
            }

            if (select.tomselect) {
                select.tomselect.setValue(value, true);
            } else {
                select.value = value;
            }
        }

        function activeFamilySelect() {
            return isCompanyTransfer() ? transferFamilySelect : (familyMoveSelect || createFamilySelect);
        }

        function isCompanyTransfer() {
            return Boolean(originalCompanyId && companySelect?.value && companySelect.value !== originalCompanyId);
        }

        function syncActiveFamilySelect() {
            const select = activeFamilySelect();

            if (select && window.AdminForm) {
                window.AdminForm.syncTomSelect(select);
            }
        }

        function setSelectEnabled(select, enabled) {
            if (!select || !window.AdminForm) {
                return;
            }

            window.AdminForm.setSelectEnabled(select, enabled);
        }

        function destroyTomSelect(select) {
            if (select?.tomselect) {
                select.tomselect.destroy();
                select.tomselect = null;
            }
        }

        function reinitFamilySelect(select, defaultValue) {
            if (!select) {
                return;
            }

            if (select === transferFamilySelect && isCompanyTransfer()) {
                ensureTransferSelectReady();
            } else if (select.classList.contains('js-searchable-select')) {
                window.AdminForm?.initSearchableSelect(select);
            }

            setSelectValue(select, defaultValue);

            if (window.AdminForm) {
                window.AdminForm.syncTomSelect(select);
            }
        }

        function ensureTransferSelectReady() {
            if (!transferFamilySelect || !isCompanyTransfer()) {
                return;
            }

            const wasUninitialized = !transferFamilySelect.tomselect;

            if (window.AdminForm?.initSearchableSelect && wasUninitialized) {
                window.AdminForm.initSearchableSelect(transferFamilySelect);
            }

            if (transferFamilySelect.tomselect && wasUninitialized) {
                transferFamilySelect.tomselect.on('change', refreshFamilyCode);
            }

            setSelectEnabled(transferFamilySelect, true);
        }

        function toggleFamilyFields() {
            const transferring = isCompanyTransfer();

            if (transferFamilyField) {
                transferFamilyField.hidden = !transferring;
            }

            if (withinCompanyFamilyField) {
                withinCompanyFamilyField.hidden = transferring;
            }

            if (transferFamilySelect) {
                transferFamilySelect.name = transferring ? 'existing_family_number' : '';
            }

            if (familyMoveSelect) {
                familyMoveSelect.name = transferring ? '' : 'family_move_to';
            }

            setSelectEnabled(familyMoveSelect, !transferring);

            if (!transferring) {
                setSelectEnabled(transferFamilySelect, false);
                setSelectValue(transferFamilySelect, '');
            }
        }

        async function loadFamilies() {
            const select = activeFamilySelect();

            if (!select || !companySelect?.value || !familyCodeInput?.dataset.familiesUrl) {
                return;
            }

            const currentValue = getSelectValue(select);
            const isMoveSelect = select === familyMoveSelect;
            const originalFamilyCode = familyCodeInput.dataset.originalFamilyCode || familyCodeInput.value;
            const baseOptions = isMoveSelect
                ? [
                    {
                        value: 'keep',
                        label: 'Keep current' + (originalFamilyCode ? ' — ' + originalFamilyCode : ''),
                    },
                    { value: 'new', label: 'New — single (S)' },
                ]
                : [{ value: '', label: 'New — single (S)' }];

            destroyTomSelect(select);

            select.innerHTML = '';
            baseOptions.forEach(function (optionData) {
                const option = document.createElement('option');
                option.value = optionData.value;
                option.textContent = optionData.label;
                select.appendChild(option);
            });
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
                    if (isMoveSelect && String(family.family_number) === originalFamilyNumber) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = family.family_number;
                    option.textContent = family.label;
                    select.appendChild(option);
                });

                if (isMoveSelect) {
                    const oldMove = @json(old('family_move_to', 'keep'));
                    reinitFamilySelect(select, oldMove || currentValue || 'keep');
                    updateFamilyMoveHint();
                } else {
                    const oldValue = @json(old('existing_family_number'));
                    const defaultValue = (select === transferFamilySelect && isCompanyTransfer())
                        ? ''
                        : (oldValue || currentValue || '');
                    reinitFamilySelect(select, defaultValue);
                    updateFamilyHint();
                }
            } catch (error) {
                loadedFamilies = [];
            }
        }

        function selectedFamilyFrom(select) {
            const value = getSelectValue(select);

            if (!value || value === 'keep' || value === 'new') {
                return null;
            }

            return loadedFamilies.find(function (item) {
                return String(item.family_number) === value;
            }) ?? null;
        }

        function updateFamilyHint() {
            const family = selectedFamilyFrom(activeFamilySelect());

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

        function updateFamilyMoveHint() {
            if (!familyMoveHint || !familyMoveSelect) {
                return;
            }

            const moveTo = getSelectValue(familyMoveSelect);
            const family = selectedFamilyFrom(familyMoveSelect);

            if (moveTo === 'new') {
                familyMoveHint.textContent = 'A new single family code will be assigned.';
            } else if (family) {
                familyMoveHint.textContent = 'Members: ' + family.members.map(function (m) {
                    return m.suffix + ' ' + m.name;
                }).join(', ');
            } else {
                familyMoveHint.textContent = '';
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

            if (isCompanyTransfer()) {
                const transferFamilyNumber = getSelectValue(transferFamilySelect);

                if (transferFamilyNumber) {
                    params.append('family_number', transferFamilyNumber);
                } else {
                    params.append('family_move_to', 'new');
                }
            } else if (familyMoveSelect) {
                const moveTo = getSelectValue(familyMoveSelect) || 'keep';
                params.append('family_move_to', moveTo);

                if (moveTo === 'keep' && familyCodeInput.dataset.pilgrimId) {
                    params.append('pilgrim_id', familyCodeInput.dataset.pilgrimId);
                } else if (moveTo !== 'keep' && moveTo !== 'new') {
                    params.append('family_number', moveTo);
                }
            } else if (getSelectValue(createFamilySelect)) {
                params.append('family_number', getSelectValue(createFamilySelect));
            } else if (familyCodeInput.dataset.pilgrimId) {
                params.append('pilgrim_id', familyCodeInput.dataset.pilgrimId);
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

        async function handleCompanyChange() {
            toggleFamilyFields();
            await loadFamilies();
            await refreshFamilyCode();
        }

        function bindSelectChange(select, handler) {
            if (!select) {
                return;
            }

            select.addEventListener('change', handler);

            if (select.tomselect) {
                select.tomselect.on('change', handler);
            }
        }

        function initFamilyControls() {
            bindSelectChange(companySelect, handleCompanyChange);
            bindSelectChange(hajjYearInput, function () {
                updateAgeDisplay();
                handleCompanyChange();
            });
            bindSelectChange(createFamilySelect, function () {
                updateFamilyHint();
                refreshFamilyCode();
            });
            bindSelectChange(transferFamilySelect, refreshFamilyCode);
            bindSelectChange(familyMoveSelect, function () {
                updateFamilyMoveHint();
                refreshFamilyCode();
            });

            toggleFamilyFields();
            loadFamilies().then(refreshFamilyCode);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFamilyControls);
        } else {
            setTimeout(initFamilyControls, 0);
        }
    })();
</script>
@endpush
