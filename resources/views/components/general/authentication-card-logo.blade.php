<div class="text-center account-logo-box" >
    <div class="mt-2 mb-2">
        <a class='btn btn-secondary waves-effect waves-light langBtn' rel="alternate" href="{{App::getLocale() == 'en' ? LaravelLocalization::getLocalizedURL('ar', null, [], true) :   LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
            {{ app()->getLocale() == 'ar'? 'English' : 'العربية' }}
        </a>
    </div>
</div>
