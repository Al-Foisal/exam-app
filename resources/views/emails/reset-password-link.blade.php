<x-mail::message>
@slot('header')
# Hello Admin,

Click below to change your password.

<x-mail::button :url=" $url ">
Reset password
</x-mail::button>

Thanks,<br>
{{-- {{ $company->name }} --}}
</x-mail::message>
