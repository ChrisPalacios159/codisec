<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ConfirmedSaveFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_create_requires_confirmation_then_notifies_and_returns_to_the_module(): void
    {
        $administrator = User::factory()->create([
            'role' => 'administrator',
            'is_active' => true,
        ]);
        $moduleUrl = UserResource::getUrl('index', panel: 'admin');

        $component = Livewire::actingAs($administrator)
            ->test(CreateUser::class)
            ->assertActionExists(TestAction::make('create')->schemaComponent('form-actions', schema: 'content'), fn (Action $action): bool => $action->isConfirmationRequired()
                && ((string) $action->getModalHeading() === 'Confirmar registro')
                && ($action->getModalSubmitActionLabel() === 'Sí, guardar'))
            ->mountAction(TestAction::make('create')->schemaComponent('form-actions', schema: 'content'))
            ->assertMountedActionModalSee(['Confirmar registro', '¿Deseas guardar este nuevo registro?', 'Sí, guardar'])
            ->unmountAction()
            ->set('previousUrl', $moduleUrl)
            ->fillForm([
                'name' => 'Nuevo editor',
                'email' => 'nuevo.editor@example.test',
                'role' => 'editor',
                'is_active' => true,
                'password' => 'password-seguro',
            ])
            ->call('create');

        $component
            ->assertNotified('Registro guardado')
            ->assertRedirect($moduleUrl);
        $this->assertDatabaseHas(User::class, [
            'name' => 'Nuevo editor',
            'email' => 'nuevo.editor@example.test',
            'role' => 'editor',
            'is_active' => true,
        ]);
    }

    public function test_edit_requires_confirmation_then_notifies_and_returns_to_the_module(): void
    {
        $administrator = User::factory()->create([
            'role' => 'administrator',
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'editor',
            'is_active' => true,
        ]);
        $moduleUrl = UserResource::getUrl('index', panel: 'admin');

        $component = Livewire::actingAs($administrator)
            ->test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertActionExists(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'), fn (Action $action): bool => $action->isConfirmationRequired()
                && ((string) $action->getModalHeading() === 'Confirmar cambios')
                && ($action->getModalSubmitActionLabel() === 'Sí, guardar'))
            ->mountAction(TestAction::make('save')->schemaComponent('form-actions', schema: 'content'))
            ->assertMountedActionModalSee(['Confirmar cambios', '¿Deseas guardar los cambios realizados?', 'Sí, guardar'])
            ->unmountAction()
            ->set('previousUrl', $moduleUrl)
            ->fillForm([
                'name' => 'Editor actualizado',
                'email' => $user->email,
                'role' => 'editor',
                'is_active' => true,
                'password' => null,
            ])
            ->call('save');

        $component
            ->assertNotified('Cambios guardados')
            ->assertRedirect($moduleUrl);
        $this->assertDatabaseHas(User::class, [
            'id' => $user->id,
            'name' => 'Editor actualizado',
        ]);
    }
}
