<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Настройки') }}
        </h2>
    </x-slot>

    <x-card>
        <div class="flex items-center justify-start mt-4">
            <button type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'"
                    onclick="window.location='{{ route('dashboard') }}'">Назад</button>
        </div>
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('editSettings') }}">
        @csrf
            <!--token -->
            <div class="mt-2">
                <x-label for="token" :value="__('Токен бота')" />
                <x-input id="token" class="block mt-1 w-full" type="text" name="token" value="{{$params['token']}}"/>
            </div>

            <!--username-->
            <div class="mt-2">
                <x-label for="username" :value="__('Имя бота')" />
                <x-input id="username" class="block mt-1 w-full min-h-50" type="text" name="username" value="{{$params['username']}}"/>
            </div>

            <!-- Message -->
            <div class="mt-2">
                <x-label for="welcomeMessage" :value="__('Приветственное сообщение')" />
                <x-textarea type="text" id="welcomeMessage" class="block mt-1 w-full min-h-50" name="welcomeMessage">{{$params['startMessage']}}</x-textarea>
            </div>

                <div class="flex items-center justify-start mt-4">
                    <x-button>
                        {{ __('Изменить параметры') }}
                    </x-button>
                </div>

        </form>

        <form method="POST" action="{{ route('editWebHook') }}">
        @csrf
            <!-- webhook -->
            <div class="mt-2">
                <x-label for="webhookURL" :value="__('Адрес вебхука')"/>
                <x-input id="webhookURL" class="block mt-1 w-full" type="text" name="webhookURL" value="{{$params['webHookURL']}}"/>
            </div>
            <div class="block inline-flex mb-5">
                <div class="flex items-center justify-end mt-4">
                    <x-button value="change" name="sumbitButton">
                        {{ __('Создать вебхук') }}
                    </x-button>

                    <x-button class="ml-3" value="delete" name="sumbitButton">
                        {{ __('Удалить вебхук') }}
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-layout>
