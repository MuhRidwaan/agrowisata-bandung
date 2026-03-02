<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | {{ config('app.name', 'Jabar Agro') }}</title>
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
            color: #dc3545;
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
        .btn-retry {
            background-color: #dc3545;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-retry:hover {
            background-color: #c82333;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
            color: white;
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
            <i class="fas fa-tools"></i>
        </div>
        <div class="error-code">500</div>
        <div class="error-message">Aduh, Ada Masalah di Server Kami!</div>
        <p class="error-description">
            Terjadi kesalahan yang tidak terduga pada server kami. 
            Kami sudah mencatat masalah ini dan sedang berusaha memperbaikinya secepat mungkin.
        </p>
        <a href="javascript:history.back()" class="btn btn-retry me-2">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary py-2 px-4 rounded-pill">
            Ke Beranda
        </a>
    </div>
</body>
</html>
