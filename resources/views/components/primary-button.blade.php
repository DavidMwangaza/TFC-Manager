<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest shadow-md shadow-blue-500/20 hover:bg-blue-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200']) }}>
    {{ $slot }}
</button>
