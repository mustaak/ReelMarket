<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section as InfoSection;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('id', '!=', auth()->id()))
            ->columns([
                TextColumn::make('id')->sortable(),

                ImageColumn::make('profile.profile_picture')
                    ->label('Avatar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),

                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                IconColumn::make('status')->boolean()->label('Status'),
                TextColumn::make('roles.name')->badge()->label('Role'),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('status'),
                SelectFilter::make('roles')->relationship('roles', 'name'),
            ])
            ->recordActions([
                Action::make('viewProfile')
                    ->visible(fn () => auth()->user()->can('view_profile'))
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('lg')
                    ->schema([
                        // Cover photo banner
                        ImageEntry::make('profile.cover_photo')
                            ->hiddenLabel()
                            ->disk('public')
                            ->height(160)
                            ->extraImgAttributes(['style' => 'width: 100%; object-fit: cover; border-radius: 12px;'])
                            ->columnSpanFull(),

                        // Avatar + Name + Username row
                        InfoSection::make()
                            ->schema([
                                ImageEntry::make('profile.profile_picture')
                                    ->hiddenLabel()
                                    ->circular()
                                    ->disk('public')
                                    ->extraImgAttributes(['style' => 'width: 90px; height: 90px; border: 3px solid white; margin-top: -60px;'])
                                    ->defaultImageUrl(fn(User $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&size=90'),

                                TextEntry::make('name')
                                ->hiddenLabel()
                                ->weight('bold')
                                ->extraAttributes(['style' => 'font-size: 18px;']),

                                TextEntry::make('email')
                                    ->hiddenLabel()
                                    ->color('gray'),
                            ])
                            ->columns(1),

                        // Stats row — Instagram style
                        InfoSection::make()
                            ->schema([
                                TextEntry::make('posts_count')
                                    ->state(fn(User $record) => $record->posts()->count())
                                    ->label('')
                                    ->formatStateUsing(fn($state) => $state . "\nPosts")
                                    ->html()
                                    ->formatStateUsing(fn($state) => "<div style='text-align:center'><span style='font-size:18px;font-weight:700'>{$state}</span><br><span style='color:#6b7280;font-size:13px'>Posts</span></div>"),

                                TextEntry::make('followers_count')
                                    ->state(fn(User $record) => $record->followers()->count())
                                    ->label('')
                                    ->html()
                                    ->formatStateUsing(fn($state) => "<div style='text-align:center'><span style='font-size:18px;font-weight:700'>{$state}</span><br><span style='color:#6b7280;font-size:13px'>Followers</span></div>"),

                                TextEntry::make('following_count')
                                    ->state(fn(User $record) => $record->following()->count())
                                    ->label('')
                                    ->html()
                                    ->formatStateUsing(fn($state) => "<div style='text-align:center'><span style='font-size:18px;font-weight:700'>{$state}</span><br><span style='color:#6b7280;font-size:13px'>Following</span></div>"),

                                TextEntry::make('reels_count')
                                    ->state(fn(User $record) => $record->reels()->count())
                                    ->label('')
                                    ->html()
                                    ->formatStateUsing(fn($state) => "<div style='text-align:center'><span style='font-size:18px;font-weight:700'>{$state}</span><br><span style='color:#6b7280;font-size:13px'>Reels</span></div>"),
                            ])
                            ->columns(4),

                        // Bio
                        TextEntry::make('profile.bio')
                            ->hiddenLabel()
                            ->default('No bio available.')
                            ->columnSpanFull(),

                        // Public/Private badge
                        IconEntry::make('profile.is_public')
                            ->label('Public account')
                            ->boolean(),
                    ]),

                ActionGroup::make([
                    Action::make('editProfile')
                        ->visible(fn () => auth()->user()->can('edit_profile'))
                        ->label('Edit profile')
                        ->icon('heroicon-o-user-circle')
                        ->color('warning')
                        ->modalHeading(fn(User $record) => "Edit profile: {$record->name}")
                        ->fillForm(fn(User $record): array => [
                            'bio' => $record->profile?->bio,
                            'profile_picture' => $record->profile?->profile_picture,
                            'cover_photo' => $record->profile?->cover_photo,
                            'is_public' => $record->profile?->is_public ?? true,
                        ])
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    FileUpload::make('profile_picture')
                                        ->label('Profile picture')
                                        ->image()
                                        ->avatar()
                                        ->disk('public')
                                        ->directory('profiles/avatars')
                                        ->maxSize(2048),

                                    FileUpload::make('cover_photo')
                                        ->label('Cover photo')
                                        ->image()
                                        ->disk('public')
                                        ->directory('profiles/covers')
                                        ->maxSize(4096),
                                ]),

                            Textarea::make('bio')
                                ->label('Bio')
                                ->rows(3)
                                ->maxLength(255)
                                ->placeholder('Write user bio...')
                                ->columnSpanFull(),

                            Toggle::make('is_public')
                                ->label('Public profile')
                                ->default(true),
                        ])
                        ->action(function (User $record, array $data): void {
                            $record->profile()->updateOrCreate(
                                ['user_id' => $record->id],
                                [
                                    'bio' => $data['bio'],
                                    'profile_picture' => $data['profile_picture'],
                                    'cover_photo' => $data['cover_photo'],
                                    'is_public' => $data['is_public'],
                                ]
                            );

                            Notification::make()
                                ->title('Profile updated')
                                ->success()
                                ->send();
                        }),

                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('toggleStatus')
                        ->visible(fn () => auth()->user()->can('deactivate_profile'))
                        ->label(fn(User $record) => $record->status ? 'Deactivate' : 'Activate')
                        ->icon(fn(User $record) => $record->status ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn(User $record) => $record->status ? 'danger' : 'success')
                        ->action(fn(User $record) => $record->update(['status' => ! $record->status]))
                        ->requiresConfirmation(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
