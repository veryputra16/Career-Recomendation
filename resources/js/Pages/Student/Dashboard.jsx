import { Head, Link, usePage } from '@inertiajs/react';

export default function StudentDashboard() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Student Portal Placeholder" />

            <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-blue-500 selection:text-white">
                {/* Background Glows */}
                <div className="fixed inset-0 pointer-events-none overflow-hidden">
                    <div className="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
                    <div className="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-600/15 rounded-full blur-3xl"></div>
                </div>

                {/* Header */}
                <header className="relative z-10 border-b border-slate-800/80 bg-slate-900/40 backdrop-blur-md">
                    <div className="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-500/20">
                                ST
                            </div>
                            <span className="font-semibold tracking-tight text-slate-200">
                                Student Portal
                            </span>
                        </div>

                        <div className="flex items-center gap-4">
                            <span className="text-xs text-slate-400">
                                Signed in as <strong className="text-slate-200">{auth?.user?.name}</strong>
                            </span>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                className="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 text-xs font-medium transition-colors"
                            >
                                Sign Out
                            </Link>
                        </div>
                    </div>
                </header>

                {/* Main Content */}
                <main className="relative z-10 max-w-4xl mx-auto px-6 py-16 flex-1 flex flex-col items-center justify-center text-center">
                    <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-6">
                        Protected Student Route (/student)
                    </span>

                    <h1 className="text-3xl sm:text-4xl font-extrabold tracking-tight text-white mb-4">
                        Student Authorization Verified
                    </h1>

                    <p className="text-slate-400 max-w-xl mb-8 text-sm">
                        You have successfully authenticated with the <strong className="text-blue-400">student</strong> role. Server-side middleware (<code className="text-slate-300 font-mono">role:student</code>) has permitted access to your student dashboard.
                    </p>

                    <div className="p-5 rounded-xl bg-slate-900/60 border border-slate-800 text-left max-w-md w-full text-xs space-y-2 mb-8">
                        <div className="text-slate-400 font-semibold uppercase tracking-wider mb-2">
                            Student Profile Context
                        </div>
                        <div className="flex justify-between py-1 border-b border-slate-800">
                            <span className="text-slate-400">Student Name:</span>
                            <span className="text-slate-200">{auth?.user?.student?.full_name || auth?.user?.name}</span>
                        </div>
                        <div className="flex justify-between py-1 border-b border-slate-800">
                            <span className="text-slate-400">Student No:</span>
                            <span className="text-slate-200 font-mono">{auth?.user?.student?.student_number || 'N/A'}</span>
                        </div>
                        <div className="flex justify-between py-1 border-b border-slate-800">
                            <span className="text-slate-400">Major:</span>
                            <span className="text-slate-200">{auth?.user?.student?.major || 'N/A'}</span>
                        </div>
                        <div className="flex justify-between py-1">
                            <span className="text-slate-400">Account Role:</span>
                            <span className="text-blue-400 font-semibold uppercase">{auth?.user?.role}</span>
                        </div>
                    </div>

                    <Link
                        href="/"
                        className="text-xs text-slate-400 hover:text-slate-200 transition-colors"
                    >
                        ← Back to Home
                    </Link>
                </main>

                {/* Footer */}
                <footer className="relative z-10 border-t border-slate-800/80 bg-slate-900/40 backdrop-blur-md py-4 text-center text-xs text-slate-500">
                    <p>© 2026 AI Career Recommendation System • Student Zone</p>
                </footer>
            </div>
        </>
    );
}
