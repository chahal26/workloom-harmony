<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveEntitlementResource\Pages;
use App\Filament\Resources\LeaveEntitlementResource\RelationManagers;
use App\Models\LeaveEntitlement;
use App\Models\LeaveEntitlementType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveEntitlementResource extends Resource
{
    protected static ?string $model = LeaveEntitlement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Leave Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('leave_entitlement_type_id')
                    ->label('Leave Entitlement Type')
                    ->options(LeaveEntitlementType::all()->pluck('title', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('entitlement_count')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('leaveEntitlementType.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entitlement_count')
                    ->numeric()
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
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if(!$user->hasRole('super_admin')){
                    return $query->where('user_id', $user->id);
                }
            })
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
            'index' => Pages\ListLeaveEntitlements::route('/'),
            'create' => Pages\CreateLeaveEntitlement::route('/create'),
            'edit' => Pages\EditLeaveEntitlement::route('/{record}/edit'),
        ];
    }
}
