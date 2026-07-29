@component('mail::message')
# Pendaftaran Universitas Disetujui

Yth. {{ $credentials['name'] }},

Selamat! Pendaftaran universitas {{ $credentials['nama_universitas'] }} telah disetujui.

Berikut adalah kredensial login Anda:<br>
- **Nama:** {{ $credentials['name'] }}
- **Email:** {{ $credentials['email'] }}
- **Peran:** {{ $credentials['otoritas'] }}

Silakan login menggunakan kredensial di atas.

@component('mail::button', ['url' => url('/login')])
Login Sekarang
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}

<small>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</small>
@endcomponent