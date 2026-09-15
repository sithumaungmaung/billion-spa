<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    ```
    <title>Select Branch — Billion Spa</title>

    @vite(['resources/css/app.css'])
    ```

</head>

<body class="min-h-screen bg-gray-50 text-gray-900 antialiased dark:bg-gray-950 dark:text-white">

    ```
    <div class="relative min-h-screen">

        {{-- Background --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div
                class="absolute -left-32 -top-32 h-80 w-80 rounded-full bg-primary-100/40 blur-3xl dark:bg-primary-900/10">
            </div>
            <div
                class="absolute -bottom-32 -right-32 h-80 w-80 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/5">
            </div>
        </div>

        {{-- Header --}}
        <header
            class="relative border-b border-gray-200 bg-white/80 backdrop-blur-xl dark:border-gray-800 dark:bg-gray-950/80">
            <div class="mx-auto flex h-16 max-w-6xl items-center px-5 sm:px-6">

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-900 text-white dark:bg-white dark:text-gray-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                d="M3 21h18M5 21V5a2 2 0 012-2h6a2 2 0 012 2v16M15 21V9a2 2 0 012-2h1a2 2 0 012 2v12M9 7h2M9 11h2M9 15h2" />
                        </svg>
                    </div>

                    <div>
                        <div class="text-sm font-bold tracking-[0.16em] text-gray-950 dark:text-white">
                            BILLION SPA
                        </div>

                        <div class="text-[9px] font-medium uppercase tracking-[0.18em] text-gray-400">
                            Management System
                        </div>
                    </div>

                </div>

            </div>
        </header>


        {{-- Main --}}
        <main class="relative mx-auto max-w-6xl px-5 py-12 sm:px-6 sm:py-16">

            {{-- Heading --}}
            <div class="mx-auto max-w-xl text-center">

                <h1 class="text-3xl font-semibold tracking-tight text-gray-950 sm:text-4xl dark:text-white">
                    Select a branch
                </h1>

                <p class="mt-3 text-sm leading-6 text-gray-500 sm:text-base dark:text-gray-400">
                    Choose the branch you want to manage.
                </p>

            </div>


            {{-- Branches --}}
            @if ($branches->isNotEmpty())

                <div class="mx-auto mt-10 grid max-w-4xl grid-cols-1 gap-4 sm:grid-cols-2">

                    @foreach ($branches as $branch)
                        <form method="POST" action="{{ route('branch.select', $branch) }}">
                            @csrf

                            <button type="submit"
                                class="group relative w-full overflow-hidden rounded-2xl border border-gray-200 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-primary-700 dark:focus:ring-offset-gray-950">

                                {{-- Subtle hover effect --}}
                                <div
                                    class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-primary-50 opacity-0 blur-2xl transition duration-300 group-hover:opacity-100 dark:bg-primary-900/20">
                                </div>


                                <div class="relative flex items-center gap-4">

                                    {{-- Icon --}}
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-700 transition duration-200 group-hover:bg-primary-600 group-hover:text-white dark:bg-gray-800 dark:text-gray-300 dark:group-hover:bg-primary-600 dark:group-hover:text-white">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                                                d="M3 21h18M5 21V5a2 2 0 012-2h6a2 2 0 012 2v16M15 21V9a2 2 0 012-2h1a2 2 0 012 2v12M9 7h2M9 11h2M9 15h2" />
                                        </svg>
                                    </div>


                                    {{-- Branch Info --}}
                                    <div class="min-w-0 flex-1">

                                        <h2 class="truncate text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $branch->name }}
                                        </h2>

                                        @if ($branch->address)
                                            <div
                                                class="mt-1.5 flex items-start gap-1.5 text-sm text-gray-500 dark:text-gray-400">

                                                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.7"
                                                        d="M12 21s8-4.5 8-11a8 8 0 10-16 0c0 6.5 8 11 8 11z" />
                                                    <circle cx="12" cy="10" r="2.5" />
                                                </svg>

                                                <span class="line-clamp-2">
                                                    {{ $branch->address }}
                                                </span>

                                            </div>
                                        @endif

                                    </div>


                                    {{-- Arrow --}}
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-gray-400 transition duration-200 group-hover:bg-primary-600 group-hover:text-white dark:text-gray-500">
                                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-0.5"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14m-4-4l4 4-4 4" />
                                        </svg>
                                    </div>

                                </div>

                            </button>

                        </form>
                    @endforeach

                </div>
            @else
                {{-- Empty State --}}
                <div
                    class="mx-auto mt-10 max-w-md rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center dark:border-gray-700 dark:bg-gray-900">

                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">

                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                d="M3 21h18M5 21V5a2 2 0 012-2h6a2 2 0 012 2v16M15 21V9a2 2 0 012-2h1a2 2 0 012 2v12" />
                        </svg>

                    </div>

                    <h2 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                        No branches available
                    </h2>

                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                        There are currently no branches assigned to your account.
                    </p>

                </div>

            @endif

        </main>

    </div>
    ```

</body>

</html>
