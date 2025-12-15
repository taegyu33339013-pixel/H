<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LottoAI - AI가 찾아주는 나만의 행운 조합</title>
    <meta name="description" content="데이터 기반 로또 분석 서비스. AI가 과거 당첨 패턴을 분석하여 최적의 번호 조합을 추천합니다.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css" rel="stylesheet">
    
    <style>
        :root {
            /* Color Palette - Deep Ocean Tech */
            --primary-dark: #0a0f1c;
            --primary: #0d1829;
            --secondary: #162136;
            --accent-cyan: #00d4aa;
            --accent-gold: #ffd700;
            --accent-coral: #ff6b6b;
            --text-primary: #ffffff;
            --text-secondary: #a8b5c8;
            --text-muted: #6b7a8f;
            --gradient-cyan: linear-gradient(135deg, #00d4aa 0%, #00b894 100%);
            --gradient-gold: linear-gradient(135deg, #ffd700 0%, #f39c12 100%);
            --gradient-hero: linear-gradient(180deg, #0a0f1c 0%, #0d1829 50%, #162136 100%);
            --shadow-glow: 0 0 60px rgba(0, 212, 170, 0.15);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Pretendard', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-dark);
            color: var(--text-primary);
            line-height: 1.7;
            overflow-x: hidden;
        }

        /* Utility Classes */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .section {
            padding: 120px 0;
            position: relative;
        }

        /* Typography */
        h1, h2, h3, h4 {
            font-family: 'Outfit', 'Pretendard', sans-serif;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        /* Animated Background */
        .bg-grid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0, 212, 170, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 212, 170, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        .floating-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
            animation: float 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 212, 170, 0.15) 0%, transparent 70%);
            top: -200px;
            right: -200px;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            bottom: 20%;
            left: -100px;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(5deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 0;
            z-index: 1000;
            background: rgba(10, 15, 28, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 12px 0;
            background: rgba(10, 15, 28, 0.95);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-cyan);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .nav-cta {
            padding: 12px 28px;
            background: var(--gradient-cyan);
            color: var(--primary-dark);
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .nav-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 212, 170, 0.3);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding-top: 80px;
            background: var(--gradient-hero);
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(0, 212, 170, 0.1);
            border: 1px solid rgba(0, 212, 170, 0.3);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--accent-cyan);
            margin-bottom: 24px;
            animation: fadeInUp 0.8s ease forwards;
        }

        .hero-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--accent-cyan);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        .hero h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            margin-bottom: 24px;
            animation: fadeInUp 0.8s ease 0.1s forwards;
            opacity: 0;
        }

        .hero h1 .highlight {
            background: var(--gradient-cyan);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 32px;
            animation: fadeInUp 0.8s ease 0.2s forwards;
            opacity: 0;
        }

        .hero-features {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 40px;
            animation: fadeInUp 0.8s ease 0.3s forwards;
            opacity: 0;
        }

        .hero-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-secondary);
        }

        .hero-feature .icon {
            width: 24px;
            height: 24px;
            background: rgba(0, 212, 170, 0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-cyan);
            font-size: 0.8rem;
        }

        .hero-cta-group {
            display: flex;
            gap: 16px;
            animation: fadeInUp 0.8s ease 0.4s forwards;
            opacity: 0;
        }

        .btn-primary {
            padding: 18px 36px;
            background: var(--gradient-cyan);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0, 212, 170, 0.3);
        }

        .btn-secondary {
            padding: 18px 36px;
            background: transparent;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-secondary:hover {
            border-color: var(--accent-cyan);
            color: var(--accent-cyan);
        }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeIn 1s ease 0.5s forwards;
            opacity: 0;
        }

        .lotto-display {
            position: relative;
            padding: 40px;
            background: rgba(22, 33, 54, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 32px;
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-card);
        }

        .lotto-display::before {
            content: '';
            position: absolute;
            inset: -1px;
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.3), transparent, rgba(255, 215, 0, 0.2));
            border-radius: 32px;
            z-index: -1;
            opacity: 0.5;
        }

        .lotto-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .lotto-header span {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .lotto-header h3 {
            font-size: 1.3rem;
            margin-top: 4px;
        }

        .lotto-balls {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .ball {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3), inset 0 -4px 0 rgba(0, 0, 0, 0.2);
            animation: ballPop 0.5s ease forwards;
            opacity: 0;
            transform: scale(0);
        }

        .ball:nth-child(1) { background: linear-gradient(145deg, #ffd700, #f39c12); animation-delay: 0.1s; }
        .ball:nth-child(2) { background: linear-gradient(145deg, #3498db, #2980b9); animation-delay: 0.2s; }
        .ball:nth-child(3) { background: linear-gradient(145deg, #e74c3c, #c0392b); animation-delay: 0.3s; }
        .ball:nth-child(4) { background: linear-gradient(145deg, #9b59b6, #8e44ad); animation-delay: 0.4s; }
        .ball:nth-child(5) { background: linear-gradient(145deg, #1abc9c, #16a085); animation-delay: 0.5s; }
        .ball:nth-child(6) { background: linear-gradient(145deg, #2ecc71, #27ae60); animation-delay: 0.6s; }

        @keyframes ballPop {
            0% { opacity: 0; transform: scale(0) rotate(-180deg); }
            60% { transform: scale(1.2) rotate(10deg); }
            100% { opacity: 1; transform: scale(1) rotate(0deg); }
        }

        .confidence-bar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .confidence-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .confidence-track {
            flex: 1;
            height: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        .confidence-fill {
            height: 100%;
            background: var(--gradient-cyan);
            border-radius: 4px;
            width: 0%;
            animation: fillBar 1.5s ease 1s forwards;
        }

        @keyframes fillBar {
            to { width: 83%; }
        }

        .confidence-value {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--accent-cyan);
        }

        /* Subheader Section */
        .subheader {
            text-align: center;
            padding: 80px 0;
            background: var(--primary);
            position: relative;
            overflow: hidden;
        }

        .subheader::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 2px;
            background: var(--gradient-cyan);
        }

        .subheader-text {
            max-width: 600px;
            margin: 0 auto;
        }

        .subheader h2 {
            font-size: clamp(1.5rem, 3vw, 2rem);
            line-height: 1.5;
            color: var(--text-primary);
        }

        .subheader h2 span {
            color: var(--accent-cyan);
        }

        /* Features Section */
        .features {
            background: var(--primary-dark);
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-label {
            display: inline-block;
            padding: 8px 20px;
            background: rgba(0, 212, 170, 0.1);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--accent-cyan);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.8rem);
            margin-bottom: 16px;
        }

        .section-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feature-card {
            padding: 40px 32px;
            background: var(--secondary);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--gradient-cyan);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(0, 212, 170, 0.3);
            box-shadow: var(--shadow-glow);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 64px;
            height: 64px;
            background: rgba(0, 212, 170, 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 24px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        /* Free Offer Section */
        .free-offer {
            background: var(--primary);
            position: relative;
        }

        .offer-card {
            background: linear-gradient(135deg, rgba(0, 212, 170, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%);
            border: 1px solid rgba(0, 212, 170, 0.3);
            border-radius: 32px;
            padding: 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .offer-card::before {
            content: '🎁';
            position: absolute;
            top: 20px;
            right: 40px;
            font-size: 4rem;
            opacity: 0.2;
        }

        .offer-badge {
            display: inline-block;
            padding: 10px 24px;
            background: var(--gradient-gold);
            color: var(--primary-dark);
            font-weight: 700;
            border-radius: 50px;
            font-size: 1rem;
            margin-bottom: 24px;
        }

        .offer-title {
            font-size: 2.2rem;
            margin-bottom: 32px;
        }

        .offer-list {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .offer-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
        }

        .offer-item .check {
            width: 28px;
            height: 28px;
            background: var(--accent-cyan);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-weight: bold;
        }

        .offer-note {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        /* How It Works Section */
        .how-it-works {
            background: var(--primary-dark);
        }

        .steps-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            position: relative;
        }

        .steps-container::before {
            content: '';
            position: absolute;
            top: 50px;
            left: 10%;
            width: 80%;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-gold));
            opacity: 0.3;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 56px;
            height: 56px;
            background: var(--secondary);
            border: 2px solid var(--accent-cyan);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--accent-cyan);
            margin: 0 auto 20px;
            position: relative;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .step:hover .step-number {
            background: var(--accent-cyan);
            color: var(--primary-dark);
            transform: scale(1.1);
        }

        .step h4 {
            font-size: 1rem;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .step p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .ai-quote {
            text-align: center;
            margin-top: 60px;
            padding: 40px;
            background: rgba(0, 212, 170, 0.05);
            border-radius: 20px;
            border: 1px solid rgba(0, 212, 170, 0.1);
        }

        .ai-quote p {
            font-size: 1.3rem;
            font-style: italic;
            color: var(--text-secondary);
        }

        .ai-quote p span {
            color: var(--accent-cyan);
            font-weight: 600;
        }

        /* Example Section */
        .example {
            background: var(--primary);
        }

        .example-card {
            max-width: 600px;
            margin: 0 auto;
            background: var(--secondary);
            border-radius: 32px;
            padding: 48px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: var(--shadow-card);
        }

        .example-header {
            margin-bottom: 32px;
        }

        .example-header span {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .example-header h3 {
            font-size: 1.5rem;
            margin-top: 8px;
        }

        .example-balls {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-bottom: 32px;
        }

        .example-balls .ball {
            width: 64px;
            height: 64px;
            font-size: 1.5rem;
        }

        .example-confidence {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 20px;
            background: rgba(0, 212, 170, 0.1);
            border-radius: 16px;
            margin-bottom: 32px;
        }

        .example-confidence span {
            color: var(--text-secondary);
        }

        .example-confidence strong {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            color: var(--accent-cyan);
        }

        /* Stats Section */
        .stats {
            background: var(--primary-dark);
            position: relative;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 60px;
        }

        .stat-item {
            text-align: center;
            padding: 40px;
            background: var(--secondary);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 212, 170, 0.3);
        }

        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            background: var(--gradient-cyan);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .trust-tagline {
            text-align: center;
            font-size: 1.5rem;
            color: var(--text-muted);
        }

        .trust-tagline span {
            color: var(--accent-cyan);
        }

        /* Reviews Section */
        .reviews {
            background: var(--primary);
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .review-card {
            background: var(--secondary);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 28px 24px;
            transition: all 0.3s ease;
        }

        .review-card:hover {
            border-color: rgba(0, 212, 170, 0.3);
            transform: translateY(-4px);
        }

        .review-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .review-avatar {
            width: 44px;
            height: 44px;
            background: var(--gradient-cyan);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary-dark);
            font-size: 1.1rem;
        }

        .review-info {
            flex: 1;
        }

        .review-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .review-date {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .review-stars {
            color: #ffd700;
            font-size: 0.9rem;
            letter-spacing: 2px;
        }

        .review-text {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .review-text strong {
            color: var(--accent-cyan);
        }

        /* Trust Badges */
        .trust-badges {
            background: var(--primary-dark);
            padding: 40px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .badges-wrapper {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 32px;
        }

        .badge-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50px;
            font-size: 0.85rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .badge-item:hover {
            border-color: rgba(0, 212, 170, 0.3);
            color: var(--text-primary);
        }

        .badge-icon {
            font-size: 1.1rem;
        }

        /* FAQ Section */
        .faq {
            background: var(--primary);
        }

        .faq-list {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .faq-item {
            background: var(--secondary);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .faq-item:hover {
            border-color: rgba(0, 212, 170, 0.2);
        }

        .faq-item.active {
            border-color: rgba(0, 212, 170, 0.3);
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-primary);
        }

        .faq-toggle {
            font-size: 1.5rem;
            color: var(--accent-cyan);
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-toggle {
            transform: rotate(45deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 300px;
            padding: 0 24px 20px;
        }

        .faq-answer p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .faq-answer strong {
            color: var(--accent-cyan);
        }

        @media (max-width: 1024px) {
            .reviews-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            /* Section Headers Mobile */
            .section-header {
                margin-bottom: 28px;
            }

            .section-label {
                padding: 6px 14px;
                font-size: 0.75rem;
                margin-bottom: 12px;
            }

            .section-title {
                font-size: 1.3rem;
                margin-bottom: 10px;
            }

            .section-subtitle {
                font-size: 0.85rem;
            }

            /* Reviews Mobile */
            .reviews {
                padding: 60px 0;
            }

            .reviews-grid {
                gap: 16px;
            }

            .review-card {
                padding: 20px 16px;
                border-radius: 16px;
            }

            .review-header {
                gap: 10px;
                margin-bottom: 12px;
            }

            .review-avatar {
                width: 38px;
                height: 38px;
                font-size: 0.95rem;
            }

            .review-name {
                font-size: 0.85rem;
            }

            .review-date {
                font-size: 0.7rem;
            }

            .review-stars {
                font-size: 0.75rem;
            }

            .review-text {
                font-size: 0.85rem;
                line-height: 1.6;
            }

            /* Trust Badges Mobile */
            .trust-badges {
                padding: 24px 0;
            }

            .badges-wrapper {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                padding: 0 8px;
            }

            .badge-item {
                padding: 10px 12px;
                font-size: 0.7rem;
                border-radius: 10px;
                justify-content: center;
            }

            .badge-icon {
                font-size: 0.95rem;
            }

            .badge-item:nth-child(5) {
                grid-column: 1 / -1;
                justify-content: center;
            }

            /* FAQ Mobile */
            .faq {
                padding: 60px 0;
            }

            .faq-list {
                gap: 10px;
            }

            .faq-item {
                border-radius: 12px;
            }

            .faq-question {
                padding: 14px 16px;
                font-size: 0.85rem;
                gap: 12px;
            }

            .faq-question span:first-child {
                flex: 1;
            }

            .faq-toggle {
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .faq-item.active .faq-answer {
                padding: 0 16px 14px;
            }

            .faq-answer p {
                font-size: 0.8rem;
                line-height: 1.6;
            }

            /* Final CTA Mobile */
            .final-cta {
                padding: 60px 0 140px;
            }

            .cta-title {
                font-size: 1.4rem;
                margin-bottom: 12px;
            }

            .cta-subtitle {
                font-size: 0.95rem;
                margin-bottom: 24px;
            }

            .final-cta .btn-primary {
                font-size: 1rem !important;
                padding: 16px 32px !important;
                width: 100%;
                justify-content: center;
            }
        }

        /* Final CTA Section */
        .final-cta {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 120px 0;
            text-align: center;
            position: relative;
        }

        .final-cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(ellipse at 30% 20%, rgba(0, 212, 170, 0.1) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(255, 215, 0, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-title {
            font-size: clamp(2rem, 4vw, 3rem);
            margin-bottom: 16px;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 40px;
        }

        .cta-subtitle span {
            color: var(--accent-gold);
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background: var(--primary-dark);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 40px 0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-features {
                align-items: center;
            }

            .hero-cta-group {
                justify-content: center;
                flex-wrap: wrap;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .steps-container {
                grid-template-columns: repeat(3, 1fr);
            }

            .steps-container::before {
                display: none;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .section {
                padding: 60px 0;
            }

            .container {
                padding: 0 16px;
            }

            /* Mobile Hero - 첫 화면 = 전환 창구 */
            .hero {
                min-height: calc(100vh - 80px);
                padding-top: 70px;
                padding-bottom: 100px; /* 하단 고정 버튼 공간 */
            }

            .hero h1 {
                font-size: 1.8rem;
                line-height: 1.2;
                margin-bottom: 16px;
            }

            .hero-subtitle {
                font-size: 1rem;
                margin-bottom: 20px;
            }

            .hero-badge {
                font-size: 0.75rem;
                padding: 6px 12px;
                margin-bottom: 16px;
            }

            .hero-features {
                gap: 8px;
                margin-bottom: 24px;
            }

            .hero-feature {
                font-size: 0.85rem;
            }

            .hero-cta-group {
                display: none; /* 모바일에서 히어로 버튼 숨김 - 하단 고정 CTA 사용 */
            }

            /* Mobile Lotto Display */
            .lotto-display {
                padding: 24px 16px;
                border-radius: 20px;
            }

            .lotto-header h3 {
                font-size: 1rem;
            }

            .lotto-balls {
                gap: 8px;
            }

            .ball {
                width: 42px;
                height: 42px;
                font-size: 1rem;
            }

            .confidence-bar {
                padding: 12px 14px;
                gap: 10px;
            }

            .confidence-label {
                font-size: 0.8rem;
            }

            .confidence-value {
                font-size: 1rem;
            }

            /* Mobile Sections */
            .subheader {
                padding: 50px 0;
            }

            .subheader h2 {
                font-size: 1.1rem;
                line-height: 1.6;
            }

            .section-header {
                margin-bottom: 32px;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .section-subtitle {
                font-size: 0.9rem;
            }

            .feature-card {
                padding: 28px 20px;
                border-radius: 16px;
            }

            .feature-icon {
                width: 48px;
                height: 48px;
                font-size: 1.4rem;
                margin-bottom: 16px;
            }

            .feature-card h3 {
                font-size: 1.1rem;
            }

            .feature-card p {
                font-size: 0.85rem;
            }

            .offer-card {
                padding: 32px 20px;
                border-radius: 20px;
            }

            .offer-title {
                font-size: 1.4rem;
                margin-bottom: 20px;
            }

            .offer-list {
                flex-direction: column;
                gap: 12px;
            }

            .offer-item {
                font-size: 0.95rem;
            }

            .steps-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .step-number {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }

            .step h4 {
                font-size: 0.9rem;
            }

            .step p {
                font-size: 0.8rem;
            }

            .ai-quote {
                padding: 24px 16px;
            }

            .ai-quote p {
                font-size: 1rem;
            }

            .example-card {
                padding: 32px 20px;
                border-radius: 20px;
            }

            .example-balls {
                gap: 10px;
            }

            .example-balls .ball {
                width: 48px;
                height: 48px;
                font-size: 1.2rem;
            }

            .stats-grid {
                gap: 16px;
            }

            .stat-item {
                padding: 24px 16px;
                border-radius: 16px;
            }

            .stat-value {
                font-size: 2.5rem;
            }

            .stat-label {
                font-size: 0.85rem;
            }

            .trust-tagline {
                font-size: 1.1rem;
            }

            .final-cta {
                padding: 80px 0 120px; /* 하단 고정 버튼 공간 */
            }

            .cta-title {
                font-size: 1.5rem;
            }

            .cta-subtitle {
                font-size: 1rem;
            }

            .footer-content {
                flex-direction: column;
                gap: 12px;
                text-align: center;
                padding-bottom: 100px; /* 하단 고정 버튼 공간 */
            }

            /* Animations 속도 최적화 - 0.3~0.5초 */
            .reveal {
                transition: all 0.4s ease;
            }

            @keyframes ballPop {
                0% { opacity: 0; transform: scale(0); }
                100% { opacity: 1; transform: scale(1); }
            }
        }

        /* ===== 모바일 하단 고정 CTA ===== */
        .mobile-fixed-cta {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px 16px 20px;
            background: linear-gradient(to top, var(--primary-dark) 80%, transparent);
            z-index: 999;
        }

        .mobile-fixed-cta .cta-wrapper {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .mobile-fixed-cta .btn-mobile-primary {
            width: 100%;
            height: 56px;
            background: var(--gradient-cyan);
            border: none;
            border-radius: 16px;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(0, 212, 170, 0.4);
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .mobile-fixed-cta .btn-mobile-primary:active {
            transform: scale(0.98);
        }

        .mobile-fixed-cta .btn-mobile-secondary {
            width: 100%;
            height: 44px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .mobile-fixed-cta {
                display: block;
            }
        }
    </style>
</head>