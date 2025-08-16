<section class="px-4 mt-8 max-w-4xl mx-auto" aria-label="Comments">
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-semibold">@lang('Comments')</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $totalCount }} @lang('total')</span>
        </div>

        <!-- Add comment form -->
        <form wire:submit.prevent="addComment"
              x-data
              x-on:focus-comment.window="$nextTick(() => { $refs.comment?.focus(); $refs.comment?.scrollIntoView({behavior:'smooth', block:'center'}); })"
              class="p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700">
            @if($replyTo)
                <div class="mb-3 inline-flex items-center gap-2 text-xs px-2 py-1 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">
                    <span>@lang('Replying')</span>
                    <button type="button" wire:click="cancelReply" class="underline hover:no-underline">@lang('Cancel')</button>
                </div>
            @endif
            <div class="flex gap-3">
{{--                <img src="{{ getUserProfileImage(auth()->user()) }}" onerror="{{ getErrorProfile(auth()->user()) }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" alt="me" />--}}
                <div class="flex-1">
                    <textarea x-ref="comment" wire:model.defer="body" rows="3" class="w-full text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500/30" placeholder="@lang('Write a comment...')"></textarea>
                    @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <div class="mt-3 flex items-center gap-3">
                        @auth
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">@lang('Post')</button>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">@lang('Login to comment')</a>
                        @endauth
                    </div>
                </div>
            </div>
        </form>

        <!-- Comments list -->
        <div class="p-4 sm:p-5">
            @forelse($comments as $c)
                <div class="flex gap-3 py-4 first:pt-0 last:pb-0 border-b last:border-b-0 border-gray-200 dark:border-gray-700">
                    <img src="{{ getUserProfileImage($c->user) }}" onerror="{{ getErrorProfile($c->user) }}" class="w-9 h-9 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" alt="avatar" />
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <a wire:navigate href="{{ route('web.user.wise.post', $c->user_id) }}" class="text-gray-700 dark:text-gray-300 font-medium hover:text-indigo-600 dark:hover:text-indigo-400">{{ $c->user->name ?? 'User' }}</a>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            <span>{{ $c->created_at?->diffForHumans() }}</span>
                            @auth
                                @if(auth()->id() === $c->user_id || auth()->id() === ($post->user_id ?? null))
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <button type="button" wire:click="deleteComment({{ $c->id }})" class="text-[11px] text-red-600 hover:underline">@lang('Delete')</button>
                                @endif
                            @endauth
                        </div>
                        <div class="mt-1 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{!! nl2br(e($c->body)) !!}</div>
                        <div class="mt-2 flex items-center gap-3">
                            <button type="button" wire:click="startReply({{ $c->id }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">@lang('Reply')</button>
                        </div>

                        <!-- Children level 1 -->
                        @if($c->children && $c->children->count())
                            <div class="mt-3 space-y-3">
                                @foreach($c->children as $child)
                                    <div class="flex gap-3">
                                        <img src="{{ getUserProfileImage($child->user) }}" onerror="{{ getErrorProfile($child->user) }}" class="w-8 h-8 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" alt="avatar" />
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                <a wire:navigate href="{{ route('web.user.wise.post', $child->user_id) }}" class="text-gray-700 dark:text-gray-300 font-medium hover:text-indigo-600 dark:hover:text-indigo-400">{{ $child->user->name ?? 'User' }}</a>
                                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                <span>{{ $child->created_at?->diffForHumans() }}</span>
                                                @auth
                                                    @if(auth()->id() === $child->user_id || auth()->id() === ($post->user_id ?? null))
                                                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                        <button type="button" wire:click="deleteComment({{ $child->id }})" class="text-[11px] text-red-600 hover:underline">@lang('Delete')</button>
                                                    @endif
                                                @endauth
                                            </div>
                                            <div class="mt-1 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{!! nl2br(e($child->body)) !!}</div>
                                            <div class="mt-2">
                                                <button type="button" wire:click="startReply({{ $child->id }})" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">@lang('Reply')</button>
                                            </div>

                                            <!-- Children level 2 -->
                                            @if($child->children && $child->children->count())
                                                <div class="mt-3 space-y-3">
                                                    @foreach($child->children as $gchild)
                                                        <div class="flex gap-3">
                                                            <img src="{{ getUserProfileImage($gchild->user) }}" onerror="{{ getErrorProfile($gchild->user) }}" class="w-7 h-7 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" alt="avatar" />
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                                                    <a wire:navigate href="{{ route('web.user.wise.post', $gchild->user_id) }}" class="text-gray-700 dark:text-gray-300 font-medium hover:text-indigo-600 dark:hover:text-indigo-400">{{ $gchild->user->name ?? 'User' }}</a>
                                                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                                    <span>{{ $gchild->created_at?->diffForHumans() }}</span>
                                                                    @auth
                                                                        @if(auth()->id() === $gchild->user_id || auth()->id() === ($post->user_id ?? null))
                                                                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                                                            <button type="button" wire:click="deleteComment({{ $gchild->id }})" class="text-[11px] text-red-600 hover:underline">@lang('Delete')</button>
                                                                        @endif
                                                                    @endauth
                                                                </div>
                                                                <div class="mt-1 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{!! nl2br(e($gchild->body)) !!}</div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">@lang('Be the first to comment.')</p>
            @endforelse
        </div>
    </div>
</section>
