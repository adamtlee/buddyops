<x-filament-panels::page>
    <style>
        .bo-wrap { width: 100%; display: flex; justify-content: center; container-type: inline-size; }
        .bo-chat { --bo-border: var(--gray-200); --bo-surface: #fff; --bo-soft: var(--gray-100); --bo-text: var(--gray-950); --bo-muted: var(--gray-500);
            display: flex; flex-direction: column; width: 100%; max-width: 48rem; margin-inline: auto; height: calc(100dvh - 10rem); min-height: 28rem; }
        .dark .bo-chat { --bo-border: rgba(255,255,255,.12); --bo-surface: var(--gray-900); --bo-soft: rgba(255,255,255,.08); --bo-text: #fff; --bo-muted: var(--gray-400); }
        .bo-chat svg { width: 1.25rem; height: 1.25rem; }
        .bo-scroll { flex: 1; overflow-y: auto; padding: .5rem .25rem 1.5rem; display: flex; flex-direction: column; gap: 1.75rem; }
        .bo-row-user { display: flex; justify-content: flex-end; }
        .bo-user { max-width: 80%; white-space: pre-wrap; background: var(--primary-600); color: #fff; padding: .75rem 1.1rem; border-radius: 1.4rem 1.4rem .3rem 1.4rem; font-size: 1rem; line-height: 1.5; box-shadow: 0 1px 2px rgba(0,0,0,.12); }
        .bo-row-bot { display: flex; gap: .85rem; align-items: flex-start; }
        .bo-avatar { flex: none; width: 2.1rem; height: 2.1rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; background: var(--primary-600); color: #fff; }
        .bo-bot { max-width: 85%; white-space: pre-wrap; background: var(--bo-surface); color: var(--bo-text); border: 1px solid var(--bo-border); padding: .75rem 1.1rem; border-radius: .3rem 1.4rem 1.4rem 1.4rem; font-size: 1rem; line-height: 1.6; box-shadow: 0 1px 2px rgba(0,0,0,.06); }
        .bo-error { border-color: #fca5a5; color: #b91c1c; }
        .bo-empty { margin: auto; display: flex; flex-direction: column; align-items: center; gap: 1.25rem; text-align: center; }
        .bo-logo { width: 4rem; height: 4rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #fff; background: linear-gradient(135deg, var(--primary-400), var(--primary-700)); box-shadow: 0 8px 20px rgba(0,0,0,.18); }
        .bo-logo svg { width: 2rem; height: 2rem; }
        .bo-title { font-size: 1.9rem; font-weight: 600; color: var(--bo-text); }
        .bo-sub { color: var(--bo-muted); font-size: .95rem; margin-top: -.75rem; }
        .bo-chips { display: grid; grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr)); gap: .75rem; width: 100%; max-width: 40rem; }
        .bo-chip { text-align: left; padding: .9rem 1rem; border-radius: 1rem; border: 1px solid var(--bo-border); background: var(--bo-surface); color: var(--bo-text); font-size: .9rem; cursor: pointer; transition: all .15s; }
        .bo-chip:hover { border-color: var(--primary-500); background: var(--bo-soft); transform: translateY(-1px); }
        .bo-composer { display: flex; align-items: flex-end; gap: .5rem; padding: .5rem .5rem .5rem 1.25rem; border-radius: 1.75rem; border: 1px solid var(--bo-border); background: var(--bo-surface); box-shadow: 0 4px 16px rgba(0,0,0,.08); transition: border-color .15s, box-shadow .15s; }
        .bo-composer:focus-within { border-color: var(--primary-500); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-500) 25%, transparent); }
        .bo-input { flex: 1; resize: none; border: 0 !important; outline: 0 !important; box-shadow: none !important; background: transparent !important; color: var(--bo-text); font-size: 1rem; line-height: 1.5; padding: .6rem 0 !important; max-height: 12rem; }
        .bo-send { flex: none; width: 2.6rem; height: 2.6rem; border-radius: 9999px; border: 0; cursor: pointer; display: flex; align-items: center; justify-content: center; background: var(--primary-600); color: #fff; transition: background .15s, transform .1s; }
        .bo-send:hover { background: var(--primary-500); }
        .bo-send:active { transform: scale(.94); }
        .bo-note { margin-top: .5rem; text-align: center; font-size: .75rem; color: var(--bo-muted); }
        @container (max-width: 40rem) { .bo-user, .bo-bot { max-width: 92%; } .bo-title { font-size: 1.5rem; } }
    </style>

    <div class="bo-wrap">
    <div class="bo-chat">
        @php($dogs = auth()->user()->dogs()->orderBy('id')->get(['id', 'name']))
        @if ($dogs->count() > 1)
            <label class="mb-3 flex items-center gap-2 self-start text-sm text-gray-600 dark:text-gray-300">
                <span>Chat about</span>
                <select wire:model.live.number="dogId" aria-label="Select dog" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-950 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                    @foreach ($dogs as $dog)
                        <option value="{{ $dog->id }}">{{ $dog->name }}</option>
                    @endforeach
                </select>
            </label>
        @endif

        <div
            class="bo-scroll"
            x-data
            x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)"
            x-effect="$wire.messages.length; $nextTick(() => $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' }))"
        >
            @forelse ($messages as $message)
                @if ($message['role'] !== 'tool' && $message['content'] !== '')
                    @if ($message['role'] === 'user')
                        <div class="bo-row-user"><div class="bo-user">{{ $message['content'] }}</div></div>
                    @else
                        <div class="bo-row-bot">
                            <div class="bo-avatar"><x-filament::icon icon="heroicon-o-sparkles" /></div>
                            <div class="bo-bot {{ ! empty($message['is_error']) ? 'bo-error' : '' }}">{{ $message['content'] }}</div>
                        </div>
                    @endif
                @endif
            @empty
                <div class="bo-empty">
                    <div class="bo-logo"><x-filament::icon icon="heroicon-o-sparkles" /></div>
                    <div class="bo-title">What can I help with?</div>
                    <div class="bo-sub">Ask about Buddy's feeding, walks, play and vet history.</div>
                    <div class="bo-chips">
                        @foreach (['When did Buddy last eat?', 'Log a 30 minute walk', 'Show the vet history'] as $suggestion)
                            <button type="button" class="bo-chip" wire:click="$set('prompt', @js($suggestion))">{{ $suggestion }}</button>
                        @endforeach
                    </div>
                </div>
            @endforelse

            <div wire:loading.flex wire:target="send" class="bo-row-bot" role="status" aria-live="polite">
                <div class="bo-avatar"><x-filament::icon icon="heroicon-o-sparkles" /></div>
                <div class="bo-bot">Thinking…</div>
            </div>
        </div>

        <form wire:submit="send">
            <div class="bo-composer">
                <textarea
                    wire:model="prompt"
                    rows="1"
                    class="bo-input"
                    placeholder="Message Buddy Ops"
                    x-data
                    x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 192) + 'px'"
                    x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.send().then(() => $el.style.height = 'auto') }"
                ></textarea>
                <button type="submit" class="bo-send" aria-label="Send" wire:loading.attr="disabled" wire:target="send"><x-filament::icon icon="heroicon-m-arrow-up" /></button>
            </div>
        </form>
    </div>
    </div>
</x-filament-panels::page>
