<?php

namespace App\Filament\Widgets;

use App\Enums\EnquiryStatus;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestEnquiries extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $ids = Enquiry::query()->latest()->limit(5)->pluck('id');

        return $table
            ->heading('Latest enquiries')
            ->query(Enquiry::query()->whereIn('id', $ids)->latest())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('phone'),
                TextColumn::make('job_type')->label('Job'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (EnquiryStatus $state): string => $state->getColor()),
                TextColumn::make('created_at')->since(),
            ])
            ->paginated(false)
            ->recordUrl(fn (Enquiry $record): string => EnquiryResource::getUrl('view', ['record' => $record]));
    }
}
