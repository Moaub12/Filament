<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
       

        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                ->label('User')
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->minLength(8)
                        ->confirmed()
                        ->required(),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->password()
                        ->required(),
                ])
                ->relationship('user', 'name')
                ->options(User::leftJoin('clients', 'users.id', '=', 'clients.user_id')
                ->whereNull('clients.user_id')
                ->select('users.id', 'users.name')
                ->get()->pluck('name', 'id'))
                ->required(),
                Forms\Components\TextInput::make('contact')
                    ->label('Phone number')
                    ->tel()
                    ->required(),
                Fieldset::make()
                    ->relationship('clientDetails')
                    ->schema([
                        TextInput::make('fname')
                            ->label('First Name')
                            ->required(),
                        TextInput::make('lname')
                            ->label('Last Name')
                            ->required(),
                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->required(),
                        TextInput::make('country')
                            ->label('Country')
                            ->required(),
                        TextInput::make('city')
                            ->label('City')
                            ->required(),
                        TextInput::make('state')
                            ->label('State')
                            ->required(),
                        TextInput::make('zip')
                            ->label('Zip')
                            ->required(),
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.name')
                ->label("Username"),
                Tables\Columns\TextColumn::make('clientDetails.fname')
                ->label("First Name"),
                Tables\Columns\TextColumn::make('clientDetails.lname')
                ->label("Last Name"),
                Tables\Columns\TextColumn::make('user.email')
                ->label("Email"),
                Tables\Columns\TextColumn::make('contact')
                ->label("Phone"),
                Tables\Columns\TextColumn::make('adress.name'),
                Tables\Columns\TextColumn::make('clientDetails.country')
                ->label("country"),
                Tables\Columns\TextColumn::make('clientDetails.zip')
                ->label("Zip"),
                Tables\Columns\TextColumn::make('clientDetails.state')
                ->label("State"),
                Tables\Columns\TextColumn::make('clientDetails.company_name')
                ->label("Company"),
              
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
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\AdressRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }    
}
