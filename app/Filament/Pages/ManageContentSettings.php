<?php

namespace App\Filament\Pages;

use App\Settings\ContentSettings;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageContentSettings extends SettingsPage
{
    protected static string $settings = ContentSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Page content';

    protected static string|\UnitEnum|null $navigationGroup = 'Site settings';

    protected static ?int $navigationSort = 11;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero')->schema([
                TextInput::make('hero_heading')->required()->columnSpanFull(),
                TextInput::make('hero_text')->required()->columnSpanFull(),
            ]),
            Section::make('Owner section')->schema([
                TextInput::make('owner_heading')->required()->columnSpanFull(),
                RichEditor::make('owner_bio')
                    ->fileAttachments(false)
                    ->toolbarButtons([
                        ['bold', 'italic', 'link'],
                        ['bulletList'],
                    ])
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('owner_signoff')->label('Sign-off')->columnSpanFull(),
            ]),
            Section::make('Typical kitchen job')->schema([
                Repeater::make('lead_times')
                    ->label('Lead times')
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('duration')->required(),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->columnSpanFull(),
            ]),
            Section::make('Aftercare')->schema([
                Repeater::make('aftercare')
                    ->schema([
                        TextInput::make('heading')->required()->columnSpanFull(),
                        TagsInput::make('dos')->label('Do')->columnSpanFull(),
                        TagsInput::make('donts')->label("Don't")->columnSpanFull(),
                    ])
                    ->reorderable()
                    ->itemLabel(fn (array $state): ?string => $state['heading'] ?? null)
                    ->columnSpanFull(),
            ]),
            Section::make('Terms')->schema([
                Repeater::make('terms')
                    ->schema([
                        TextInput::make('heading')->required(),
                        RichEditor::make('body')
                            ->fileAttachments(false)
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                ['bulletList', 'orderedList'],
                            ])
                            ->required(),
                    ])
                    ->reorderable()
                    ->itemLabel(fn (array $state): ?string => $state['heading'] ?? null)
                    ->columnSpanFull(),
            ]),
            Section::make('Privacy')->schema([
                TextInput::make('enquiry_retention_months')
                    ->label('Keep enquiries for (months)')
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->helperText('Older enquiries are deleted automatically. This is the POPIA retention period.'),
            ]),
        ]);
    }
}
