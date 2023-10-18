@component('mail::message')
# {{$severity == 'critical' ? 'A CRITICAL' : 'An'}} error occurred at Cintas Columbus!

## Error Type:

{{$type}}


## Error Description:

{{$message}}


## Error timestamp:

{{$timestamp->formatLocalized('%A, %d %B %Y, %H:%M:%S')}} (Europe/Berlin)

{{$timestamp->timezone('EST')->formatLocalized('%A, %d %B %Y, %H:%M:%S')}} (EST)

@endcomponent
