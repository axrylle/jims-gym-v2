<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipResource\Pages;
use App\Filament\Resources\MembershipResource\RelationManagers;
use App\Models\Membership;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;

class MembershipResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static ?string $navigationIcon = 'heroicon-s-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Membership Information')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('duration')
                            ->required()
                            ->numeric()
                            ->suffix('days'),
                        Forms\Components\MarkdownEditor::make('description')
                            ->required()
                            // ->maxLength(255)
                            ->columnSpan('full'),
                    ]),
                Section::make('Membership Pricing')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('image_name')
                            ->disk('public')
                            ->directory('images')
                            ->image()
                            ->imageEditor(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('₱'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_name')
                    ->disk('public')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(25),
                Tables\Columns\TextColumn::make('duration')
                    ->formatStateUsing(fn ($state) => $state . ' days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberships::route('/'),
            'create' => Pages\CreateMembership::route('/create'),
            'edit' => Pages\EditMembership::route('/{record}/edit'),
        ];
    }
}
