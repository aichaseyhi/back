@component('mail::message')
# Confirmation de commande 

Pour suivre votre commande, veuillez cliquer ici !

@component('mail::button', ['url' => 'http://localhost:4200/order_traking'])
Suivi Commande
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent