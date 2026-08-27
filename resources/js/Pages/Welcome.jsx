import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome({ appName, laravelVersion, phpVersion }) {
    const { auth } = usePage().props;

    const techStack = [
        { name: 'Backend', value: `Laravel ${laravelVersion}`, status: 'Connected' },
        { name: 'Runtime', value: `PHP ${phpVersion}`, status: 'Active' },
        { name: 'Adapter', value: 'Inertia.js v2', status: 'Ready' },
        { name: 'Frontend', value: 'React 19 + Vite', status: 'Mounted' },
    ];

    const destinationUrl = auth?.user?.role === 'admin' ? '/admin' : '/student';

    return (
        <>
            <Head title="AI Career Recommendation System" />
            <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-blue-500 selection:text-white">
                {/* Background Accent Gradients */}
                <div className="fixed inset-0 pointer-events-none overflow-hidden">
                    <div className="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
                    <div className="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl"></div>
                    <div className="absolute -bottom-40 left-1/3 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
                </div>

                {/* Header */}
                <header className="relative z-10 border-b border-slate-800/80 bg-slate-900/40 backdrop-blur-md">
                    <div className="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-500/20">
                                AI
                            </div>
                            <span className="font-semibold tracking-tight text-slate-200">
                                {appName || 'AI Career Recommendation'}
                            </span>
                        </div>

                        <div className="flex items-center gap-3">
                            {auth?.user ? (
                                <Link
                                    href={destinationUrl}
                                    className="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-lg shadow-blue-500/20 transition-all"
                                >
                                    Go to {auth.user.role === 'admin' ? 'Admin Panel' : 'Student Portal'} →
                                </Link>
                            ) : (
                                <Link
                                    href="/login"
                                    className="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 transition-all"
                                >
                                    Sign In
                                </Link>
                            )}
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="relative z-10 max-w-4xl mx-auto px-6 py-16 flex-1 flex flex-col items-center justify-center text-center">
                    <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-900/80 border border-slate-800 text-xs text-slate-400 mb-8 shadow-inner">
                        <span className="font-mono text-blue-400">Laravel</span>
                        <span>→</span>
                        <span className="font-mono text-indigo-400">Inertia</span>
                        <span>→</span>
                        <span className="font-mono text-cyan-400">React</span>
                        <span>→</span>
                        <span className="font-mono text-emerald-400">Vite</span>
                    </div>

                    <h1 className="text-4xl sm:text-5xl font-extrabold tracking-tight text-white mb-4">
                        AI Career Recommendation System
                    </h1>
                    
                    <p className="text-lg text-slate-400 max-w-2xl mx-auto mb-10">
                        Intelligent CV analysis, automated skill mapping, and data-driven career recommendations for university students.
                    </p>

                    <div className="flex items-center gap-4 mb-12">
                        {auth?.user ? (
                            <Link
                                href={destinationUrl}
                                className="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-xl shadow-blue-500/25 transition-all"
                            >
                                Open Dashboard ({auth.user.role})
                            </Link>
                        ) : (
                            <Link
                                href="/login"
                                className="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-xl shadow-blue-500/25 transition-all"
                            >
                                Sign In to Portal
                            </Link>
                        )}
                    </div>

                    {/* Stack Verification Grid */}
                    <div className="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12 text-left">
                        {techStack.map((tech) => (
                            <div
                                key={tech.name}
                                className="p-4 rounded-xl bg-slate-900/60 border border-slate-800 hover:border-slate-700 transition-colors shadow-sm"
                            >
                                <div className="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">
                                    {tech.name}
                                </div>
                                <div className="text-base font-semibold text-slate-200 mb-2">
                                    {tech.value}
                                </div>
                                <div className="flex items-center gap-1.5 text-xs text-emerald-400">
                                    <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>{tech.status}</span>
                                </div>
                            </div>
                        ))}
                    </div>
                </main>

                {/* Footer */}
                <footer className="relative z-10 border-t border-slate-800/80 bg-slate-900/40 backdrop-blur-md py-6 text-center text-xs text-slate-400">
                    <p>© 2026 AI Career Recommendation System • Student Career & Skill Intelligence Platform</p>
                </footer>
            </div>
        </>
    );
}
