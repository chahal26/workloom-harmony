<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource\RelationManagers;
use App\Models\LeaveType;
use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Leave Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Employee')
                    ->options(User::all()->pluck('name', 'id'))
                    ->visible(auth()->user()->hasRole('super_admin'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('leave_type_id')
                    ->label('Leave Type')
                    ->options(LeaveType::all()->pluck('name', 'id'))
                    ->required()
                    ->preload()
                    ->searchable()
                    ->reactive(),
                Forms\Components\DatePicker::make('start_date')
                    ->minDate(self::minStartDate())
                    ->required()
                    ->reactive(),
                Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->minDate(fn(Forms\Get $get) => self::minEndDate($get('start_date')))
                    ->visible(fn(Forms\Get $get) => self::multipleDays($get('leave_type_id'))),
                Forms\Components\TextInput::make('start_time')
                    ->required()
                    ->visible(fn(Forms\Get $get) => self::isTimeRequired($get('leave_type_id'))),
                Forms\Components\TextInput::make('end_time')
                    ->required()
                    ->visible(fn(Forms\Get $get) => self::isTimeRequired($get('leave_type_id'))),
                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_time')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }

    public static function isTimeRequired($leave_type_id): bool
    {
        $leave_type = LeaveType::find($leave_type_id);
        return $leave_type && $leave_type->max_holidays > 0 && $leave_type->max_holidays < 1;
    }

    public static function multipleDays($leave_type_id): bool
    {
        $leave_type = LeaveType::find($leave_type_id);
        return $leave_type && $leave_type->max_holidays == 0;
    }

    public static function minStartDate()
    {
        if(!auth()->user()->hasRole('super_admin'))
        {
            return now()->format('Y-m-d');
        }
        return false;
    }

    public static function minEndDate($start_date)
    {
        if(!auth()->user()->hasRole('super_admin'))
        {
            $startDate = $start_date ? Carbon::parse($start_date) : now();
            return $startDate->add(1, 'day')->format('Y-m-d');
        }
        return false;
    }

}
