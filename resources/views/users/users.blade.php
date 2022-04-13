<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Пользователи') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 pr-10 lg:px-8">
                    @empty($users)
                        <h1 class="text-center">Пока здесь пусто.</h1>
                    @endempty
                    @isset($users)
                        @if(count($users) === 0)
                            <h1 class="text-center">Пока здесь пусто.</h1>
                        @else
                            <div class="align-middle inline-block min-w-full shadow overflow-hidden bg-white shadow-dashboard px-8 pt-3 rounded-bl-lg rounded-br-lg">
                                <table class="w-full items-center">
                                    <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider items-center">Id</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">Кастомный никнейм</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">Является ботом</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">Забанен</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">Подробнее</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                            @foreach($users as $user)
                                                <tr class="w-full">
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5 text-center">{{ $user->userId }}</td>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5 text-center">{{ $user->customUsername ?? "Пусто" }}</td>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5 text-center">{{ $user->is_bot ? 'Да' : 'Нет' }}</td>
                                                    <td class="px-6 py-4 whitespace-no-wrap border-b text-blue-900 border-gray-500 text-sm leading-5 text-center">{{ $user->isBanned ? 'Да' : 'Нет' }}</td>
                                                    <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-500 text-sm leading-5 text-center">
                                                        <button class="min-w-full px-6 py-2 border-blue-500 border text-blue-500 rounded transition duration-300 hover:bg-blue-700 hover:text-white focus:outline-none"
                                                                onclick="window.location='{{ route('userInfo',$user->userId) }}'">Детали</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endisset
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
