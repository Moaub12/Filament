<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;


use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->maxValue(42949672.95),

                Forms\Components\TextInput::make('quantity')
                    ->numeric(),
                Forms\Components\Select::make('feature')
                    ->options([
                        'product' => 'product',
                        'ingredient' => 'ingredient',

                    ])
                     ->disabledOn('edit')
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    
                    ->searchable()
                    ->preload()
                    
                    ->label('Category')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('Icon')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend('Category_'),
                            )
                            ->disk('public')->directory('images')->required(),




                    ])->required(),
                    
                    Forms\Components\Select::make('tag_id')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(function(callable $get){
                        $feature=$get('feature');
                        if($feature=="product")return true;
                        else return false;
                    })
                    ->label('Tags')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                    ->prepend('Tag_'),
                            )
                            ->disk('public')->directory('images'),




                    ]),

                Forms\Components\Select::make('addons')
                    ->relationship('addons', 'name')
                    ->options(Product::where('feature', 'ingredient')->get()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->visible(function(callable $get){
                        $feature=$get('feature');
                        if($feature=="product")return true;
                        else return false;
                    })
                    ->label('addons'),
                    Forms\Components\Select::make('ingrdients')
                    ->relationship('ingredients', 'name')
                    ->options(Product::where('feature', 'ingredient')->get()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->visible(function(callable $get){
                        $feature=$get('feature');
                        if($feature=="product")return true;
                        else return false;
                    })
                    ,
                Forms\Components\FileUpload::make('image')
                    ->getUploadedFileNameForStorageUsing(
                        fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                            ->prepend('Product_'),
                    )
                    ->disk('public')->directory('images')->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('feature'),
                Tables\Columns\TextColumn::make('price')->sortable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('category.Name'),
                Tables\Columns\TextColumn::make('tags.name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options([
                        'product' => 'product',
                        'ingredient' => 'pngredient',
                    ]),
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
            RelationManagers\IngredientsRelationManager::class,
        ];

    }
    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
