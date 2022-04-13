<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Создать рассылку') }}
        </h2>
    </x-slot>

    <x-card>
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" enctype="multipart/form-data" action="{{ route('createDistributionPost') }}">
        @csrf
            <!-- Image -->
            <div class="mt-2">
                <x-label for="image" :value="__('Изображение')" />
                <x-input id="image" type="file" accept="image/*" name="image"/>
            </div>

            <!-- Message -->
            <div class="mt-2">
                <x-label for="messageText" :value="__('Сообщение')" />
                <x-textarea id="messageText" class="block mt-1 w-full min-h-50" name="messageText"></x-textarea>
            </div>

            <!-- DateTime -->
            <div class="mt-2">
                <x-label for="sendingDate" :value="__('Дата и время отправки')" />

                <x-input id="sendingDate" class="block mt-1 w-full" type="datetime-local" name="sendingDate"/>
            </div>

            <div class="block inline-flex mb-5">
                <div class="flex items-center justify-start mt-4">
                    <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'"
                            onclick="window.location='{{ route('distributions') }}'">Назад</button>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <x-button class="ml-3">
                        {{ __('Создать') }}
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-layout>
