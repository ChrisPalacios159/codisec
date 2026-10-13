import { execFileSync } from 'node:child_process';
import { join } from 'node:path';
import { expect, test } from '@playwright/test';

const token = `e2e-${Date.now()}`;
const pageSlug = `${token}-pagina`;
const categorySlug = `${token}-categoria`;
const documentSlug = `${token}-documento`;
const activitySlug = `${token}-actividad`;
const sessionSlug = `${token}-sesion`;
const contactEmail = `${token}@example.test`;
const navigationLabel = `Enlace ${token}`;
const mediaTitle = `Medio ${token}`;
const editorEmail = `${token}-editor@example.test`;
const editorPassword = `Editor!${Date.now()}`;
const updatedSiteName = `CODISEC validado ${token}`;
const documentModules = [
    ['normas-legales', 'Normas Legales'],
    ['evaluacion-de-desempeno', 'Evaluación de Desempeño'],
    ['plan-local-de-seguridad-ciudadana', 'Plan Local de Seguridad Ciudadana'],
    ['boletines-estadisticos', 'Boletines Estadísticos'],
    ['informes-trimestrales-de-seguimiento', 'Informes Trimestrales de Seguimiento'],
    ['informes-trimestrales-de-cumplimiento', 'Informes Trimestrales de Cumplimiento'],
];
let originalSiteNameBase64 = '';

const pdfBuffer = Buffer.from(`%PDF-1.4
1 0 obj
<< /Type /Catalog /Pages 2 0 R >>
endobj
2 0 obj
<< /Type /Pages /Kids [3 0 R] /Count 1 >>
endobj
3 0 obj
<< /Type /Page /Parent 2 0 R /MediaBox [0 0 200 200] /Contents 4 0 R >>
endobj
4 0 obj
<< /Length 44 >>
stream
BT /F1 12 Tf 20 100 Td (CODISEC E2E) Tj ET
endstream
endobj
xref
0 5
0000000000 65535 f 
trailer
<< /Root 1 0 R /Size 5 >>
startxref
300
%%EOF`);
const activityImagePath = join(process.cwd(), 'storage', 'app', 'public', 'legacy', '2016', '07', 'favicon1.png');

function artisan(code) {
    const php = process.env.E2E_PHP_BINARY || 'php';

    return execFileSync(php, ['artisan', 'tinker', `--execute=${code}`], {
        cwd: process.cwd(),
        encoding: 'utf8',
        stdio: ['ignore', 'pipe', 'pipe'],
    }).trim();
}

async function login(page) {
    expect(process.env.E2E_EMAIL, 'Falta E2E_EMAIL').toBeTruthy();
    expect(process.env.E2E_PASSWORD, 'Falta E2E_PASSWORD').toBeTruthy();
    await page.goto('/admin/login');
    await page.getByLabel('Correo electrónico').fill(process.env.E2E_EMAIL);
    await page.locator('input[type="password"]').fill(process.env.E2E_PASSWORD);
    await page.getByRole('button', { name: 'Entrar' }).click();
    await expect(page).toHaveURL(/\/admin$/, { timeout: 60_000 });
}

async function createRecord(page, resource) {
    await page.getByRole('button', { name: 'Crear', exact: true }).click();
    await page.waitForURL((url) => url.pathname.startsWith(`/admin/${resource}/`) && !url.pathname.endsWith('/create'));

    await page.waitForLoadState('networkidle');

    return page.url().endsWith('/edit') ? page.url() : `${page.url()}/edit`;
}

async function saveRecord(page) {
    await page.getByRole('button', { name: /Guardar cambios|Save changes/i }).click();
    await expect(page.getByText(/Guardado|Saved/i).first()).toBeVisible();
}

async function setSwitch(page, name, checked) {
    const control = page.getByRole('switch', { name: new RegExp(name, 'i') });
    await expect(control).toBeVisible();
    if ((await control.getAttribute('aria-checked')) !== String(checked)) {
        await control.click();
    }
}

async function upload(page, fieldName, file) {
    const field = page.getByRole('group', { name: fieldName });

    await field.locator('input[type="file"]').setInputFiles(file);
    await expect(field.locator('.filepond--item').first()).toHaveAttribute('data-filepond-item-state', 'processing-complete', { timeout: 30_000 });
}

function cleanTestRecords() {
    const restoreSetting = originalSiteNameBase64
        ? `App\\Models\\SiteSetting::where('key', 'site_name')->update(['value' => base64_decode('${originalSiteNameBase64}')]);`
        : '';
    artisan(`
        ${restoreSetting}
        App\\Models\\Document::where('slug', 'like', '${token}-%')->get()->each(function ($document) {
            if ($document->file_path) { Illuminate\\Support\\Facades\\Storage::disk('public')->delete($document->file_path); }
            $document->delete();
        });
        $activity = App\\Models\\Activity::where('slug', '${activitySlug}')->first();
        if ($activity) { if ($activity->cover_image) { Illuminate\\Support\\Facades\\Storage::disk('public')->delete($activity->cover_image); } $activity->delete(); }
        $session = App\\Models\\CodisecSession::where('slug', '${sessionSlug}')->first();
        if ($session) { if ($session->document_path) { Illuminate\\Support\\Facades\\Storage::disk('public')->delete($session->document_path); } $session->delete(); }
        App\\Models\\NavigationItem::where('label', '${navigationLabel}')->delete();
        App\\Models\\MediaAsset::where('title', '${mediaTitle}')->delete();
        App\\Models\\ContactMessage::where('email', '${contactEmail}')->delete();
        App\\Models\\User::where('email', '${editorEmail}')->delete();
        App\\Models\\DocumentCategory::where('slug', '${categorySlug}')->delete();
        App\\Models\\Page::where('slug', '${pageSlug}')->delete();
    `);
}

test.afterAll(() => {
    cleanTestRecords();
});

test('sitio público responde y el menú móvil funciona', async ({ page }) => {
    for (const path of ['/', '/seccion/directorio', '/documentos', '/actividades', '/sesiones', '/contacto']) {
        const response = await page.goto(path, { waitUntil: 'domcontentloaded' });
        expect(response?.ok(), `${path} debe responder correctamente`).toBeTruthy();
        await expect(page.locator('body')).toBeVisible();
    }

    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await page.getByRole('button', { name: 'Abrir menú' }).click();
    await expect(page.getByRole('navigation', { name: 'Navegación móvil' })).toBeVisible();
    await expect(page.getByRole('navigation', { name: 'Navegación móvil' }).getByText('Directorio')).toBeVisible();
});

test('panel muestra diez módulos y cada sección documental guarda en su categoría', async ({ page }) => {
    test.setTimeout(300_000);
    await login(page);

    const modules = page.locator('[data-content-module]');
    await expect(modules).toHaveCount(10);

    const expectedModules = [
        ['codisec', 'CODISEC'],
        ...documentModules.slice(0, 1),
        ['directorio', 'Directorio'],
        ['sesiones', 'Sesiones'],
        ...documentModules.slice(1, 2),
        ['actividades', 'Actividades y Buenas Prácticas'],
        ...documentModules.slice(2),
    ];

    for (const [slug, title] of expectedModules) {
        await expect(page.locator(`[data-content-module="${slug}"]`)).toContainText(title);
    }

    for (const pageModuleSlug of ['codisec', 'directorio']) {
        const pageRecord = JSON.parse(artisan(`echo json_encode(App\\Models\\Page::where('slug', '${pageModuleSlug}')->firstOrFail(['id', 'excerpt'])->toArray());`));
        const originalExcerpt = Buffer.from(pageRecord.excerpt || '', 'utf8').toString('base64');
        const testExcerpt = `Edición validada ${token} ${pageModuleSlug}`;

        try {
            const editUrl = await page.locator(`[data-content-module="${pageModuleSlug}"]`).getByRole('link', { name: 'Administrar' }).getAttribute('href');
            await page.goto(editUrl);
            await page.locator('[id="form.excerpt"]').fill(testExcerpt);
            await saveRecord(page);
            expect(artisan(`echo App\\Models\\Page::findOrFail(${pageRecord.id})->excerpt;`)).toBe(testExcerpt);
        } finally {
            artisan(`App\\Models\\Page::findOrFail(${pageRecord.id})->update(['excerpt' => base64_decode('${originalExcerpt}')]);`);
        }

        await page.goto('/admin');
    }

    for (const [categorySlug, categoryName] of documentModules) {
        const card = page.locator(`[data-content-module="${categorySlug}"]`);
        const createUrl = await card.getByRole('link', { name: 'Agregar contenido' }).getAttribute('href');
        expect(createUrl).toBeTruthy();
        await page.goto(createUrl);

        const sectionField = page.getByLabel('Sección del sitio');
        await expect(sectionField).toBeDisabled();
        await expect(sectionField).toContainText(categoryName);

        const moduleDocumentSlug = `${token}-${categorySlug}`;
        await page.locator('[id="form.title"]').fill(`Prueba ${categoryName} ${token}`);
        await page.locator('[id="form.slug"]').fill(moduleDocumentSlug);
        await page.locator('[id="form.status"]').selectOption('published');
        await page.locator('[id="form.external_url"]').fill(`https://example.test/${moduleDocumentSlug}.pdf`);
        await createRecord(page, 'documents');

        const storedCategory = artisan(`echo App\\Models\\Document::where('slug', '${moduleDocumentSlug}')->firstOrFail()->category->slug;`);
        expect(storedCategory).toBe(categorySlug);

        await page.goto('/admin');
    }
});

test('mantenimiento guarda, actualiza y publica contenido con archivos', async ({ page, request, browser }) => {
    test.setTimeout(300_000);
    await login(page);
    const publicPage = await page.context().newPage();

    await test.step('Página: crear, reflejar y editar', async () => {
        await page.goto('/admin/pages/create');
        await page.locator('[id="form.title"]').fill(`Página ${token}`);
        await page.locator('[id="form.slug"]').fill(pageSlug);
        await page.locator('[id="form.excerpt"]').fill(`Resumen ${token}`);
        await page.getByRole('textbox', { name: 'Contenido' }).fill(`Contenido público inicial ${token}`);
        await page.locator('[id="form.status"]').selectOption('published');
        await page.locator('[id="form.template"]').selectOption('default');
        const editUrl = await createRecord(page, 'pages');

        await publicPage.goto(`/seccion/${pageSlug}`);
        await expect(publicPage.getByRole('heading', { name: `Página ${token}`, exact: true })).toBeVisible();
        await expect(publicPage.getByText(`Contenido público inicial ${token}`)).toBeVisible();

        await page.goto(editUrl);
        await page.locator('[id="form.title"]').fill(`Página actualizada ${token}`);
        await page.getByRole('textbox', { name: 'Contenido' }).fill(`Contenido público actualizado ${token}`);
        await saveRecord(page);
        await publicPage.goto(`/seccion/${pageSlug}`);
        await expect(publicPage.getByRole('heading', { name: `Página actualizada ${token}`, exact: true })).toBeVisible();
        await expect(publicPage.getByText(`Contenido público actualizado ${token}`)).toBeVisible();
    });

    await test.step('Categoría: crear y mostrar en portada', async () => {
        await page.goto('/admin/document-categories/create');
        await page.locator('[id="form.name"]').fill(`Categoría ${token}`);
        await page.locator('[id="form.slug"]').fill(categorySlug);
        await page.locator('[id="form.description"]').fill(`Categoría de validación ${token}`);
        await page.locator('[id="form.sort_order"]').fill('0');
        await setSwitch(page, 'Sección activa', true);
        await createRecord(page, 'document-categories');
    });

    await test.step('Documento PDF: cargar, publicar y descargar', async () => {
        await page.goto('/admin/documents/create');
        await page.locator('[id="form.title"]').fill(`Documento ${token}`);
        await page.locator('[id="form.slug"]').fill(documentSlug);
        await page.getByLabel('Sección del sitio').click();
        await page.getByRole('option', { name: `Categoría ${token}` }).click();
        await page.locator('[id="form.status"]').selectOption('published');
        await page.getByRole('textbox', { name: 'Descripción' }).fill(`Descripción pública ${token}`);
        await upload(page, 'Archivo local', { name: `${token}.pdf`, mimeType: 'application/pdf', buffer: pdfBuffer });
        await createRecord(page, 'documents');

        await publicPage.goto(`/documentos/${documentSlug}`);
        await expect(publicPage.getByRole('heading', { name: `Documento ${token}` })).toBeVisible();
        await expect(publicPage.getByRole('article').getByText(`Categoría ${token}`, { exact: true })).toBeVisible();
        await expect(publicPage.getByText(`Descripción pública ${token}`)).toBeVisible();
        const downloadUrl = await publicPage.getByRole('link', { name: /Abrir o descargar documento/ }).getAttribute('href');
        const response = await request.get(downloadUrl);
        expect(response.ok()).toBeTruthy();
        expect(response.headers()['content-type']).toContain('application/pdf');
    });

    await test.step('Actividad con imagen: cargar y publicar', async () => {
        await page.goto('/admin/activities/create');
        await page.locator('[id="form.title"]').fill(`Actividad ${token}`);
        await page.locator('[id="form.slug"]').fill(activitySlug);
        await page.locator('[id="form.occurred_at"]').fill('2026-09-12');
        await page.locator('[id="form.status"]').selectOption('published');
        await page.locator('[id="form.summary"]').fill(`Resumen de actividad ${token}`);
        await page.getByRole('textbox', { name: 'Contenido' }).fill(`Contenido de actividad ${token}`);
        await upload(page, 'Imagen principal', activityImagePath);
        await createRecord(page, 'activities');

        await publicPage.goto(`/actividades/${activitySlug}`);
        await expect(publicPage.getByRole('heading', { name: `Actividad ${token}` })).toBeVisible();
        await expect(publicPage.getByText(`Contenido de actividad ${token}`)).toBeVisible();
        const imageUrl = await publicPage.locator('article img').getAttribute('src');
        expect((await request.get(imageUrl)).ok()).toBeTruthy();
    });

    await test.step('Sesión con acta PDF: cargar y publicar', async () => {
        await page.goto('/admin/codisec-sessions/create');
        await page.locator('[id="form.title"]').fill(`Sesión ${token}`);
        await page.locator('[id="form.slug"]').fill(sessionSlug);
        await page.locator('[id="form.session_date"]').fill('2026-09-12');
        await page.locator('[id="form.status"]').selectOption('published');
        await page.locator('[id="form.description"]').fill(`Descripción de sesión ${token}`);
        await upload(page, 'Acta o documento', { name: `${token}-acta.pdf`, mimeType: 'application/pdf', buffer: pdfBuffer });
        await createRecord(page, 'codisec-sessions');

        await publicPage.goto('/sesiones');
        const sessionCard = publicPage.locator('article').filter({ hasText: `Sesión ${token}` });
        await expect(sessionCard).toBeVisible();
        const minutesUrl = await sessionCard.getByRole('link', { name: /Ver acta/ }).getAttribute('href');
        const response = await request.get(minutesUrl);
        expect(response.ok()).toBeTruthy();
        expect(response.headers()['content-type']).toContain('application/pdf');
    });

    await test.step('Navegación: crear enlace y reflejarlo públicamente', async () => {
        await page.goto('/admin/navigation-items/create');
        await page.locator('[id="form.label"]').fill(navigationLabel);
        await page.locator('[id="form.url"]').fill(`/seccion/${pageSlug}`);
        await page.locator('[id="form.target"]').fill('_self');
        await page.locator('[id="form.sort_order"]').fill('999');
        await setSwitch(page, 'Enlace activo', true);
        await createRecord(page, 'navigation-items');
        await publicPage.goto('/');
        await expect(publicPage.getByText(navigationLabel, { exact: true }).first()).toBeVisible();
    });

    await test.step('Biblioteca de medios: guardar metadatos y resolver el archivo público', async () => {
        await page.goto('/admin/media-assets/create');
        await page.locator('[id="form.title"]').fill(mediaTitle);
        await page.locator('[id="form.alt_text"]').fill(`Texto alternativo ${token}`);
        await page.locator('[id="form.file_path"]').fill('legacy/2016/07/favicon1.png');
        await page.locator('[id="form.mime_type"]').fill('image/png');
        await page.locator('[id="form.file_size"]').fill('1536');
        await createRecord(page, 'media-assets');

        const storedAsset = JSON.parse(artisan(`echo json_encode(App\\Models\\MediaAsset::where('title', '${mediaTitle}')->firstOrFail(['alt_text', 'file_path', 'mime_type'])->toArray());`));
        expect(storedAsset).toEqual({
            alt_text: `Texto alternativo ${token}`,
            file_path: 'legacy/2016/07/favicon1.png',
            mime_type: 'image/png',
        });
        expect((await request.get('/storage/legacy/2016/07/favicon1.png')).ok()).toBeTruthy();
    });

    await test.step('Configuración: editar nombre y reflejarlo en el pie público', async () => {
        const setting = JSON.parse(artisan("echo json_encode(App\\Models\\SiteSetting::where('key', 'site_name')->firstOrFail(['id', 'value'])->toArray());"));
        originalSiteNameBase64 = Buffer.from(setting.value || '', 'utf8').toString('base64');
        await page.goto(`/admin/site-settings/${setting.id}/edit`);
        await page.locator('[id="form.value"]').fill(updatedSiteName);
        await saveRecord(page);
        await publicPage.goto('/');
        await expect(publicPage.locator('footer').getByText(updatedSiteName)).toBeVisible();

        await page.goto(`/admin/site-settings/${setting.id}/edit`);
        await page.locator('[id="form.value"]').fill(setting.value || 'CODISEC Surquillo');
        await saveRecord(page);
    });

    await test.step('Contacto: enviar desde la web y recibir en mantenimiento', async () => {
        await publicPage.goto('/contacto');
        await publicPage.getByLabel('Nombre').fill(`Vecino ${token}`);
        await publicPage.getByLabel('Correo').fill(contactEmail);
        await publicPage.getByLabel('Teléfono').fill('999888777');
        await publicPage.getByLabel('Asunto').fill(`Consulta ${token}`);
        await publicPage.getByLabel('Mensaje').fill(`Mensaje público ${token}`);
        await publicPage.getByRole('button', { name: 'Enviar mensaje' }).click();
        await expect(publicPage.getByText(/recibido|gracias|enviado/i)).toBeVisible();

        const messageId = artisan(`echo App\\Models\\ContactMessage::where('email', '${contactEmail}')->value('id');`);
        expect(messageId).toBeTruthy();
        await page.goto(`/admin/contact-messages/${messageId}`);
        await expect(page.getByText(contactEmail, { exact: true })).toBeVisible();
        await expect(page.getByText(`Mensaje público ${token}`, { exact: true })).toBeVisible();
    });

    await test.step('Usuarios: crear editor y comprobar su acceso al mantenimiento', async () => {
        await page.goto('/admin/users/create');
        await page.locator('[id="form.name"]').fill(`Editor ${token}`);
        await page.locator('[id="form.email"]').fill(editorEmail);
        await page.locator('[id="form.role"]').selectOption('editor');
        await setSwitch(page, 'Usuario activo', true);
        await page.locator('[id="form.password"]').fill(editorPassword);
        await createRecord(page, 'users');

        const editorContext = await browser.newContext({ baseURL: process.env.E2E_BASE_URL || 'http://127.0.0.1:8000' });
        const editorPage = await editorContext.newPage();
        await editorPage.goto('/admin/login');
        await editorPage.getByLabel('Correo electrónico').fill(editorEmail);
        await editorPage.locator('input[type="password"]').fill(editorPassword);
        await editorPage.getByRole('button', { name: 'Entrar' }).click();
        await expect(editorPage).toHaveURL(/\/admin$/, { timeout: 60_000 });
        await editorContext.close();
    });
});
