<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Registrasi Berhasil</title>
</head>

<body style="margin:0;padding:0;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 0;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.05);">

    {{-- HEADER --}}
    <tr>
        <td style="background:#4f46e5;padding:20px;text-align:center;color:#ffffff;">
            <h1 style="margin:0;font-size:22px;">POS System</h1>
        </td>
    </tr>

    {{-- BODY --}}
    <tr>
        <td style="padding:30px;color:#333333;">

            <h2 style="margin-top:0;">Registrasi Berhasil 🎉</h2>

            <p>Halo <strong>{{ $user['name'] }}</strong>,</p>

            <p>
                Berikut akun POS anda.
            </p>

            <p>
                {{ $user['email'] }} <br>
                {{ $user['password_plain'] }} <br>
                {{ $user['created_at']->format('d M Y H:i') }}
            </p>

            <p style="color:#e11d48;">
                Segera ganti password untuk menjaga keamanan akun.
            </p>

            <div style="text-align:center;margin:30px 0;">
                <a href="#" 
                   style="
                   background:#4f46e5;
                   color:#ffffff;
                   padding:12px 24px;
                   text-decoration:none;
                   border-radius:6px;
                   display:inline-block;
                   font-weight:bold;">
                    Buka Aplikasi
                </a>
            </div>

            <p style="font-size:13px;color:#888;">
                Email ini dikirim otomatis oleh sistem.
            </p>

        </td>
    </tr>

    {{-- FOOTER --}}
    <tr>
        <td style="background:#f9fafb;padding:15px;text-align:center;font-size:12px;color:#999;">
            © {{ date('Y') }} POS System — All rights reserved
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>