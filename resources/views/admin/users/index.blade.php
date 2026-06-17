<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Painel de Administração</h1>
            
            @foreach($users as $user)
                <div class="bg-white p-6 rounded-lg shadow mb-4">
                    <h2 class="font-bold text-lg text-emerald-700">{{ $user->name }} ({{ $user->email }})</h2>
                    <ul class="list-disc ml-6 mt-2">
                        @forelse($user->medications as $med)
                            <li class="text-gray-600">{{ $med->name }} - {{ $med->dosage }}</li>
                        @empty
                            <li class="text-gray-400 italic">Sem medicamentos.</li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>