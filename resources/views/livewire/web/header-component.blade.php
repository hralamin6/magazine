<header class="h-16 bg-white/90 dark:bg-gray-800/90 backdrop-blur border-b border-gray-200 dark:border-gray-700 flex items-center px-2 sticky top-0 z-20">
    <div class="flex items-center justify-between w-full space-x-3">
        <button @click="toggleSidebar()" class="px-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition"><i class='bx bx-menu text-2xl'></i></button>
        <div class="relative flex-1 max-w-xl">
            <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
            <form wire:submit.prevent="goSearch">
                <input type="text" wire:model.lazy="q"  placeholder="Search posts, tags, or authors" class="w-full pl-10 pr-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:placeholder-gray-400" aria-label="Search" />
            </form>
        </div>
        <div class="flex items-center space-x-2 gap-2 md:gap-6">
            <button @click="toggleDark()" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Toggle dark mode"><i class='bx' :class="darkMode ? 'bx-moon' : 'bx-sun'"></i></button>

            @auth
                @php
                    $unReadNotificationCount = auth()->user()->unreadNotifications->where('type','!=','App\Notifications\ModelUpdateNotification')->count();
                @endphp
                <a href="{{route('app.notifications')}}" wire:navigate class="relative p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="Notifications">
                    <i class='bx bx-bell text-xl'></i>
                    @if($unReadNotificationCount)
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-indigo-600 text-white text-[10px] flex items-center justify-center font-semibold">{{$unReadNotificationCount}}</span>
                    @endif
                </a>
                <div x-data="{open:false}" class="relative">
                    <button @click="open=!open" class="flex items-center space-x-2">
                        <img src="https://i.pravatar.cc/40?img=13" class="w-10 h-10 rounded-full ring-2 ring-indigo-500/40" alt="User" />
                    </button>
                    <div x-cloak x-show="open" @click.outside="open=false" x-transition.origin.top.right class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm">
                            <p class="font-semibold">{{auth()->user()->name}}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{auth()->user()->email}}</p>
                        </div>
                        <ul class="py-1 text-sm">
                            <li><a wire:navigate href="{{route('app.profile')}}" class="flex items-center px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700"><i class='bx bx-user mr-2'></i>Profile</a></li>
                        </ul>
                        <button wire:click.prevent="logout" class="w-full flex items-center px-4 py-2 text-left text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 text-sm"><i class='bx bx-log-out mr-2'></i>Logout</button>
                    </div>
                </div>
            @endauth
            @guest
                <a wire:navigate href="{{ route('login') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">Login</a>
                {{--                <a wire:navigate href="{{ route('register') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition">Register</a>--}}
            @endguest
        </div>
    </div>
</header>
