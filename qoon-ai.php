<?php
require "conn.php";
$pageTitle = "QOON AI Agent";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <style>
        body, html { height: 100%; margin: 0; background: #F8FAFC; overflow: hidden; }
        .app-envelope { display: flex; height: 100dvh; width: 100%; border-radius: 0; overflow: hidden; }
        @media (max-width: 991px) { .sb-container { display: none !important; } }
        .chat-scroll::-webkit-scrollbar { width: 4px; }
        .chat-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>
        <main id="root" class="flex-1 bg-white relative flex flex-col overflow-hidden"></main>
    </div>

    <script type="text/babel">
        const { useState, useEffect, useRef } = React;

        const AGENT_MAP = {
            'Adam':    { name: 'Adam', img: 'adam.jpg', color: '#7C3AED', title: 'User Specialist' },
            'Mahjoub': { name: 'Mahjoub', img: 'mahjoub.jpg', color: '#D97706', title: 'Finance Expert' },
            'Tamo':    { name: 'Tamo', img: 'tamo.jpg', color: '#E11D48', title: 'Logistics Manager' },
            'Ali':     { name: 'Ali', img: 'ali.webp', color: '#2563EB', title: 'Operations Lead' },
            'Warda':   { name: 'Warda', img: 'warda.jpg', color: '#E11D48', title: 'Communications' },
            'Chemsy':  { name: 'Chemsy', img: 'chemsy.webp', color: '#F59E0B', title: 'Configurations' }
        };

        const detectAgent = (text) => {
            const low = text.toLowerCase();
            if (low.includes('user') || low.includes('member') || low.includes('customer')) return 'Adam';
            if (low.includes('money') || low.includes('revenue') || low.includes('income') || low.includes('wallet') || low.includes('debt') || low.includes('finance')) return 'Mahjoub';
            if (low.includes('driver') || low.includes('courier') || low.includes('fleet')) return 'Tamo';
            if (low.includes('order') || low.includes('transit') || low.includes('delivery')) return 'Ali';
            if (low.includes('notif') || low.includes('message') || low.includes('broadcast')) return 'Warda';
            if (low.includes('config') || low.includes('slider') || low.includes('app') || low.includes('category')) return 'Chemsy';
            return null;
        };

        function App() {
            const [messages, setMessages] = useState([
                { id: 1, role: 'assistant', text: "Welcome to QOON AI. I am your ecosystem orchestrator. Ask me anything about users, revenue, or fleet operations." }
            ]);
            const [input, setInput] = useState("");
            const [loading, setLoading] = useState(false);
            const scrollRef = useRef(null);

            useEffect(() => {
                if(scrollRef.current) scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
            }, [messages, loading]);

            const sendMessage = async () => {
                if(!input.trim() || loading) return;
                const userMsg = input.trim();
                setMessages(p => [...p, { id: Date.now(), role: 'user', text: userMsg }]);
                setInput("");
                setLoading(true);

                try {
                    const res = await fetch('ai-chat-api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: userMsg, history: messages.slice(-2) })
                    });
                    const data = await res.json();
                    const detected = detectAgent(userMsg) || detectAgent(data.reply || "");
                    setMessages(p => [...p, { id: Date.now()+1, role: 'assistant', text: data.reply || "No response.", agent: detected }]);
                } catch(e) {
                    setMessages(p => [...p, { id: Date.now()+1, role: 'assistant', text: "Error connecting to AI engine." }]);
                } finally {
                    setLoading(false);
                }
            };

            return (
                <div className="flex flex-col h-full w-full">
                    {/* Header */}
                    <div className="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white/80 backdrop-blur-md z-10">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                                <i className="fas fa-brain"></i>
                            </div>
                            <h1 className="text-xl font-bold text-slate-900">QOON Intelligence</h1>
                        </div>
                        <div className="flex items-center gap-2 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                             <span className="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                             <span className="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Live Engine</span>
                        </div>
                    </div>

                    {/* Messages */}
                    <div ref={scrollRef} className="flex-1 overflow-y-auto p-4 md:px-20 lg:px-64 space-y-6 chat-scroll bg-[#FBFBFE]">
                        {messages.map((msg) => {
                            const agent = msg.agent ? AGENT_MAP[msg.agent] : null;
                            const isUser = msg.role === 'user';
                            return (
                                <div key={msg.id} className={`flex gap-3 ${isUser ? 'flex-row-reverse' : 'flex-row'}`}>
                                    <div className="flex-shrink-0 mt-1">
                                        {!isUser ? (
                                            agent ? (
                                                <img 
                                                    src={agent.img} 
                                                    className="w-10 h-10 rounded-xl object-cover shadow-sm border border-slate-200" 
                                                    onError={(e) => { e.target.src = `https://ui-avatars.com/api/?name=${agent.name}&background=f1f5f9&color=${agent.color.replace('#','')}&bold=true`; }}
                                                />
                                            ) : (
                                                <div className="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xs">
                                                    <i className="fas fa-magic"></i>
                                                </div>
                                            )
                                        ) : (
                                            <div className="w-10 h-10 bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 text-xs">
                                                <i className="fas fa-user"></i>
                                            </div>
                                        )}
                                    </div>
                                    <div className={`flex flex-col gap-1 max-w-[85%] ${isUser ? 'items-end' : 'items-start'}`}>
                                        {!isUser && agent && (
                                            <span className="text-[10px] font-bold uppercase text-slate-400 ml-1">{agent.name} • {agent.title}</span>
                                        )}
                                        <div className={`p-4 rounded-2xl text-[15px] leading-relaxed shadow-sm ${isUser ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-800 rounded-tl-none'}`}>
                                            <div dangerouslySetInnerHTML={{ __html: msg.text.replace(/\n/g, '<br>') }}></div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                        {loading && (
                            <div className="flex gap-3">
                                <div className="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center animate-pulse">
                                    <i className="fas fa-circle-notch fa-spin text-slate-300"></i>
                                </div>
                                <div className="p-4 rounded-2xl bg-white border border-slate-100 w-32 h-12 animate-pulse"></div>
                            </div>
                        )}
                    </div>

                    {/* Footer */}
                    <div className="p-4 md:p-8 bg-white border-top border-slate-100">
                        <div className="max-w-4xl mx-auto flex gap-2">
                             <input 
                                value={input}
                                onChange={e => setInput(e.target.value)}
                                onKeyDown={e => e.key === 'Enter' && sendMessage()}
                                placeholder="Consult QOON Intelligence..."
                                className="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 outline-none focus:border-indigo-500 transition-colors"
                             />
                             <button 
                                onClick={sendMessage}
                                className={`w-12 h-12 rounded-2xl flex items-center justify-center transition-all ${input.trim() ? 'bg-indigo-600 text-white shadow-lg' : 'bg-slate-100 text-slate-300'}`}
                             >
                                <i className="fas fa-paper-plane"></i>
                             </button>
                        </div>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>
</html>