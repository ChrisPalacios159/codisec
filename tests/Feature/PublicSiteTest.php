<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\CodisecSession;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_sections_render(): void
    {
        $category = DocumentCategory::create(['name' => 'Planes', 'slug' => 'planes']);
        Document::create(['document_category_id' => $category->id, 'title' => 'Plan de prueba', 'slug' => 'plan-prueba', 'status' => 'published', 'external_url' => 'https://example.test/plan.pdf']);
        Activity::create(['title' => 'Actividad de prueba', 'slug' => 'actividad-prueba', 'status' => 'published']);
        CodisecSession::create(['title' => 'Sesión de prueba', 'slug' => 'sesion-prueba', 'status' => 'published']);
        Page::create(['title' => 'CODISEC', 'slug' => 'codisec', 'status' => 'published']);

        $this->get('/')->assertOk()->assertSee('Información clara');
        $this->get('/documentos')->assertOk()->assertSee('Plan de prueba');
        $this->get('/actividades')->assertOk()->assertSee('Actividad de prueba');
        $this->get('/sesiones')->assertOk()->assertSee('Sesión de prueba');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post('/contacto', ['name' => 'Vecino', 'email' => 'vecino@example.test', 'message' => 'Mensaje de consulta'])
            ->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', ['email' => 'vecino@example.test']);
    }

    public function test_maintenance_panel_requires_an_authorized_user(): void
    {
        $this->get('/admin')->assertRedirect();
        $user = User::factory()->create(['role' => 'administrator', 'is_active' => true]);
        $this->actingAs($user)->get('/admin')->assertOk();
        $this->get('/admin/pages/create')->assertOk();
        $this->get('/admin/documents/create')->assertOk();
        $this->get('/admin/activities/create')->assertOk();
        $this->get('/admin/codisec-sessions/create')->assertOk();
    }
}
