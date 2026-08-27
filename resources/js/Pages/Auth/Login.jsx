import { Head, useForm, Link } from '@inertiajs/react';
import { useEffect } from 'react';

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post('/login');
    };

    const fillDemoCredentials = (role) => {
        if (role === 'admin') {
            setData({
                email: 'admin@example.com',
                password: 'password',
                remember: false,
            });
        } else {
            setData({
                email: 'student@example.com',
                password: 'password',
                remember: false,
            });
        }
    };

    return (
        <>
            <Head title="Sign In — AI Career Recommendation System" />

            <div className="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between selection:bg-blue-500 selection:text-white">
                {/* Background Ambient Glows */}
                <div className="fixed inset-0 pointer-events-none overflow-hidden">
                    <div className="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
                    <div className="absolute top-1/3 -right-40 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl"></div>
                    <div className="absolute -bottom-40 left-1/3 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl"></div>
                </div>

                {/* Header */}
                <header className="relative z-10 border-b border-slate-800/80 bg-slate-900/40 backdrop-blur-md">
                    <div className="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                        <Link href="/" className="flex items-center gap-3 group">
                            <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-bold text-white shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform">
                                AI
                            </div>
                            <span className="font-semibold tracking-tight text-slate-200 group-hover:text-white transition-colors">
                                AI Career Recommendation
                            </span>
                        </Link>
                        <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                            Authentication
                        </span>
                    </div>
                </header>

                {/* Main Content */}
                <main className="relative z-10 max-w-md w-full mx-auto px-6 py-12 flex-1 flex flex-col justify-center">
                    <div className="bg-slate-900/80 border border-slate-800 rounded-2xl p-8 shadow-xl shadow-black/40 backdrop-blur-xl">
                        <div className="text-center mb-8">
                            <h1 className="text-2xl font-bold tracking-tight text-white mb-2">
                                Welcome Back
                            </h1>
                            <p className="text-sm text-slate-400">
                                Sign in to access your student profile or admin panel
                            </p>
                        </div>

                        {status && (
                            <div className="mb-6 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                                {status}
                            </div>
                        )}

                        {errors.email && (
                            <div className="mb-6 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-medium">
                                {errors.email}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label className="block text-xs font-medium uppercase tracking-wider text-slate-300 mb-1.5" htmlFor="email">
                                    Email Address
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    className="w-full px-3.5 py-2.5 rounded-lg bg-slate-950/70 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    placeholder="your.email@example.com"
                                    autoComplete="username"
                                    required
                                    onChange={(e) => setData('email', e.target.value)}
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-medium uppercase tracking-wider text-slate-300 mb-1.5" htmlFor="password">
                                    Password
                                </label>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    value={data.password}
                                    className="w-full px-3.5 py-2.5 rounded-lg bg-slate-950/70 border border-slate-700/80 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                                    placeholder="••••••••"
                                    autoComplete="current-password"
                                    required
                                    onChange={(e) => setData('password', e.target.value)}
                                />
                                {errors.password && (
                                    <p className="mt-1 text-xs text-red-400">{errors.password}</p>
                                )}
                            </div>

                            <div className="flex items-center justify-between">
                                <label className="flex items-center gap-2 cursor-pointer text-xs text-slate-400 hover:text-slate-300">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="rounded bg-slate-950 border-slate-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900"
                                    />
                                    <span>Remember session</span>
                                </label>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-2.5 px-4 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            >
                                {processing ? 'Authenticating...' : 'Sign In'}
                            </button>
                        </form>

                        {/* Demo Accounts Quick-Fill Helper */}
                        <div className="mt-8 pt-6 border-t border-slate-800">
                            <p className="text-xs text-slate-400 text-center mb-3">
                                Development Quick Login:
                            </p>
                            <div className="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    onClick={() => fillDemoCredentials('student')}
                                    className="px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-800 text-xs text-slate-300 border border-slate-700/60 transition-colors text-center"
                                >
                                    Fill Demo Student
                                </button>
                                <button
                                    type="button"
                                    onClick={() => fillDemoCredentials('admin')}
                                    className="px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-800 text-xs text-slate-300 border border-slate-700/60 transition-colors text-center"
                                >
                                    Fill Demo Admin
                                </button>
                            </div>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="relative z-10 border-t border-slate-800/80 bg-slate-900/40 backdrop-blur-md py-4 text-center text-xs text-slate-500">
                    <p>© 2026 AI Career Recommendation System • Authentication Layer</p>
                </footer>
            </div>
        </>
    );
}
