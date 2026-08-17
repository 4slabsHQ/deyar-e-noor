@php
    $companyName = $pilgrim->company?->name ?? config('branding.legal_name');
    $companyCode = $pilgrim->company?->code;
    $companyLogoUrl = $pilgrim->company?->logo
        ? Storage::url($pilgrim->company->logo)
        : asset('images/logo.png');
    $companyLogoAlt = $pilgrim->company?->name ?? config('branding.title');
@endphp

<article class="pilgrim-registration-doc">
    <header class="pilgrim-doc-header">
        <div class="pilgrim-doc-header-top">
            <div class="pilgrim-doc-logo-wrap">
                <img src="{{ $companyLogoUrl }}" alt="{{ $companyLogoAlt }}" class="pilgrim-doc-logo">
            </div>

            <div class="pilgrim-doc-title-wrap">
                <h1 class="pilgrim-doc-title">Hajj Registration Form</h1>
            </div>

            <div class="pilgrim-doc-photo-wrap">
                <span class="pilgrim-doc-photo-label">Photograph</span>
                @if ($pilgrim->photo_url)
                    <img src="{{ $pilgrim->photo_url }}" alt="Pilgrim photo" class="pilgrim-doc-photo">
                @else
                    <div class="pilgrim-doc-photo placeholder">No Photo</div>
                @endif
            </div>
        </div>

        <div class="pilgrim-doc-key-info">
            <div class="pilgrim-doc-key-item">
                <span class="key-label">Family Code</span>
                <span class="key-value">{{ $pilgrim->family_code }}</span>
            </div>
            <div class="pilgrim-doc-key-item">
                <span class="key-label">Passport No</span>
                <span class="key-value">{{ $pilgrim->passport_no }}</span>
            </div>
            <div class="pilgrim-doc-key-item">
                <span class="key-label">Hajj Year</span>
                <span class="key-value">{{ $pilgrim->hajj_year }}</span>
            </div>
        </div>
    </header>

    <div class="pilgrim-doc-body">
        <section class="pilgrim-doc-section">
            <h2 class="pilgrim-doc-section-title">Registration Details</h2>
            <div class="pilgrim-doc-grid">
                <div class="pilgrim-doc-field">
                    <span class="field-label">Booking Date</span>
                    <span class="field-value">{{ $pilgrim->booking_date?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Form Owner</span>
                    <span class="field-value">{{ $pilgrim->formOwner?->name ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Company</span>
                    <span class="field-value">
                        @if ($companyName)
                            {{ $companyName }}@if($companyCode) ({{ $companyCode }})@endif
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Maktab Category</span>
                    <span class="field-value">
                        @if ($pilgrim->maktabCategory)
                            {{ $pilgrim->maktabCategory->name }} ({{ $pilgrim->maktabCategory->zone }})
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Package</span>
                    <span class="field-value">
                        @if ($pilgrim->package)
                            {{ $pilgrim->package->number }} — {{ $pilgrim->package->name }}
                        @else
                            —
                        @endif
                    </span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Care Off</span>
                    <span class="field-value">{{ $pilgrim->careOff?->name ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">POD (City)</span>
                    <span class="field-value">{{ $pilgrim->podCity?->name ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Room Type</span>
                    <span class="field-value">{{ $pilgrim->roomType?->name ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Family Code</span>
                    <span class="field-value">{{ $pilgrim->family_code ?? '—' }}</span>
                </div>
            </div>
        </section>

        <section class="pilgrim-doc-section">
            <h2 class="pilgrim-doc-section-title">Personal Details</h2>
            <div class="pilgrim-doc-grid">
                <div class="pilgrim-doc-field">
                    <span class="field-label">Full Name</span>
                    <span class="field-value">{{ $pilgrim->full_name }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Gender</span>
                    <span class="field-value">{{ $pilgrim->gender?->label() ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Surname</span>
                    <span class="field-value">{{ $pilgrim->surname }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Given Name</span>
                    <span class="field-value">{{ $pilgrim->given_name }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Father / Husband Name</span>
                    <span class="field-value">{{ $pilgrim->father_husband_name }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Age</span>
                    <span class="field-value">{{ $pilgrim->age }} years</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Date of Birth</span>
                    <span class="field-value">{{ $pilgrim->date_of_birth?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Birth Place</span>
                    <span class="field-value">{{ $pilgrim->birth_place }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Blood Group</span>
                    <span class="field-value">{{ $pilgrim->blood_group?->label() ?? '—' }}</span>
                </div>
            </div>
        </section>

        <section class="pilgrim-doc-section">
            <h2 class="pilgrim-doc-section-title">Passport & Contact</h2>
            <div class="pilgrim-doc-grid">
                <div class="pilgrim-doc-field">
                    <span class="field-label">Passport No</span>
                    <span class="field-value">{{ $pilgrim->passport_no }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Passport Expiry</span>
                    <span class="field-value">{{ $pilgrim->passport_expiry?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">CNIC</span>
                    <span class="field-value">{{ $pilgrim->cnic }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Mobile</span>
                    <span class="field-value">{{ $pilgrim->mobile }}</span>
                </div>
                <div class="pilgrim-doc-field full-width">
                    <span class="field-label">Address</span>
                    <span class="field-value">{{ $pilgrim->address }}</span>
                </div>
            </div>
        </section>

        <section class="pilgrim-doc-section">
            <h2 class="pilgrim-doc-section-title">Mehram</h2>
            <div class="pilgrim-doc-grid">
                <div class="pilgrim-doc-field">
                    <span class="field-label">Mehram Name</span>
                    <span class="field-value">{{ $pilgrim->mehram_name }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Relation</span>
                    <span class="field-value">{{ $pilgrim->mehramRelation?->name ?? '—' }}</span>
                </div>
            </div>
        </section>

        <section class="pilgrim-doc-section">
            <h2 class="pilgrim-doc-section-title">Waris</h2>
            <div class="pilgrim-doc-grid">
                <div class="pilgrim-doc-field">
                    <span class="field-label">Waris Name</span>
                    <span class="field-value">{{ $pilgrim->waris_name }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Relation</span>
                    <span class="field-value">{{ $pilgrim->warisRelation?->name ?? '—' }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Waris CNIC</span>
                    <span class="field-value">{{ $pilgrim->waris_cnic }}</span>
                </div>
                <div class="pilgrim-doc-field">
                    <span class="field-label">Waris Mobile</span>
                    <span class="field-value">{{ $pilgrim->waris_mobile }}</span>
                </div>
            </div>
        </section>
    </div>

    <footer class="pilgrim-doc-footer">
        <span>Registered on {{ $pilgrim->created_at?->format('d M Y, h:i A') ?? '—' }}</span>
        <span>Document generated {{ now()->format('d M Y, h:i A') }}</span>
    </footer>
</article>
