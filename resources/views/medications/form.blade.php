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