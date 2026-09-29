@php
    $firstSenderId = $messages->first()?->sender_id;
@endphp

<div style="max-height: 28rem; overflow-y: auto; padding: 0.5rem;">
    @forelse ($messages as $message)
        @php
            $isRight = $message->sender_id !== $firstSenderId;
        @endphp

        <div style="display: flex; justify-content: {{ $isRight ? 'flex-end' : 'flex-start' }}; margin-bottom: 1rem;">
            <div style="max-width: 75%; display: flex; flex-direction: column; align-items: {{ $isRight ? 'flex-end' : 'flex-start' }};">
                <span style="font-size: 0.75rem; font-weight: 600; color: #9ca3af; margin-bottom: 0.25rem; padding: 0 0.25rem;">
                    {{ $message->sender->name }}
                </span>

                <div style="
                    padding: 0.625rem 1rem;
                    border-radius: 1rem;
                    {{ $isRight ? 'border-bottom-right-radius: 0.25rem;' : 'border-bottom-left-radius: 0.25rem;' }}
                    font-size: 0.875rem;
                    line-height: 1.5;
                    background-color: {{ $isRight ? '#4f46e5' : '#f3f4f6' }};
                    color: {{ $isRight ? '#ffffff' : '#111827' }};
                ">
                    {{ $message->content }}
                </div>

                <span style="font-size: 0.6875rem; color: #9ca3af; margin-top: 0.25rem; padding: 0 0.25rem;">
                    {{ $message->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
    @empty
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 3rem 0; color: #9ca3af;">
            <p style="font-size: 0.875rem;">No messages yet.</p>
        </div>
    @endforelse
</div>