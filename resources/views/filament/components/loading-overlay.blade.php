<div
    x-data='{
        visible: false,
        timer: null,
        start() {
            window.clearTimeout(this.timer);
            this.visible = true;
        },
        finish() {
            window.clearTimeout(this.timer);
            this.timer = window.setTimeout(() => this.visible = false, 180);
        },
    }'
    x-init='
        const attachLivewireHook = () => {
            if (! window.Livewire || window.__codisecLoadingHookRegistered) return;

            window.__codisecLoadingHookRegistered = true;
            window.Livewire.hook(`commit`, ({ succeed, fail }) => {
                start();
                succeed(() => queueMicrotask(() => finish()));
                fail(() => finish());
            });
        };

        attachLivewireHook();
        document.addEventListener(`livewire:init`, attachLivewireHook, { once: true });
    '
    x-on:livewire:navigating.window='start()'
    x-on:livewire:navigated.window='finish()'
    x-show='visible'
    x-transition.opacity.duration.180ms
    x-cloak
    class='codisec-loading-layer'
    role='status'
    aria-live='polite'
    aria-label='Cargando contenido'
>
    <div class='codisec-loading-card'>
        <span class='codisec-loading-spinner' aria-hidden='true'></span>
        <span>
            <strong>Actualizando contenido</strong>
            <small>Un momento, por favor...</small>
        </span>
    </div>
</div>
