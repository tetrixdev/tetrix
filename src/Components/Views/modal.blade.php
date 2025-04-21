<div x-data="{
         close() {
             $el.remove();
         }
     }"
     x-init="
        // Directly access or initialize the store and add the modal
        $store.modals = $store.modals || {};
        $store.modals['{{ $id  }}'] = $data;"
     hx-headers="{{ json_encode(["ZP-Current-Modal-Url" => url()->current()]) }}"
     id="{{ $id }}"
     class="fixed inset-0 z-60 flex items-center justify-center m-6">
    <div class="absolute inset-0 bg-black opacity-75 -m-6"></div>
    <div class="relative">
        <x-tx::card>
            @isset($title)
                <x-slot:header>
                    <div class="flex">
                        <div class="flex-none">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 m-0 p-0">{{ $title }}</h2>
                        </div>
                        <div class="flex-1"></div>
                        <div class="flex-none">
                            <button @click="close()"
                                    type="button">
                                <span class="text-lg"><i class="fa-solid fa-xmark fa-lg"></i></span>
                            </button>
                        </div>
                    </div>
                </x-slot:header>
            @endisset
            <div class="max-h-[calc(100dvh-300px)] overflow-y-auto">
            {{ $slot }}
            </div>
            @isset($footer)
                <x-slot:footer>
                    {{ $footer }}
                </x-slot:footer>
            @endisset
        </x-tx::card>
    </div>
</div>