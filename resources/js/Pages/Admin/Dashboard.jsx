import { Head, usePage } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import PageHeader from '@/Components/UI/PageHeader';
import StatCard from '@/Components/UI/StatCard';
import EmptyState from '@/Components/UI/EmptyState';

export default function AdminDashboard({ counts = {} }) {
    const { auth } = usePage().props;

    // Metric Icons
    const StudentIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    );

    const SkillIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
    );

    const CareerIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    );

    const JobIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
    );

    return (
        <AdminLayout title="Admin Dashboard">
            <PageHeader
                title="System Overview"
                subtitle={`Welcome back, ${auth?.user?.name || 'Administrator'}. Here is the current database and platform state.`}
                breadcrumbs={[
                    { label: 'Admin', href: '/admin' },
                    { label: 'Dashboard' },
                ]}
            />

            {/* Baseline Database Metrics Section */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <StatCard
                    title="Total Students"
                    value={counts.students ?? 0}
                    description="Registered student profiles in database"
                    icon={StudentIcon}
                    variant="purple"
                    badge="Live Data"
                />
                <StatCard
                    title="Total Skills"
                    value={counts.skills ?? 0}
                    description="Standardized technical skills catalog"
                    icon={SkillIcon}
                    variant="indigo"
                    badge="Live Data"
                />
                <StatCard
                    title="Career Paths"
                    value={counts.careers ?? 0}
                    description="Structured career recommendation targets"
                    icon={CareerIcon}
                    variant="blue"
                    badge="Live Data"
                />
                <StatCard
                    title="Job Vacancies"
                    value={counts.jobs ?? 0}
                    description="Industry & internship opportunities"
                    icon={JobIcon}
                    variant="emerald"
                    badge="Live Data"
                />
            </div>

            {/* Content Split: System Status & Roadmap Placeholders */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                {/* System Status Panel */}
                <div className="lg:col-span-1 bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <div className="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <h2 className="text-sm font-bold text-slate-900 tracking-tight">
                            Platform Status
                        </h2>
                        <span className="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Operational
                        </span>
                    </div>

                    <div className="space-y-3 text-xs">
                        <div className="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span className="text-slate-500">Framework</span>
                            <span className="font-semibold text-slate-800">Laravel 12 (PHP 8.2+)</span>
                        </div>
                        <div className="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span className="text-slate-500">Frontend Stack</span>
                            <span className="font-semibold text-slate-800">React 19 + Inertia v2</span>
                        </div>
                        <div className="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span className="text-slate-500">Authentication</span>
                            <span className="font-semibold text-slate-800">Session + Role Middleware</span>
                        </div>
                        <div className="flex items-center justify-between py-1.5 border-b border-slate-100">
                            <span className="text-slate-500">Database</span>
                            <span className="font-semibold text-slate-800">MySQL (15 Migrations)</span>
                        </div>
                        <div className="flex items-center justify-between py-1.5">
                            <span className="text-slate-500">Current Phase</span>
                            <span className="font-semibold text-purple-700">Phase 04 (Shell & Nav)</span>
                        </div>
                    </div>
                </div>

                {/* Management Modules (Roadmap Preview) */}
                <div className="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <div className="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div>
                            <h2 className="text-sm font-bold text-slate-900 tracking-tight">
                                Management Modules Roadmap
                            </h2>
                            <p className="text-xs text-slate-500 mt-0.5">
                                Feature modules scheduled for subsequent development phases
                            </p>
                        </div>
                        <span className="text-xs text-slate-400 font-medium">
                            Phases 05 – 12
                        </span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div className="flex items-center justify-between mb-1">
                                <span className="font-semibold text-slate-800">Student Profiles & CVs</span>
                                <span className="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200">
                                    Phase 05–06
                                </span>
                            </div>
                            <p className="text-slate-500 leading-relaxed text-[11px]">
                                Student roster management, profile inspection, and uploaded CV document handling.
                            </p>
                        </div>

                        <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div className="flex items-center justify-between mb-1">
                                <span className="font-semibold text-slate-800">Skills Catalog Editor</span>
                                <span className="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                                    Phase 06
                                </span>
                            </div>
                            <p className="text-slate-500 leading-relaxed text-[11px]">
                                Technical skill taxonomy, category assignment, and synonym normalization rules.
                            </p>
                        </div>

                        <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div className="flex items-center justify-between mb-1">
                                <span className="font-semibold text-slate-800">Career Recommendation Engine</span>
                                <span className="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                                    Phase 08
                                </span>
                            </div>
                            <p className="text-slate-500 leading-relaxed text-[11px]">
                                AI recommendation parameters, career skill weightings, and score calibration.
                            </p>
                        </div>

                        <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <div className="flex items-center justify-between mb-1">
                                <span className="font-semibold text-slate-800">Analytics & Reports</span>
                                <span className="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Phase 11–12
                                </span>
                            </div>
                            <p className="text-slate-500 leading-relaxed text-[11px]">
                                Aggregated student readiness metrics, skill gap distributions, and PDF exports.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Activity Stream Placeholder */}
            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <div className="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h2 className="text-sm font-bold text-slate-900 tracking-tight">
                        Recent Administrative Activity
                    </h2>
                    <span className="text-xs text-slate-400">
                        Audit Logging
                    </span>
                </div>

                <EmptyState
                    title="No activity records yet"
                    description="Audit logging and event tracking will populate automatically as administrative features and student operations become active in subsequent phases."
                    status="Activity Stream Placeholder"
                />
            </div>
        </AdminLayout>
    );
}
