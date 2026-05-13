{{-- resources/views/ai/chat.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AI Chat Assistant</title>

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            overflow: hidden;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        #chatContainer::-webkit-scrollbar {
            width: 6px;
        }

        #chatContainer::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 999px;
        }

        .typing-dot {
            animation: bounce 1s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: .2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: .4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
                opacity: .5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>

<body class="bg-slate-950 text-white">

    <div class="h-screen w-full bg-linear-to-br from-slate-950 via-slate-900 to-black">

        <div class="flex h-full overflow-hidden">

            {{-- SIDEBAR --}}
            <aside id="sidebar"
                class="fixed lg:relative z-50 left-0 top-0 h-full w-75 bg-white/5 backdrop-blur-xl border-r border-white/10 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">

                {{-- LOGO --}}
                <div class="p-5 border-b border-white/10 flex items-center justify-between">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center font-bold text-lg">
                            AI
                        </div>

                        <div>
                            <h2 class="font-bold text-lg">
                                AI Assistant
                            </h2>

                            <p class="text-sm text-slate-400">
                                Laravel 13 SDK
                            </p>
                        </div>
                    </div>

                    <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white text-2xl">
                        ×
                    </button>
                </div>

                {{-- NEW CHAT --}}
                <div class="p-4">
                    <button id="newChatBtn"
                        class="w-full bg-indigo-600 hover:bg-indigo-500 transition rounded-2xl py-3 px-4 flex items-center justify-center gap-2 font-medium">
                        <span>+</span>
                        <span>New Chat</span>
                    </button>
                </div>

                {{-- SEARCH --}}
                <div class="px-4 pb-4">
                    <input type="text" placeholder="Search conversation..."
                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>

                {{-- CHAT HISTORY --}}
                <div class="flex-1 overflow-y-auto px-3 pb-4 space-y-2 no-scrollbar">

                    @for ($i = 1; $i <= 10; $i++)
                        <button
                            class="w-full text-left p-4 rounded-2xl bg-white/3 hover:bg-white/10 transition border border-transparent hover:border-white/10">
                            <h3 class="font-medium truncate">
                                Laravel AI Chat {{ $i }}
                            </h3>

                            <p class="text-xs text-slate-400 mt-1 truncate">
                                Previous conversation preview...
                            </p>
                        </button>
                    @endfor

                </div>

                {{-- USER --}}
                <div class="p-4 border-t border-white/10">

                    <div class="flex items-center gap-3">

                        <img src="https://ui-avatars.com/api/?background=4f46e5&color=fff&name={{ urlencode(auth()->user()->name ?? 'User') }}"
                            class="w-11 h-11 rounded-full">

                        <div>
                            <h4 class="font-medium">
                                {{ auth()->user()->name ?? 'Guest User' }}
                            </h4>

                            <p class="text-xs text-slate-400">
                                Online
                            </p>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- OVERLAY --}}
            <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

            {{-- MAIN --}}
            <main class="flex-1 flex flex-col h-full">

                {{-- TOP BAR --}}
                <header
                    class="border-b border-white/10 bg-white/3 backdrop-blur-xl px-4 md:px-6 py-4 flex items-center justify-between">

                    <div class="flex items-center gap-4">

                        <button id="openSidebar"
                            class="lg:hidden w-11 h-11 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center">
                            ☰
                        </button>

                        <div>
                            <h1 class="font-bold text-xl">
                                AI Chat Assistant
                            </h1>

                            <p class="text-sm text-slate-400">
                                Ask anything to your AI
                            </p>
                        </div>
                    </div>
                </header>

                {{-- CHAT AREA --}}
                <div id="chatContainer" class="flex-1 overflow-y-auto px-4 md:px-8 py-8 space-y-8">

                    {{-- AI MESSAGE --}}
                    <div class="flex gap-4 items-start">

                        <div
                            class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center shrink-0 font-bold">
                            AI
                        </div>

                        <div class="max-w-3xl">

                            <div class="bg-white/4 border border-white/10 rounded-3xl rounded-tl-sm px-5 py-4">

                                <p class="leading-8 text-slate-200">
                                    Hello 👋 <br><br>

                                    I am your Laravel AI assistant. <br>
                                    Ask me anything about coding, business, writing, or design.
                                </p>

                            </div>

                            <div class="text-xs text-slate-500 mt-2">
                                AI Assistant • Just now
                            </div>
                        </div>
                    </div>

                </div>

                {{-- INPUT --}}
                <div class="border-t border-white/10 bg-black/20 backdrop-blur-xl p-4 md:p-6">

                    <form id="chatForm" class="max-w-5xl mx-auto">

                        @csrf

                        <div class="relative">

                            <textarea id="messageInput" rows="1" placeholder="Message AI assistant..."
                                class="w-full resize-none overflow-hidden bg-white/4 border border-white/10 rounded-3xl px-6 py-5 pr-36 text-white placeholder:text-slate-500 outline-none focus:ring-2 focus:ring-indigo-500"></textarea>

                            <div class="absolute right-4 bottom-4 flex items-center gap-2">

                                <button type="button"
                                    class="w-11 h-11 rounded-2xl hover:bg-white/10 transition flex items-center justify-center">
                                    📎
                                </button>

                                <button type="submit" id="sendBtn"
                                    class="bg-indigo-600 hover:bg-indigo-500 transition px-5 py-3 rounded-2xl font-medium">
                                    Send
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-3 px-2 text-xs text-slate-500">

                            <p>
                                AI can make mistakes. Verify important information.
                            </p>

                            <p class="hidden md:block">
                                Enter ↵ Send • Shift + Enter New line
                            </p>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    {{-- TYPING TEMPLATE --}}
    <template id="typingTemplate">

        <div class="flex gap-4 items-start typing-wrapper">

            <div class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center shrink-0 font-bold">
                AI
            </div>

            <div class="bg-white/4 border border-white/10 rounded-3xl rounded-tl-sm px-5 py-5">

                <div class="flex items-center gap-2">

                    <div class="typing-dot w-2 h-2 rounded-full bg-white"></div>
                    <div class="typing-dot w-2 h-2 rounded-full bg-white"></div>
                    <div class="typing-dot w-2 h-2 rounded-full bg-white"></div>

                </div>
            </div>
        </div>

    </template>

    <!-- Marked.js -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- DOMPurify -->
    <script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('chatForm');
            const input = document.getElementById('messageInput');
            const chatContainer = document.getElementById('chatContainer');

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            const openSidebar = document.getElementById('openSidebar');
            const closeSidebar = document.getElementById('closeSidebar');

            // MOBILE SIDEBAR
            openSidebar.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });

            closeSidebar.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);

            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            // AUTO HEIGHT
            input.addEventListener('input', () => {
                input.style.height = 'auto';
                input.style.height = input.scrollHeight + 'px';
            });

            // ENTER SEND
            input.addEventListener('keydown', function(e) {

                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

            // SUGGESTIONS
            document.querySelectorAll('.suggestionBtn').forEach(btn => {

                btn.addEventListener('click', () => {
                    input.value = btn.innerText;
                    input.focus();
                });
            });

            // FORM SUBMIT
            form.addEventListener('submit', async function(e) {

                e.preventDefault();

                const message = input.value.trim();

                if (!message) return;

                appendUserMessage(message);

                input.value = '';
                input.style.height = 'auto';

                const typing = appendTyping();

                try {
                    const response = await fetch(
                        `{{ route('ai.chat-stream') }}?message=${encodeURIComponent(message)}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    const data = await response.json();

                    typing.remove();

                    appendAiMessage(data.message || 'No response.');

                } catch (error) {

                    typing.remove();

                    appendAiMessage('Something went wrong.');

                    console.error(error);
                }
            });

            // USER MESSAGE
            function appendUserMessage(message) {

                const html = `
            <div class="flex justify-end">

                <div class="max-w-3xl">

                    <div class="bg-indigo-600 rounded-3xl rounded-br-sm px-5 py-4">
                        <p class="leading-8 whitespace-pre-wrap">${escapeHtml(message)}</p>
                    </div>

                    <div class="text-xs text-slate-500 mt-2 text-right">
                        You • Just now
                    </div>
                </div>
            </div>
        `;

                chatContainer.insertAdjacentHTML('beforeend', html);

                scrollBottom();
            }

            // AI MESSAGE
            function appendAiMessage(message) {

                const rawHtml = marked.parse(message);

                const cleanHtml = DOMPurify.sanitize(rawHtml);

                const html = `
        <div class="flex gap-4 items-start">

            <div class="w-11 h-11 rounded-2xl bg-indigo-600 flex items-center justify-center shrink-0 font-bold">
                AI
            </div>

            <div class="max-w-3xl">

                <div class="bg-white/4 border border-white/10 rounded-3xl rounded-tl-sm px-5 py-4">

                    <div class="leading-8 text-slate-200 prose prose-invert max-w-none">
                        ${cleanHtml}
                    </div>

                </div>

                <div class="text-xs text-slate-500 mt-2">
                    AI Assistant • Just now
                </div>
            </div>
        </div>
    `;

                chatContainer.insertAdjacentHTML('beforeend', html);

                scrollBottom();
            }

            // TYPING
            function appendTyping() {

                const template = document.getElementById('typingTemplate');

                const clone = template.content.cloneNode(true);

                chatContainer.appendChild(clone);

                scrollBottom();

                return chatContainer.querySelector('.typing-wrapper:last-child');
            }

            // SCROLL
            function scrollBottom() {

                chatContainer.scrollTop = chatContainer.scrollHeight;
            }

            // ESCAPE HTML
            function escapeHtml(text) {

                const div = document.createElement('div');

                div.innerText = text;

                return div.innerHTML;
            }
        });
    </script>

</body>

</html>
