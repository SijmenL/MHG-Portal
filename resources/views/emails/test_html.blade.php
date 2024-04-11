@component('mail::message')
   <h1>Recent aangemaakt account:</h1>

    <p>Recent account data:</p>
    <ul>
        <li>Name: {{ $account->name }}</li>
        <li>Email: {{ $account->email }}</li>
        <li>Sex: {{ $account->sex }}</li>
        <li>Infix: {{ $account->infix }}</li>
        <li>Last Name: {{ $account->last_name }}</li>
        <li>Birth Date: {{ $account->birth_date }}</li>
        <li>Street: {{ $account->street }}</li>
        <li>Postal Code: {{ $account->postal_code }}</li>
        <li>City: {{ $account->city }}</li>
        <li>Phone: {{ $account->phone }}</li>
        <li>Avg: {{ $account->avg ? 'Yes' : 'No' }}</li>
        <li>Member Date: {{ $account->member_date }}</li>
        <li>Dolfijnen Name: {{ $account->dolfijnen_name }}</li>
        <li>Children: {{ $account->children }}</li>
        <li>Parents: {{ $account->parents }}</li>
    </ul>

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
