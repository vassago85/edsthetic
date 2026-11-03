<?php

namespace App\Filament\Resources\Enquiries;

use App\Enums\EnquiryStatus;
use App\Enums\JobType;
use App\Filament\Resources\Enquiries\Pages\EditEnquiry;
use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Filament\Resources\Enquiries\Pages\ViewEnquiry;
use App\Models\Enquiry;
use App\Support\Phone;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static ?string $navigationLabel = 'Enquiries';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Enquiry')->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('phone')->tel()->required()->maxLength(40),
                TextInput::make('email')->email()->maxLength(255),
                TextInput::make('suburb')->maxLength(120),
                Select::make('job_type')
                    ->label('Job type')
                    ->options(JobType::class)
                    ->required(),
                Textarea::make('message')->rows(5)->columnSpanFull(),
            ])->columns(2),
            Section::make('Follow-up')->schema([
                Select::make('status')
                    ->options(EnquiryStatus::class)
                    ->required(),
                Textarea::make('internal_notes')
                    ->rows(4)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Enquiry')->schema([
                TextEntry::make('name'),
                TextEntry::make('phone'),
                TextEntry::make('email')->placeholder('Not given'),
                TextEntry::make('suburb')->placeholder('Not given'),
                TextEntry::make('job_type')->label('Job type')->badge(),
                TextEntry::make('message')->placeholder('Not given')->columnSpanFull(),
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('ip_address')->label('IP address'),
            ])->columns(2),
            Section::make('Follow-up')->schema([
                TextEntry::make('status')->badge(),
                TextEntry::make('read_at')->dateTime()->placeholder('Unread'),
                TextEntry::make('internal_notes')->placeholder('None')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('job_type')->label('Job type')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (EnquiryStatus $state): string => $state->getColor())
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('read_at')
                    ->label('Read')
                    ->badge()
                    ->state(fn (Enquiry $record): string => $record->read_at ? 'Read' : 'Unread')
                    ->color(fn (string $state): string => $state === 'Read' ? 'gray' : 'warning'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(EnquiryStatus::class),
                SelectFilter::make('job_type')->label('Job type')->options(JobType::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('call')
                    ->label('Call')
                    ->icon(Heroicon::OutlinedPhone)
                    ->url(fn (Enquiry $record): string => Phone::tel($record->phone)),
                Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                    ->url(
                        fn (Enquiry $record): string => Phone::whatsapp($record->phone) ?? '',
                        true,
                    )
                    ->visible(fn (Enquiry $record): bool => Phone::whatsapp($record->phone) !== null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('status')
                        ->label('Change status')
                        ->icon(Heroicon::OutlinedArrowPath)
                        ->schema([
                            Select::make('status')
                                ->options(EnquiryStatus::class)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(fn (Model $record) => $record->update([
                                'status' => $data['status'],
                            ]));
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Enquiry::query()->whereNull('read_at')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnquiries::route('/'),
            'view' => ViewEnquiry::route('/{record}'),
            'edit' => EditEnquiry::route('/{record}/edit'),
        ];
    }
}
