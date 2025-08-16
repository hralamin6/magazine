<div>
    <section class="px-4 pt-6 max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-lg font-semibold">Search results</h1>
            @if($q !== '')
                <p class="text-sm text-gray-500 dark:text-gray-400">for “{{ $q }}”</p>
            @endif
        </div>

        @if($posts->count())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    @php $img = $post->getFirstMediaUrl('post') ?: $post->getFirstMediaUrl('postImages'); @endphp
                    <article class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                        <div class="relative h-40 overflow-hidden">
                            <img src="{{ $img }}" onerror="{{ getErrorImage() }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                            <span class="absolute top-3 left-3 bg-indigo-600 text-white text-[11px] font-medium px-2 py-0.5 rounded-full">{{ $post->category->name ?? 'General' }}</span>
                            <a wire:navigate href="{{ route('web.post.details', $post->slug) }}" class="absolute inset-0" aria-label="{{ $post->title }}"></a>
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <a wire:navigate href="{{ route('web.post.details', $post->slug) }}">
                                <h3 class="font-semibold text-sm leading-snug mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $post->title }}</h3>
                            </a>
                            <p class="text-[12px] text-gray-500 dark:text-gray-400 mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->content), 100) }}</p>
                            <div class="mt-auto text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <span>{{ optional($post->published_at ?? $post->updated_at)->format('M d, Y') }}</span>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                <span>{{ $post->user->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-8 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-300">No results found.</p>
            </div>
        @endif
    </section>
</div>

