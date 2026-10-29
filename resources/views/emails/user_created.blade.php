<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos accès collaborateur — ERP Suite</title>
</head>
<body style="margin: 0; padding: 0; background-color: #09090b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #f4f4f5; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #09090b; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 580px; background-color: #18181b; border-radius: 16px; border: 1px solid #27272a; overflow: hidden;">
                    <tr>
                        <td style="background-color: #18181b; padding: 28px 32px; border-bottom: 1px solid #27272a;">
                            <div style="font-size: 19px; font-weight: 900; color: #ffffff; text-transform: uppercase;">
                                ERP SUITE — PRESSING & LAVAGE AUTO
                            </div>
                            <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #a1a1aa; margin-top: 4px; font-weight: 600;">
                                Création de votre compte collaborateur
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            <h1 style="font-size: 20px; font-weight: 700; color: #ffffff; margin: 0 0 16px 0;">
                                Bonjour {{ $user->name }},
                            </h1>
                            <p style="font-size: 14px; line-height: 1.7; color: #d4d4d8; margin: 0 0 20px 0;">
                                Votre compte utilisateur a été créé sur la plateforme de gestion <strong style="color: #ffffff;">ERP Suite</strong>.
                            </p>
                            <div style="background-color: #09090b; border: 1px solid #27272a; border-radius: 12px; padding: 20px; margin-bottom: 24px; font-size: 13px; color: #d4d4d8; line-height: 1.8;">
                                <strong style="color: #ffffff;">Rôle :</strong> {{ $user->role === 'admin' ? 'Super Administrateur' : 'Caissier / Responsable Guichet' }}<br>
                                <strong style="color: #ffffff;">Établissement :</strong> {{ $user->pressing?->nom ?? 'Direction Générale (Réseau Global)' }}<br>
                                <strong style="color: #ffffff;">Identifiant (Email) :</strong> <span style="font-family: monospace; color: #ffffff;">{{ $user->email }}</span><br>
                                <strong style="color: #ffffff;">Mot de passe initial :</strong> <span style="font-family: monospace; color: #ffffff;">{{ $plainPassword }}</span>
                            </div>
                            <p style="font-size: 13px; color: #a1a1aa; line-height: 1.6; margin: 0;">
                                Connexion à l'interface : <a href="{{ route('login') }}" style="color: #ffffff; font-weight: bold;">{{ route('login') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
