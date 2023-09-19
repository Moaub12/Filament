<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\ProductsRelationManager;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('client_id')
                    ->label("Client")
                    ->options(Client::all()->pluck('id','id'))
                    ->reactive(),
                TextInput::make('payment_id')
                    ->label("Payment")
                    ->default('1'),
                    // ->hidden(),
                Select::make('productId')
                    ->label('Product')
                    ->options(Product::where('feature','product')->get()->pluck('name','id'))
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set)=>$set('addonsId',null))
                    ->afterStateUpdated(fn(callable $set)=>$set('removesId',null)),
                    
                TextInput::make('quantity')
                    ->numeric()
                    ->required(),
                CheckboxList::make('addonsId')
                    ->label('Addons')
                    ->options(function(callable $get)
                    {
                        $product = Product::find($get('productId'));
                        if(!$product)
                        {
                            return ;
                        }
                        return $product->addons->pluck('name','id');
                    }),
                    
                CheckboxList::make('removesId')
                ->label('Removes')
                ->options(function(callable $get)
                {
                    $product = Product::find($get('productId'));
                    if(!$product)
                    {
                        return ;
                    }
                    return $product->removables->pluck('name','id');
                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('client.user.name'),
                TextColumn::make('status'),
                TextColumn::make('ordered_date'),
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
            ProductsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }   
    public static function create()
    {
        dd("hello");
    }
    
}
