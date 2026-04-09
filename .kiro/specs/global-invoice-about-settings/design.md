# Design Document: Global Invoice & About Settings

## Overview

This feature adds 10 new configuration keys to the existing Global Settings system to enable customization of invoice pages and the about section on the homepage. All settings will be stored in the "general" category for unified management.

The implementation leverages the existing Settings infrastructure:
- `Setting` model with `getValue()` method
- `SettingController` with file upload handling
- Helper functions: `get_setting()` and `storage_asset_url()`
- Tab-based settings UI in `backend/settings/index.blade.php`

### Key Design Decisions

1. **Single Category Approach**: All 10 settings use category "general" instead of separate "invoice" and "about" categories. This simplifies the UI and avoids creating too many tabs.

2. **Graceful Fallbacks**: All views will use `get_setting()` with sensible default values to ensure the system works even before settings are configured.

3. **File Storage**: Logo and image files will be stored in the `branding` folder within public storage, consistent with existing file upload patterns.

4. **No Breaking Changes**: The existing Settings system remains unchanged; we only add new data via migration and seeder.

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                    Settings System                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Setting    │  │   Setting    │  │   Helper     │      │
│  │    Model     │  │  Controller  │  │  Functions   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ├─────────────────┬──────────────────┐
                            ▼                 ▼                  ▼
                    ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
                    │   Invoice    │  │    About     │  │   Settings   │
                    │     View     │  │   Section    │  │   Admin UI   │
                    └──────────────┘  └──────────────┘  └──────────────┘
```

### Data Flow

1. **Admin Configuration**:
   - Admin accesses Settings page → selects "general" tab
   - Fills in text fields or uploads images
   - SettingController validates and stores data
   - Files saved to `storage/app/public/branding/`

2. **Invoice Generation**:
   - PaymentController renders invoice view
   - View calls `get_setting('invoice_company_name', 'Agrowisata Tour')`
   - Helper retrieves value from database or returns fallback
   - For images: `storage_asset_url()` resolves file path

3. **About Section Display**:
   - FrontendController renders home page
   - About section calls `get_setting('about_title', 'Default Title')`
   - Image displayed via `storage_asset_url(get_setting('about_image'), asset('frontend/img/default.jpg'))`

## Components and Interfaces

### Database Schema

**Migration**: `2026_04_03_100000_add_invoice_about_settings.php`

```php
// Adds 10 rows to `settings` table
// Structure: id, key, value, category, label, type, created_at, updated_at
```

**Settings to be added**:

| Key | Category | Label | Type | Default Value |
|-----|----------|-------|------|---------------|
| invoice_company_logo | general | Company Logo | file | null |
| invoice_company_name | general | Company Name | text | Agrowisata Tour |
| invoice_company_address | general | Company Address | textarea | Jl. Raya Pariwisata No. 123\nBandung, West Java |
| invoice_company_phone | general | Company Phone | text | (022) 123-4567 |
| invoice_company_email | general | Company Email | text | info@agrowisata.com |
| invoice_footer_text | general | Invoice Footer Text | textarea | Thank you for your payment... |
| invoice_notes | general | Important Notes | textarea | Thank you for your payment... |
| about_title | general | About Title | text | Tentang Agrowisata Bandung |
| about_description | general | About Description | textarea | AgroBandung adalah platform... |
| about_image | general | About Image | file | null |

### Seeder Implementation

**Seeder**: `InvoiceAboutSettingsSeeder.php`

The seeder will use `updateOrCreate()` to safely insert default values without duplicating existing records.

```php
Setting::updateOrCreate(
    ['key' => 'invoice_company_name'],
    [
        'value' => 'Agrowisata Tour',
        'category' => 'general',
        'label' => 'Company Name',
        'type' => 'text'
    ]
);
```

### View Integration

#### Invoice View (`resources/views/backend/payments/invoice.blade.php`)

**Current State**: Hardcoded company information
**Target State**: Dynamic values from settings

Changes required:
- Replace hardcoded logo with `storage_asset_url(get_setting('invoice_company_logo'), asset('frontend/img/logo.png'))`
- Replace company name with `get_setting('invoice_company_name', 'Agrowisata Tour')`
- Replace address with `get_setting('invoice_company_address', 'Jl. Raya...')`
- Replace phone with `get_setting('invoice_company_phone', '(022) 123-4567')`
- Replace email with `get_setting('invoice_company_email', 'info@agrowisata.com')`
- Replace footer text with `get_setting('invoice_footer_text', 'Thank you...')`
- Replace notes with `get_setting('invoice_notes', 'Thank you...')`

#### About Section (`resources/views/frontend/home.blade.php`)

**Current State**: Uses `get_setting()` with basic fallbacks
**Target State**: Enhanced with new dedicated settings

The about section already uses:
- `get_setting('about_title', 'Tentang Agrotourism Bandung')`
- `get_setting('about_description', 'AgroBandung adalah...')`

Changes required:
- Update image source to use `storage_asset_url(get_setting('about_image'), asset('frontend/img/default.jpg'))`

### File Upload Handling

The existing `SettingController::update()` method already handles file uploads:

```php
if ($setting->type === 'file' && $request->hasFile($key)) {
    if ($setting->value) {
        Storage::disk('public')->delete($setting->value);
    }
    $path = $request->file($key)->store('branding', 'public');
    $setting->update(['value' => $path]);
}
```

**Validation Rules** (to be added in controller):
- File types: jpg, jpeg, png, webp
- Max size: 2MB (2048 KB)
- Storage path: `storage/app/public/branding/`

### Settings UI

The existing settings UI (`backend/settings/index.blade.php`) uses a tab-based layout. The "general" tab will display all 10 new settings alongside existing general settings.

**UI Behavior**:
- Text inputs render as `<input type="text">`
- Textarea inputs render as `<textarea rows="3">`
- File inputs render with preview and delete button
- All settings show their key in small text below the input

## Data Models

### Setting Model

**Existing Structure** (no changes needed):

```php
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'category', 'label', 'type'];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
```

**Table Schema**:
```sql
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) NOT NULL UNIQUE,
    value TEXT NULL,
    category VARCHAR(255) NOT NULL,
    label VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## Error Handling

### File Upload Errors

**Validation Failures**:
- Invalid file type → Display error: "File must be jpg, jpeg, png, or webp"
- File too large → Display error: "File size must not exceed 2MB"
- Upload failure → Display error: "Failed to upload file. Please try again."

**Implementation**:
```php
$request->validate([
    'invoice_company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    'about_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
]);
```

### Missing Settings

**Scenario**: Setting key doesn't exist in database
**Handling**: `get_setting()` returns the provided default value
**Example**: `get_setting('invoice_company_name', 'Agrowisata Tour')` returns "Agrowisata Tour" if key is missing

### Missing Files

**Scenario**: Setting value points to a file that doesn't exist
**Handling**: `storage_asset_url()` returns the fallback asset
**Example**: `storage_asset_url(get_setting('invoice_company_logo'), asset('frontend/img/logo.png'))` returns default logo if file is missing

### Database Errors

**Scenario**: Database connection fails during settings retrieval
**Handling**: Laravel's exception handler catches the error; application shows 500 error page
**Mitigation**: Use try-catch in critical paths if needed, but generally rely on Laravel's error handling

## Testing Strategy

### Unit Tests

This feature involves simple CRUD operations and view rendering. Property-based testing is not applicable here because:
- We're testing configuration management, not algorithmic logic
- Behavior doesn't vary meaningfully with different inputs
- This is primarily integration between existing components

**Test Coverage**:

1. **Migration Test**
   - Verify migration creates 10 settings records
   - Verify all keys, categories, labels, and types are correct
   - Verify default values are set correctly

2. **Seeder Test**
   - Verify seeder creates settings if they don't exist
   - Verify seeder doesn't duplicate settings on re-run
   - Verify all default values match requirements

3. **Setting Model Test**
   - Test `getValue()` returns correct value when key exists
   - Test `getValue()` returns default when key doesn't exist
   - Test `getValue()` handles null values correctly

4. **Helper Function Test**
   - Test `get_setting()` retrieves correct values
   - Test `storage_asset_url()` resolves file paths correctly
   - Test `storage_asset_url()` returns fallback for missing files

5. **Controller Test**
   - Test settings update with valid text input
   - Test file upload with valid image
   - Test file upload validation (type, size)
   - Test file deletion removes file from storage
   - Test settings page renders all categories

6. **View Integration Test**
   - Test invoice view displays settings values
   - Test invoice view uses fallbacks when settings are empty
   - Test about section displays settings values
   - Test about section uses fallbacks when settings are empty
   - Test invoice remains printable after integration

### Integration Tests

1. **End-to-End Settings Flow**
   - Admin uploads company logo → logo appears in invoice
   - Admin changes company name → name updates in invoice
   - Admin uploads about image → image appears in about section
   - Admin deletes logo → invoice shows default logo

2. **File Storage Integration**
   - Upload file → verify file exists in `storage/app/public/branding/`
   - Delete file → verify file removed from storage
   - Update file → verify old file deleted, new file stored

3. **UI Responsiveness**
   - Test settings form on mobile viewport
   - Test invoice print layout with custom logo
   - Test about section responsive layout with custom image

### Manual Testing Checklist

- [ ] Navigate to Settings page, verify "general" tab shows 10 new settings
- [ ] Upload company logo (valid image) → success message, preview shown
- [ ] Upload company logo (invalid file) → error message displayed
- [ ] Upload company logo (>2MB) → error message displayed
- [ ] Fill in all text fields → save → verify success message
- [ ] Navigate to invoice page → verify all custom values displayed
- [ ] Navigate to homepage → verify about section shows custom values
- [ ] Delete company logo → verify default logo shown in invoice
- [ ] Print invoice → verify layout is correct
- [ ] Test on mobile device → verify responsive layout
- [ ] Clear browser cache → verify images load correctly

### Edge Cases

1. **Empty Settings**: All views should display fallback values
2. **Special Characters**: Test company name with quotes, ampersands, etc.
3. **Long Text**: Test with very long company address (>500 chars)
4. **Unicode**: Test with non-ASCII characters (Indonesian, emoji)
5. **Concurrent Updates**: Two admins editing settings simultaneously
6. **Storage Full**: Disk space exhausted during file upload

## Implementation Notes

### Migration Order

The migration should run after existing settings migrations. Suggested timestamp: `2026_04_03_100000`

### Seeder Execution

The seeder should be called in `DatabaseSeeder.php` or run manually:
```bash
php artisan db:seed --class=InvoiceAboutSettingsSeeder
```

### Deployment Steps

1. Run migration: `php artisan migrate`
2. Run seeder: `php artisan db:seed --class=InvoiceAboutSettingsSeeder`
3. Ensure storage link exists: `php artisan storage:link`
4. Clear cache: `php artisan config:clear && php artisan view:clear`

### Performance Considerations

- Settings are queried individually via `get_setting()`, which hits the database each time
- For high-traffic pages, consider caching settings in Redis or file cache
- Current implementation is acceptable for low-to-medium traffic

### Security Considerations

- File uploads are validated for type and size
- Files stored outside web root (in `storage/app/public/`)
- Only authenticated admins can access settings page (existing middleware)
- CSRF protection on settings update form (existing)

## Future Enhancements

1. **Settings Caching**: Cache settings in Redis to reduce database queries
2. **Image Optimization**: Automatically resize/compress uploaded images
3. **Multi-language Support**: Add locale-specific settings for invoice/about
4. **Settings History**: Track changes to settings with audit log
5. **Bulk Import/Export**: Allow admins to export/import settings as JSON
6. **Preview Mode**: Show live preview of invoice/about before saving settings
