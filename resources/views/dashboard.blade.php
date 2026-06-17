<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="block md:hidden px-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                    Acesso Rápido
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    
                    {{-- Atalho 1: Medicamentos --}}
                    <a href="{{ route('medications.index') }}" class="flex flex-col items-center justify-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm active:bg-slate-50 transition text-center">
                        <div class="bg-emerald-50 text-emerald-600 p-3 rounded-xl mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 002-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-sm text-slate-800">Medicamentos</span>
                        <span class="text-xxs text-slate-400 mt-0.5">Ver e editar lista</span>
                    </a>

                    {{-- Atalho 2: Agenda --}}
                    {{-- Nota: Caso você crie uma rota específica para a agenda futuramente (ex: agenda.index), mude aqui. Por enquanto aponta para o index --}}
                    <a href="{{ route('medications.agenda') }}" class="flex flex-col items-center justify-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm active:bg-slate-50 transition text-center">
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-sm text-slate-800">Agenda</span>
                        <span class="text-xxs text-slate-400 mt-0.5">Horários do dia</span>
                    </a>

                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6 text-gray-700 leading-relaxed space-y-3">
                    <p class="font-medium text-slate-800">
                        Este é seu painel para cadastro de medicamentos e horários, bem como a visualização de sua agenda, basta navegar pelo menu e começar.
                    </p>
                    <p class="text-sm text-gray-500">
                        Cadastre uma medicação com um horário próximo, verifique se cadastrou seu número de <span class="font-semibold text-emerald-600">WhatsApp</span> e aguarde o recebimento do alerta. Depois basta acessar sua agenda e marcar o mesmo como <span class="italic font-medium">"já tomado"</span>.
                    </p>
                    <div class="pt-4 border-t border-gray-50 text-xs text-gray-400">
                        ✨ Em breve mais informações aqui.
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>