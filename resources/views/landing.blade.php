<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BuddyOps - The smart care planner for your dog</title>
        <meta name="description" content="Track feeding, walks, vet visits and grooming in one place. Soon: AI insights that help your dog live a happier, healthier life.">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FFF8F0] text-stone-800 antialiased font-sans">

        {{-- Nav --}}
        <header class="sticky top-0 z-50 backdrop-blur bg-[#FFF8F0]/80 border-b border-amber-100">
            <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="#" class="flex items-center gap-2 text-xl font-extrabold tracking-tight">
                    <span class="text-2xl">🐾</span> Buddy<span class="text-orange-500">Ops</span>
                </a>
                <div class="hidden items-center gap-8 text-sm font-medium text-stone-600 md:flex">
                    <a href="#features" class="hover:text-orange-500">Features</a>
                    <a href="#ai" class="hover:text-orange-500">AI Insights</a>
                    <a href="#how" class="hover:text-orange-500">How it works</a>
                    <a href="#faq" class="hover:text-orange-500">FAQ</a>
                </div>
                <a href="#join" class="rounded-full bg-orange-500 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-600">Join the waitlist</a>
            </nav>
        </header>

        {{-- Hero --}}
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-orange-200/50 blur-3xl"></div>
            <div class="pointer-events-none absolute top-40 -left-24 h-80 w-80 rounded-full bg-amber-200/50 blur-3xl"></div>
            <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-6 py-20 lg:grid-cols-2 lg:py-28">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-orange-100 px-4 py-1.5 text-sm font-semibold text-orange-700">
                        ✨ AI-powered insights coming soon
                    </span>
                    <h1 class="mt-6 text-5xl font-extrabold leading-[1.05] tracking-tight sm:text-6xl">
                        Every wag, walk &amp; walk-in
                        <span class="text-orange-500">- all in one place.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-stone-600">
                        BuddyOps is the calm, simple way to keep your dog's day on track: meals, walks, vet visits, grooming and everything in between. Soon, our AI will spot patterns and tell you what your best friend can't.
                    </p>
                    <div id="join" class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="#" class="rounded-full bg-orange-500 px-8 py-4 text-center text-base font-bold text-white shadow-lg shadow-orange-500/30 transition hover:-translate-y-0.5 hover:bg-orange-600">Get started free</a>
                        <a href="#features" class="rounded-full border border-stone-300 bg-white px-8 py-4 text-center text-base font-semibold text-stone-700 transition hover:border-orange-400">See how it works</a>
                    </div>
                    <p class="mt-4 text-sm text-stone-500">Free for your first pup. No credit card needed.</p>
                </div>

                {{-- Mock dashboard card --}}
                <div class="relative mx-auto w-full max-w-md">
                    <div class="rotate-2 rounded-3xl bg-white p-6 shadow-2xl shadow-orange-900/10 ring-1 ring-amber-100">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-4xl">🐶</div>
                            <div>
                                <p class="text-lg font-bold">Biscuit's Day</p>
                                <p class="text-sm text-stone-500">Golden Retriever &middot; 3 yrs</p>
                            </div>
                        </div>
                        <ul class="mt-6 space-y-3 text-sm">
                            <li class="flex items-center justify-between rounded-2xl bg-orange-50 px-4 py-3"><span>🍖 Breakfast</span><span class="font-semibold text-emerald-600">Done 7:30</span></li>
                            <li class="flex items-center justify-between rounded-2xl bg-orange-50 px-4 py-3"><span>🦮 Morning walk</span><span class="font-semibold text-emerald-600">Done 8:15</span></li>
                            <li class="flex items-center justify-between rounded-2xl bg-orange-50 px-4 py-3"><span>💉 Vet: vaccines</span><span class="font-semibold text-orange-600">Thu 2:00</span></li>
                            <li class="flex items-center justify-between rounded-2xl bg-orange-50 px-4 py-3"><span>🛁 Grooming</span><span class="font-semibold text-stone-500">Sat 10:00</span></li>
                        </ul>
                        <div class="mt-5 rounded-2xl bg-gradient-to-r from-violet-500 to-fuchsia-500 p-4 text-sm text-white">
                            <p class="font-bold">✨ Buddy AI</p>
                            <p class="mt-1 opacity-90">Biscuit's walks are 20% shorter this week. Maybe an extra trip to the park?</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-6 -left-6 -rotate-6 rounded-2xl bg-white px-4 py-3 text-sm font-semibold shadow-xl ring-1 ring-amber-100">🔔 Time for dinner!</div>
                </div>
            </div>
        </section>

        {{-- Social proof strip --}}
        <section class="border-y border-amber-100 bg-white">
            <div class="mx-auto grid max-w-6xl grid-cols-2 gap-6 px-6 py-8 text-center md:grid-cols-4">
                @foreach ([['🐕', 'Built for dog lovers'], ['⏰', 'Never miss a meal or appointment'], ['👨‍👩‍👧', 'Share care with family'], ['🔒', 'Your data stays yours']] as [$icon, $text])
                    <div><div class="text-2xl">{{ $icon }}</div><p class="mt-1 text-sm font-semibold text-stone-600">{{ $text }}</p></div>
                @endforeach
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="mx-auto max-w-6xl px-6 py-24">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-bold uppercase tracking-widest text-orange-500">Everything they need</p>
                <h2 class="mt-3 text-4xl font-extrabold tracking-tight">Because good dogs deserve great care</h2>
                <p class="mt-4 text-lg text-stone-600">From the first bowl of the morning to the last walk at night, BuddyOps keeps it all organized.</p>
            </div>
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['🍖', 'Feeding', 'Set meal times and portions, log treats, and see who fed the pup so nobody gets double dinner.', 'bg-orange-100'],
                    ['🦮', 'Walks & exercise', 'Track every walk and playtime. Build streaks and keep tails wagging with healthy routines.', 'bg-emerald-100'],
                    ['🩺', 'Vet & health', 'Vaccines, medications and check-ups with reminders, plus a health history ready for your next visit.', 'bg-sky-100'],
                    ['🛁', 'Grooming', 'Baths, trims, nails and teeth. Never wonder when the last haircut was.', 'bg-pink-100'],
                    ['📝', 'Everything else', 'Training sessions, boarding, birthdays, flea treatments. Add any custom activity you like.', 'bg-amber-100'],
                    ['👥', 'Family sharing', 'Everyone in the pack stays in sync, from the kids to the dog sitter.', 'bg-violet-100'],
                ] as [$icon, $title, $copy, $bg])
                    <div class="group rounded-3xl bg-white p-8 ring-1 ring-amber-100 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-900/5">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl text-3xl {{ $bg }}">{{ $icon }}</div>
                        <h3 class="mt-5 text-xl font-bold">{{ $title }}</h3>
                        <p class="mt-2 leading-relaxed text-stone-600">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- AI --}}
        <section id="ai" class="px-6">
            <div class="mx-auto max-w-6xl overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-stone-900 via-violet-950 to-stone-900 px-8 py-20 text-white sm:px-16">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div>
                        <span class="rounded-full bg-white/10 px-4 py-1.5 text-sm font-semibold text-fuchsia-200">Coming soon</span>
                        <h2 class="mt-6 text-4xl font-extrabold tracking-tight sm:text-5xl">If only your dog could <span class="text-fuchsia-300">tell you</span> how they're doing.</h2>
                        <p class="mt-5 text-lg leading-relaxed text-violet-100/80">
                            Buddy AI looks at your dog's routine and turns it into clear, friendly insights. Catch small changes early, before they become big worries.
                        </p>
                        <p class="mt-4 text-sm text-violet-200/60">AI insights are informational and never replace advice from your veterinarian.</p>
                    </div>
                    <div class="space-y-4">
                        @foreach ([
                            ['📈', 'Spot patterns', '"Biscuit eats slower on days with shorter walks."'],
                            ['⚠️', 'Notice changes early', '"Appetite is down 3 days in a row. Worth mentioning to your vet."'],
                            ['💡', 'Get smart suggestions', '"Based on age and activity, try splitting meals into three portions."'],
                            ['🗓️', 'Plan ahead', '"Booster due in 2 weeks. Want me to add a reminder?"'],
                        ] as [$icon, $title, $copy])
                            <div class="flex gap-4 rounded-2xl bg-white/5 p-5 ring-1 ring-white/10">
                                <div class="text-2xl">{{ $icon }}</div>
                                <div><p class="font-bold">{{ $title }}</p><p class="mt-1 text-sm text-violet-100/70">{{ $copy }}</p></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- How it works --}}
        <section id="how" class="mx-auto max-w-6xl px-6 py-24">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-4xl font-extrabold tracking-tight">Up and wagging in minutes</h2>
            </div>
            <div class="mt-14 grid gap-8 md:grid-cols-3">
                @foreach ([['1', 'Add your dog', 'Name, breed, age. Add photos and notes to make it theirs.'], ['2', 'Build the routine', 'Schedule meals, walks, vet visits and grooming, one-time or recurring.'], ['3', 'Stay in the loop', 'Get reminders, log what happened, and let insights roll in.']] as [$n, $title, $copy])
                    <div class="text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-orange-500 text-xl font-extrabold text-white">{{ $n }}</div>
                        <h3 class="mt-5 text-xl font-bold">{{ $title }}</h3>
                        <p class="mt-2 text-stone-600">{{ $copy }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Testimonials --}}
        <section class="bg-amber-100/60 py-24">
            <div class="mx-auto max-w-6xl px-6">
                <h2 class="text-center text-4xl font-extrabold tracking-tight">Tails are wagging</h2>
                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @foreach ([
                        ['I used to text my husband "did you feed Luna?" five times a day. Not anymore.', 'Maria & Luna', '🐩'],
                        ['The vet reminders alone are worth it. Max has never been so up to date on his shots.', 'Dan & Max', '🐕'],
                        ['Our sitter sees the whole routine in one tap. Total peace of mind on trips.', 'Priya & Mochi', '🐶'],
                    ] as [$quote, $who, $icon])
                        <figure class="rounded-3xl bg-white p-8 shadow-sm">
                            <div class="text-orange-400">★★★★★</div>
                            <blockquote class="mt-4 leading-relaxed text-stone-700">"{{ $quote }}"</blockquote>
                            <figcaption class="mt-5 flex items-center gap-3 text-sm font-semibold"><span class="text-2xl">{{ $icon }}</span>{{ $who }}</figcaption>
                        </figure>
                    @endforeach
                </div>
                <p class="mt-6 text-center text-xs text-stone-500">Sample testimonials for illustration.</p>
            </div>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="mx-auto max-w-3xl px-6 py-24">
            <h2 class="text-center text-4xl font-extrabold tracking-tight">Good questions</h2>
            <div class="mt-10 space-y-4">
                @foreach ([
                    ['Can I track more than one dog?', 'Yes. Give every pup their own profile and schedule, and switch between them easily.'],
                    ['When will the AI features launch?', 'We are integrating an LLM now. Join the waitlist and you will be first to try Buddy AI.'],
                    ['Is the AI a replacement for my vet?', 'No. Buddy AI highlights patterns in your data, but always check with your vet on health matters.'],
                    ['What can I track?', 'Feeding, walks, vet visits, grooming, medication, training, and any custom activity.'],
                ] as [$q, $a])
                    <details class="group rounded-2xl bg-white p-6 ring-1 ring-amber-100">
                        <summary class="flex cursor-pointer list-none items-center justify-between font-bold">{{ $q }}<span class="text-orange-500 transition group-open:rotate-45 text-2xl">+</span></summary>
                        <p class="mt-3 text-stone-600">{{ $a }}</p>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- CTA --}}
        <section class="px-6 pb-24">
            <div class="mx-auto max-w-4xl rounded-[2.5rem] bg-orange-500 px-8 py-16 text-center text-white shadow-xl shadow-orange-500/30">
                <div class="text-5xl">🐕‍🦺</div>
                <h2 class="mt-4 text-4xl font-extrabold tracking-tight">Your best friend is waiting.</h2>
                <p class="mx-auto mt-4 max-w-xl text-lg text-orange-50">Start organizing their care today and be first in line for AI insights.</p>
                <a href="#" class="mt-8 inline-block rounded-full bg-white px-8 py-4 font-bold text-orange-600 transition hover:-translate-y-0.5 hover:shadow-lg">Get started free</a>
            </div>
        </section>

        <footer class="border-t border-amber-100 py-8 text-center text-sm text-stone-500">
            &copy; {{ date('Y') }} BuddyOps. Made with 🧡 for dogs and the people who love them.
        </footer>
    </body>
</html>
