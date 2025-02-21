<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Expense';
    protected static string $color = 'danger';

    protected function getData(): array
    {
        $startDate = isset($this->filters['startDate']) && $this->filters['startDate']
            ? Carbon::parse($this->filters['startDate'])
            : now()->subDays(30);
        $endDate = isset($this->filters['endDate']) && $this->filters['endDate']
            ? Carbon::parse($this->filters['endDate'])
            : now();
        $data = Trend::query(Transaction::expense())
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Expenses per Day',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
