import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import NavLink from '@/Components/Navigation/NavLink';
import UserDropdown from '@/Components/Navigation/UserDropdown';
import FlashMessage from '@/Components/UI/FlashMessage';

export default function AdminLayout({ title = 'Admin Portal', children }) {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    // Nav icons
    const DashboardIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
    );

    const StudentsIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );

    const SkillsIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
    );

    const CareersIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    );

    const JobsIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
    );

    const ReportsIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
    );

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900 flex flex-col antialiased">
            <Head title={title} />

            {/* Mobile Sidebar Overlay */}
            {mobileMenuOpen && (
                <div
                    className="fixed inset-0 bg-slate-900/50 z-40 lg:hidden backdrop-blur-xs transition-opacity"
                    onClick={() => setMobileMenuOpen(false)}
                />
            )}

            <div className="flex flex-1">
                {/* Sidebar */}
                <aside
                    className={`fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto ${
                        mobileMenuOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full'
                    }`}
                >
                    <div>
                        {/* Brand Logo & Title */}
                        <div className="h-16 px-6 border-b border-slate-100 flex items-center justify-between">
                            <Link href="/admin" className="flex items-center gap-2.5">
                                <div className="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center font-bold text-white text-sm shadow-md shadow-purple-500/20">
                                    AD
                                </div>
                                <div className="flex flex-col">
                                    <span className="font-bold text-sm tracking-tight text-slate-900">
                                        Admin Center
                                    </span>
                                    <span className="text-[10px] uppercase font-semibold tracking-wider text-purple-600">
                                        Career Platform
                                    </span>
                                </div>
                            </Link>

                            <button
                                type="button"
                                onClick={() => setMobileMenuOpen(false)}
                                className="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100"
                                aria-label="Close menu"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {/* Navigation Links */}
                        <div className="px-3 py-4 space-y-6">
                            <div>
                                <div className="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Overview
                                </div>
                                <div className="space-y-1">
                                    <NavLink href="/admin" icon={DashboardIcon}>
                                        Dashboard
                                    </NavLink>
                                </div>
                            </div>

                            <div>
                                <div className="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Management (Roadmap)
                                </div>
                                <div className="space-y-1">
                                    <NavLink href="#" icon={StudentsIcon} disabled badge="Phase 06">
                                        Students
                                    </NavLink>
                                    <NavLink href="#" icon={SkillsIcon} disabled badge="Phase 06">
                                        Skills Catalog
                                    </NavLink>
                                    <NavLink href="#" icon={CareersIcon} disabled badge="Phase 08">
                                        Career Paths
                                    </NavLink>
                                    <NavLink href="#" icon={JobsIcon} disabled badge="Phase 10">
                                        Job Vacancies
                                    </NavLink>
                                    <NavLink href="#" icon={ReportsIcon} disabled badge="Phase 12">
                                        Reports & Analytics
                                    </NavLink>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Sidebar Footer Info */}
                    <div className="p-4 border-t border-slate-100">
                        <div className="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p className="text-[11px] font-semibold text-slate-700">
                                System Foundation
                            </p>
                            <p className="text-[10px] text-slate-500 mt-0.5">
                                Phase 04 Active • Shell & Navigation
                            </p>
                        </div>
                    </div>
                </aside>

                {/* Main Content Area */}
                <div className="flex-1 flex flex-col min-w-0">
                    {/* Topbar */}
                    <header className="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                onClick={() => setMobileMenuOpen(true)}
                                className="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-hidden"
                                aria-label="Open sidebar menu"
                                id="mobile-sidebar-toggle"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div className="hidden sm:flex items-center gap-2">
                                <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
                                    Admin Workspace
                                </span>
                            </div>
                        </div>

                        {/* Topbar Right Area */}
                        <div className="flex items-center gap-3">
                            <UserDropdown />
                        </div>
                    </header>

                    {/* Page Content */}
                    <main className="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                        <FlashMessage />
                        {children}
                    </main>

                    {/* App Shell Footer */}
                    <footer className="border-t border-slate-200 bg-white py-4 px-6 text-center text-xs text-slate-500">
                        <p>© 2026 AI Career Recommendation System • Administrator Portal</p>
                    </footer>
                </div>
            </div>
        </div>
    );
}
