<div x-data="{
        visible: false,
        title: '{{ $title ?? 'Confirm Action' }}',
        content: '{{ $content ?? 'Are you sure you want to proceed?' }}',
        confirmCallback: null,

        open(params = {}) {
            if (!params.callback) {
                console.error('Confirmation callback is required');
                return;
            }

            if (params.title) this.title = params.title;
            if (params.content) this.content = params.content;
            this.confirmCallback = params.callback;
            this.visible = true;
        },

        close() {
            this.visible = false;
        },

        confirm() {
            this.confirmCallback();
            this.close();

            // Reset when closed
            setTimeout(() => {
                this.title = '{{ $title ?? 'Confirm Action' }}';
                this.content = '{{ $content ?? 'Are you sure you want to proceed?' }}',
                this.confirmCallback = null;
            }, 300); // Small delay to ensure transition completes
        }
    }"
     x-init="
        // Register confirm modal in global store
        window.$confirmModal = $data;

        // Listen for open-confirm-modal event globally
        window.addEventListener('open-confirm-modal', (event) => {
            const params = {
                title: event.detail.title || title,
                content: event.detail.content || '',
                callback: event.detail.onConfirm
            };

            if (!params.callback) {
                console.error('Confirmation callback is required');
                return;
            }

            open(params);
        });
     "
     x-show="visible"
     x-cloak
     x-transition
     class="fixed inset-0 z-70 flex items-center justify-center m-6">

    <div class="absolute inset-0 bg-black opacity-75 -m-6" @click="close()"></div>
    <div class="relative">
        <x-tx::card>
            <x-slot:header>
                <div class="flex">
                    <div class="flex-none">
                        <x-tx::h2 x-text="title"></x-tx::h2>
                    </div>
                    <div class="flex-1"></div>
                    <div class="flex-none">
                        <button @click="close()" type="button">
                            <span class="text-lg"><i class="fa-solid fa-xmark fa-lg"></i></span>
                        </button>
                    </div>
                </div>
            </x-slot:header>

            <div class="max-h-[calc(100dvh-300px)] overflow-y-auto">
                <div x-html="content"></div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end space-x-2">
                    <x-tx::button @click="close()" variant="secondary">Cancel</x-tx::button>
                    <x-tx::button @click="confirm()" variant="primary">Confirm</x-tx::button>
                </div>
            </x-slot:footer>
        </x-tx::card>
    </div>
</div>