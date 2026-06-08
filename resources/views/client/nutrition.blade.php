<x-layouts.client>
    <x-slot:title>{{ __('client.nutrition.heading') }}</x-slot:title>

    <div class="px-4 py-5 space-y-4" x-data="nutritionLogger()">
        <!-- Header with Date Navigation -->
        <div class="mb-5">
            <div class="flex items-center justify-between">
                <h1 class="font-display text-2xl font-bold text-[#181b22] dark:text-[#f0f2f5] tracking-tight">{{ __('client.nutrition.heading') }}</h1>
                @if(($unreadCommentCount ?? 0) > 0)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e]" data-testid="unread-comments-badge">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        {{ $unreadCommentCount === 1
                            ? __('client.nutrition.comments.unread_badge_one')
                            : __('client.nutrition.comments.unread_badge', ['count' => $unreadCommentCount]) }}
                    </span>
                @endif
            </div>
            <div class="mt-3 flex items-center justify-between">
                <a :href="prevUrl" class="p-2 rounded-lg text-[#555b66] dark:text-[#a4abb6] hover:text-[#181b22] dark:hover:text-[#f0f2f5] hover:bg-[rgba(18,22,31,0.06)] dark:hover:bg-[rgba(255,255,255,0.06)] transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>

                <div class="flex items-center space-x-2">
                    <input type="date" x-model="currentDate" @change="navigateToDate()" max="{{ now()->format('Y-m-d') }}"
                        class="rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm text-[#181b22] dark:text-[#f0f2f5]">
                    <button @click="goToToday()" x-show="currentDate !== today"
                        class="text-xs text-[#5c7a10] dark:text-[#c6f24e] hover:opacity-80 font-semibold">
                        {{ __('client.nutrition.today') }}
                    </button>
                </div>

                <template x-if="currentDate < today">
                    <a :href="nextUrl" class="p-2 rounded-lg text-[#555b66] dark:text-[#a4abb6] hover:text-[#181b22] dark:hover:text-[#f0f2f5] hover:bg-[rgba(18,22,31,0.06)] dark:hover:bg-[rgba(255,255,255,0.06)] transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </template>
                <template x-if="currentDate >= today">
                    <div class="w-9"></div>
                </template>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-[#e8ffea] dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-4">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-4">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        @if($assignment && $assignedItems->isNotEmpty())
            @php
                // Order sections by FIRST-SEEN of each item's meal_type, preserving sort_order within each section.
                $orderedSectionLabels = $assignedItems->map(fn ($i) => $i['item']->meal_type)->unique()->values();
                $groupedAssignedItems = $assignedItems->groupBy(fn ($i) => $i['item']->meal_type);
                $totalAssigned = $assignedItems->count();
                $completedAssigned = $assignedItems->filter(fn ($i) => $i['completed'])->count();
            @endphp

            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5 mb-4">
                <div class="mb-4">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">
                        {{ __('client.nutrition.assigned_plan.heading', ['name' => $assignment->dayPlan->name]) }}
                    </h2>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.nutrition.assigned_plan.subtitle') }}</p>
                </div>

                <div class="space-y-4">
                    @foreach($orderedSectionLabels as $sectionLabel)
                        @php $group = $groupedAssignedItems->get($sectionLabel, collect()); @endphp
                        @if($group->isNotEmpty())
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-[#8c93a0] dark:text-[#6b7280] mb-2">{{ $sectionLabel }}</h3>
                                <div class="space-y-2">
                                    @foreach($group as $entry)
                                        @php
                                            $item = $entry['item'];
                                            $completed = $entry['completed'];
                                        @endphp
                                        <div class="flex items-center justify-between p-3 rounded-lg border {{ $completed ? 'border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-900/20' : 'border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] bg-gray-50 dark:bg-[rgba(255,255,255,0.03)]' }}">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate">{{ $item->name }}</p>
                                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                                    {{ (int) $item->calories }} kcal &middot;
                                                    P {{ $item->protein }}g &middot;
                                                    C {{ $item->carbs }}g &middot;
                                                    F {{ $item->fat }}g
                                                </p>
                                            </div>
                                            @if($completed)
                                                <span class="ml-3 inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-700 dark:text-green-400">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    {{ __('client.nutrition.assigned_plan.logged') }}
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('client.nutrition.store') }}" class="ml-3">
                                                    @csrf
                                                    <input type="hidden" name="date" value="{{ $date }}">
                                                    @if($item->meal_id)
                                                        <input type="hidden" name="meal_id" value="{{ $item->meal_id }}">
                                                    @endif
                                                    <input type="hidden" name="day_plan_item_id" value="{{ $item->id }}">
                                                    <input type="hidden" name="meal_type" value="{{ $item->meal_type }}">
                                                    <input type="hidden" name="name" value="{{ $item->name }}">
                                                    <input type="hidden" name="calories" value="{{ (int) $item->calories }}">
                                                    <input type="hidden" name="protein" value="{{ $item->protein }}">
                                                    <input type="hidden" name="carbs" value="{{ $item->carbs }}">
                                                    <input type="hidden" name="fat" value="{{ $item->fat }}">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-[#c6f24e] text-[#14180a] rounded-lg transition-colors hover:bg-[#b4e438]">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        {{ __('client.nutrition.assigned_plan.mark_eaten') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex justify-between text-xs text-[#8c93a0] dark:text-[#6b7280] mb-1">
                        <span>{{ __('client.nutrition.assigned_plan.progress', ['done' => $completedAssigned, 'total' => $totalAssigned]) }}</span>
                        <span>{{ $totalAssigned > 0 ? round(($completedAssigned / $totalAssigned) * 100) : 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-green-500" style="width: {{ $totalAssigned > 0 ? ($completedAssigned / $totalAssigned) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        @endif

        @if($hasPreviousDayLogs)
            <form method="POST" action="{{ route('client.nutrition.copy-yesterday') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-[#181b21] border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] text-[#181b22] dark:text-[#f0f2f5] text-sm font-medium rounded-xl shadow-[0_1px_2px_rgba(18,22,31,.05)] hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4 text-[#5c7a10] dark:text-[#c6f24e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    {{ __('client.nutrition.quick_log.copy_yesterday') }}
                </button>
            </form>
        @endif

        <!-- Macro Progress Bars -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
            @if($macroGoal)
                @php
                    $macros = [
                        ['label' => 'Calories', 'current' => $totals['calories'], 'target' => $macroGoal->calories, 'unit' => 'kcal', 'color' => 'bg-[#c6f24e]'],
                        ['label' => 'Protein', 'current' => $totals['protein'], 'target' => $macroGoal->protein, 'unit' => 'g', 'color' => 'bg-green-500'],
                        ['label' => 'Carbs', 'current' => $totals['carbs'], 'target' => $macroGoal->carbs, 'unit' => 'g', 'color' => 'bg-yellow-500'],
                        ['label' => 'Fat', 'current' => $totals['fat'], 'target' => $macroGoal->fat, 'unit' => 'g', 'color' => 'bg-red-500'],
                    ];
                @endphp
                <div class="space-y-4">
                    @foreach($macros as $macro)
                        @php
                            $pct = $macro['target'] > 0 ? min(100, ($macro['current'] / $macro['target']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $macro['label'] }}</span>
                                <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ number_format($macro['current'], $macro['unit'] === 'kcal' ? 0 : 1) }} / {{ number_format($macro['target'], $macro['unit'] === 'kcal' ? 0 : 1) }}{{ $macro['unit'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $macro['color'] }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-2">
                    <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.nutrition.no_macro_goals') }}</p>
                    @if($totals['calories'] > 0)
                        <p class="text-sm text-[#181b22] dark:text-[#f0f2f5] mt-2">
                            Today: {{ $totals['calories'] }} kcal &middot;
                            P {{ number_format($totals['protein'], 1) }}g &middot;
                            C {{ number_format($totals['carbs'], 1) }}g &middot;
                            F {{ number_format($totals['fat'], 1) }}g
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Logged Meals -->
        @if($mealLogs->count() > 0)
            <div class="space-y-3">
                @foreach($mealLogs->groupBy('meal_type') as $type => $logs)
                    <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] overflow-hidden">
                        <div class="px-5 py-3 bg-gray-50 dark:bg-[rgba(255,255,255,0.03)] border-b border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                            <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ $type }}</h3>
                        </div>
                        <div class="divide-y divide-[rgba(18,22,31,0.06)] dark:divide-[rgba(255,255,255,0.06)]">
                            @foreach($logs as $log)
                                <div class="px-5 py-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $log->name }}</p>
                                            <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                                {{ $log->calories }} kcal &middot;
                                                P {{ $log->protein }}g &middot;
                                                C {{ $log->carbs }}g &middot;
                                                F {{ $log->fat }}g
                                            </p>
                                        </div>
                                        <form method="POST" action="{{ route('client.nutrition.destroy', $log) }}" onsubmit="return confirm('{{ __('client.nutrition.remove_meal_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-[#8c93a0] dark:text-[#6b7280] hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    @if($log->comments->isNotEmpty())
                                        <div class="mt-2 ml-2 pl-3 border-l-2 border-[rgba(198,242,78,0.4)] bg-gray-50 dark:bg-gray-800/40 rounded-r-md py-2 pr-2">
                                            <p class="text-[11px] font-semibold uppercase tracking-wider text-[#8c93a0] dark:text-[#6b7280] mb-1.5">
                                                {{ __('client.nutrition.comments.heading') }}
                                            </p>
                                            <ul class="space-y-2">
                                                @foreach($log->comments as $comment)
                                                    <li class="flex items-start gap-2">
                                                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[rgba(198,242,78,0.15)] text-[#5c7a10] dark:bg-[rgba(198,242,78,0.12)] dark:text-[#c6f24e] text-[11px] font-semibold flex items-center justify-center">
                                                            {{ mb_strtoupper(mb_substr($comment->author?->name ?? '?', 0, 1)) }}
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs text-[#555b66] dark:text-gray-300 whitespace-pre-line">{{ $comment->body }}</p>
                                                            <p class="text-[11px] text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                                                {{ $comment->author?->name }} · {{ $comment->created_at->diffForHumans() }}
                                                            </p>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Nutrition Charts (Last 30 Days) -->
        @if($nutritionStats['daysLogged'] > 0)
            <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5] mb-4">{{ __('client.nutrition.charts_heading') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-2">{{ __('client.nutrition.calories') }}</h3>
                        <div class="h-56">
                            <canvas
                                x-data="clientCaloriesChart({{ json_encode($nutritionData) }})"
                                x-ref="canvas"
                                x-init="renderChart()"
                            ></canvas>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-[#555b66] dark:text-[#a4abb6] mb-2">{{ __('client.nutrition.macros') }}</h3>
                        <div class="h-56">
                            <canvas
                                x-data="clientMacrosChart({{ json_encode($nutritionData) }})"
                                x-ref="canvas"
                                x-init="renderChart()"
                            ></canvas>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    <div class="text-center">
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('client.nutrition.avg_calories') }}</p>
                        <p class="text-lg font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ number_format($nutritionStats['avgCalories']) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('client.nutrition.avg_protein') }}</p>
                        <p class="text-lg font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $nutritionStats['avgProtein'] }}g</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('client.nutrition.avg_carbs') }}</p>
                        <p class="text-lg font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $nutritionStats['avgCarbs'] }}g</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('client.nutrition.avg_fat') }}</p>
                        <p class="text-lg font-bold font-mono text-[#181b22] dark:text-[#f0f2f5]">{{ $nutritionStats['avgFat'] }}g</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] uppercase">{{ __('client.nutrition.adherence') }}</p>
                        @if($nutritionStats['adherenceRate'] !== null)
                            <p class="text-lg font-bold font-mono {{ $nutritionStats['adherenceRate'] >= 80 ? 'text-green-600' : ($nutritionStats['adherenceRate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $nutritionStats['adherenceRate'] }}%</p>
                        @else
                            <p class="text-lg font-bold font-mono text-[#8c93a0] dark:text-[#6b7280]">—</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($favorites->isNotEmpty())
            <div x-data="{ active: null, mealType: 'Breakfast', mealTypes: ['Breakfast', 'Lunch', 'Dinner', 'Snack'] }"
                class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] p-5">
                <div class="mb-3">
                    <h2 class="font-display text-base font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.quick_log.favorites_heading') }}</h2>
                    <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.nutrition.quick_log.favorites_subtitle') }}</p>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($favorites as $i => $fav)
                        <button type="button" @click="active = (active === {{ $i }} ? null : {{ $i }})"
                            :class="active === {{ $i }} ? 'bg-[rgba(198,242,78,0.15)] dark:bg-[rgba(198,242,78,0.12)] border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-[#555b66] dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="px-3 py-1.5 text-xs font-medium border rounded-full transition-colors">
                            {{ $fav['name'] }}
                            <span class="text-[10px] opacity-70 ml-1">{{ (int) $fav['calories'] }} kcal</span>
                        </button>
                    @endforeach
                </div>

                @foreach($favorites as $i => $fav)
                    <div x-show="active === {{ $i }}" x-cloak x-transition class="mt-4 pt-4 border-t border-[rgba(18,22,31,0.06)] dark:border-[rgba(255,255,255,0.06)]">
                        <form method="POST" action="{{ route('client.nutrition.store') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="name" value="{{ $fav['name'] }}">
                            <input type="hidden" name="calories" value="{{ (int) $fav['calories'] }}">
                            <input type="hidden" name="protein" value="{{ $fav['protein'] }}">
                            <input type="hidden" name="carbs" value="{{ $fav['carbs'] }}">
                            <input type="hidden" name="fat" value="{{ $fav['fat'] }}">
                            @if(! empty($fav['meal_id']))
                                <input type="hidden" name="meal_id" value="{{ $fav['meal_id'] }}">
                            @endif
                            <input type="hidden" name="meal_type" :value="mealType">

                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3">
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ $fav['name'] }}</p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                    {{ (int) $fav['calories'] }} kcal &middot;
                                    P {{ number_format($fav['protein'], 1) }}g &middot;
                                    C {{ number_format($fav['carbs'], 1) }}g &middot;
                                    F {{ number_format($fav['fat'], 1) }}g
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-2">{{ __('client.nutrition.meal_type') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="type in mealTypes" :key="type">
                                        <button type="button" @click="mealType = type"
                                            :class="mealType === type ? 'bg-[#c6f24e] text-[#14180a] border-[#c6f24e]' : 'bg-white dark:bg-gray-800 text-[#555b66] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                            class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors" x-text="type"></button>
                                    </template>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="button" @click="active = null"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-[#555b66] dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    {{ __('client.nutrition.quick_log.favorites_cancel') }}
                                </button>
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-[#c6f24e] text-[#14180a] text-sm font-semibold rounded-xl hover:bg-[#b4e438] transition-colors">
                                    {{ __('client.nutrition.quick_log.favorites_save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Add Meal Section -->
        <div class="bg-white dark:bg-[#181b21] rounded-xl border border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] shadow-[0_1px_2px_rgba(18,22,31,.05),0_5px_16px_rgba(18,22,31,.05)] dark:shadow-[0_1px_2px_rgba(0,0,0,.4),0_6px_18px_rgba(0,0,0,.3)] overflow-hidden">
            <div class="border-b border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)]">
                <nav class="flex">
                    <button @click="mode = 'library'" :class="mode === 'library' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#555b66] dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors" type="button">
                        {{ __('client.nutrition.library') }}
                    </button>
                    <button @click="mode = 'custom'" :class="mode === 'custom' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#555b66] dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors" type="button">
                        {{ __('client.nutrition.custom') }}
                    </button>
                    <button @click="mode = 'search'" :class="mode === 'search' ? 'border-[#c6f24e] text-[#5c7a10] dark:text-[#c6f24e]' : 'border-transparent text-[#8c93a0] dark:text-[#6b7280] hover:text-[#555b66] dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="flex-1 py-3 px-4 text-center text-sm font-medium border-b-2 transition-colors" type="button">
                        {{ __('client.nutrition.food_search.tab') }}
                    </button>
                </nav>
            </div>

            <div class="p-5">
                <!-- Library Mode -->
                <div x-show="mode === 'library'" class="space-y-4">
                    <div>
                        <input type="text" x-model="mealSearch" @input.debounce.300ms="searchMeals()" placeholder="{{ __('client.nutrition.search_meals') }}"
                            class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                    </div>

                    <div x-show="libraryMeals.length > 0" class="max-h-60 overflow-y-auto space-y-2">
                        <template x-for="meal in libraryMeals" :key="meal.id">
                            <button @click="selectLibraryMeal(meal)" type="button"
                                :class="selectedMeal && selectedMeal.id === meal.id ? 'border-[#c6f24e] bg-[rgba(198,242,78,0.12)] dark:bg-[rgba(198,242,78,0.08)]' : 'border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] hover:bg-gray-50 dark:hover:bg-gray-800'"
                                class="w-full text-left p-3 rounded-xl border transition-colors">
                                <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]" x-text="meal.name"></p>
                                <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">
                                    <span x-text="meal.calories"></span> kcal &middot;
                                    P <span x-text="meal.protein"></span>g &middot;
                                    C <span x-text="meal.carbs"></span>g &middot;
                                    F <span x-text="meal.fat"></span>g
                                </p>
                            </button>
                        </template>
                    </div>

                    <div x-show="mealSearch && libraryMeals.length === 0 && !searching" class="text-center py-4">
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.nutrition.no_meals_found') }}</p>
                    </div>

                    <div x-show="selectedMeal" class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <!-- Portion Selector -->
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-2">{{ __('client.nutrition.quick_log.portion') }}</label>
                            <div class="grid grid-cols-5 gap-2">
                                <template x-for="p in portionOptions" :key="p">
                                    <button type="button" @click="portion = p; customPortionMode = false"
                                        :class="(portion === p && !customPortionMode) ? 'bg-[#c6f24e] text-[#14180a] border-[#c6f24e]' : 'bg-white dark:bg-gray-800 text-[#555b66] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors">
                                        <span x-text="p + '×'"></span>
                                    </button>
                                </template>
                                <button type="button" @click="enableCustomPortion()"
                                    :class="customPortionMode ? 'bg-[#c6f24e] text-[#14180a] border-[#c6f24e]' : 'bg-white dark:bg-gray-800 text-[#555b66] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                    class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors">
                                    <span x-show="!(customPortionMode && !portionOptions.includes(portion))">{{ __('client.nutrition.quick_log.portion_custom') }}</span>
                                    <span x-show="customPortionMode && !portionOptions.includes(portion)" x-cloak>
                                        {{ __('client.nutrition.quick_log.portion_custom') }} (×<span x-text="Math.round(portion * 100) / 100"></span>)
                                    </span>
                                </button>
                            </div>
                            <div x-show="customPortionMode" x-cloak class="mt-2 flex gap-2">
                                <input type="number" step="0.1" min="0.1" max="10"
                                    x-model="customPortionInput"
                                    @keydown.enter.prevent="applyCustomPortion()"
                                    placeholder="{{ __('client.nutrition.quick_log.portion_custom_placeholder') }}"
                                    class="flex-1 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                                <button type="button" @click="applyCustomPortion()"
                                    class="px-4 py-1.5 text-sm font-medium bg-[#c6f24e] text-[#14180a] rounded-lg transition-colors hover:bg-[#b4e438]">
                                    {{ __('client.nutrition.quick_log.portion_custom_apply') }}
                                </button>
                            </div>
                            <div x-show="selectedMeal" class="mt-2 text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                <span x-text="scaledName"></span> &middot;
                                <span x-text="scaledCalories"></span> kcal &middot;
                                P <span x-text="scaledProtein"></span>g &middot;
                                C <span x-text="scaledCarbs"></span>g &middot;
                                F <span x-text="scaledFat"></span>g
                            </div>
                        </div>

                        <form method="POST" action="{{ route('client.nutrition.store') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="meal_id" :value="selectedMeal?.id">
                            <input type="hidden" name="name" :value="scaledName">
                            <input type="hidden" name="calories" :value="scaledCalories">
                            <input type="hidden" name="protein" :value="scaledProtein">
                            <input type="hidden" name="carbs" :value="scaledCarbs">
                            <input type="hidden" name="fat" :value="scaledFat">

                            <!-- Meal Type Selector -->
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-2">{{ __('client.nutrition.meal_type') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="type in mealTypes" :key="type">
                                        <button type="button" @click="mealType = type; customMealType = ''"
                                            :class="mealType === type && !customMealType ? 'bg-[#c6f24e] text-[#14180a] border-[#c6f24e]' : 'bg-white dark:bg-gray-800 text-[#555b66] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                            class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors" x-text="type">
                                        </button>
                                    </template>
                                </div>
                                <input type="text" x-model="customMealType" @input="if(customMealType) mealType = ''" placeholder="{{ __('client.nutrition.or_type_custom') }}"
                                    class="mt-2 w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                                <input type="hidden" name="meal_type" :value="customMealType || mealType">
                            </div>

                            <button type="submit" :disabled="!(mealType || customMealType)"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#c6f24e] text-[#14180a] text-sm font-semibold rounded-xl hover:bg-[#b4e438] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('client.nutrition.log_meal') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Custom Mode -->
                <div x-show="mode === 'custom'">
                    <form method="POST" action="{{ route('client.nutrition.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">

                        <div>
                            <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm"
                                placeholder="{{ __('client.nutrition.meal_name_placeholder') }}">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meal Type Selector -->
                        <div>
                            <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-2">{{ __('client.nutrition.meal_type') }} <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="type in mealTypes" :key="type">
                                    <button type="button" @click="customFormMealType = type; customFormCustomType = ''"
                                        :class="customFormMealType === type && !customFormCustomType ? 'bg-[#c6f24e] text-[#14180a] border-[#c6f24e]' : 'bg-white dark:bg-gray-800 text-[#555b66] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors" x-text="type">
                                    </button>
                                </template>
                            </div>
                            <input type="text" x-model="customFormCustomType" @input="if(customFormCustomType) customFormMealType = ''" placeholder="{{ __('client.nutrition.or_type_custom') }}"
                                class="mt-2 w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                            <input type="hidden" name="meal_type" :value="customFormCustomType || customFormMealType">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.calories') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="calories" required min="0" value="{{ old('calories') }}"
                                class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm"
                                placeholder="0">
                            @error('calories')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.protein_g') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="protein" required min="0" step="0.1" value="{{ old('protein') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.carbs_g') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="carbs" required min="0" step="0.1" value="{{ old('carbs') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm"
                                    placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.fat_g') }} <span class="text-red-500">*</span></label>
                                <input type="number" name="fat" required min="0" step="0.1" value="{{ old('fat') }}"
                                    class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm"
                                    placeholder="0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.notes') }}</label>
                            <textarea name="notes" rows="2"
                                class="mt-1 block w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm"
                                placeholder="{{ __('client.nutrition.optional_notes') }}">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" :disabled="!(customFormMealType || customFormCustomType)"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#c6f24e] text-[#14180a] text-sm font-semibold rounded-xl hover:bg-[#b4e438] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ __('client.nutrition.log_meal') }}
                        </button>
                    </form>
                </div>

                <!-- Search Mode (Open Food Facts) -->
                <div x-show="mode === 'search'" class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-[#181b22] dark:text-[#f0f2f5]">{{ __('client.nutrition.food_search.heading') }}</h3>
                        <p class="text-xs text-[#8c93a0] dark:text-[#6b7280] mt-0.5">{{ __('client.nutrition.food_search.subtitle') }}</p>
                    </div>

                    <input type="text" x-model="foodQuery" @input.debounce.300ms="searchFoods()"
                        placeholder="{{ __('client.nutrition.food_search.placeholder') }}"
                        class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">

                    <div x-show="foodSearching" class="text-center py-4">
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.nutrition.food_search.loading') }}</p>
                    </div>

                    <div x-show="!foodSearching && foodQuery.length > 0 && foodQuery.length < 2" x-cloak class="text-center py-4">
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.nutrition.food_search.min_chars') }}</p>
                    </div>

                    <div x-show="!foodSearching && foodQuery.length >= 2 && foodResults.length === 0 && foodSearched" x-cloak class="text-center py-4">
                        <p class="text-sm text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.nutrition.food_search.no_results') }}</p>
                    </div>

                    <div x-show="foodResults.length > 0" class="max-h-72 overflow-y-auto space-y-2">
                        <template x-for="item in foodResults" :key="item.code">
                            <button @click="selectFoodItem(item)" type="button"
                                :class="selectedFood && selectedFood.code === item.code ? 'border-[#c6f24e] bg-[rgba(198,242,78,0.12)] dark:bg-[rgba(198,242,78,0.08)]' : 'border-[rgba(18,22,31,0.09)] dark:border-[rgba(255,255,255,0.08)] hover:bg-gray-50 dark:hover:bg-gray-800'"
                                class="w-full text-left p-3 rounded-xl border transition-colors flex items-start gap-3">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-lg object-cover bg-gray-100 dark:bg-gray-800 flex-shrink-0">
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex-shrink-0 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#8c93a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16M4 6v12a2 2 0 002 2h12a2 2 0 002-2V6"/>
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] truncate" x-text="item.name"></p>
                                    <p x-show="item.brand" class="text-xs text-[#8c93a0] dark:text-[#6b7280] truncate" x-text="item.brand"></p>
                                    <p class="text-xs text-[#555b66] dark:text-[#a4abb6] mt-1">
                                        <span x-text="Math.round(item.kcal_per_100g)"></span> kcal &middot;
                                        P<span x-text="item.protein_per_100g"></span>/C<span x-text="item.carbs_per_100g"></span>/F<span x-text="item.fat_per_100g"></span>
                                        <span class="text-[#8c93a0] dark:text-[#6b7280]">{{ __('client.nutrition.food_search.per_100g') }}</span>
                                    </p>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div x-show="selectedFood" x-cloak class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <form method="POST" action="{{ route('client.nutrition.store') }}" @submit="if (!foodFormReady()) $event.preventDefault()">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="name" :value="foodScaledName">
                            <input type="hidden" name="calories" :value="foodScaledCalories">
                            <input type="hidden" name="protein" :value="foodScaledProtein">
                            <input type="hidden" name="carbs" :value="foodScaledCarbs">
                            <input type="hidden" name="fat" :value="foodScaledFat">
                            <input type="hidden" name="meal_type" :value="foodCustomMealType || foodMealType">

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-2">{{ __('client.nutrition.food_search.portion_label') }}</label>
                                <input type="number" min="1" max="2000" step="1" x-model.number="foodPortion"
                                    class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                                <p class="mt-2 text-xs text-[#8c93a0] dark:text-[#6b7280]">
                                    <span x-text="foodScaledCalories"></span> kcal &middot;
                                    P <span x-text="foodScaledProtein"></span>g &middot;
                                    C <span x-text="foodScaledCarbs"></span>g &middot;
                                    F <span x-text="foodScaledFat"></span>g
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="block text-sm font-medium text-[#181b22] dark:text-[#f0f2f5] mb-2">{{ __('client.nutrition.food_search.meal_type_label') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="type in mealTypes" :key="type">
                                        <button type="button" @click="foodMealType = type; foodCustomMealType = ''"
                                            :class="foodMealType === type && !foodCustomMealType ? 'bg-[#c6f24e] text-[#14180a] border-[#c6f24e]' : 'bg-white dark:bg-gray-800 text-[#555b66] dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
                                            class="px-3 py-1.5 text-sm font-medium border rounded-lg transition-colors" x-text="type"></button>
                                    </template>
                                </div>
                                <input type="text" x-model="foodCustomMealType" @input="if (foodCustomMealType) foodMealType = ''"
                                    placeholder="{{ __('client.nutrition.or_type_custom') }}"
                                    class="mt-2 w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-[#c6f24e] focus:ring-[#c6f24e] text-sm">
                            </div>

                            <button type="submit"
                                :disabled="!foodFormReady()"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#c6f24e] text-[#14180a] text-sm font-semibold rounded-xl hover:bg-[#b4e438] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                {{ __('client.nutrition.food_search.save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Mark all unread coach meal-log comments as read on page load
        document.addEventListener('DOMContentLoaded', () => {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                return;
            }
            fetch('{{ route('client.meal-log-comments.mark-read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            }).catch(() => { /* swallow — non-critical */ });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <script>
        function chartTheme() {
            const dark = document.documentElement.classList.contains('dark');
            return {
                tickColor:  dark ? '#9ca3af' : '#6b7280',
                gridColor:  dark ? 'rgba(75, 85, 99, 0.25)' : 'rgba(229, 231, 235, 1)',
                legendColor: dark ? '#d1d5db' : '#374151',
            };
        }

        function clientCaloriesChart(nutritionData) {
            return {
                renderChart() {
                    const existing = Chart.getChart(this.$refs.canvas);
                    if (existing) existing.destroy();
                    const ctx = this.$refs.canvas.getContext('2d');
                    const theme = chartTheme();
                    const labels = nutritionData.map(d => {
                        const date = new Date(d.date + 'T00:00:00');
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    });
                    const calories = nutritionData.map(d => d.calories);
                    const goals = nutritionData.map(d => d.goalCalories);

                    const bgColors = nutritionData.map(d => {
                        if (!d.goalCalories || d.calories === 0) return 'rgba(209, 213, 219, 0.5)';
                        const dev = Math.abs(d.calories - d.goalCalories) / d.goalCalories;
                        if (dev <= 0.10) return 'rgba(34, 197, 94, 0.7)';
                        if (dev <= 0.25) return 'rgba(234, 179, 8, 0.7)';
                        return 'rgba(239, 68, 68, 0.7)';
                    });

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                { label: 'Calories', data: calories, backgroundColor: bgColors, borderRadius: 3 },
                                { label: 'Goal', data: goals, type: 'line', borderColor: 'rgba(107, 114, 128, 0.5)', borderDash: [5, 5], pointRadius: 0, fill: false, borderWidth: 2 }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, color: theme.legendColor } } },
                            scales: {
                                x: { ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                                y: { beginAtZero: true, ticks: { color: theme.tickColor }, grid: { color: theme.gridColor } }
                            }
                        }
                    });
                }
            };
        }

        function clientMacrosChart(nutritionData) {
            return {
                renderChart() {
                    const existing = Chart.getChart(this.$refs.canvas);
                    if (existing) existing.destroy();
                    const ctx = this.$refs.canvas.getContext('2d');
                    const theme = chartTheme();
                    const labels = nutritionData.map(d => {
                        const date = new Date(d.date + 'T00:00:00');
                        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    });

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                { label: 'Protein', data: nutritionData.map(d => d.protein), backgroundColor: 'rgba(59, 130, 246, 0.7)', borderRadius: 2 },
                                { label: 'Carbs', data: nutritionData.map(d => d.carbs), backgroundColor: 'rgba(234, 179, 8, 0.7)', borderRadius: 2 },
                                { label: 'Fat', data: nutritionData.map(d => d.fat), backgroundColor: 'rgba(239, 68, 68, 0.7)', borderRadius: 2 },
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 12, color: theme.legendColor } } },
                            scales: {
                                x: { stacked: true, ticks: { maxTicksLimit: 10, color: theme.tickColor }, grid: { color: theme.gridColor } },
                                y: { stacked: true, beginAtZero: true, ticks: { color: theme.tickColor }, grid: { color: theme.gridColor } }
                            }
                        }
                    });
                }
            };
        }

        function nutritionLogger() {
            const currentDate = '{{ $date }}';
            const today = '{{ now()->format("Y-m-d") }}';
            const baseUrl = '{{ route("client.nutrition") }}';
            const mealsUrl = '{{ route("client.nutrition.meals") }}';
            const foodSearchUrl = '{{ route("client.nutrition.food-search") }}';

            function shiftDate(dateStr, days) {
                const d = new Date(dateStr + 'T00:00:00');
                d.setDate(d.getDate() + days);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            return {
                currentDate: currentDate,
                today: today,
                mode: 'library',
                mealSearch: '',
                libraryMeals: [],
                selectedMeal: null,
                searching: false,
                mealType: 'Breakfast',
                customMealType: '',
                customFormMealType: 'Breakfast',
                customFormCustomType: '',
                mealTypes: ['Breakfast', 'Lunch', 'Dinner', 'Snack'],
                portion: 1,
                portionOptions: [0.5, 1, 1.5, 2],
                customPortionMode: false,
                customPortionInput: '',
                foodQuery: '',
                foodResults: [],
                foodSearching: false,
                foodSearched: false,
                selectedFood: null,
                foodPortion: 100,
                foodMealType: 'Breakfast',
                foodCustomMealType: '',

                get scaledCalories() {
                    if (!this.selectedMeal) return 0;
                    return Math.round(Number(this.selectedMeal.calories) * this.portion);
                },
                get scaledProtein() {
                    if (!this.selectedMeal) return 0;
                    return Math.round(Number(this.selectedMeal.protein) * this.portion * 10) / 10;
                },
                get scaledCarbs() {
                    if (!this.selectedMeal) return 0;
                    return Math.round(Number(this.selectedMeal.carbs) * this.portion * 10) / 10;
                },
                get scaledFat() {
                    if (!this.selectedMeal) return 0;
                    return Math.round(Number(this.selectedMeal.fat) * this.portion * 10) / 10;
                },
                get scaledName() {
                    if (!this.selectedMeal) return '';
                    if (this.portion === 1) return this.selectedMeal.name;
                    const display = Math.round(this.portion * 100) / 100;
                    return this.selectedMeal.name + ' (×' + display + ')';
                },

                enableCustomPortion() {
                    this.customPortionMode = true;
                    if (this.portionOptions.includes(this.portion)) {
                        this.customPortionInput = '';
                    } else {
                        this.customPortionInput = String(Math.round(this.portion * 100) / 100);
                    }
                },
                applyCustomPortion() {
                    const value = parseFloat(this.customPortionInput);
                    if (isNaN(value) || value < 0.1 || value > 10) {
                        return;
                    }
                    this.portion = Math.round(value * 100) / 100;
                    this.customPortionMode = true;
                },

                get foodScale() {
                    const grams = Number(this.foodPortion);
                    if (isNaN(grams) || grams < 1) return 0;
                    return grams / 100;
                },
                get foodScaledCalories() {
                    if (!this.selectedFood) return 0;
                    return Math.round(Number(this.selectedFood.kcal_per_100g) * this.foodScale);
                },
                get foodScaledProtein() {
                    if (!this.selectedFood) return 0;
                    return Math.round(Number(this.selectedFood.protein_per_100g) * this.foodScale * 10) / 10;
                },
                get foodScaledCarbs() {
                    if (!this.selectedFood) return 0;
                    return Math.round(Number(this.selectedFood.carbs_per_100g) * this.foodScale * 10) / 10;
                },
                get foodScaledFat() {
                    if (!this.selectedFood) return 0;
                    return Math.round(Number(this.selectedFood.fat_per_100g) * this.foodScale * 10) / 10;
                },
                get foodScaledName() {
                    if (!this.selectedFood) return '';
                    const base = this.selectedFood.brand
                        ? this.selectedFood.name + ' (' + this.selectedFood.brand + ')'
                        : this.selectedFood.name;
                    return base;
                },

                async searchFoods() {
                    const q = (this.foodQuery || '').trim();
                    if (q.length < 2) {
                        this.foodResults = [];
                        this.foodSearched = false;
                        return;
                    }
                    this.foodSearching = true;
                    this.foodSearched = false;
                    try {
                        const response = await fetch(foodSearchUrl + '?q=' + encodeURIComponent(q), {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!response.ok) {
                            this.foodResults = [];
                            return;
                        }
                        const data = await response.json();
                        this.foodResults = Array.isArray(data.results) ? data.results : [];
                    } catch (e) {
                        this.foodResults = [];
                    } finally {
                        this.foodSearching = false;
                        this.foodSearched = true;
                    }
                },

                selectFoodItem(item) {
                    this.selectedFood = this.selectedFood?.code === item.code ? null : item;
                    this.foodPortion = 100;
                },

                foodFormReady() {
                    if (!this.selectedFood) return false;
                    const grams = Number(this.foodPortion);
                    if (isNaN(grams) || grams < 1 || grams > 2000) return false;
                    if (!(this.foodMealType || this.foodCustomMealType)) return false;
                    if (this.foodScaledCalories <= 0) return false;
                    return true;
                },

                get prevUrl() {
                    return baseUrl + '?date=' + shiftDate(this.currentDate, -1);
                },
                get nextUrl() {
                    const next = shiftDate(this.currentDate, 1);
                    return next <= today ? baseUrl + '?date=' + next : '#';
                },
                navigateToDate() {
                    if (this.currentDate && this.currentDate <= today) {
                        window.location.href = baseUrl + '?date=' + this.currentDate;
                    }
                },
                goToToday() {
                    window.location.href = baseUrl;
                },

                async searchMeals() {
                    if (!this.mealSearch) {
                        this.libraryMeals = [];
                        this.loadAllMeals();
                        return;
                    }
                    this.searching = true;
                    try {
                        const response = await fetch(mealsUrl + '?search=' + encodeURIComponent(this.mealSearch));
                        this.libraryMeals = await response.json();
                    } finally {
                        this.searching = false;
                    }
                },

                selectLibraryMeal(meal) {
                    this.selectedMeal = this.selectedMeal?.id === meal.id ? null : meal;
                    this.portion = 1;
                    this.customPortionMode = false;
                    this.customPortionInput = '';
                },

                loadAllMeals() {
                    fetch(mealsUrl)
                        .then(r => r.json())
                        .then(meals => { this.libraryMeals = meals; });
                },

                init() {
                    this.loadAllMeals();
                }
            };
        }
    </script>
    @endpush
</x-layouts.client>
