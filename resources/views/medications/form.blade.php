<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do Medicamento</label>
    <input type="text" name="name" value="{{ old('name', $medication->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
</div>

<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dosagem (ex: 500mg, 10 gotas)</label>
    <input type="text" name="dosage" value="{{ old('dosage', $medication->dosage ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Intervalo (em horas)</label>
        <input type="number" name="interval_hours" min="1" value="{{ old('interval_hours', $medication->interval_hours ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora da 1ª Dose</label>
        <input type="time" name="start_time" value="{{ old('start_time', isset($medication->start_time) ? \Carbon\Carbon::parse($medication->start_time)->format('H:i') : '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
</div>

<div class="mt-4">
    <x-input-label value="{{ __('Dias da Semana') }}" />
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
        Selecione os dias em que este medicamento deve ser tomado.
    </p>
    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-gray-50 dark:bg-gray-900/50 p-4 rounded-md border border-gray-200 dark:border-gray-700">
        @foreach(['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'] as $dia)
            <label class="inline-flex items-center">
                <input type="checkbox" name="days_of_week[]" value="{{ $dia }}" 
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-emerald-600 shadow-sm focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:focus:ring-offset-gray-800"
                    {{ 
                        // 1. Mantém marcado se o formulário voltou com erro de validação (old)
                        (is_array(old('days_of_week')) && in_array($dia, old('days_of_week'))) || 
                        
                        // 2. SE NÃO for uma edição (tela de cadastro), marca por padrão
                        (!isset($medication) && !is_array(old('days_of_week'))) || 
                        
                        // 3. Se for uma edição, mantém o que veio do banco de dados
                        (isset($medication) && is_array($medication->days_of_week) && in_array($dia, $medication->days_of_week)) 
                        ? 'checked' : '' 
                    }}>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $dia }}</span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('days_of_week')" class="mt-2" />
</div>