<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#111827">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Crimpers POS">
    <title>Login — The Crimpers</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.svg') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--y1:#F7DF79;--y2:#FBEFBC;--yd:#c9a800;--yk:#a07800;--ybg:#fffdf0;}
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Outfit',sans-serif;}

        body{
            min-height:100vh;
            display:flex;
            background:#0f0f0f;
            overflow-x:hidden;
        }
        @media(max-width:480px){
            body{overflow-y:auto;}
        }

        /* Left decorative panel */
        .auth-left{
            flex:1;
            background:linear-gradient(160deg,#1a1a1a 0%,#111 100%);
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:60px 40px;
            position:relative;
            overflow:hidden;
        }
        .auth-left::before{
            content:'';position:absolute;top:-80px;left:-80px;
            width:400px;height:400px;border-radius:50%;
            background:radial-gradient(circle,rgba(247,223,121,.12) 0%,transparent 70%);
        }
        .auth-left::after{
            content:'';position:absolute;bottom:-100px;right:-60px;
            width:300px;height:300px;border-radius:50%;
            background:radial-gradient(circle,rgba(247,223,121,.07) 0%,transparent 70%);
        }
        .auth-left-content{position:relative;z-index:1;text-align:center;max-width:340px;}
        .auth-brand-icon{
            width:72px;height:72px;border-radius:20px;
            background:linear-gradient(135deg,var(--y1),var(--yd));
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 24px;
            box-shadow:0 12px 30px rgba(199,168,0,.3);
        }
        .auth-brand-name{font-size:2rem;font-weight:800;color:#fff;letter-spacing:-.03em;margin-bottom:8px;}
        .auth-brand-sub{font-size:.9rem;color:#71717a;line-height:1.6;}
        .auth-features{margin-top:40px;display:flex;flex-direction:column;gap:14px;text-align:left;}
        .auth-feature{display:flex;align-items:center;gap:12px;color:#a1a1aa;font-size:.85rem;}
        .auth-feature-dot{width:28px;height:28px;border-radius:8px;background:rgba(247,223,121,.1);border:1px solid rgba(247,223,121,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--y1);}

        /* Right form panel */
        .auth-right{
            width:460px;
            background:#fff;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:50px 48px;
            position:relative;
        }
        @media(max-width:768px){
            .auth-left{display:none;}
            .auth-right{width:100%;padding:40px 24px;min-height:100vh;justify-content:flex-start;padding-top:60px;}
            .form-header{margin-bottom:24px;}
            .btn-login{padding:15px;font-size:1rem;}
            .f-input{padding:13px 14px;font-size:1rem;}
        }
        @media(max-width:380px){
            .auth-right{padding:40px 18px;}
        }

        .form-header{width:100%;margin-bottom:32px;}
        .form-header h2{font-size:1.6rem;font-weight:800;color:#0f172a;letter-spacing:-.02em;margin-bottom:6px;}
        .form-header p{font-size:.875rem;color:#64748b;}

        /* Type selector */
        .type-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:28px;width:100%;}
        .type-btn{
            padding:14px 12px;border:1.5px solid #e2e8f0;border-radius:14px;
            background:#fff;cursor:pointer;transition:.2s;
            display:flex;flex-direction:column;align-items:center;gap:7px;
        }
        .type-btn:hover{border-color:var(--yd);background:var(--ybg);}
        .type-btn.active{border-color:var(--yd);background:var(--ybg);box-shadow:0 0 0 3px rgba(199,168,0,.12);}
        .type-btn-icon{width:36px;height:36px;border-radius:10px;background:#f4f4f5;display:flex;align-items:center;justify-content:center;color:#52525b;transition:.2s;}
        .type-btn.active .type-btn-icon{background:var(--y2);color:var(--yk);}
        .type-btn-label{font-size:.78rem;font-weight:700;color:#374151;}
        .type-btn.active .type-btn-label{color:var(--yk);}

        /* Form */
        .login-form{width:100%;}
        .login-form.visible{animation:fadeUp .3s ease-out;}

        .f-group{margin-bottom:18px;}
        .f-label{display:block;font-size:.82rem;font-weight:700;color:#334155;margin-bottom:7px;}
        .f-input{
            width:100%;padding:11px 14px;
            border:1.5px solid #e2e8f0;border-radius:11px;
            font-family:'Outfit',sans-serif;font-size:.9rem;color:#18181b;
            outline:none;transition:.2s;background:#fff;
        }
        .f-input:focus{border-color:var(--yd);box-shadow:0 0 0 3px rgba(199,168,0,.12);}
        .pw-wrap{position:relative;}
        .pw-wrap .f-input{padding-right:44px;}
        .pw-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;display:flex;align-items:center;padding:4px;}
        .pw-toggle:hover{color:#52525b;}

        .btn-login{
            width:100%;padding:13px;
            background:linear-gradient(135deg,var(--y1),var(--yd));
            border:none;border-radius:11px;
            color:#18181b;font-size:.95rem;font-weight:700;
            cursor:pointer;font-family:'Outfit',sans-serif;
            box-shadow:0 4px 14px rgba(199,168,0,.3);
            transition:.2s;margin-top:6px;
        }
        .btn-login:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(199,168,0,.4);}

        .error-box{
            background:#fef2f2;border:1.5px solid #fecaca;
            border-radius:10px;padding:11px 14px;
            margin-bottom:20px;color:#dc2626;
            font-size:.83rem;font-weight:600;
            display:flex;align-items:center;gap:8px;
        }

        .form-footer{margin-top:20px;text-align:center;}
        .form-footer a{font-size:.82rem;color:#64748b;text-decoration:none;font-weight:600;}
        .form-footer a:hover{color:#18181b;}

        .auth-footer{position:absolute;bottom:20px;font-size:.72rem;color:#94a3b8;font-weight:500;}

        @keyframes fadeUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    </style>
</head>
<body>

    {{-- Left Panel --}}
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="auth-brand-icon">
                <svg width="36" height="36" fill="none" stroke="#18181b" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 21a9 9 0 110-18 9 9 0 010 18z"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <div class="auth-brand-name">The Crimpers</div>
            <div class="auth-brand-sub">Professional salon management system for modern businesses.</div>

            <div class="auth-features">
                <div class="auth-feature">
                    <div class="auth-feature-dot">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    POS Terminal & Invoicing
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-dot">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    Staff & Attendance Management
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-dot">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    Inventory & Product Control
                </div>
                <div class="auth-feature">
                    <div class="auth-feature-dot">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    Appointments & Bookings
                </div>
            </div>
        </div>
    </div>

    {{-- Right Form Panel --}}
    <div class="auth-right">
        <div class="form-header">
            <h2>Welcome back</h2>
            <p>Sign in to your account to continue</p>
        </div>



        @if($errors->any())
        <div class="error-box" style="width:100%;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm" class="login-form">
            @csrf

            <div class="f-group">
                <label class="f-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="f-input" placeholder="your@email.com" autofocus>
            </div>

            <div class="f-group">
                <label class="f-label">Password</label>
                <div class="pw-wrap">
                    <input type="password" name="password" id="password" required class="f-input" placeholder="••••••••">
                    <button type="button" class="pw-toggle" onclick="togglePassword()">
                        <svg id="eye-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="form-footer" style="width:100%;">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
        </div>

        <div class="auth-footer">Powered by The BroshTech</div>
    </div>

    <script>

        function togglePassword() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                pass.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

    </script>
</body>
</html>
