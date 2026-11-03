<?php

namespace App\Filament\Pages;

use App\Models\Project;
use App\Rules\EmailOrPlaceholder;
use App\Rules\OptionalUrl;
use App\Settings\BusinessSettings;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ManageBusinessSettings extends SettingsPage
{
    protected static string $settings = BusinessSettings::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'Business details';

    protected static string|\UnitEnum|null $navigationGroup = 'Site settings';

    protected static ?int $navigationSort = 10;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contact')->schema([
                TextInput::make('business_name')->required()->maxLength(255),
                TextInput::make('phone')->required()->maxLength(40),
                TextInput::make('whatsapp_number')
                    ->label('WhatsApp number')
                    ->helperText('Use a South African number. A leading 0 is converted to 27 for the chat link.')
                    ->maxLength(40),
                TextInput::make('email')->required()->maxLength(255),
                TextInput::make('notification_email')
                    ->label('Enquiry notification email')
                    ->required()
                    ->rules([new EmailOrPlaceholder])
                    ->helperText('Quote requests are emailed here. The message is queued.')
                    ->maxLength(255),
                TextInput::make('address')->required()->columnSpanFull(),
                TextInput::make('hours')->label('Hours')->required(),
                TextInput::make('service_area')->label('Areas we work in')->required(),
                TextInput::make('callback_days')
                    ->label('Callback time')
                    ->helperText('Shown as “We’ll call you within [this] working days”.')
                    ->required()
                    ->maxLength(40),
            ])->columns(2),
            Section::make('Registration')->schema([
                TextInput::make('registration_number')->label('Registration number')->maxLength(255),
                TextInput::make('vat_number')->label('VAT number')->maxLength(255),
                TextInput::make('facebook_url')
                    ->label('Facebook URL')
                    ->rules([new OptionalUrl])
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (mixed $state): string => is_string($state) ? $state : ''),
                TextInput::make('instagram_url')
                    ->label('Instagram URL')
                    ->rules([new OptionalUrl])
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (mixed $state): string => is_string($state) ? $state : ''),
            ])->columns(2),
            Section::make('Search and sharing')->schema([
                TextInput::make('meta_title')->label('Page title')->required()->maxLength(255)->columnSpanFull(),
                TextInput::make('meta_description')->label('Meta description')->required()->maxLength(300)->columnSpanFull(),
                Select::make('og_image')
                    ->label('Open Graph image')
                    ->options(fn (): array => Media::query()
                        ->where('model_type', Project::class)
                        ->where('collection_name', 'photos')
                        ->with('model')
                        ->orderBy('id')
                        ->get()
                        ->mapWithKeys(fn (Media $media): array => [
                            (string) $media->getKey() => ($media->model->title ?? 'Project').' — '.$media->file_name,
                        ])
                        ->all())
                    ->searchable()
                    ->placeholder('None selected')
                    ->columnSpanFull(),
            ]),
        ]);
    }
}
