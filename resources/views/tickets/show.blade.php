<x-layouts.app title="{{ $ticket->ticket_number }} - IT HELPDESK" heading="Ticket Details">
    @include('tickets._details', ['ticket' => $ticket])
</x-layouts.app>
