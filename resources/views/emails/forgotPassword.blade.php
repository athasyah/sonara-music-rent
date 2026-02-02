<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Lupa Password</title>
</head>

<body style="margin:0; padding:0; background:#f1f5f9; font-family:Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:50px 15px;">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#2563eb; padding:24px; text-align:center; border-radius:12px 12px 0 0;">
                            <h1 style="margin:0; color:#ffffff; font-size:24px;">
                                Sonara Music Rent
                            </h1>
                            <p style="margin:6px 0 0; color:#dbeafe; font-size:14px;">
                                Verifikasi Lupa Password
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:32px;">
                            <p style="margin-top:0; color:#111827; font-size:16px;">
                                Halo 👋
                            </p>

                            <p style="color:#374151; line-height:1.7;">
                                Gunakan kode OTP berikut untuk memverifikasi.
                            </p>

                            <div style="margin:30px 0; text-align:center;">
                                <span
                                    style="
                                    display:inline-block;
                                    padding:16px 32px;
                                    font-size:28px;
                                    font-weight:700;
                                    letter-spacing:6px;
                                    color:#1e3a8a;
                                    background:#e0e7ff;
                                    border-radius:8px;
                                ">
                                    {{ $otp }}
                                </span>
                            </div>

                            <p style="color:#111827; font-weight:600;">
                                Berlaku selama <span style="color:#dc2626;">15 menit</span>
                            </p>

                            <p style="color:#6b7280; font-size:14px;">
                                Demi keamanan akun Anda, jangan bagikan kode ini kepada siapa pun.
                            </p>
                            <p style="color:#6b7280; font-size:14px;">
                                Abaikan email ini jika Anda tidak merasa melakukan permintaan reset password.
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:0 32px;">
                            <hr style="border:none; border-top:1px solid #e5e7eb;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px; text-align:center; font-size:12px; color:#9ca3af;">
                            © {{ date('Y') }} Sonara Music Rent
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
