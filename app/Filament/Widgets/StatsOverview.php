<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;
        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $income = Transaction::income()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $expense = Transaction::expense()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        return [
            Stat::make('Total Income', $income),
            Stat::make('Total Expense', $expense),
            Stat::make('Net Balance', $income - $expense),
        ];
    }
}
