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
        :root {
            color-scheme: light;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #fefce8 0%, #f3f4f6 100%);
            color: #111827;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: min(920px, 100%);
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
        }

        .header .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(247, 223, 121, 0.22);
            color: #7a5c00;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 8px 12px;
            border-radius: 999px;
            margin-bottom: 12px;
        }

        .header h1 {
            font-size: clamp(1.6rem, 3vw, 2.3rem);
            font-weight: 800;
            color: #111827;
            margin: 0 0 8px;
        }

        .header p {
            color: #64748b;
            font-size: clamp(0.95rem, 2vw, 1.05rem);
            margin: 0 auto;
            max-width: 560px;
            line-height: 1.6;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .card-form {
            display: flex;
        }

        .card {
            width: 100%;
            background: white;
            border-radius: 22px;
            padding: 24px 20px;
            text-align: left;
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card:hover,
        .card:focus-within {
            transform: translateY(-4px);
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
            border-color: #f7df79;
        }

        .card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 5px;
            background: linear-gradient(90deg, #f5efc0, #f7df79);
        }

        .icon-wrap {
            width: 56px;
            height: 56px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .icon-wrap svg {
            width: 28px;
            height: 28px;
        }

        .card h2 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0 0 8px;
            color: #111827;
        }

        .card p {
            color: #64748b;
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .card .meta {
            margin-top: 14px;
            color: #7a5c00;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        @media (max-width: 768px) {
            body {
                padding: 16px;
                align-items: flex-start;
            }

            .container {
                padding-top: 10px;
            }

            .header {
                margin-bottom: 22px;
            }

            .cards {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .card {
                min-height: 170px;
                padding: 20px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="eyebrow">Crimpers POS</div>
            <h1>Welcome, {{ auth()->user()->name }}</h1>
            <p>Select the branch you want to work in, then you’ll be taken straight to your dashboard.</p>
        </div>

        <div class="cards">
            @foreach($branches as $branch)
            <form action="{{ route('admin.branch.switch_from_select') }}" method="POST" class="card-form">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <button type="submit" class="card" style="border:0; appearance:none;">
                    <div class="icon-wrap">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h2>{{ $branch->name }}</h2>
                    <p>{{ $branch->address ?? 'Branch location will appear here.' }}</p>
                    <div class="meta">Open branch</div>
                </button>
            </form>
            @endforeach
        </div>
    </div>
</body>
</html>
