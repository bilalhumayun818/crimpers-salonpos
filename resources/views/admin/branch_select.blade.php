<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Select Branch — The Crimpers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #f3f4f6;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1e293b;
        }
        .header p {
            color: #64748b;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        .cards {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            flex: 1;
            min-width: 280px;
            max-width: 350px;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #F7DF79;
        }
        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: linear-gradient(90deg, #F5EFC0, #F7DF79);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .card:hover::before {
            opacity: 1;
        }
        .card h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #18181b;
        }
        .card p {
            color: #64748b;
            margin-bottom: 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .icon-wrap {
            width: 70px;
            height: 70px;
            background: #F5EFC0;
            color: #7A5C00;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            transition: transform 0.3s ease;
        }
        .card:hover .icon-wrap {
            transform: scale(1.1);
            background: #F7DF79;
        }
        .icon-wrap svg {
            width: 34px;
            height: 34px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>Please select a branch to continue</p>
        </div>
        <div class="cards">
            @foreach($branches as $branch)
            <form action="{{ route('admin.branch.switch_from_select') }}" method="POST" style="flex:1; min-width: 280px; max-width: 350px;">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <div class="card" onclick="this.parentNode.submit()">
                    <div class="icon-wrap">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h2>{{ $branch->name }}</h2>
                    <p>{{ $branch->address ?? 'Branch Location' }}</p>
                </div>
            </form>
            @endforeach
        </div>
    </div>
</body>
</html>
