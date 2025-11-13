@extends('landingpage.template')

@push('css')
<!-- Add FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Hero Section Styles */
    #hero-animated {
        background: linear-gradient(135deg, #3b82f6 0%, #b6cff6 100%);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
        margin-top: 0;
        padding-top: 0;
    }

    #hero-animated::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff08" points="0,1000 1000,0 1000,1000"/></svg>');
        pointer-events: none;
    }

    /* Floating particles animation */
    .particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 1;
    }

    .particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    .particle:nth-child(1) {
        width: 80px;
        height: 80px;
        left: 10%;
        animation-delay: 0s;
        animation-duration: 8s;
    }

    .particle:nth-child(2) {
        width: 120px;
        height: 120px;
        left: 80%;
        animation-delay: 2s;
        animation-duration: 10s;
    }

    .particle:nth-child(3) {
        width: 60px;
        height: 60px;
        left: 60%;
        animation-delay: 4s;
        animation-duration: 7s;
    }

    .particle:nth-child(4) {
        width: 100px;
        height: 100px;
        left: 30%;
        animation-delay: 1s;
        animation-duration: 9s;
    }

    .particle:nth-child(5) {
        width: 40px;
        height: 40px;
        left: 90%;
        animation-delay: 3s;
        animation-duration: 6s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        50% {
            transform: translateY(-100px) rotate(180deg);
            opacity: 0.8;
        }
    }

    /* Animated geometric shapes */
    .hero-shapes {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .shape {
        position: absolute;
        opacity: 0.1;
    }

    .shape-1 {
        top: 15%;
        left: 15%;
        width: 150px;
        height: 150px;
        background: white;
        border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        animation: morph 8s ease-in-out infinite;
    }
    .shape-3 {
        top: 40%;
        right: 20%;
        width: 80px;
        height: 80px;
        background: white;
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes morph {
        0%, 100% {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            transform: rotate(0deg);
        }
        50% {
            border-radius: 70% 30% 30% 70% / 70% 70% 30% 30%;
            transform: rotate(180deg);
        }
    }

    @keyframes rotate {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0.1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.3;
        }
    }

    #hero-animated .container {
        position: relative;
        z-index: 2;
        padding-top: 0;
    }

    #hero-animated h2 {
        font-size: 3.5rem;
        font-weight: 800;
        color: white;
        margin-bottom: 1rem;
        text-shadow: 0 4px 20px rgba(0,0,0,0.3);
        letter-spacing: -0.02em;
    }

    #hero-animated p {
        font-size: 1.4rem;
        color: rgba(255,255,255,0.9);
        font-weight: 400;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        margin-bottom: 2rem;
    }

    /* Hero CTA Buttons */
    .hero-cta {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .btn-hero-primary {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-hero-primary:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .btn-hero-secondary {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.4);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Stats section in hero */
    .hero-stats {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
        gap: 60px;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
        color: white;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        display: block;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .stat-label {
        font-size: 1rem;
        opacity: 0.9;
        margin-top: 5px;
        text-shadow: 0 1px 5px rgba(0,0,0,0.2);
    }

    /* Akreditasi Section */
    .akreditasi-section {
        background: #f8fafc;
        padding: 90px 0;
    }

    .akreditasi-programs {
        display: flex;
        flex-wrap: wrap;
        gap: 48px;
    }

    .akreditasi-column {
        flex: 1 1 220px;
        min-width: 220px;
    }

    .akreditasi-column h3 {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .akreditasi-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .akreditasi-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 12px;
        color: #1f2937;
        font-weight: 500;
    }

    .akreditasi-list li::before {
        content: '\203A';
        color: #0ea5e9;
        font-size: 1.25rem;
        line-height: 1;
        transform: translateY(2px);
    }

    .akreditasi-list a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .akreditasi-list a:hover {
        color: #2563eb;
    }

    .akreditasi-empty {
        color: #94a3b8;
        font-size: 0.95rem;
        font-style: italic;
    }

    @media (max-width: 991px) {
        .akreditasi-programs {
            gap: 32px;
        }
    }

    @media (max-width: 575px) {
        .akreditasi-column {
            min-width: 100%;
        }
    }

    /* Berita Section Redesign */
    .berita-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 80px 0;
        position: relative;
    }

    .berita-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    }

    .berita-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .berita-header h2 {
        font-size: 2.8rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }

    .berita-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .berita-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        height: 100%;
        position: relative;
        border: 1px solid #e2e8f0;
    }

    .berita-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .berita-card:hover::before {
        transform: scaleX(1);
    }

    .berita-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .berita-image-wrapper {
        height: 220px;
        overflow: hidden;
        position: relative;
    }

    .berita-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .berita-card:hover .berita-image-wrapper img {
        transform: scale(1.1);
    }

    .berita-card-body {
        padding: 25px;
        display: flex;
        flex-direction: column;
    }

    .berita-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .berita-description {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
    }

    .berita-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }

    .berita-date {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .berita-pdf-indicator {
        color: #006de1c6;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .pagination-wrapper {
        margin-top: 60px;
        display: flex;
        justify-content: right;
    }

/* Hide pagination showing results text */
.pagination-wrapper p {
    display: none !important;
}

/* Alternative: Hide specific pagination info text */
.pagination-wrapper .d-flex.justify-content-between p {
    display: none !important;
}

/* Or more specific targeting for Bootstrap pagination info */
.pagination-wrapper .pagination-info,
.pagination-wrapper .showing-results {
    display: none !important;
}
@media (max-width: 360px) {
    .pagination-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 5px;
    }

    .pagination {
        min-width: max-content;
        padding: 2px 0;
    }

    .page-link {
        padding: 4px 5px;
        min-width: 28px;
        min-height: 28px;
        font-size: 0.65rem;
    }
}


    /* Pemilwa Section Styles */
    .pemilwa-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e8f0fe 100%);
        padding: 80px 0;
        position: relative;
    }

    .pemilwa-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6 0%, #b6cff6 100%);
    }

    .pemilwa-section .section-header p {
        color: #64748b;
        font-size: 1.1rem;
        margin-top: 10px;
    }

    .pemilwa-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.12);
        position: relative;
        overflow: hidden;
    }

    .pemilwa-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6 0%, #b6cff6 100%);
        border-radius: 24px 24px 0 0;
    }

    .pemilwa-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 24px;
    }

    .pemilwa-card-header h4 {
        margin-bottom: 0;
    }

    .pemilwa-card-header small {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 600;
    }

    .pemilwa-card-body {
        display: flex;
        flex-wrap: wrap;
        gap: 32px;
        align-items: stretch;
    }

    .chart-container {
        flex: 1 1 360px;
        min-height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .voting-stats {
        flex: 1 1 320px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .stat-box {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(59, 130, 246, 0.1);
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid rgba(59, 130, 246, 0.1);
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        transform: translateX(5px);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.2);
    }

    .stat-box.voted {
        border-left: 3px solid #3b82f6;
    }

    .stat-box.not-voted {
        border-left: 3px solid #93b9f7;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .stat-box.voted .stat-icon {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }

    .stat-box.not-voted .stat-icon {
        background: linear-gradient(135deg, #93b9f7 0%, #b6cff6 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(147, 185, 247, 0.3);
    }

    .stat-content {
        flex: 1;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .pemilwa-section {
            padding: 60px 0;
        }

        .pemilwa-card {
            padding: 30px;
        }

        .pemilwa-card-body {
            gap: 24px;
        }

        .chart-container {
            min-height: 280px;
        }

        .stat-box {
            padding: 20px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.3rem;
        }

        .stat-value {
            font-size: 1.6rem;
        }

        .stat-label {
            font-size: 0.85rem;
        }
    }

    @media (max-width: 768px) {
        .stat-box {
            padding: 18px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 1.2rem;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-label {
            font-size: 0.8rem;
        }
    }

    /* Layanan Section - EXISTING STYLES PRESERVED */
    .service-item{
        border: none;
        border-radius: 15px;
        width: 100%;
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        position: relative;
    }

    .service-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
    }

    .layanan {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        padding: 60px 0;
    }

    .service-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 15px;
        padding: 30px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .service-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .service-card:hover::before {
        transform: scaleX(1);
    }

    .service-card h4 a {
        text-decoration: none;
        color: #1e293b;
        font-weight: 700;
        font-size: 1.1rem;
        line-height: 1.4;
        transition: color 0.3s ease;
    }

    .service-card:hover h4 a {
        color: var(--color-secondary);
    }

    .service-card .service-description {
        color: #64748b;
        font-size: 0.875rem;
        margin-top: 8px;
        opacity: 0.8;
    }

    .icon-wrapper {
        width: 70px;
        height: 70px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--color-secondary) 0%, #3b82f6 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .service-card:hover .icon-wrapper {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
    }

    .icon-wrapper i {
        font-size: 1.8rem;
        color: white;
        display: block;
        font-weight: 900; /* Ensure solid icons display */
        font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", sans-serif; /* Explicit font family */
    }

    /* Force icon display with higher specificity */
    .service-card .icon-wrapper i {
        font-size: 1.8rem !important;
        color: white !important;
        display: block !important;
        line-height: 1 !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    /* Ensure FontAwesome icons load properly */
    .fas, .fa-solid {
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-header h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }

    .section-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--color-secondary) 0%, #3b82f6 100%);
        border-radius: 2px;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    /* Enhanced scroll animations */
    .fade-in-up {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease-out;
    }

    .fade-in-up.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .fade-in-left {
        opacity: 0;
        transform: translateX(-30px);
        transition: all 0.6s ease-out;
    }

    .fade-in-left.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .fade-in-right {
        opacity: 0;
        transform: translateX(30px);
        transition: all 0.6s ease-out;
    }

    .fade-in-right.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .scale-in {
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.6s ease-out;
    }

    .scale-in.visible {
        opacity: 1;
        transform: scale(1);
    }

    /* Staggered animation delays */
    .stagger-1 { transition-delay: 0.1s; }
    .stagger-2 { transition-delay: 0.2s; }
    .stagger-3 { transition-delay: 0.3s; }
    .stagger-4 { transition-delay: 0.4s; }
    .stagger-5 { transition-delay: 0.5s; }
    .stagger-6 { transition-delay: 0.6s; }

    /* Enhanced navbar integration */
    body {
        padding-top: 0;
        overflow-x: hidden;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        #hero-animated h2 {
            font-size: 2.5rem;
        }

        #hero-animated p {
            font-size: 1.1rem;
        }

        .hero-cta {
            flex-direction: column;
            align-items: center;
        }

        .hero-stats {
            gap: 30px;
        }

        .stat-number {
            font-size: 2rem;
        }

        .particle {
            display: none;
        }

        .berita-header h2 {
            font-size: 2.2rem;
        }

        .berita-section {
            padding: 60px 0;
        }

        .service-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .section-header h2 {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        #hero-animated h2 {
            font-size: 2rem;
        }

        .btn-hero-primary,
        .btn-hero-secondary {
            padding: 12px 25px;
            font-size: 1rem;
        }

        .hero-stats {
            gap: 20px;
        }

        .berita-header h2 {
            font-size: 1.8rem;
        }

        .berita-card-body {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
    <section id="hero-animated" class="hero-animated d-flex align-items-center">
        <!-- Floating Particles -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <!-- Animated Shapes -->
        <div class="hero-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-3"></div>
        </div>

        <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative"
            data-aos="zoom-out" data-aos-duration="1000">
            <h2 data-aos="fade-up" data-aos-delay="200" style="font-size: 3rem;">{{ env('APP_NAME') }}</h2>
            <p data-aos="fade-up" data-aos-delay="400">Sekolah Vokasi <br> Universitas Sebelas Maret</p>

            <!-- CTA Buttons -->
            <div class="hero-cta" data-aos="fade-up" data-aos-delay="600">
                <a href="#Akademik-services" class="btn-hero-primary scrollto">
                    <i class="fas fa-rocket"></i>
                    Mulai Layanan
                </a>
            </div>

        </div>
    </section>

    <main id="main">
        <!-- Berita Section Redesigned -->
        <section class="berita-section">
            <div class="container">
                <div class="berita-header fade-in-up">
                    <h2>INFORMASI PENTING</h2>
                </div>
                <div class="row g-4">
                    @foreach($berita as $index => $b)
                    <div class="col-lg-4 col-md-6">
                        <div class="berita-card fade-in-up stagger-{{ ($index % 3) + 1 }}"
                             data-aos="fade-up"
                             data-aos-duration="600"
                             data-aos-delay="{{ $index * 100 }}"
                             onclick="window.location.href='{{ route('berita.detail', $b->id) }}'">
                            <div class="berita-image-wrapper">
                                <img src="{{ $b->gambar ? asset('storage/'.$b->gambar) : asset('/back/assets/images/Default_News.png') }}"
                                     alt="{{ $b->judul }}">
                            </div>
                            <div class="berita-card-body">
                                <h5 class="berita-title">{{ $b->judul }}</h5>
                                <p class="berita-description">
                                    {{ implode(' ', array_slice(explode(' ', $b->deskripsi), 0, 15))}}{{ strlen($b->deskripsi) > strlen(implode(' ', array_slice(explode(' ', $b->deskripsi), 0, 15))) ? '...' : '' }}
                                </p>
                                <div class="berita-meta">
                                    <span class="berita-date">
                                        {{ \Carbon\Carbon::parse($b->tanggal)->format('d M Y') }}
                                    </span>
                                    @if($b->PDF)
                                    <span class="berita-pdf-indicator">
                                        <i class="fa fa-file-pdf"></i> PDF
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper fade-in-up">
                    {{ $berita->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </section>

        <!-- Pemilwa Section -->
        <section class="pemilwa-section">
            <div class="container">
                <div class="section-header fade-in-up">
                    <h2>Statistik Pemilihan Mahasiswa</h2>
                    <p>Suara per paslon untuk setiap pemilihan yang sedang aktif</p>
                </div>

                @php $hasPemilwa = isset($pemilwaSummaries) && count($pemilwaSummaries) > 0; @endphp
                @if($hasPemilwa)
                    @foreach($pemilwaSummaries as $pem)
                        <div class="pemilwa-card fade-in-up mb-5">
                            <div class="pemilwa-card-header">
                                <h4>{{ $pem['name'] }}</h4>
                                <small>Total suara masuk: {{ number_format($pem['total_votes'] ?? 0) }}</small>
                            </div>
                            <div class="pemilwa-card-body">
                                <div class="chart-container">
                                    <canvas id="votingChart-{{ $pem['slug'] }}"></canvas>
                                </div>
                                <div class="voting-stats">
                                    @foreach($pem['candidates'] as $idx => $c)
                                        <div class="stat-box voted">
                                            <div class="stat-icon">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div class="stat-content">
                                                <div class="stat-value">{{ $c['votes'] }}</div>
                                                <div class="stat-label">{{ $c['label'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">Belum ada pemilihan aktif saat ini.</div>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Layanan Section - UNCHANGED -->
        <div class="layanan">
            @foreach (getLayanan() as $kategoriIndex => $kategori)
                <section id="{{$kategori->name}}-services" class="featured-services">
                    <div class="container">
                        <div class="section-header fade-in-up">
                            <h2>Layanan {{$kategori->name}}</h2>
                        </div>
                        <div class="service-grid">
                            @foreach ($kategori->layanan as $layananIndex => $layanan)
                            @php
                                $user = auth()->user();
                                $isStaff = $user && $user->roles && $user->roles->gate_name == 'staff';

                                // Updated icon mapping dengan fallback
                                $icons = [
                                    'Perpanjangan Studi' => 'fa-solid fa-hourglass-half',
                                    'Penundaan Pembayaran UKT' => 'fa-solid fa-clock',
                                    'Selang/Cuti' => 'fa-solid fa-plane-departure',
                                    'Undur Diri' => 'fa-solid fa-door-open',
                                    'Pembayaran UKT di Luar Jadwal' => 'fa-solid fa-money-bill-wave',
                                    'Keringanan UKT' => 'fa-solid fa-hand-holding-dollar',
                                    'Layanan Surat Keterangan Lulus' => 'fa-solid fa-certificate',
                                    'Verifikasi Wisuda' => 'fa-solid fa-graduation-cap',
                                    'Transkrip Nilai' => 'fa-solid fa-scroll',
                                    'SKPI' => 'fa-solid fa-id-card',
                                    'Surat Keterangan/Pengantar' => 'fa-solid fa-file-alt',
                                    'Surat Keterangan Masih Kuliah' => 'fa-solid fa-user-graduate',
                                    'Surat Tugas Delegasi' => 'fa-solid fa-people-carry-box',
                                    'Surat Izin Kegiatan' => 'fa-solid fa-calendar-check',
                                    'Laporan Pertanggungjawaban' => 'fa-solid fa-clipboard-list',
                                    'Legalisir' => 'fa-solid fa-stamp',
                                    'default' => 'fa-solid fa-cogs'
                                ];

                                $icon = $icons[$layanan->name] ?? $icons['default'];
                                // Debug: pastikan icon class tidak kosong
                                if (empty($icon)) {
                                    $icon = 'fa-solid fa-cogs';
                                }
                            @endphp
                            <div data-aos="fade-up"
                                 data-aos-delay="{{ $layananIndex * 100 }}"
                                 data-aos-duration="600"
                                 class="scale-in stagger-{{ ($layananIndex % 4) + 1 }}">
                                <div class="service-item position-relative service-card">
                                    <div class="icon-wrapper">
                                        <i class="{{ $icon }}" style="font-size: 1.8rem; color: white; display: block;"></i>
                                    </div>

                                    <h4 class="mb-2">
                                        @if($layanan->name == 'Verifikasi Wisuda')
                                            <a href="/verifikasiWisuda/informasi" class="stretched-link">{{$layanan->name}}</a>
                                        @else
                                            <a href="{{$layanan->url_mhs}}" class="stretched-link">{{$layanan->name}}</a>
                                        @endif
                                    </h4>
                                    <p class="service-description">Klik untuk mengakses layanan</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endforeach
        </div>

        @if(isset($prodisAkreditasi) && $prodisAkreditasi->count())
        <section id="akreditasi-overview" class="akreditasi-section">
            <div class="container">
                <div class="section-header fade-in-up">
                    <h2 data-aos="fade-up">Akreditasi Program Studi</h2>
                    <p data-aos="fade-up" data-aos-delay="120">Telusuri dokumen akreditasi resmi untuk setiap program studi di Sekolah Vokasi UNS.</p>
                </div>

                @php
                    $akreditasiItems = $prodisAkreditasi instanceof \Illuminate\Contracts\Pagination\Paginator
                        ? collect($prodisAkreditasi->items())
                        : collect($prodisAkreditasi);

                    $akreditasiGroups = [
                        'universitas' => ['title' => 'Universitas Sebelas Maret', 'items' => []],
                        'sarjana' => ['title' => 'Sarjana Terapan', 'items' => []],
                        'diploma' => ['title' => 'Diploma', 'items' => []],
                    ];

                    foreach ($akreditasiItems as $prodi) {
                        $name = $prodi->name ?? '';
                        $lower = \Illuminate\Support\Str::lower($name);

                        if (\Illuminate\Support\Str::contains($lower, 'universitas sebelas maret')) {
                            $akreditasiGroups['universitas']['items'][] = $prodi;
                        } elseif (\Illuminate\Support\Str::contains($lower, 'diploma')) {
                            $akreditasiGroups['diploma']['items'][] = $prodi;
                        } elseif (\Illuminate\Support\Str::contains($lower, 'sarjana')) {
                            $akreditasiGroups['sarjana']['items'][] = $prodi;
                        } else {
                            $akreditasiGroups['sarjana']['items'][] = $prodi;
                        }
                    }
                @endphp

                <div class="akreditasi-programs">
                    @foreach($akreditasiGroups as $key => $group)
                        <div class="akreditasi-column" data-aos="fade-up" data-aos-delay="{{ $loop->index * 120 }}">
                            <h3>{{ $group['title'] }}</h3>
                            @if(count($group['items']))
                                <ul class="akreditasi-list">
                                    @foreach($group['items'] as $prodi)
                                        <li>
                                            <a href="{{ route('akreditasi.prodi', encodeId($prodi->id)) }}">
                                                {{ $prodi->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="akreditasi-empty">Belum ada data.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    </main>
@endsection

@push('js')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Enhanced scroll animations with colorful navbar integration
document.addEventListener('DOMContentLoaded', function() {
    // Initialize per-election charts dynamically
    const pemilwaData = {!! json_encode($pemilwaSummaries ?? []) !!};
    const palette = [
        'rgba(59, 130, 246, 0.85)', // blue
        'rgba(16, 185, 129, 0.85)', // green
        'rgba(245, 158, 11, 0.85)', // amber
        'rgba(239, 68, 68, 0.85)',  // red
        'rgba(99, 102, 241, 0.85)', // indigo
        'rgba(236, 72, 153, 0.85)', // pink
        'rgba(34, 197, 94, 0.85)',  // emerald
        'rgba(250, 204, 21, 0.85)'  // yellow
    ];

    (pemilwaData || []).forEach(function(p, idx) {
        const canvasId = `votingChart-${p.slug}`;
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const labels = (p.candidates || []).map(c => c.label);
        const data = (p.candidates || []).map(c => c.votes);
        const colors = labels.map((_, i) => palette[i % palette.length]);
        const borderColors = colors.map(c => c.replace('0.85', '1'));

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: borderColors,
                    borderWidth: 2,
                    hoverOffset: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '55%', // doughnut hole size; set to 0 for full pie
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 16,
                            font: { size: 14, family: "'Inter', sans-serif", weight: '600' },
                            color: '#1e293b',
                            usePointStyle: true,
                            boxWidth: 14,
                            boxHeight: 14
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30,41,59,0.9)',
                        padding: 12,
                        titleFont: { size: 14, weight: '700', family: "'Inter', sans-serif" },
                        bodyFont: { size: 13, family: "'Inter', sans-serif" },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const value = (context.parsed ?? 0) || 0;
                                const total = (context.dataset.data || []).reduce((a, b) => (a || 0) + (b || 0), 0);
                                const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${value} suara (${pct}%)`;
                            }
                        }
                    }
                },
                animation: { animateRotate: true, animateScale: true, duration: 1200, easing: 'easeOutQuart' }
            }
        });
    });

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observe all elements with animation classes
    document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(el => {
        observer.observe(el);
    });

    // Enhanced parallax effect for hero section
    let ticking = false;

    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('#hero-animated');
        const header = document.getElementById('header');

        if (parallax) {
            const speed = scrolled * 0.3;
            parallax.style.transform = `translateY(${speed}px)`;
        }

        ticking = false;
    }

    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }

        const header = document.getElementById('header');
        const heroSection = document.querySelector('#hero-animated');

        // Add subtle parallax to hero when navbar becomes colorful
        if (header.classList.contains('scrolled') && heroSection) {
            const scrolled = window.pageYOffset;
            const speed = scrolled * 0.2;
            heroSection.style.transform = `translateY(${speed}px)`;
        }
    });

    // Add staggered animations for service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
