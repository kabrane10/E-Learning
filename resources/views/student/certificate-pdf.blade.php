<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificat - {{ $course->title }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap');
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        .certificate {
            width: 100%;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            box-sizing: border-box;
        }
        
        .certificate-inner {
            background: white;
            height: 100%;
            border-radius: 20px;
            padding: 60px;
            box-sizing: border-box;
            position: relative;
            border: 8px solid #f3f4f6;
        }
        
        .border-decoration {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px solid #e5e7eb;
            pointer-events: none;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 20px;
        }
        
        .title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 18px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .content {
            text-align: center;
            margin: 50px 0;
        }
        
        .presented-to {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        
        .student-name {
            font-family: 'Playfair Display', serif;
            font-size: 52px;
            color: #4f46e5;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .course-title {
            font-size: 28px;
            color: #1f2937;
            margin-bottom: 40px;
        }
        
        .details {
            display: table;
            width: 100%;
            margin: 40px 0;
        }
        
        .detail-item {
            display: table-cell;
            text-align: center;
            width: 33%;
        }
        
        .detail-label {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 18px;
            color: #374151;
            font-weight: 600;
        }
        
        .signatures {
            display: table;
            width: 100%;
            margin-top: 60px;
        }
        
        .signature {
            display: table-cell;
            text-align: center;
            width: 50%;
        }
        
        .signature-line {
            width: 200px;
            height: 1px;
            background: #d1d5db;
            margin: 0 auto 10px;
        }
        
        .signature-name {
            font-size: 14px;
            color: #374151;
            font-weight: 600;
        }
        
        .signature-title {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .certificate-id {
            text-align: center;
            margin-top: 40px;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-inner">
            <div class="border-decoration"></div>
            
            <div class="header">
                <div class="logo">🎓 E-Learn</div>
                <div class="title">Certificat de Réussite</div>
                <div class="subtitle">Ce certificat est fièrement présenté à</div>
            </div>
            
            <div class="content">
                <div class="student-name">{{ $user->name }}</div>
                <div class="presented-to">pour avoir complété avec succès le cours</div>
                <div class="course-title">{{ $course->title }}</div>
            </div>
            
            <div class="details">
                <div class="detail-item">
                    <div class="detail-label">Date d'obtention</div>
                    <div class="detail-value">{{ $enrollment->completed_at->format('d/m/Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Durée du cours</div>
                    <div class="detail-value">{{ floor($course->total_duration / 3600) }} heures</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Formateur</div>
                    <div class="detail-value">{{ $course->instructor->name }}</div>
                </div>
            </div>
            
            <div class="signatures">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $course->instructor->name }}</div>
                    <div class="signature-title">Formateur</div>
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-name">E-Learn Platform</div>
                    <div class="signature-title">Directeur de la formation</div>
                </div>
            </div>
            
            <div class="certificate-id">
                ID du certificat : CERT-{{ strtoupper(substr(md5($user->id . $course->id . $enrollment->completed_at), 0, 16)) }}<br>
                Vérifiable en ligne sur {{ config('app.url') }}
            </div>
        </div>
    </div>
</body>
</html>