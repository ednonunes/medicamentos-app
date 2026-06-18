<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do Medicamento</label>
    <input type="text" name="name" value="{{ old('name', $medication->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
</div>

<div class="mt-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dosagem (ex: 500mg, 10 gotas)</label>
    <input type="text" name="dosage" value="{{ old('dosage', $medication->dosage ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
</div>

{{-- Campos alinhados horizontalmente --}}
<div class="flex flex-row gap-4 mt-4">
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Intervalo (em horas)</label>
        <input type="number" name="interval_hours" min="1" value="{{ old('interval_hours', $medication->interval_hours ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Primeira dose</label>
        <input type="time" name="start_time" value="{{ old('start_time', isset($medication->start_time) ? \Carbon\Carbon::parse($medication->start_time)->format('H:i') : '') }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Doses por dia</label>
        <input type="number" name="daily_limit" min="1" value="{{ old('daily_limit', $medication->daily_limit ?? 1) }}" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
</div>

<div class="block mt-4">
    <label for="take_on_empty_stomach" class="inline-flex items-center">
        <input id="take_on_empty_stomach" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-emerald-600 shadow-sm focus:ring-emerald-500 dark:focus:ring-emerald-600" name="take_on_empty_stomach" value="1" {{ isset($medication->take_on_empty_stomach) &&  $medication->take_on_empty_stomach ? 'checked' : '' }}>
        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Tomar em jejum') }}</span>
    </label>
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
                        (is_array(old('days_of_week')) && in_array($dia, old('days_of_week'))) || 
                        (!isset($medication) && !is_array(old('days_of_week'))) || 
                        (isset($medication) && is_array($medication->days_of_week) && in_array($dia, $medication->days_of_week)) 
                        ? 'checked' : '' 
                    }}>
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ $dia }}</span>
            </label>
        @endforeach
    </div>
    <x-input-error :messages="$errors->get('days_of_week')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="observations" :value="__('Observações (exemplo: pode causar sonolência)')" />
    <textarea id="observations" name="observations" rows="3" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('observations', $medication->observations ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('observations')" class="mt-2" />
</div>