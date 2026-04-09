# Implementation Plan: Global Invoice & About Settings

## Overview

This implementation adds 10 new settings to the existing Global Settings system (category "general") to enable customization of invoice pages and the about section. The implementation leverages existing infrastructure (Setting model, SettingController, helper functions) and only requires adding new data via migration/seeder and updating views to use the new settings.

## Tasks

- [x] 1. Create database migration for 10 new settings
  - Create migration file `2026_04_03_100000_add_invoice_about_settings.php`
  - Add 10 settings with category "general" (7 invoice keys + 3 about keys)
  - Use `updateOrInsert` pattern to avoid duplicates
  - Include rollback logic in `down()` method
  - _Requirements: 1.1-1.7, 2.1-2.3_

- [x] 2. Create seeder with default values
  - Create seeder file `InvoiceAboutSettingsSeeder.php`
  - Use `updateOrCreate` to prevent duplicate data
  - Set default values for all text/textarea fields
  - Leave file-type settings (logo, image) empty
  - _Requirements: 3.1-3.10_

- [x] 3. Update invoice view to use settings
  - [x] 3.1 Replace hardcoded company logo with `storage_asset_url(get_setting('invoice_company_logo'), asset('frontend/img/logo.png'))`
    - Add logo image element in invoice header
    - Use fallback to default logo if setting is empty
    - _Requirements: 4.2_
  
  - [x] 3.2 Replace hardcoded company information with settings
    - Replace company name with `get_setting('invoice_company_name', 'Agrowisata Tour')`
    - Replace address with `get_setting('invoice_company_address', 'Jl. Raya Pariwisata No. 123\nBandung, West Java')`
    - Replace phone with `get_setting('invoice_company_phone', '(022) 123-4567')`
    - Replace email with `get_setting('invoice_company_email', 'info@agrowisata.com')`
    - _Requirements: 4.3-4.6_
  
  - [x] 3.3 Replace hardcoded footer text and notes with settings
    - Replace footer text with `get_setting('invoice_footer_text', 'Thank you for your payment...')`
    - Replace important notes with `get_setting('invoice_notes', 'Thank you for your payment...')`
    - _Requirements: 4.7-4.8_
  
  - [ ]* 3.4 Test invoice print functionality
    - Verify invoice prints correctly with custom logo
    - Verify all text fields display properly
    - Test responsive layout on mobile
    - _Requirements: 4.9_

- [x] 4. Update about section to use settings
  - [x] 4.1 Update about section image to use settings
    - Replace hardcoded image path with `storage_asset_url(get_setting('about_image'), asset('frontend/img/logo.png'))`
    - Ensure fallback to default image works
    - _Requirements: 5.4_
  
  - [x] 4.2 Verify about section title and description
    - Confirm `get_setting('about_title')` is already in use
    - Confirm `get_setting('about_description')` is already in use
    - Update fallback values if needed
    - _Requirements: 5.2-5.3_
  
  - [ ]* 4.3 Test about section responsive layout
    - Verify section displays correctly on mobile
    - Verify section displays correctly on desktop
    - Verify CSS variables are properly applied
    - _Requirements: 5.5-5.6_

- [x] 5. Checkpoint - Verify all changes
  - Run migration: `php artisan migrate`
  - Run seeder: `php artisan db:seed --class=InvoiceAboutSettingsSeeder`
  - Verify 10 settings appear in Settings admin page under "general" tab
  - Test invoice page displays default values
  - Test about section displays default values
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional testing tasks and can be skipped for faster MVP
- All settings use category "general" for unified management (design decision)
- File uploads (logo, image) will be stored in `storage/app/public/branding/` folder
- Existing Settings infrastructure (model, controller, helpers) requires no changes
- All views use graceful fallbacks to ensure system works before settings are configured
