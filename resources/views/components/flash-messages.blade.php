<div 
    x-data="{ 
        toasts: [],
        addToast(message, type = 'success') {
            const id = Date.now() + Math.random().toString(36).substr(2, 9);
            this.toasts.push({ id, message, type });
            setTimeout(() => this.removeToast(id), 6000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-init="
        @if(session('success')) addToast('{{ addslashes(session('success')) }}', 'success'); @endif
        @if(session('error')) addToast('{{ addslashes(session('error')) }}', 'error'); @endif
        @if(session('status')) addToast('{{ addslashes(session('status')) }}', 'info'); @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                addToast('{{ addslashes($error) }}', 'error');
            @endforeach
        @endif
    "
    class="fixed top-5 right-5 z-[9999] flex flex-col gap-3.5 w-full max-w-sm pointer-events-none"
>
    <template x-for="(toast, index) in toasts" :key="toast.id">
        <div 
            x-show="true"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-4 opacity-0 scale-90 translate-x-10"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 scale-100 translate-x-0"
            x-transition:leave-end="opacity-0 scale-90 translate-x-10"
            :class="{
                'bg-green-50 border-green-200/60 text-green-800': toast.type === 'success',
                'bg-red-50 border-red-200/60 text-red-800': toast.type === 'error',
                'bg-blue-50 border-blue-200/60 text-blue-800': toast.type === 'info'
            }"
            class="px-4 py-3.5 rounded-2xl border shadow-xl flex items-start gap-3 backdrop-blur-md pointer-events-auto hover:shadow-2xl transition-shadow duration-300"
        >
            <!-- Toast Dynamic Icon -->
            <div class="shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <x-icon name="check-circle" class="h-5 w-5 text-green-500" />
                </template>
                <template x-if="toast.type === 'error'">
                    <x-icon name="x-circle" class="h-5 w-5 text-red-500" />
                </template>
                <template x-if="toast.type === 'info'">
                    <x-icon name="information-circle" class="h-5 w-5 text-blue-500" />
                </template>
            </div>
            
            <!-- Toast Message Body -->
            <div class="flex-1 text-xs font-bold leading-relaxed" x-text="toast.message"></div>
            
            <!-- Close Button -->
            <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600 transition shrink-0 p-0.5 hover:bg-slate-100/50 rounded-lg">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
