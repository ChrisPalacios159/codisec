<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

abstract class ConfirmedEditRecord extends EditRecord
{
    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->requiresConfirmation()
            ->modalHeading('Confirmar cambios')
            ->modalDescription('¿Deseas guardar los cambios realizados?')
            ->modalSubmitActionLabel('Sí, guardar');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cambios guardados')
            ->body('La información se actualizó correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResourceUrl();
    }
}
