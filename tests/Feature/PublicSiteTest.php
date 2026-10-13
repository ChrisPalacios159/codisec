<?php

namespace Tests\Feature;

use App\Filament\Resources\BoletinesEstadisticos\BoletinesEstadisticosResource;
use App\Filament\Resources\EvaluacionDesempeno\EvaluacionDesempenoResource;
use App\Filament\Resources\InformesCumplimiento\InformesCumplimientoResource;
use App\Filament\Resources\InformesSeguimiento\InformesSeguimientoResource;
use App\Filament\Resources\NormasLegales\NormasLegalesResource;
use App\Filament\Resources\PlanLocalSeguridad\PlanLocalSeguridadResource;
use App\Models\Activity;
use App\Models\CodisecSession;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\NavigationItem;
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

        $this->get('/')
            ->assertOk()
            ->assertSee('Un Surquillo más seguro')
            ->assertSee('Boletines Estadísticos')
            ->assertSee('Informes Trimestrales de Seguimiento')
            ->assertSee('Informes Trimestrales de Cumplimiento');
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

    public function test_public_navigation_does_not_repeat_imported_links(): void
    {
        NavigationItem::create(['label' => 'Directorio', 'url' => '/seccion/directorio', 'sort_order' => 1, 'is_active' => true]);
        NavigationItem::create(['label' => 'Directorio duplicado', 'url' => '/seccion/directorio', 'sort_order' => 2, 'is_active' => true]);
        NavigationItem::create(['label' => 'Inicio importado', 'url' => '/', 'sort_order' => 3, 'is_active' => true]);

        $html = $this->get('/')->assertOk()->getContent();
        preg_match('/<nav[^>]+aria-label="Navegación principal"[\s\S]*?<\/nav>/', $html, $navigation);

        $this->assertNotEmpty($navigation);
        $this->assertSame(1, substr_count($navigation[0], 'href="/seccion/directorio"'));
        $this->assertSame(1, substr_count($navigation[0], '>Inicio '));
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

    public function test_maintenance_dashboard_guides_staff_to_public_content_sections(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'is_active' => true]);
        Page::create(['title' => 'Directorio', 'slug' => 'directorio', 'status' => 'published']);

        $response = $this->actingAs($user)->get('/admin')
            ->assertOk()
            ->assertSee('Una sección, un módulo de contenido')
            ->assertSee('CODISEC')
            ->assertSee('Normas Legales')
            ->assertSee('Directorio')
            ->assertSee('Sesiones')
            ->assertSee('Evaluación de Desempeño')
            ->assertSee('Actividades y Buenas Prácticas')
            ->assertSee('Plan Local de Seguridad Ciudadana')
            ->assertSee('Boletines Estadísticos')
            ->assertSee('Informes Trimestrales de Seguimiento')
            ->assertSee('Informes Trimestrales de Cumplimiento');

        $this->assertSame(10, substr_count($response->getContent(), 'data-content-module='));
    }

    public function test_maintenance_tables_show_relevant_headings_in_spanish(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'is_active' => true]);
        $tableHeadings = [
            '/admin/pages' => 'Tipo de página',
            '/admin/documents' => 'Sección',
            '/admin/document-categories' => 'Nombre de la sección',
            '/admin/activities' => 'Fecha de la actividad',
            '/admin/codisec-sessions' => 'Fecha de la sesión',
            '/admin/contact-messages' => 'Correo electrónico',
            '/admin/media-assets' => 'Nombre del archivo',
            '/admin/navigation-items' => 'Texto del menú',
            '/admin/site-settings' => 'Dato del sitio',
            '/admin/users' => 'Puede ingresar',
        ];

        foreach ($tableHeadings as $url => $heading) {
            $this->actingAs($user)->get($url)->assertSee($heading);
        }
    }

    public function test_document_sections_have_independent_admin_tables(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'is_active' => true]);
        $sections = [
            [NormasLegalesResource::class, 'Normas Legales', 'normas-legales', 'Ordenanza exclusiva'],
            [EvaluacionDesempenoResource::class, 'Evaluación de Desempeño', 'evaluacion-de-desempeno', 'Evaluación exclusiva'],
            [PlanLocalSeguridadResource::class, 'Plan Local de Seguridad Ciudadana', 'plan-local-de-seguridad-ciudadana', 'Plan exclusivo'],
            [BoletinesEstadisticosResource::class, 'Boletines Estadísticos', 'boletines-estadisticos', 'Boletín exclusivo'],
            [InformesSeguimientoResource::class, 'Informes Trimestrales de Seguimiento', 'informes-trimestrales-de-seguimiento', 'Seguimiento exclusivo'],
            [InformesCumplimientoResource::class, 'Informes Trimestrales de Cumplimiento', 'informes-trimestrales-de-cumplimiento', 'Cumplimiento exclusivo'],
        ];

        foreach ($sections as [, , $slug, $documentTitle]) {
            $category = DocumentCategory::create(['name' => $documentTitle, 'slug' => $slug]);
            Document::create([
                'document_category_id' => $category->id,
                'title' => $documentTitle,
                'slug' => $slug.'-prueba',
                'status' => 'published',
                'external_url' => 'https://example.test/'.$slug.'.pdf',
            ]);
        }

        foreach ($sections as [$resource, $title, , $documentTitle]) {
            $response = $this->actingAs($user)
                ->get($resource::getUrl(panel: 'admin'))
                ->assertOk()
                ->assertSee($title)
                ->assertSee($documentTitle)
                ->assertSee('Agregar documento');

            foreach ($sections as [, , , $otherDocumentTitle]) {
                if ($otherDocumentTitle !== $documentTitle) {
                    $response->assertDontSee($otherDocumentTitle);
                }
            }
        }
    }

    public function test_admin_renders_the_loading_indicator(): void
    {
        $user = User::factory()->create(['role' => 'administrator', 'is_active' => true]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Actualizando contenido');
    }
}
