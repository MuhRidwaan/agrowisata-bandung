<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | {{ config('app.name', 'Jabar Agro') }}</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Source Sans Pro', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }
        .error-code {
            font-size: 100px;
            font-weight: 900;
            color: #28a745;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 24px;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 15px;
        }
        .error-description {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn-home {
            background-color: #28a745;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-home:hover {
            background-color: #218838;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        .illustration {
            font-size: 80px;
            color: #e9ecef;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="illustration">
            <i class="fas fa-map-marked-alt"></i>
        </div>
        <div class="error-code">404</div>
        <div class="error-message">Waduh! Halaman Hilang.</div>
        <p class="error-description">
            Sepertinya rute yang Anda tuju tidak ada atau sudah dipindahkan. 
            Jangan khawatir, mari kembali ke jalan yang benar.
        </p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-home">
            <i class="fas fa-home me-2"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>
