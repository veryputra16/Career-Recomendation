import { Head, usePage } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import PageHeader from '@/Components/UI/PageHeader';
import StatCard from '@/Components/UI/StatCard';
import EmptyState from '@/Components/UI/EmptyState';

export default function StudentDashboard({ stats = {} }) {
    const { auth } = usePage().props;
    const student = auth?.user?.student;

    // Icons
    const CvIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
    );

    const SkillIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
    );

    const CareerIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    );

    const JobIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    );

    return (
        <StudentLayout title="Student Dashboard">
            <PageHeader
                title="Career Readiness Hub"
                subtitle={`Welcome, ${student?.full_name || auth?.user?.name || 'Student'}. Monitor your career progression, skill analytics, and recommendations.`}
                breadcrumbs={[
                    { label: 'Student', href: '/student' },
                    { label: 'Dashboard' },
                ]}
            />

            {/* Student Profile Quick Banner */}
            <div className="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-6 text-white shadow-md mb-8">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="flex items-center gap-4">
                        <div className="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center font-bold text-xl text-white shadow-inner">
                            {(student?.full_name || auth?.user?.name || 'S').charAt(0)}
                        </div>
                        <div>
                            <div className="flex items-center gap-2">
                                <h2 className="text-lg sm:text-xl font-bold tracking-tight">
                                    {student?.full_name || auth?.user?.name}
                                </h2>
                                <span className="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-blue-500/30 border border-blue-400/30">
                                    Student Profile
                                </span>
                            </div>
                            <p className="text-xs text-blue-100 mt-1">
                                {student?.major || 'Undergraduate'} • NIM: {student?.student_number || 'N/A'} • {auth?.user?.email}
                            </p>
                        </div>
                    </div>

                    <div className="flex sm:flex-col items-start sm:items-end justify-between border-t sm:border-t-0 border-white/10 pt-3 sm:pt-0">
                        <span className="text-[11px] text-blue-200 uppercase tracking-wider font-semibold">
                            Analysis Status
                        </span>
                        <span className="text-xs font-semibold px-2.5 py-1 rounded-full bg-white/15 border border-white/20 mt-1">
                            {stats.has_cv ? 'CV Under Review' : 'Awaiting CV Upload'}
                        </span>
                    </div>
                </div>
            </div>

            {/* Metric Summary Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <StatCard
                    title="CV Uploads"
                    value={stats.cv_count ?? 0}
                    description={stats.has_cv ? 'Latest CV on file' : 'No CV document uploaded'}
                    icon={CvIcon}
                    variant="blue"
                    badge={stats.has_cv ? 'Uploaded' : 'Pending'}
                />
                <StatCard
                    title="Identified Skills"
                    value={stats.skills_count ?? 0}
                    description="Extracted technical competencies"
                    icon={SkillIcon}
                    variant="indigo"
                    badge="Phase 07"
                />
                <StatCard
                    title="Career Recommendations"
                    value={stats.recommendations_count ?? 0}
                    description="AI-matched target careers"
                    icon={CareerIcon}
                    variant="purple"
                    badge="Phase 08"
                />
                <StatCard
                    title="Job & Internship Matches"
                    value={stats.job_matches_count ?? 0}
                    description="Opportunities matched to profile"
                    icon={JobIcon}
                    variant="emerald"
                    badge="Phase 10"
                />
            </div>

            {/* 4-Step Career Navigation Workflow Sections */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {/* CV Status Section */}
                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div className="flex items-center gap-2">
                                <span className="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center">
                                    1
                                </span>
                                <h3 className="text-sm font-bold text-slate-900">
                                    CV Status
                                </h3>
                            </div>
                            <span className="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                Phase 05
                            </span>
                        </div>

                        {stats.has_cv && stats.latest_cv ? (
                            <div className="p-4 rounded-xl bg-blue-50/60 border border-blue-100 text-xs space-y-2">
                                <p className="font-semibold text-blue-900">Current Resume</p>
                                <p className="text-slate-700 font-mono">{stats.latest_cv.file_name}</p>
                                <p className="text-slate-500 text-[11px]">Uploaded: {stats.latest_cv.uploaded_at}</p>
                            </div>
                        ) : (
                            <EmptyState
                                title="No CV uploaded yet"
                                description="Upload your curriculum vitae in PDF format to initiate the automated career evaluation pipeline."
                                status="Pending Upload"
                            />
                        )}
                    </div>

                    <p className="mt-4 text-[11px] text-slate-400 text-center">
                        CV upload and text extraction will be enabled in Phase 05.
                    </p>
                </div>

                {/* Skills Analysis Section */}
                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div className="flex items-center gap-2">
                                <span className="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                                    2
                                </span>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Skills Identified
                                </h3>
                            </div>
                            <span className="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                Phase 07
                            </span>
                        </div>

                        <EmptyState
                            title="No skills analyzed yet"
                            description="Skills will be automatically extracted, categorized, and scored by AI once your CV is uploaded and processed."
                            status="Awaiting Analysis"
                        />
                    </div>

                    <p className="mt-4 text-[11px] text-slate-400 text-center">
                        AI skill extraction and normalization will activate in Phase 07.
                    </p>
                </div>

                {/* Career Recommendations Section */}
                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div className="flex items-center gap-2">
                                <span className="w-6 h-6 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center">
                                    3
                                </span>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Career Recommendations
                                </h3>
                            </div>
                            <span className="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                Phase 08
                            </span>
                        </div>

                        <EmptyState
                            title="No career recommendations yet"
                            description="Complete your CV analysis to receive personalized career recommendations with skill match percentages and gap analysis."
                            status="Pending Workflow"
                        />
                    </div>

                    <p className="mt-4 text-[11px] text-slate-400 text-center">
                        AI career recommendations and gap analysis will activate in Phase 08.
                    </p>
                </div>

                {/* Job Matches Section */}
                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div className="flex items-center gap-2">
                                <span className="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center">
                                    4
                                </span>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Job & Internship Matches
                                </h3>
                            </div>
                            <span className="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                Phase 10
                            </span>
                        </div>

                        <EmptyState
                            title="No job matches available yet"
                            description="Matching industry vacancies and internship opportunities will appear here based on your verified skill profile."
                            status="Pending Matching"
                        />
                    </div>

                    <p className="mt-4 text-[11px] text-slate-400 text-center">
                        Job and internship matching algorithms will activate in Phase 10.
                    </p>
                </div>
            </div>

            {/* How It Works Guide Section */}
            <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <h3 className="text-sm font-bold text-slate-900 mb-4">
                    How The AI Career Recommendation Journey Works
                </h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div className="font-semibold text-slate-800 mb-1">1. Upload Resume</div>
                        <p className="text-slate-500 leading-relaxed text-[11px]">
                            Submit your CV in PDF format for secure, automated text extraction.
                        </p>
                    </div>
                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div className="font-semibold text-slate-800 mb-1">2. Skill Extraction</div>
                        <p className="text-slate-500 leading-relaxed text-[11px]">
                            AI extracts, parses, and normalizes your technical and soft skill profile.
                        </p>
                    </div>
                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div className="font-semibold text-slate-800 mb-1">3. Career Matching</div>
                        <p className="text-slate-500 leading-relaxed text-[11px]">
                            Calculates career readiness scores, match explanations, and skill gap insights.
                        </p>
                    </div>
                    <div className="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div className="font-semibold text-slate-800 mb-1">4. Opportunities</div>
                        <p className="text-slate-500 leading-relaxed text-[11px]">
                            Get tailored internship and job vacancies matching your unique capabilities.
                        </p>
                    </div>
                </div>
            </div>
        </StudentLayout>
    );
}
