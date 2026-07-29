@component('mail::message')
# Pendaftaran Universitas Ditolak

Yth. {{ $credentials['name'] }},

Kami informasikan bahwa pendaftaran universitas **{{ $credentials['nama_universitas'] }}** telah **ditolak** dengan detail sebagai berikut:

**Alasan Penolakan:**  
{{ $reason ?? 'Tidak ada alasan spesifik yang diberikan.' }}

**Detail Pendaftaran:**
- Nama Universitas: {{ $credentials['nama_universitas'] }}
- Email: {{ $credentials['email'] }}
- Tanggal Pendaftaran: {{ $credentials['tanggal_pendaftaran'] }}

Hormat kami,<br>
{{ config('app.name') }}

<small>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</small>
@endcomponent