# HealthNexus — Propuesta de rediseño visual

Rama: `rediseno-visual`. **No cambia lógica de negocio**: controladores, rutas, permisos, modelos y migraciones quedan intactos. Todo el trabajo está en vistas, estilos y un helper de navegación.

## Cómo probarlo

```bash
composer install
npm install
npm run build      # o npm run dev
php artisan serve
```

> No hace falta migrar nada nuevo. Si ya tenías el proyecto corriendo, basta con `npm run build` (o `npm run dev`) y refrescar.

---

## 1. Identidad unificada

| Antes | Ahora |
|---|---|
| ~981 usos de `indigo-*`, ~194 de `purple/violet-*`, hex sueltos (`#7C3AED`, `#172554`) | Una sola paleta en `tailwind.config.js`: **`brand`** (azul marino/índigo de la cruz del logo) y **`accent`** (violeta de la "N") |
| 4 logos distintos (corazón, cápsulas SVG, PNG, logo de Laravel) | Un solo componente `<x-application-logo>` basado en el logotipo oficial (`public/images/logo-mark.png` recortado del PNG original) |
| Figtree | **Inter**, con cifras tabulares en tablas y KPIs |
| `text-[10px]` / `text-[11px]` en ~935 lugares | Mínimo 12px (`text-xs`) |
| Gradientes de color por rol (teal, naranja, fucsia) | Colores de estado (rojo/ámbar/verde) reservados para estados clínicos |
| `dark:` parcial que se activaba con el sistema operativo | Desactivado (`darkMode: 'class'`) hasta implementarlo completo |

Los alias `nexus-*` se conservan y ahora apuntan a la paleta oficial, así ninguna vista se rompe.

## 2. Layout nuevo

- **Topbar propio** (`layouts/navigation.blade.php`) en lugar de la barra de Breeze: breadcrumb automático, búsqueda, campana de alertas con contador y menú de usuario en español.
- **Paleta de comandos `Ctrl + K` / `⌘ + K`**: salta a cualquier módulo permitido para el rol o busca un paciente por nombre.
- **Sidebar** (`components/sidebar.blade.php`):
  - Se fija expandido o colapsado con un botón (ya no depende del hover) y se recuerda entre visitas.
  - Colapsado muestra tooltips con el nombre de cada módulo.
  - En móvil funciona como drawer con fondo oscurecido (antes el botón no abría nada y quedaba un margen vacío).
  - Usuario y rol en el pie.
- **Menú centralizado** en `config/navegacion.php` + `app/Support/Navegacion.php`. Sidebar, breadcrumb y Ctrl+K leen la misma fuente; agregar un módulo es una línea.
- El encabezado de cada página vive dentro del contenido (sin la banda blanca de Breeze).
- **Toasts** (`<x-toasts>`): los mensajes `success`/`error`/`warning`/`status` de sesión aparecen como notificación flotante. Se retiraron 49 bloques de alerta duplicados en las vistas. Desde JS: `window.hnToast('Guardado', 'success')`.

## 3. Componentes Blade (sistema de diseño)

| Componente | Uso |
|---|---|
| `<x-page-header title subtitle icon>` + `<x-slot:actions>` | Encabezado de página |
| `<x-button variant size icon href>` | `primary`, `secondary`, `ghost`, `danger`, `accent` |
| `<x-card title subtitle :padding>` | Contenedor base |
| `<x-kpi label value icon tone hint href badge>` | Indicadores |
| `<x-badge tone dot>` | Estados (emerald = activo, amber = pendiente, red = crítico…) |
| `<x-input name label icon hint>` | Campo con etiqueta y error |
| `<x-empty-state icon title description>` | Tablas/listas vacías |
| `<x-auth-shell title subtitle>` | Pantallas de acceso |
| `<x-application-logo wordmark variant full>` | Logotipo |

Además se reestilizaron los componentes de Breeze (`primary-button`, `secondary-button`, `danger-button`, `text-input`, `input-label`).

## 4. Pantallas

- **Login / PIN / recuperar contraseña / registro / reset**: misma estructura en dos columnas, etiquetas visibles en los campos, botón para mostrar contraseña, estado "Verificando…", panel de marca con el lema oficial. Se quitó la leyenda **"HIPAA Compliant"** (certificación de EE. UU. que el sistema no acredita). Se eliminó una línea de texto suelta que aparecía en la pantalla de PIN.
- **Dashboard de administración**: saludo compacto en lugar del banner grande; bloque **"Requiere atención"** (alertas críticas, stock bajo, ocupación > 85 %, eventos de auditoría) o "Todo en orden"; KPIs con `<x-kpi>` y barra de ocupación de camas; "Operación de hoy" en una sola tarjeta; modelos analíticos con tarjetas neutras (antes usaban rojo/naranja, que se confunden con alertas).
- **Dashboards de médico, enfermería y farmacia**: mismo encabezado con saludo; se quitaron los banners de colores por rol.
- **Medicamentos** (módulo modelo): `<x-page-header>`, botón `<x-button>`, estados con `<x-badge>`, acciones por fila en menú "⋯" y estado vacío con `<x-empty-state>`.
- **Gráficas**: Chart.js y ApexCharts heredan tipografía, colores y tooltips de la marca (`resources/js/app.js`).
- Tarjetas en toda la app: radio `rounded-2xl`, borde sutil `ring-slate-200/70` y sombra `shadow-card` en lugar de `rounded-3xl` + saltos en hover.

## Capturas

En `docs/rediseno/` hay capturas de cada pantalla (login, PIN, dashboards, Medicamentos, sidebar colapsado, Ctrl+K, móvil y toast) para la presentación.

## 5. Pendiente para siguientes iteraciones

- Migrar los demás módulos al patrón de Medicamentos (`x-page-header`, `x-badge`, menú de acciones, `x-empty-state`). Ya heredan colores, tipografía y tarjetas, pero no usan aún los componentes.
- Reemplazar los ~450 `<svg>` escritos a mano por `<x-heroicon-*>`.
- Quedarse con una sola librería de gráficas (hoy conviven Chart.js, ApexCharts por CDN y Flowbite sin usar).
- Modo oscuro completo, si se desea, ahora que todo usa tokens.

## Nota fuera del diseño

La carpeta `app/enums` está en minúscula pero el namespace es `App\Enums`. En Windows funciona; en Linux (por ejemplo, un servidor en AWS) el autoload no encuentra `App\Enums\AuditEvent`. Renombrarla a `app/Enums` lo resuelve. No se cambió en esta rama para no mezclarlo con el rediseño.
