@if(config('services.google_analytics_id'))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics_id') }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{{ config('services.google_analytics_id') }}', { 'anonymize_ip': true });
</script>
@endif

@if(session('ga_login'))
<script>
(function(){
    if (typeof gtag !== 'function') return;
    gtag('event', 'login', { method: 'google', event_category: 'auth' });
    @if(session('ga_new_user'))
    gtag('event', 'sign_up', { method: 'google', event_category: 'auth' });
    @endif
})();
</script>
@endif
