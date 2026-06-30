<div class="glass-card rounded-2xl shadow-sm bg-white border border-slate-200 overflow-hidden flex flex-col" style="height: 600px;">
    {{-- Header --}}
    <div class="p-4 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-primary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
            </svg>
            <span>Discussion avec le Directeur</span>
        </h3>
        <span class="text-xs text-slate-500 bg-slate-200/50 px-2.5 py-1 rounded-md font-medium">Historique officiel</span>
    </div>

    {{-- Messages Area --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container" style="background-color: #f8fafc; background-image: radial-gradient(#e2e8f0 1px, transparent 1px); background-size: 20px 20px;">
        @if($subject->messages->isEmpty())
            <div class="flex flex-col items-center justify-center h-full text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-3 opacity-20">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
                <p class="text-sm font-medium text-slate-500">Aucun message pour le moment.</p>
                <p class="text-xs mt-1">Commencez la discussion ci-dessous.</p>
            </div>
        @else
            @foreach($subject->messages as $message)
                @php
                    $isMine = $message->sender_id === Auth::id();
                @endphp
                <div class="flex w-full {{ $isMine ? 'justify-end' : 'justify-start' }}">
                    <div class="flex flex-col {{ $isMine ? 'items-end' : 'items-start' }} max-w-[85%] sm:max-w-[75%]">
                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 px-1">
                            {{ $isMine ? 'Vous' : $message->sender->name }}
                        </div>
                        <div class="px-4 py-2.5 shadow-sm text-[13px] leading-relaxed relative 
                            {{ $isMine ? 'bg-primary text-white rounded-2xl rounded-tr-sm' : 'bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-sm' }}">
                            {!! nl2br(e($message->body)) !!}
                        </div>
                        <div class="text-[9px] font-medium text-slate-400 mt-1 px-1 flex items-center gap-1">
                            {{ $message->created_at->format('d/m/Y à H:i') }}
                            @if($isMine)
                                @if($message->isRead())
                                    <x-icon name="check-circle" class="w-3.5 h-3.5 text-emerald-500" />
                                @else
                                    <x-icon name="check" class="w-3.5 h-3.5 text-slate-300" />
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Input Area --}}
    <div class="p-3 sm:p-4 border-t border-slate-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.02)]">
        <form action="{{ route('messages.store', $subject) }}" method="POST" class="flex gap-2 items-end">
            @csrf
            <div class="relative flex-1">
                <textarea name="body" rows="1" id="chat-input"
                    placeholder="Écrivez votre message..." 
                    class="block w-full rounded-2xl border-slate-300 bg-slate-50 text-sm focus:border-primary focus:ring-primary/20 resize-none py-3 px-4 shadow-inner min-h-[46px] max-h-[120px]"
                    required></textarea>
            </div>
            <button type="submit" 
                class="shrink-0 h-[46px] w-[46px] flex items-center justify-center rounded-2xl bg-gradient-to-r from-primary to-primary-dark text-white hover:opacity-90 focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all shadow-md group border border-primary-light">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 -mt-0.5 ml-0.5 group-hover:scale-110 transition-transform text-white">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
            </button>
        </form>
        @error('body')
            <p class="text-xs text-rose-500 mt-2 font-medium px-2">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('chat-input');
        
        // Auto-resize textarea up to a max height
        if(textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = '46px';
                this.style.height = (this.scrollHeight < 120 ? this.scrollHeight : 120) + 'px';
            });
            
            // Enter to submit (Shift+Enter for new line)
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if(this.value.trim() !== '') {
                        this.closest('form').submit();
                    }
                }
            });
        }
    });
</script>

<script>
    // Scroll to bottom of messages container
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    // Handle hash to show messages tab if '#messagerie' is in URL
    if (window.location.hash === '#messagerie') {
        document.addEventListener('alpine:init', () => {
            Alpine.data('tabs', () => ({
                currentTab: 'messages'
            }));
        });
        // We'll also just brutally trigger it using x-data if Alpine is already loaded
        setTimeout(() => {
            const tabsContainer = document.querySelector('[x-data]');
            if (tabsContainer && tabsContainer.__x) {
                tabsContainer.__x.$data.currentTab = 'messages';
            }
        }, 100);
    }
</script>
