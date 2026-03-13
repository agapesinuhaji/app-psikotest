<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Batch;

class BatchChart extends ChartWidget
{
    protected ?string $heading = 'Batch Chart';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Batch::selectRaw('MONTH(created_at) as month, count(*) as total')
            ->groupBy('month')
            ->pluck('total')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Batch',
                    'data' => $data,
                ],
            ],
            'labels' => [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}