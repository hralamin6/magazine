<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta property="og:url" content="@yield('url', config('app.url'))" />
<meta property="og:site_name" content="{{ setup('name', 'starter') }}" />

@php($title=Str::title(str_replace(['.', '_'], ' ',  request()->route()->getName())))
<title>@yield('title', $title) - {{ setup('name', 'starter') }}</title>
<meta property="og:title" content="@yield('title', $title) - {{ setup('name', 'starter') }}" />
<meta name="twitter:title" content="@yield('title', $title) - {{ setup('name', 'starter') }}" />

<meta name="description" content="@yield('description', setup('details', 'dummy description') ) - {{ setup('name', 'starter') }}">
<meta property="og:description" content="@yield('description', setup('details', 'dummy description') ) - {{ setup('name', 'starter') }}" />
<meta name="twitter:description" content="@yield('description', setup('details', 'dummy description') ) - {{ setup('name', 'starter') }}" />

<meta property="og:image:width" content="1536" />
<meta property="og:image:height" content="1024" />
<meta name="twitter:card" content="summary_large_image" />

<meta property="og:image" content="@yield('image', getSettingImage('iconImage'))" />
<meta property="og:image:secure_url" content="@yield('image', getSettingImage('iconImage'))" />
<meta name="twitter:image" content="@yield('image', getSettingImage('iconImage'))" />
<link rel="shortcut icon" href="@yield('image', getSettingImage('iconImage'))">

<link rel="manifest" href="{{ route('laravelpwa.manifest') }}">
<meta name="theme-color" content="{{ $config['theme_color'] }}">
<meta name="mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="application-name" content="{{ $config['short_name'] }}">
<link rel="icon" sizes="{{ data_get(end($config['icons']), 'sizes') }}" href="{{ getSettingImage('iconImage') }}">


<meta name="apple-mobile-web-app-capable" content="{{ $config['display'] == 'standalone' ? 'yes' : 'no' }}">
<meta name="apple-mobile-web-app-status-bar-style" content="{{  $config['status_bar'] }}">
<meta name="apple-mobile-web-app-title" content="{{ $config['short_name'] }}">
<link rel="apple-touch-icon" href="{{ data_get(end($config['icons']), 'src') }}">


<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 621px) and (device-height: 1104px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />
<link href="{{ getSettingImage('iconImage') }}" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image" />

<!-- Tile for Win8 -->
<meta name="msapplication-TileColor" content="{{ $config['background_color'] }}">
<meta name="msapplication-TileImage" content="{{ data_get(end($config['icons']), 'src') }}">

<script type="text/javascript">
    // Initialize the service worker
    const publicVapidKey = "{{ config('webpush.vapid.public_key') }}";
{{--    const publicVapidKey = "{{ env('VAPID_PUBLIC_KEY') }}";--}}

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        registerServiceWorker().catch(err => console.error('Service Worker registration failed:', err));
    }

    async function registerServiceWorker() {
        try {
            const register = await navigator.serviceWorker.register('/serviceworker.js');
            console.log('Service Worker registered:', register);

            const subscription = await subscribeUser(register);
            console.log('Push Subscription:', subscription);

            await sendGuestSubscriptionToServer(subscription);
            await sendSubscriptionToServer(subscription);
        } catch (error) {
            console.error('Error in registering Service Worker:', error);
        }
    }

    async function subscribeUser(register) {
        return await register.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(publicVapidKey)
        });
    }

    async function sendGuestSubscriptionToServer(subscription) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/subscribe-guest', {
            method: 'POST',
            body: JSON.stringify(subscription),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });
    }
    async function sendSubscriptionToServer(subscription) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/subscribe', {
            method: 'POST',
            body: JSON.stringify(subscription),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        });
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = base64String.replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    Notification.requestPermission().then(permission => {
        if (permission !== 'granted') {
            console.log('Notification permission denied');
        }
    });
</script>
