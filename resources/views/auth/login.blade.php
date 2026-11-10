<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - LipSip POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --navy: #0b1f3a;
            --navy-deep: #071528;
            --royal: #1d4ed8;
            --violet: #5b21b6;
            --cyan: #22d3ee;
            --cyan-soft: #67e8f9;
            --ink: #0f172a;
            --muted: #64748b;
            --card: #ffffff;
            --line: rgba(148, 163, 184, 0.28);
            --shadow: 0 24px 60px rgba(7, 21, 40, 0.28);
            --radius: 1.25rem;
            --font-display: "Outfit", system-ui, sans-serif;
            --font-body: "Source Sans 3", system-ui, sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-body);
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 18%, rgba(34, 211, 238, 0.22), transparent 34%),
                radial-gradient(circle at 88% 12%, rgba(91, 33, 182, 0.28), transparent 32%),
                linear-gradient(135deg, #071528 0%, #0b1f3a 42%, #123a7a 72%, #1e3a8a 100%);
            overflow-x: hidden;
        }

        .login-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.25rem;
        }

        .login-stage {
            width: min(1120px, 100%);
            min-height: min(680px, calc(100vh - 2.5rem));
            display: grid;
            grid-template-columns: 1.08fr 0.92fr;
            border-radius: calc(var(--radius) + 0.35rem);
            overflow: hidden;
            box-shadow: var(--shadow);
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            animation: stageIn 0.7s ease both;
        }

        @keyframes stageIn {
            from { opacity: 0; transform: translateY(18px) scale(0.985); }
            to { opacity: 1; transform: none; }
        }

        .login-brand {
            position: relative;
            color: #fff;
            padding: clamp(1.75rem, 4vw, 3rem);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            isolation: isolate;
            overflow: hidden;
            background:
                linear-gradient(160deg, rgba(7, 21, 40, 0.35) 0%, rgba(11, 31, 58, 0.55) 40%, rgba(29, 78, 216, 0.72) 100%),
                url("https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1400&q=80") center/cover no-repeat;
        }

        .login-brand::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(7, 21, 40, 0.15) 0%, rgba(7, 21, 40, 0.78) 78%),
                radial-gradient(circle at 80% 20%, rgba(34, 211, 238, 0.35), transparent 40%),
                radial-gradient(circle at 20% 80%, rgba(91, 33, 182, 0.35), transparent 42%);
            z-index: -1;
        }

        .login-brand::after {
            content: "";
            position: absolute;
            width: 18rem;
            height: 18rem;
            right: -4rem;
            top: -3rem;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 211, 238, 0.28), transparent 68%);
            z-index: -1;
            animation: glowPulse 5s ease-in-out infinite;
        }

        @keyframes glowPulse {
            0%, 100% { transform: scale(1); opacity: 0.75; }
            50% { transform: scale(1.08); opacity: 1; }
        }

        .brand-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            width: fit-content;
            margin-bottom: 1rem;
            animation: riseIn 0.7s ease 0.1s both;
        }

        .brand-title {
            font-family: var(--font-display);
            font-size: clamp(2.2rem, 4vw, 3.4rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.03em;
            margin: 0 0 0.75rem;
            animation: riseIn 0.7s ease 0.18s both;
        }

        .brand-title span {
            background: linear-gradient(90deg, #fff 10%, var(--cyan-soft) 55%, #c4b5fd 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .brand-tagline {
            font-size: clamp(1rem, 1.6vw, 1.2rem);
            color: rgba(226, 232, 240, 0.92);
            max-width: 22rem;
            margin-bottom: 1.75rem;
            animation: riseIn 0.7s ease 0.26s both;
        }

        .brand-points {
            display: grid;
            gap: 0.75rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .brand-points li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0.9rem;
            border-radius: 0.9rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(6px);
            animation: riseIn 0.65s ease both;
        }

        .brand-points li:nth-child(1) { animation-delay: 0.34s; }
        .brand-points li:nth-child(2) { animation-delay: 0.42s; }
        .brand-points li:nth-child(3) { animation-delay: 0.5s; }

        .brand-points .icon {
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 0.7rem;
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            color: var(--navy-deep);
            background: linear-gradient(135deg, var(--cyan-soft), #a5b4fc);
            font-size: 1.05rem;
        }

        .brand-points strong {
            display: block;
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 700;
        }

        .brand-points span {
            display: block;
            font-size: 0.8rem;
            color: rgba(226, 232, 240, 0.78);
        }

        @keyframes riseIn {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: none; }
        }

        .login-panel {
            background:
                radial-gradient(circle at 100% 0%, rgba(34, 211, 238, 0.08), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #ffffff 40%, #f1f5f9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1.25rem, 3vw, 2.5rem);
        }

        .login-card {
            width: min(420px, 100%);
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--line);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            padding: clamp(1.4rem, 3vw, 2.15rem);
            animation: riseIn 0.7s ease 0.2s both;
        }

        .login-logo {
            width: 3.4rem;
            height: 3.4rem;
            border-radius: 1rem;
            display: grid;
            place-items: center;
            margin: 0 auto 1rem;
            color: #fff;
            font-size: 1.45rem;
            background: linear-gradient(135deg, var(--royal) 0%, var(--violet) 55%, #0891b2 100%);
            box-shadow: 0 12px 24px rgba(29, 78, 216, 0.35);
        }

        .login-card h1 {
            font-family: var(--font-display);
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-align: center;
            margin: 0 0 0.35rem;
            color: var(--navy);
        }

        .login-card .subtitle {
            text-align: center;
            color: var(--muted);
            margin-bottom: 1.35rem;
            font-size: 0.98rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.88rem;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .input-shell {
            position: relative;
        }

        .input-shell > i {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            pointer-events: none;
        }

        .input-shell .form-control {
            height: 3rem;
            padding-left: 2.55rem;
            border-radius: 0.85rem;
            border: 1px solid #dbe3ef;
            background: #f8fafc;
            font-size: 0.98rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .input-shell .form-control:focus {
            background: #fff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 0.25rem rgba(34, 211, 238, 0.18);
        }

        .input-shell.password-shell .form-control {
            padding-right: 2.75rem;
        }

        .toggle-password {
            position: absolute;
            right: 0.35rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 0.65rem;
            color: #64748b;
            display: grid;
            place-items: center;
        }

        .toggle-password:hover {
            background: #eef2ff;
            color: var(--royal);
        }

        .login-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin: 0.85rem 0 1.25rem;
            font-size: 0.9rem;
        }

        .login-meta .form-check-input {
            width: 1.05rem;
            height: 1.05rem;
            border-radius: 0.3rem;
            border-color: #cbd5e1;
        }

        .login-meta .form-check-input:checked {
            background-color: var(--royal);
            border-color: var(--royal);
        }

        .login-meta .form-check-label {
            color: #475569;
            font-weight: 500;
        }

        .forgot-link {
            color: var(--royal);
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
        }

        .forgot-link:hover {
            color: var(--violet);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            height: 3.05rem;
            border: 0;
            border-radius: 0.9rem;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 1.02rem;
            letter-spacing: 0.01em;
            color: #fff;
            background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 48%, #0891b2 100%);
            box-shadow: 0 12px 24px rgba(29, 78, 216, 0.28);
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .btn-login:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(29, 78, 216, 0.36);
            filter: saturate(1.08);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 8px 16px rgba(29, 78, 216, 0.25);
        }

        .login-alert {
            border: 0;
            border-radius: 0.85rem;
            background: #fef2f2;
            color: #991b1b;
            font-size: 0.9rem;
            padding: 0.75rem 0.9rem;
            margin-bottom: 1rem;
        }

        .login-footnote {
            margin-top: 1.15rem;
            text-align: center;
            color: #94a3b8;
            font-size: 0.78rem;
        }

        @media (max-width: 991.98px) {
            .login-stage {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .login-brand {
                min-height: 280px;
                justify-content: center;
            }

            .brand-points {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .login-shell { padding: 0.75rem; }
            .login-brand { min-height: 240px; padding: 1.25rem; }
            .brand-points li { padding: 0.65rem 0.75rem; }
            .login-meta {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="login-stage">
            <section class="login-brand" aria-label="LipSip POS branding">
                <div class="brand-kicker">
                    <i class="bi bi-shop-window" aria-hidden="true"></i>
                    Retail POS Platform
                </div>
                <h1 class="brand-title"><span>LipSip POS</span></h1>
                <p class="brand-tagline">Manage Your Business. Simplify Your Sales.</p>
                <ul class="brand-points">
                    <li>
                        <span class="icon" aria-hidden="true"><i class="bi bi-box-seam"></i></span>
                        <div>
                            <strong>Smart Inventory Management</strong>
                            <span>Track stock in real time across branches</span>
                        </div>
                    </li>
                    <li>
                        <span class="icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span>
                        <div>
                            <strong>Fast &amp; Secure Billing</strong>
                            <span>Checkout quickly with reliable payments</span>
                        </div>
                    </li>
                    <li>
                        <span class="icon" aria-hidden="true"><i class="bi bi-graph-up-arrow"></i></span>
                        <div>
                            <strong>Powerful Sales Analytics</strong>
                            <span>Understand performance at a glance</span>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="login-panel">
                <div class="login-card">
                    <div class="login-logo" aria-hidden="true">
                        <i class="bi bi-cart4"></i>
                    </div>
                    <h1>Welcome Back!</h1>
                    <p class="subtitle">Sign in to access your POS dashboard.</p>

                    @if($errors->any())
                        <div class="login-alert" role="alert">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" autocomplete="on">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="user_name">Username</label>
                            <div class="input-shell">
                                <i class="bi bi-person" aria-hidden="true"></i>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="user_name"
                                    name="user_name"
                                    value="{{ old('user_name') }}"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Enter your username">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-shell password-shell">
                                <i class="bi bi-lock" aria-hidden="true"></i>
                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password">
                                <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password">
                                    <i class="bi bi-eye" id="togglePasswordIcon" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="login-meta">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="remember_ui">
                                <label class="form-check-label" for="remember_ui">Remember Me</label>
                            </div>
                            <span class="forgot-link" title="Contact your administrator to reset your password">Forgot Password?</span>
                        </div>

                        <button type="submit" class="btn btn-login">
                            <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>
                            Login
                        </button>
                    </form>

                    <div class="login-footnote">Secure access to your LipSip POS workspace</div>
                </div>
            </section>
        </div>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('togglePassword');
            var input = document.getElementById('password');
            var icon = document.getElementById('togglePasswordIcon');
            if (!btn || !input || !icon) return;

            btn.addEventListener('click', function () {
                var showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                icon.className = showing ? 'bi bi-eye' : 'bi bi-eye-slash';
                btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
            });
        })();
    </script>
</body>
</html>
