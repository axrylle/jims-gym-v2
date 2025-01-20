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
use Filament\Forms\Components\Section;
use App\Models\Coupon;
use Filament\Notifications\Notification;

class MembersResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Section::make('Member Information')
                    ->columnSpan(2)
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('last_name')
                            ->autofocus()
                            ->required(),
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('middle_initial')
                            ->required(),
                        Forms\Components\TextInput::make('contact_number')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->rows(3)
                            ->columnSpan(3),
                    ]),
                Section::make('Membership Information')
                    ->visible(fn ($livewire) => $livewire instanceof Pages\CreateMembers)
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Select::make('membership_id')
                            ->relationship('memberships', 'name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $membership = Membership::find($state);
                    
                                    if ($membership) {
                                        $expiryDate = now()->addDays($membership->duration)->toDateString();
                                        $set('expiry', $expiryDate);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('coupon_code')
                            ->nullable()
                            ->afterStateUpdated(function (callable $set, $state) {
                                // If the coupon code is not empty
                                if ($state) {
                                    $coupon = Coupon::where('code', $state)->first();
                        
                                    // If coupon doesn't exist
                                    if (!$coupon) {
                                        Notification::make()
                                            ->title('Coupon does not exist')
                                            ->danger()
                                            ->send();
                                        // Clear the coupon_code state to avoid saving a non-existent coupon
                                        $set('coupon_code', null);
                                        return;
                                    }
                        
                                    // If coupon has already been assigned to a member
                                    if ($coupon->member_id !== null) {
                                        Notification::make()
                                            ->title('Coupon already used')
                                            ->danger()
                                            ->send();
                                        // Clear the coupon_code state
                                        $set('coupon_code', null);
                                        return;
                                    }
                        
                                    // If coupon is expired
                                    if ($coupon->status == 'expired') {
                                        Notification::make()
                                            ->title('Coupon expired')
                                            ->danger()
                                            ->send();
                                        // Clear the coupon_code state
                                        $set('coupon_code', null);
                                        return;
                                    }
                        
                                    // If coupon is valid
                                    Notification::make()
                                        ->title('Coupon valid')
                                        ->success()
                                        ->send();
                                }
                            }),
                        Forms\Components\DatePicker::make('expiry')
                            ->readOnly(), // Display only
                    ]),
                    
                    
            ]);
    }
    


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name') // Display the full name
                    ->label('Name')
                    ->limit(20)
                    ->searchable(),
                Tables\Columns\TextColumn::make('memberships.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->limit(15)
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->limit(15),
                IconColumn::make('status')
                    ->sortable()
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
                Tables\Actions\Action::make('renew')
                ->label('Renew')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\Select::make('membership_id')
                        ->relationship('memberships', 'name')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $membership = Membership::find($state);
            
                                if ($membership) {
                                    $expiryDate = now()->addDays($membership->duration)->toDateString();
                                    $set('expiry', $expiryDate);
                                }
                            }
                        }),
                    Forms\Components\TextInput::make('coupon_code')
                        ->nullable()
                        ->label('Coupon Code')
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $coupon = Coupon::where('code', $state)->first();
            
                                if (!$coupon) {
                                    Notification::make()
                                        ->title('Coupon does not exist')
                                        ->danger()
                                        ->send();
                                    $set('coupon_code', null);
                                    return;
                                }
            
                                if ($coupon->member_id !== null) {
                                    Notification::make()
                                        ->title('Coupon already used')
                                        ->danger()
                                        ->send();
                                    $set('coupon_code', null);
                                    return;
                                }
            
                                if ($coupon->status == 'expired') {
                                    Notification::make()
                                        ->title('Coupon expired')
                                        ->danger()
                                        ->send();
                                    $set('coupon_code', null);
                                    return;
                                }
            
                                Notification::make()
                                    ->title('Coupon valid')
                                    ->success()
                                    ->send();
                            }
                        }),
                    Forms\Components\DatePicker::make('expiry')
                        ->label('Expiry Date')
                        ->disabled(),
                ])
                ->action(function (Member $record, array $data) {
                    $membership = $record->memberships()->find($data['membership_id']);
            
                    if ($membership) {
                        $expiryDate = now()->addDays($membership->duration)->toDateString();
            
                        // Update the expiry in the pivot table
                        // $record->memberships()->updateExistingPivot($membership->id, ['expiry' => $expiryDate]);
            
                        // If a coupon code is provided, mark it as used
                        if (!empty($data['coupon_code'])) {
                            $coupon = Coupon::where('code', $data['coupon_code'])->first();
                            if ($coupon) {
                                $coupon->update(['member_id' => $record->id]);
                            }
                        }
            
                        Notification::make()
                            ->title('Membership renewed successfully!')
                            ->success()
                            ->send();
                    } else {
                        throw new \Exception('Membership not found for renewal.');
                    }
                })
                ->visible(fn (Member $record) => $record->status == 0)
            
            ])
            ->paginated(false)
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