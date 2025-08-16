<aside class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col transform transition-transform duration-300 ease-in-out bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border-r border-gray-200 dark:border-gray-800" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>
    <!-- Brand -->
    <div class="h-16 flex items-center px-4 border-b border-gray-200 dark:border-gray-800 justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-500 flex items-center justify-center text-white text-xl font-bold">SC</div>
            <span class="font-semibold tracking-wide">{{ setup('name', 'laravel') }}</span>
        </div>
        <button @click="toggleSidebar()" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition"><i class='bx bx-x text-xl'></i></button>
    </div>

    <!-- Nav Items -->
    <nav class="flex-1 py-4 space-y-1">
        <template x-for="item in navItems" :key="item.id">
            <a wire:navigate :href="item.href" @click="active = item.id" :aria-label="item.label" class="group w-52 flex items-center rounded-lg mx-3 px-3 py-2 transition-colors duration-150"
                    :class="active===item.id ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'">
                <i class="bx text-2xl" :class="item.icon"></i>
                <span class="ml-3 text-sm font-medium" x-text="item.label"></span>
            </a>
        </template>
    </nav>

    <!-- Profile Section -->
{{--    <div class="mt-auto p-4 border-t border-gray-200 dark:border-gray-800">--}}
{{--        <div class="flex items-center">--}}
{{--            <div class="relative">--}}
{{--                <img src="https://i.pravatar.cc/60?img=13" class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-700" alt="User" />--}}
{{--                <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full ring-2 ring-gray-900 bg-emerald-500"></span>--}}
{{--            </div>--}}
{{--            <div class="ml-3">--}}
{{--                <p class="text-sm font-semibold leading-tight">Alex Morgan</p>--}}
{{--                <p class="text-xs text-gray-400">Online</p>--}}
{{--            </div>--}}
{{--            <div class="ml-auto flex items-center space-x-2">--}}
{{--                <button @click="toggleDark()" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition"><i class='bx' :class="darkMode ? 'bx-moon' : 'bx-sun'"></i></button>--}}
{{--                <button class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-700 transition"><i class='bx bx-cog'></i></button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
</aside>
