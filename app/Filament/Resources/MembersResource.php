<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembersResource\Pages;
use App\Filament\Resources\MembersResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\IconColumn;
use App\Models\Membership;


class MembersResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('membership_id')
                ->relationship('memberships', 'name')
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state, $record) {
                    if ($state) {
                        $membership = Membership::find($state);
            
                        if ($membership) {
                            // Calculate expiry date based on the membership's duration
                            $expiryDate = now()->addDays($membership->duration)->toDateString();
                            $set('expiry', $expiryDate);

                            if ($record) {
                                $record->expiryRecord()->updateOrCreate(
                                    ['member_id' => $record->id],
                                    ['expiry' => $expiryDate]
                                );
                            }
                        }
                    }
                }),
                Forms\Components\TextInput::make('last_name')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('first_name')
                    ->maxLength(255)
                    ->required(),
                Forms\Components\TextInput::make('middle_initial')
                    ->maxLength(1),
                Forms\Components\TextInput::make('contact_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expiry')
                    ->disabled(), // Ensure this is read-only as it is auto-calculated
            ]);
    }
    


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name') // Display the full name
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('memberships.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->limit(15),
                IconColumn::make('status')
                    ->boolean(),
                    Tables\Columns\TextColumn::make('expiryRecord.expiry')
                    ->label('Expiry Date')
                    ->formatStateUsing(fn ($record) => view('components.expiry-with-days', [
                        'expiry' => $record->expiryRecord->expiry ?? 'N/A',
                        'daysRemaining' => $record->days_remaining !== 'Expired' ? $record->days_remaining : 'Expired',
                    ])->render())
                    ->html() // Allow HTML rendering for custom formatting.
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->column('status'), // This ensures Filament uses the 'status' column directly for filtering
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('renew') // Define a custom action
                    ->requiresConfirmation()
                    ->label('Renew')
                    ->action(function (Member $record) {
                        $membership = $record->membership()->first(); // Fetch the related membership
                        
                        if ($membership) {
                            $expiryDate = now()->addDays($membership->duration)->toDateString();
            
                            // Update the expiry in the pivot table
                            $record->membership()->updateExistingPivot($membership->id, ['expiry' => $expiryDate]);
            
                            $record->save(); // Save the record if any additional changes are needed
                        } else {
                            throw new \Exception('Membership not found for renewal.');
                        }
                    })
                    ->color('success'), // Optional: Add a color for the button
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMembers::route('/create'),
            'edit' => Pages\EditMembers::route('/{record}/edit'),
        ];
    }
}