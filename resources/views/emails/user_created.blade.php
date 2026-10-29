<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accès ERP Suite</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f4f4f5; font-family: Arial, sans-serif; color: #18181b; font-size: 13px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border: 1px solid #e4e4e7; border-radius: 10px; padding: 20px;">
        <div style="border-bottom: 1px solid #e4e4e7; padding-bottom: 10px; margin-bottom: 12px;">
            <strong style="font-size: 15px;">ERP SUITE — Accès Collaborateur</strong>
        </div>

        <p style="margin: 0 0 12px 0;">Bonjour <strong>{{ $user->name }}</strong>,</p>

        <div style="background: #f4f4f5; border-radius: 8px; padding: 12px; font-size: 12px; line-height: 1.6;">
            <strong>Rôle :</strong> {{ $user->role === 'admin' ? 'Admin' : 'Caissier' }} ({{ $user->pressing?->nom ?? 'Global' }})<br>
            <strong>Email :</strong> {{ $user->email }}<br>
            <strong>Mot de passe :</strong> {{ $plainPassword }}<br>
            <strong>Lien :</strong> <a href="{{ route('login') }}">{{ route('login') }}</a>
        </div>
    </div>
</body>
</html>
