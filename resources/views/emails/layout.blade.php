<!doctype html>
<html lang="fr"><head><meta charset="utf-8"><title>{{ $title ?? 'VOKAMIN' }}</title></head>
<body style="margin:0;background:#f4f6f7;font-family:Montserrat,Arial,sans-serif;color:#141a2c">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f7;padding:24px 0"><tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff">
<tr><td style="background:#08153a;padding:24px 32px"><img src="{{ asset('images/logo-vokamin-blanc.png') }}" alt="VOKAMIN Côte d'Ivoire" height="48" style="height:48px"></td></tr>
<tr><td style="padding:32px;font-size:15px;line-height:1.6">@yield('body')</td></tr>
<tr><td style="padding:20px 32px;background:#f4f6f7;font-size:12px;color:#5a6275">{{ setting('company_name') }} · {{ setting('address') }} · {{ setting('bp') }}<br>{{ setting('tel1') }} · {{ setting('email') }}</td></tr>
</table></td></tr></table>
</body></html>
