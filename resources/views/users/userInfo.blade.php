<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Информация о пользователе') }}
        </h2>
    </x-slot>

    <x-card>
        @isset($user)
            <div>
                <p><b>Id:</b> {{$user->userId}}</p>
                <p><b>Имя:</b> {{$user->first_name}}</p>
                <p><b>Фамилия:</b> {{$user->last_name ?? '-'}}</p>
                <p><b>Никнейм:</b> {{$user->username ?? '-'}}</p>
                <p><b>Кастомный никнейм:</b> {{$user->customUsername ?? '-'}}</p>
                <p><b>Является ботом:</b> {{$user->is_bot ? 'Да' : 'Нет'}}</p>
                <p><b>Забанен:</b> {{$user->isBanned ? 'Да' : 'Нет'}}</p>
                <p><b>Первое обращения к боту:</b> {{$user->created_at->format('d.m.Y H:m:s')}}</p>
                <p><b>Последнее обращения к боту:</b> {{$user->updated_at->format('d.m.Y H:m:s')}}</p>
            </div>
            @endisset
            @empty($user)
                <p>Нет информации о пользователе</p>
            @endempty

            <div class="inline-flex">
                    <div class="flex items-center justify-start mt-4">
                        <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'"
                                onclick="window.location='{{ route('users') }}'">Назад</button>
                    </div>
                @isset($user)
                        <form method="POST" action="{{ route('switchBan') }}">
                            @csrf
                            <input type="hidden" name="userId" value="{{$user->userId}}">
                            <div class="flex items-center justify-end mt-4">
                                <x-button class="ml-3">
                                    {{ __($user->isBanned ? 'Разблокировать' : 'Заблокировать') }}
                                </x-button>
                            </div>
                        </form>
                @endisset
            </div>
    </x-card>
</x-app-layout>
