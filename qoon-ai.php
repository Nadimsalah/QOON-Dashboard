<?php
require "conn.php";
$pageTitle = "QOON Intelligence";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <title><?= $pageTitle ?></title>
    <!-- Premium Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        brand: '#623CEA',
                        'brand-light': '#F0F4FF',
                    },
                    animation: {
                        'float': 'float 4s ease-in-out infinite',
                        'shimmer': 'shimmer 1.5s infinite linear',
                    },
                    keyframes: {
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-12px)' } },
                        shimmer: { '0%': { backgroundPosition: '-468px 0' }, '100%': { backgroundPosition: '468px 0' } }
                    }
                }
            }
        }
    </script>
    
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://unpkg.com/framer-motion@11.0.8/dist/framer-motion.js"></script>

    <style>
        body, html {
            height: 100%;
            margin:0;
            padding:0;
            overflow: hidden;
            background: #F8FAFC;
            -webkit-tap-highlight-color: transparent;
        }

        .app-envelope {
            display: flex;
            height: 100dvh;
            width: 100%;
            overflow: hidden;
        }

        /* Hide desktop sidebar on mobile — the sidebar.php outputs .sb-container */
        @media (max-width: 991px) {
            .sb-container { display: none !important; }
            /* Chat root: full viewport minus 68px tab bar */
            #root {
                height: calc(100dvh - 68px) !important;
                max-height: calc(100dvh - 68px) !important;
                overflow: hidden;
            }
        }

        /* Side Transition for native look */
        .chat-bubble-assistant { border-bottom-left-radius: 4px !important; }
        .chat-bubble-user { border-bottom-right-radius: 4px !important; }

        /* Custom Scroll */
        .chat-scroll::-webkit-scrollbar { width: 3px; }
        .chat-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }

        /* Shimmer Effect Background */
        .shimmer-bg {
            background: #f6f7f8;
            background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-repeat: no-repeat;
            background-size: 800px 104px;
            display: inline-block;
            position: relative;
            animation: shimmer 1s infinite linear;
        }

        /* Carousel Styling */
        .image-carousel {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 4px 2px 16px 2px;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .image-carousel::-webkit-scrollbar { display: none; }
        .carousel-item {
            flex: 0 0 calc(60% - 10px);
            min-width: 180px;
            scroll-snap-align: center;
            transition: transform 0.3s;
        }
        .carousel-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 24px;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.8);
        }
        .carousel-caption {
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
            text-align: center;
        }

        /* Stop iOS automatic zoom on focus */
        input, textarea { font-size: 16px !important; }

        /* Quick prompt chips */
        .quick-chips {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            scrollbar-width: none;
            padding-bottom: 2px;
        }
        .quick-chips::-webkit-scrollbar { display: none; }
        .chip {
            flex: 0 0 auto;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
            cursor: pointer;
            transition: 0.15s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .chip:hover, .chip:active { background: #F0F4FF; border-color: #623CEA; color: #623CEA; }

        /* Mobile input bar — sits above 68px bottom tab bar */
        @media (max-width: 991px) {
            .input-footer {
                bottom: 68px !important;
                padding-bottom: env(safe-area-inset-bottom, 0px) !important;
            }
            .chat-area-pad {
                padding-bottom: 160px !important;
            }
            /* Wider bubbles on mobile */
            .chat-bubble-mobile { max-width: 92% !important; }
        }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main id="root" class="flex-1 relative overflow-hidden bg-[#FBFBFE]">
            <!-- Skeleton Loader Screen (Boot) -->
            <div id="loader" class="flex flex-col h-full bg-white z-50 absolute inset-0">
                <div class="px-6 py-4 flex items-center justify-between border-b border-slate-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl shimmer-bg"></div>
                        <div class="w-32 h-4 rounded shimmer-bg"></div>
                    </div>
                </div>
                <div class="flex-1 p-6 space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-2xl shimmer-bg"></div>
                        <div class="w-2/3 h-16 rounded-[22px] shimmer-bg"></div>
                    </div>
                    <div class="flex gap-4 justify-end">
                        <div class="w-1/2 h-12 rounded-[22px] shimmer-bg"></div>
                        <div class="w-10 h-10 rounded-2xl shimmer-bg"></div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-2xl shimmer-bg"></div>
                        <div class="w-3/4 h-24 rounded-[22px] shimmer-bg"></div>
                    </div>
                </div>
                <div class="p-4 md:p-8">
                   <div class="h-16 w-full rounded-[30px] shimmer-bg"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- React Implementation -->
    <script type="text/babel">
        const { useState, useEffect, useRef } = React;
        const { motion, AnimatePresence } = window.Motion || window.framerMotion || window.FramerMotion;

        function App() {
            const [input, setInput] = useState("");
            const [messages, setMessages] = useState([
                { id: 1, role: 'assistant', text: "Welcome. I'm connected to your live database. Ask me anything about users, orders, or revenue." }
            ]);
            const [isTyping, setIsTyping] = useState(false);
            const [editingId, setEditingId] = useState(null);
            const [editingText, setEditingText] = useState("");
            const scrollRef = useRef(null);

            const isStarted = messages.length > 1;

            useEffect(() => {
                if (scrollRef.current) scrollRef.current.scrollTo({ top: scrollRef.current.scrollHeight, behavior: 'smooth' });
            }, [messages, isTyping]);

            const sendMessage = async (overrideText = null) => {
                const text = overrideText || input.trim();
                if (!text || isTyping) return;
                
                if (!overrideText) {
                    setMessages(p => [...p, { id: Date.now(), role: 'user', text }]);
                    setInput("");
                }
                
                setIsTyping(true);

                try {
                    const res = await fetch('ai-chat-api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ message: text, history: messages.slice(-2) })
                    });
                    const data = await res.json();
                    setMessages(p => [...p, { id: Date.now()+1, role: 'assistant', text: data.reply || "No insights found." }]);
                } catch (e) {
                    setMessages(p => [...p, { id: Date.now()+1, role: 'assistant', text: "Connection error." }]);
                } finally {
                    setIsTyping(false);
                }
            };

            const startEditing = (msg) => {
                setEditingId(msg.id);
                setEditingText(msg.text);
            };

            const saveEdit = async () => {
                if (!editingText.trim()) return;
                
                const idx = messages.findIndex(m => m.id === editingId);
                const newMessages = messages.slice(0, idx + 1);
                newMessages[idx].text = editingText;
                
                setMessages(newMessages);
                setEditingId(null);
                
                // Re-trigger response for the edited message
                await sendMessage(editingText);
            };

            return (
                <div className="flex flex-col h-full w-full relative">
                    
                    {/* 1. Header (Dynamic) */}
                    <div className="px-6 py-4 md:py-6 flex items-center justify-between border-b border-slate-100 bg-white/70 backdrop-blur-xl z-20">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 md:w-10 md:h-10 bg-brand rounded-xl flex items-center justify-center text-white text-sm shadow-lg shadow-brand/20">
                                <i className="fas fa-wand-magic-sparkles"></i>
                            </div>
                            <span className="font-extrabold text-slate-900 tracking-tight md:text-xl">QOON Intelligence</span>
                        </div>
                        <div className="flex items-center gap-2 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                            <span className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span className="text-[10px] font-black uppercase text-emerald-600 tracking-widest">Live</span>
                        </div>
                    </div>

                    {/* 2. Chat Area */}
                    <div ref={scrollRef} className="flex-1 overflow-y-auto p-4 md:px-12 lg:px-64 space-y-4 md:space-y-6 chat-scroll chat-area-pad" style={{paddingBottom:'120px'}}>
                        {/* Initial Center Piece */}
                        <AnimatePresence>
                            {!isStarted && (
                                <motion.div exit={{ opacity:0, scale:0.9 }} transition={{ duration:0.3 }} className="py-20 flex flex-col items-center text-center gap-8">
                                    <div className="w-24 h-24 bg-gradient-to-tr from-brand to-indigo-700 rounded-[40px] flex items-center justify-center text-white text-4xl shadow-2xl animate-float">
                                        <i className="fas fa-brain"></i>
                                    </div>
                                    <div className="space-y-3">
                                        <h2 className="text-2xl font-black text-slate-900">How can I help?</h2>
                                        <p className="text-sm text-slate-500 max-w-[260px] font-medium leading-relaxed">Analyze revenue or generate reports across your entire dashboard ecosystem.</p>
                                    </div>
                                </motion.div>
                            )}
                        </AnimatePresence>

                        {/* Message Stream */}
                        {messages.map((msg) => (
                            <motion.div key={msg.id} initial={{ opacity:0, y:12 }} animate={{ opacity:1, y:0 }} className={`flex flex-col gap-1 ${msg.role === 'user' ? 'items-end' : 'items-start'}`}>
                                <div className={`chat-bubble-mobile max-w-[88%] relative p-4 rounded-[22px] text-[15px] leading-relaxed shadow-sm ${
                                    msg.role === 'assistant' 
                                    ? 'bg-white border border-slate-100 text-slate-800 chat-bubble-assistant' 
                                    : 'bg-brand text-white font-medium chat-bubble-user'
                                }`}>
                                    {editingId === msg.id ? (
                                        <div className="flex flex-col gap-2">
                                            <textarea 
                                                autoFocus
                                                value={editingText}
                                                onChange={e => setEditingText(e.target.value)}
                                                className="bg-white/10 text-white outline-none border-none p-0 w-full min-h-[60px] resize-none overflow-hidden placeholder:text-white/50"
                                            />
                                            <div className="flex justify-end gap-2">
                                                <button onClick={() => setEditingId(null)} className="text-[10px] font-black uppercase opacity-60">Cancel</button>
                                                <button onClick={saveEdit} className="text-[10px] font-black uppercase bg-white text-brand px-3 py-1 rounded-full">Save</button>
                                            </div>
                                        </div>
                                    ) : (
                                        <div dangerouslySetInnerHTML={{ 
                                            __html: msg.text
                                                .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
                                                .replace(/(!\[(.*?)\]\((.*?)\)\s*){2,}/g, (match) => {
                                                    const items = match.match(/!\[(.*?)\]\((.*?)\)/g);
                                                    const carouselHtml = items.map(item => {
                                                        const [_, alt, url] = item.match(/!\[(.*?)\]\((.*?)\)/);
                                                        return `<div class="carousel-item"><img src="${url}" alt="${alt}" /><div class="carousel-caption">${alt}</div></div>`;
                                                    }).join('');
                                                    return `<div class="image-carousel">${carouselHtml}</div>`;
                                                })
                                                .replace(/!\[(.*?)\]\((.*?)\)/g, '<img src="$2" alt="$1" class="w-full max-w-[280px] rounded-3xl shadow-xl my-4 border border-slate-50" />')
                                                .replace(/\n/g, '<br/>') 
                                        }}></div>
                                    )}
                                </div>
                                {/* Touch-friendly edit button — always visible below user message */}
                                {msg.role === 'user' && editingId !== msg.id && (
                                    <button 
                                        onClick={() => startEditing(msg)}
                                        className="flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-brand px-2 py-0.5 rounded-full hover:bg-slate-100 transition-colors"
                                    >
                                        <i className="fas fa-pen text-[9px]"></i> Edit
                                    </button>
                                )}
                            </motion.div>
                        ))}

                        {isTyping && (
                            <motion.div initial={{ opacity:0 }} animate={{ opacity:1 }} className="flex justify-start">
                                <div className="bg-white border border-slate-100 p-4 rounded-[22px] chat-bubble-assistant flex flex-col gap-3 shadow-sm w-[70%]">
                                    <div className="flex items-center gap-2">
                                        <span className="text-[10px] font-black text-slate-400 uppercase tracking-widest">Generating Intelligence</span>
                                        <div className="flex gap-1">
                                            {[1,2,3].map(i => <motion.div key={i} className="w-1 h-1 bg-brand rounded-full" animate={{ opacity:[0.3,1,0.3] }} transition={{ repeat:Infinity, duration:1, delay:i*0.2 }} />)}
                                        </div>
                                    </div>
                                    <div className="h-4 w-full shimmer-bg rounded"></div>
                                    <div className="h-4 w-3/4 shimmer-bg rounded"></div>
                                </div>
                            </motion.div>
                        )}
                    </div>

                    {/* 3. Native App Style Footer */}
                    <div className="absolute bottom-0 left-0 right-0 p-3 md:p-8 bg-gradient-to-t from-[#F8FAFC] via-[#F8FAFC]/95 to-transparent z-30 input-footer">
                        <div className="max-w-4xl mx-auto flex flex-col gap-2">
                            {/* Quick Prompt Chips */}
                            {!isStarted && (
                                <div className="quick-chips">
                                    {[
                                        { icon: 'fa-chart-line', text: "Today's revenue" },
                                        { icon: 'fa-users',      text: 'New users this week' },
                                        { icon: 'fa-motorcycle', text: 'Active drivers now' },
                                    ].map(c => (
                                        <button key={c.text} className="chip" onClick={() => { setInput(c.text); sendMessage(c.text); }}>
                                            <i className={`fas ${c.icon} mr-1`}></i> {c.text}
                                        </button>
                                    ))}
                                </div>
                            )}
                            <div className="flex items-center gap-2 bg-white border border-slate-200 p-2 md:p-3 rounded-[30px] shadow-2xl shadow-indigo-200/50 input-bar-wrap transition-shadow focus-within:shadow-indigo-300/30">
                                <input 
                                    value={input} 
                                    onChange={e => setInput(e.target.value)} 
                                    onKeyDown={e => e.key === 'Enter' && sendMessage()}
                                    placeholder="Ask QOON Intelligence..." 
                                    className="flex-1 bg-transparent border-none outline-none px-3 py-2 text-[16px] text-slate-800 placeholder:text-slate-400"
                                />
                                <motion.button 
                                    whileTap={{ scale: 0.9 }}
                                    onClick={() => sendMessage()}
                                    className={`w-11 h-11 rounded-[20px] flex items-center justify-center transition-all flex-shrink-0 ${input.trim() ? 'bg-brand text-white shadow-lg shadow-brand/30' : 'bg-slate-100 text-slate-300'}`}
                                >
                                    <i className="fas fa-arrow-up"></i>
                                </motion.button>
                            </div>
                        </div>
                    </div>
                </div>
            )
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
        
        // Ensure loader stays long enough to be seen during React hydration if needed
        setTimeout(() => {
            document.getElementById('loader').classList.add('hidden');
        }, 800);
    </script>
</body>
</html>