# Requirements Document

## Introduction

Fitur ini menambahkan 10 key settings baru ke sistem Global Settings yang sudah ada untuk mengkustomisasi halaman Invoice dan About Section. Sistem Global Settings sudah memiliki SettingController, Model Setting, helper functions (get_setting, storage_asset_url), dan view dengan tab-based UI. Fitur ini hanya menambahkan settings baru dengan category "invoice" (7 keys) dan "about" (3 keys), kemudian mengintegrasikannya ke invoice page dan about section di homepage frontend.

## Glossary

- **Settings_System**: Sistem pengaturan global yang sudah ada dengan SettingController, Model Setting, dan view backend/settings/index.blade.php
- **Invoice_Page**: Halaman invoice pembayaran di resources/views/backend/payments/invoice.blade.php
- **About_Section**: Bagian "Tentang Kami" di homepage frontend (resources/views/frontend/home.blade.php)
- **Migration**: File database migration untuk menambahkan 11 settings baru
- **Seeder**: File seeder untuk mengisi default value settings baru

## Requirements

### Requirement 1: Add Invoice Settings Keys

**User Story:** Sebagai admin, saya ingin menambahkan 7 key settings baru untuk invoice, sehingga informasi invoice dapat dikustomisasi melalui Global Settings.

#### Acceptance Criteria

1. THE Migration SHALL menambahkan setting dengan key "invoice_company_logo", category "invoice", label "Company Logo", type "file"
2. THE Migration SHALL menambahkan setting dengan key "invoice_company_name", category "invoice", label "Company Name", type "text"
3. THE Migration SHALL menambahkan setting dengan key "invoice_company_address", category "invoice", label "Company Address", type "textarea"
4. THE Migration SHALL menambahkan setting dengan key "invoice_company_phone", category "invoice", label "Company Phone", type "text"
5. THE Migration SHALL menambahkan setting dengan key "invoice_company_email", category "invoice", label "Company Email", type "text"
6. THE Migration SHALL menambahkan setting dengan key "invoice_footer_text", category "invoice", label "Invoice Footer Text", type "textarea"
7. THE Migration SHALL menambahkan setting dengan key "invoice_notes", category "invoice", label "Important Notes", type "textarea"

### Requirement 2: Add About Settings Keys

**User Story:** Sebagai admin, saya ingin menambahkan 3 key settings baru untuk about section, sehingga konten about dapat dikustomisasi melalui Global Settings.

#### Acceptance Criteria

1. THE Migration SHALL menambahkan setting dengan key "about_title", category "about", label "About Title", type "text"
2. THE Migration SHALL menambahkan setting dengan key "about_description", category "about", label "About Description", type "textarea"
3. THE Migration SHALL menambahkan setting dengan key "about_image", category "about", label "About Image", type "file"

### Requirement 3: Settings Seeder with Default Values

**User Story:** Sebagai developer, saya ingin seeder mengisi default values untuk settings baru, sehingga sistem dapat langsung digunakan dengan nilai default yang masuk akal.

#### Acceptance Criteria

1. THE Seeder SHALL menggunakan method updateOrCreate untuk menghindari duplikasi data
2. THE Seeder SHALL mengisi default value untuk "invoice_company_name" dengan "Agrowisata Tour"
3. THE Seeder SHALL mengisi default value untuk "invoice_company_address" dengan "Jl. Raya Pariwisata No. 123\nBandung, West Java"
4. THE Seeder SHALL mengisi default value untuk "invoice_company_phone" dengan "(022) 123-4567"
5. THE Seeder SHALL mengisi default value untuk "invoice_company_email" dengan "info@agrowisata.com"
6. THE Seeder SHALL mengisi default value untuk "invoice_footer_text" dengan "Thank you for your payment. Please keep this invoice and show it to our staff during re-registration at the Agrowisata location."
7. THE Seeder SHALL mengisi default value untuk "invoice_notes" dengan "Thank you for your payment. Please keep this invoice and show it to our staff during re-registration at the Agrowisata location."
8. THE Seeder SHALL mengisi default value untuk "about_title" dengan "Tentang Agrowisata Bandung"
9. THE Seeder SHALL mengisi default value untuk "about_description" dengan deskripsi singkat tentang perusahaan
10. THE Seeder SHALL mengosongkan value untuk settings bertipe file (logo dan image)

### Requirement 4: Invoice Page Integration

**User Story:** Sebagai customer, saya ingin melihat invoice dengan informasi perusahaan yang lengkap dari settings, sehingga invoice terlihat profesional.

#### Acceptance Criteria

1. WHEN Invoice_Page ditampilkan, THE Invoice_Page SHALL menggunakan helper get_setting untuk mengambil data invoice settings
2. THE Invoice_Page SHALL menampilkan company logo menggunakan storage_asset_url dengan fallback ke logo default
3. THE Invoice_Page SHALL menampilkan company name dari setting "invoice_company_name" dengan fallback "Agrowisata Tour"
4. THE Invoice_Page SHALL menampilkan company address dari setting "invoice_company_address" dengan fallback alamat hardcoded
5. THE Invoice_Page SHALL menampilkan company phone dari setting "invoice_company_phone" dengan fallback nomor hardcoded
6. THE Invoice_Page SHALL menampilkan company email dari setting "invoice_company_email" dengan fallback email hardcoded
7. THE Invoice_Page SHALL menampilkan footer text dari setting "invoice_footer_text" dengan fallback teks hardcoded
8. THE Invoice_Page SHALL menampilkan important notes dari setting "invoice_notes" dengan fallback teks hardcoded
9. THE Invoice_Page SHALL tetap dapat dicetak dengan baik setelah integrasi settings

### Requirement 5: About Section Integration

**User Story:** Sebagai pengunjung website, saya ingin melihat informasi tentang perusahaan yang akurat dari settings, sehingga saya dapat mengenal perusahaan dengan baik.

#### Acceptance Criteria

1. WHEN About_Section ditampilkan di homepage, THE About_Section SHALL menggunakan helper get_setting untuk mengambil data about settings
2. THE About_Section SHALL menampilkan title dari setting "about_title" dengan fallback "Tentang Agrowisata Bandung"
3. THE About_Section SHALL menampilkan description dari setting "about_description" dengan fallback deskripsi hardcoded
4. THE About_Section SHALL menampilkan image menggunakan storage_asset_url dengan fallback ke gambar default
5. THE About_Section SHALL tetap responsive di mobile dan desktop setelah integrasi settings
6. THE About_Section SHALL menggunakan CSS variables dari frontend-ui-ux guide (--agro-primary, --font-display, dll)

### Requirement 6: File Upload Validation

**User Story:** Sebagai admin, saya ingin sistem memvalidasi file upload, sehingga hanya file yang valid yang dapat disimpan.

#### Acceptance Criteria

1. WHEN admin mengupload file untuk invoice_company_logo, THE Settings_System SHALL memvalidasi format file (jpg, jpeg, png, webp)
2. WHEN admin mengupload file untuk about_image, THE Settings_System SHALL memvalidasi format file (jpg, jpeg, png, webp)
3. WHEN admin mengupload file, THE Settings_System SHALL memvalidasi ukuran maksimal 2MB
4. WHEN validasi gagal, THE Settings_System SHALL menampilkan pesan error yang jelas
5. WHEN admin menghapus file, THE Settings_System SHALL menghapus file dari storage dan mengosongkan value di database
6. THE Settings_System SHALL menyimpan file ke folder "branding" di storage public
