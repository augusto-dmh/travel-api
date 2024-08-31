<x-guest-layout>
    @foreach ($travels as $travel)
        <p>Name: {{ $travel->name }}</p>
        <p>Description: {{ $travel->description }}</p>
        <p>Number of days: {{ $travel->number_of_days }}</p>
        <p>Number of nights: {{ $travel->number_of_nights }}</p>
    @endforeach

    {{ $travels->links() }}
</x-guest-layout>
