<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page->meta['title'] ?? config('app.name') }}</title>
    @if(isset($page->meta['description']))
        <meta name="description" content="{{ $page->meta['description'] }}">
    @endif
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --theme-bg: #f5f5f7;
            --theme-surface: rgba(255, 255, 255, 0.78);
            --theme-surface-strong: rgba(255, 255, 255, 0.92);
            --theme-surface-soft: rgba(255, 255, 255, 0.62);
            --theme-border: rgba(15, 23, 42, 0.08);
            --theme-text: #1d1d1f;
            --theme-text-muted: #6e6e73;
            --theme-accent: #0071e3;
            --theme-accent-strong: #005ecb;
            --theme-shadow: 0 24px 80px rgba(0, 0, 0, 0.08);
        }

        html.dark {
            --theme-bg: #0f0f10;
            --theme-surface: rgba(29, 29, 31, 0.82);
            --theme-surface-strong: rgba(29, 29, 31, 0.92);
            --theme-surface-soft: rgba(255, 255, 255, 0.04);
            --theme-border: rgba(255, 255, 255, 0.1);
            --theme-text: #f5f5f7;
            --theme-text-muted: #a1a1aa;
            --theme-accent: #2997ff;
            --theme-accent-strong: #6cb6ff;
            --theme-shadow: 0 24px 80px rgba(0, 0, 0, 0.32);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--theme-bg);
            color: var(--theme-text);
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .fade-in-up { opacity: 0; transform: translateY(30px); transition: opacity 0.8s ease-out, transform 0.8s ease-out; }
        .fade-in-up.visible { opacity: 1; transform: translateY(0); }
        .theme-panel {
            background: var(--theme-surface);
            border: 1px solid var(--theme-border);
            box-shadow: var(--theme-shadow);
            backdrop-filter: blur(24px);
        }
        .theme-panel-strong {
            background: var(--theme-surface-strong);
            border: 1px solid var(--theme-border);
            box-shadow: var(--theme-shadow);
            backdrop-filter: blur(24px);
        }
        .theme-soft {
            background: var(--theme-surface-soft);
            border: 1px solid var(--theme-border);
        }
        .theme-text {
            color: var(--theme-text);
        }
        .theme-muted {
            color: var(--theme-text-muted);
        }
        .theme-accent {
            color: var(--theme-accent);
        }
        .theme-accent-bg {
            background: var(--theme-accent);
            color: #fff;
        }
        .theme-accent-bg:hover {
            background: var(--theme-accent-strong);
        }
    </style>
    @foreach($blocks as $block)
        @if($block->plugin && $block->plugin->css)
            <style>{!! $block->plugin->css !!}</style>
        @endif
    @endforeach
</head>
<body class="min-h-screen flex flex-col">
    @php($pluginContext = $pluginContext ?? [])
    @foreach($blocks as $block)
        {!! $block->render($pluginContext) !!}
    @endforeach

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => { if (entry.isIntersecting) { entry.target.classList.add("visible"); observer.unobserve(entry.target); } });
            }, { threshold: 0.1 });
            document.querySelectorAll(".fade-in-up").forEach((el) => observer.observe(el));
        });
    </script>
    @foreach($blocks as $block)
        @if($block->plugin && $block->plugin->js)
            <script>{!! $block->plugin->js !!}</script>
        @endif
    @endforeach
</body>
</html>
