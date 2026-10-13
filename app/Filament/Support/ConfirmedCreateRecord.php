<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

abstract class ConfirmedCreateRecord extends CreateRecord
{
    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->requiresConfirmation()
            ->modalHeading('Confirmar registro')
            ->modalDescription('¿Deseas guardar este nuevo registro?')
            ->modalSubmitActionLabel('Sí, guardar');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Registro guardado')
            ->body('La información se guardó correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResourceUrl();
    }
}
