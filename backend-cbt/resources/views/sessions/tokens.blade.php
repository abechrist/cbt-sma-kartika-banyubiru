<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Token Ujian - {{ $session->name }} - SMA Kartika III-1 Banyubiru</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; padding: 24px; color: #1e293b; background: #f8fafc; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #12472e; padding-bottom: 12px; }
        .header h1 { font-size: 16px; font-weight: 800; color: #12472e; text-transform: uppercase; letter-spacing: 0.5px; }
        .header h2 { font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 2px; }
        .header p { font-size: 11px; color: #64748b; margin-top: 4px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .token-card { 
            border: 1.5px dashed #94a3b8; 
            padding: 14px 12px; 
            text-align: center; 
            background: white; 
            border-radius: 8px; 
            position: relative;
        }
        .token-card .school-title { font-size: 9px; font-weight: 800; color: #12472e; text-transform: uppercase; margin-bottom: 4px; }
        .token-card .token { 
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; 
            font-size: 20px; 
            font-weight: 900; 
            letter-spacing: 3px; 
            color: #0f172a;
            padding: 4px 0;
            background: #f1f5f9;
            border-radius: 6px;
            margin: 6px 0;
        }
        .token-card .info { font-size: 9px; color: #64748b; }
        .token-card .exp { font-size: 9px; font-weight: 700; color: #d97706; margin-top: 2px; }
        @media print {
            body { padding: 8px; background: white; }
            .no-print { display: none; }
            .grid { gap: 8px; }
            .token-card { border: 1px dashed #64748b; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 24px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 24px; background: #12472e; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            🖨 Cetak Lembar Token Peserta
        </button>
    </div>
    
    <div class="header">
        <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo Kartika Jaya" style="width: 54px; height: 54px; object-fit: contain; margin: 0 auto 8px auto; display: block;">
        <h1>SMA KARTIKA III-1 BANYUBIRU</h1>
        <h2>LEMBAR TOKEN RUANG UJIAN: {{ $session->name }}</h2>
        <p>{{ $session->exam->name }} • Ruang: {{ $session->room ?? 'Lab CBT' }} • Jadwal: {{ $session->start_at->format('d/m/Y H:i') }} - {{ $session->end_at->format('H:i') }} WIB</p>
    </div>
    
    <div class="grid">
        @foreach($tokens as $token)
        <div class="token-card">
            <div style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 6px;">
                <img src="{{ asset('images/logo-kartika.png') }}" alt="Logo" style="width: 18px; height: 18px; object-fit: contain;">
                <div class="school-title" style="margin-bottom: 0;">SMA KARTIKA III-1 CBT</div>
            </div>
            <div class="token">{{ $token->token }}</div>
            <div class="info">{{ $session->name }}</div>
            <div class="exp">Batas: {{ $token->expires_at->format('H:i') }} WIB</div>
        </div>
        @endforeach
    </div>
</body>
</html>