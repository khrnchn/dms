<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Livewire\Component;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class Shortcut extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function goToHome()
    {
        return Action::make('home')
            ->icon('heroicon-o-home')
            ->label('Back to Home')
            ->color('primary')
            ->keyBindings(['command+h', 'ctrl+h'])
            ->extraAttributes(['class' => 'w-full'])
            ->url('/');
    }
    public function render()
    {
        return <<<'HTML'
        <div>
            {{ $this->goToHome }}
        </div>
        HTML;
    }
}
