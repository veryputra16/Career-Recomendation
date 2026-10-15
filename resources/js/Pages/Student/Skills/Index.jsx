import { useState, useMemo } from 'react';
import { Head, Link } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import PageHeader from '@/Components/UI/PageHeader';
import StatCard from '@/Components/UI/StatCard';
import EmptyState from '@/Components/UI/EmptyState';

export default function SkillsIndex({ analysis, hasCv = false, latestCv = null }) {
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [searchQuery, setSearchQuery] = useState('');
    const [sourceFilter, setSourceFilter] = useState('all');

    // SVG Icons
    const TotalSkillsIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
    );

    const CategoryIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    );

    const ConfidenceIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
    );

    const TopDomainIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    );

    const SearchIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    );

    const CheckIcon = (props) => (
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" {...props}>
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
        </svg>
    );

    // Color definitions for category badges
    const getCategoryBadgeStyles = (categoryKey) => {
        const styles = {
            language: 'bg-blue-50 text-blue-700 border-blue-200',
            framework: 'bg-indigo-50 text-indigo-700 border-indigo-200',
            database: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            tool: 'bg-amber-50 text-amber-700 border-amber-200',
            cloud: 'bg-cyan-50 text-cyan-700 border-cyan-200',
            technical: 'bg-purple-50 text-purple-700 border-purple-200',
            soft_skill: 'bg-rose-50 text-rose-700 border-rose-200',
            other: 'bg-slate-50 text-slate-700 border-slate-200',
        };
        return styles[categoryKey] || styles.other;
    };

    const getCategoryProgressBarColor = (categoryKey) => {
        const colors = {
            language: 'bg-blue-500',
            framework: 'bg-indigo-500',
            database: 'bg-emerald-500',
            tool: 'bg-amber-500',
            cloud: 'bg-cyan-500',
            technical: 'bg-purple-500',
            soft_skill: 'bg-rose-500',
            other: 'bg-slate-400',
        };
        return colors[categoryKey] || colors.other;
    };

    // Filter skills by category, source, and search query
    const filteredSkills = useMemo(() => {
        if (!analysis?.skills) return [];

        return analysis.skills.filter((skill) => {
            const matchesCategory = selectedCategory === 'all' || skill.category === selectedCategory;
            const matchesSource = sourceFilter === 'all' || skill.source === sourceFilter;
            const matchesQuery = searchQuery.trim() === '' ||
                skill.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                (skill.description && skill.description.toLowerCase().includes(searchQuery.toLowerCase())) ||
                skill.category_label.toLowerCase().includes(searchQuery.toLowerCase());

            return matchesCategory && matchesSource && matchesQuery;
        });
    }, [analysis?.skills, selectedCategory, sourceFilter, searchQuery]);

    const totalSkills = analysis?.total_skills ?? 0;
    const categories = analysis?.categories ?? [];

    return (
        <StudentLayout title="My Skills Analysis">
            <PageHeader
                title="Skill Profile & Competency Analysis"
                subtitle="Examine your verified competencies, category distribution, and AI extraction confidence scores."
                breadcrumbs={[
                    { label: 'Student', href: '/student' },
                    { label: 'My Skills' },
                ]}
            />

            {totalSkills === 0 ? (
                /* Empty State */
                <div className="space-y-6">
                    <EmptyState
                        title="No Skills Identified Yet"
                        description={
                            hasCv
                                ? "Your CV is uploaded and ready in the system. Extract skills now using automated catalog matching."
                                : "Upload your curriculum vitae (PDF) to initiate automated skill extraction and build your competency profile."
                        }
                        status={hasCv ? "Ready for Extraction" : "Awaiting CV Upload"}
                        action={
                            <div className="flex flex-col sm:flex-row items-center justify-center gap-3 mt-2">
                                <Link
                                    href="/student/cv"
                                    className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 transition-colors shadow-xs"
                                >
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    {hasCv ? 'Go to CV Management' : 'Upload CV Document'}
                                </Link>
                                <Link
                                    href="/student"
                                    className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors shadow-xs"
                                >
                                    Return to Dashboard
                                </Link>
                            </div>
                        }
                    />

                    {/* Explanatory Info Card */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                        <h4 className="text-sm font-bold text-slate-900 mb-2">
                            How Skill Analysis Works
                        </h4>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-600 mt-4">
                            <div className="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <span className="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center">1</span>
                                <p className="font-semibold text-slate-900">1. Text Parsing & Extraction</p>
                                <p className="text-slate-500 text-[11px] leading-relaxed">
                                    Your uploaded CV is parsed locally, extracting education, project, and technical sections.
                                </p>
                            </div>
                            <div className="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <span className="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">2</span>
                                <p className="font-semibold text-slate-900">2. Catalog Normalization</p>
                                <p className="text-slate-500 text-[11px] leading-relaxed">
                                    Extracted competencies are normalized and matched against an industry standard skill catalog.
                                </p>
                            </div>
                            <div className="p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                                <span className="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center">3</span>
                                <p className="font-semibold text-slate-900">3. Profile & Scoring</p>
                                <p className="text-slate-500 text-[11px] leading-relaxed">
                                    Each skill receives an extraction confidence metric and is grouped into distinct technical domains.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            ) : (
                /* Skills Profile Content */
                <div className="space-y-8">
                    {/* Summary Metrics */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <StatCard
                            title="Total Identified Skills"
                            value={analysis.total_skills}
                            description="Unique competencies recorded"
                            icon={TotalSkillsIcon}
                            variant="indigo"
                            badge="Profile Active"
                        />
                        <StatCard
                            title="Competency Categories"
                            value={analysis.total_categories}
                            description="Distinct technical domains"
                            icon={CategoryIcon}
                            variant="blue"
                            badge="Grouped"
                        />
                        <StatCard
                            title="Average AI Confidence"
                            value={analysis.average_confidence_percentage !== null ? `${analysis.average_confidence_percentage}%` : 'N/A'}
                            description="Extraction accuracy confidence"
                            icon={ConfidenceIcon}
                            variant="purple"
                            badge={analysis.average_confidence_percentage >= 80 ? 'High' : 'Moderate'}
                        />
                        <StatCard
                            title="Primary Domain"
                            value={analysis.top_category ? analysis.top_category.label : 'N/A'}
                            description={
                                analysis.top_category
                                    ? `${analysis.top_category.count} skills (${analysis.top_category.percentage}% of skillset)`
                                    : 'No primary domain'
                            }
                            icon={TopDomainIcon}
                            variant="emerald"
                            badge="Top Category"
                        />
                    </div>

                    {/* Category Distribution Card */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                            <div>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Skillset Category Breakdown
                                </h3>
                                <p className="text-xs text-slate-500 mt-0.5">
                                    Distribution of your competencies across engineering and professional domains.
                                </p>
                            </div>
                            <span className="text-[11px] font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200 self-start sm:self-auto">
                                {totalSkills} total skills mapped
                            </span>
                        </div>

                        {/* Multi-segment Distribution Bar */}
                        <div className="mt-5 space-y-2">
                            <div className="h-3 w-full rounded-full bg-slate-100 overflow-hidden flex">
                                {categories.map((cat) => (
                                    <div
                                        key={cat.key}
                                        style={{ width: `${cat.percentage}%` }}
                                        className={`${getCategoryProgressBarColor(cat.key)} transition-all duration-300 relative group`}
                                        title={`${cat.label}: ${cat.count} skills (${cat.percentage}%)`}
                                    />
                                ))}
                            </div>

                            {/* Category Badges Grid */}
                            <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-3">
                                {categories.map((cat) => {
                                    const isSelected = selectedCategory === cat.key;
                                    return (
                                        <button
                                            key={cat.key}
                                            type="button"
                                            onClick={() => setSelectedCategory(isSelected ? 'all' : cat.key)}
                                            className={`text-left p-3 rounded-xl border transition-all duration-150 ${
                                                isSelected
                                                    ? 'bg-blue-50/80 border-blue-300 ring-2 ring-blue-500/20'
                                                    : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/60'
                                            }`}
                                        >
                                            <div className="flex items-center justify-between gap-1 mb-1">
                                                <span className="text-xs font-bold text-slate-800 truncate">
                                                    {cat.label}
                                                </span>
                                                <span className="text-[10px] font-mono font-bold text-slate-500">
                                                    {cat.count}
                                                </span>
                                            </div>
                                            <div className="flex items-center justify-between text-[11px] text-slate-500">
                                                <span>{cat.percentage}%</span>
                                                {cat.average_confidence_percentage && (
                                                    <span className="text-[10px] text-indigo-600 font-semibold">
                                                        {cat.average_confidence_percentage}% conf.
                                                    </span>
                                                )}
                                            </div>
                                        </button>
                                    );
                                })}
                            </div>
                        </div>
                    </div>

                    {/* Skill Explorer & Catalog View */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                        {/* Section Header with Search and Source Controls */}
                        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                            <div>
                                <h3 className="text-sm font-bold text-slate-900">
                                    Identified Skills Explorer
                                </h3>
                                <p className="text-xs text-slate-500 mt-0.5">
                                    Filter and inspect individual competencies, verification sources, and extraction metrics.
                                </p>
                            </div>

                            <div className="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                {/* Search Bar */}
                                <div className="relative">
                                    <SearchIcon className="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                    <input
                                        type="text"
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        placeholder="Search skills..."
                                        className="pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white placeholder-slate-400 focus:outline-hidden focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 w-full sm:w-56"
                                    />
                                    {searchQuery && (
                                        <button
                                            type="button"
                                            onClick={() => setSearchQuery('')}
                                            className="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs"
                                        >
                                            ✕
                                        </button>
                                    )}
                                </div>

                                {/* Source Filter */}
                                <select
                                    value={sourceFilter}
                                    onChange={(e) => setSourceFilter(e.target.value)}
                                    aria-label="Filter skills by source"
                                    className="text-xs rounded-xl border border-slate-200 bg-white py-1.5 pl-3 pr-8 text-slate-700 focus:outline-hidden focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                                >
                                    <option value="all">All Sources</option>
                                    <option value="cv_ai">AI Extracted ({analysis.source_breakdown?.cv_ai ?? 0})</option>
                                    <option value="manual">Manual Entry ({analysis.source_breakdown?.manual ?? 0})</option>
                                </select>
                            </div>
                        </div>

                        {/* Category Filter Pills */}
                        <div className="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                            <button
                                type="button"
                                onClick={() => setSelectedCategory('all')}
                                className={`px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap transition-colors ${
                                    selectedCategory === 'all'
                                        ? 'bg-slate-900 text-white shadow-xs'
                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                }`}
                            >
                                All Skills ({totalSkills})
                            </button>
                            {categories.map((cat) => {
                                const isSelected = selectedCategory === cat.key;
                                return (
                                    <button
                                        key={cat.key}
                                        type="button"
                                        onClick={() => setSelectedCategory(cat.key)}
                                        className={`px-3 py-1.5 rounded-xl font-semibold whitespace-nowrap transition-colors ${
                                            isSelected
                                                ? 'bg-blue-600 text-white shadow-xs'
                                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                        }`}
                                    >
                                        {cat.label} ({cat.count})
                                    </button>
                                );
                            })}
                        </div>

                        {/* Skills Grid */}
                        {filteredSkills.length === 0 ? (
                            <div className="p-8 text-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                                <p className="text-xs font-semibold text-slate-700">
                                    No skills found matching your filters.
                                </p>
                                <p className="text-[11px] text-slate-400 mt-1">
                                    Try clearing your search query or selecting a different category.
                                </p>
                                {(searchQuery || selectedCategory !== 'all' || sourceFilter !== 'all') && (
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setSearchQuery('');
                                            setSelectedCategory('all');
                                            setSourceFilter('all');
                                        }}
                                        className="mt-3 text-xs font-semibold text-blue-600 hover:text-blue-800"
                                    >
                                        Reset All Filters
                                    </button>
                                )}
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                {filteredSkills.map((skill) => (
                                    <div
                                        key={skill.id}
                                        className="bg-white rounded-xl border border-slate-200 p-4 hover:border-slate-300 hover:shadow-xs transition-all duration-150 flex flex-col justify-between"
                                    >
                                        <div>
                                            {/* Header: Skill Name & Category Badge */}
                                            <div className="flex items-start justify-between gap-2 mb-2">
                                                <h4 className="text-sm font-bold text-slate-900 tracking-tight">
                                                    {skill.name}
                                                </h4>
                                                <span
                                                    className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-md border tracking-wider shrink-0 ${getCategoryBadgeStyles(
                                                        skill.category
                                                    )}`}
                                                >
                                                    {skill.category_label}
                                                </span>
                                            </div>

                                            {/* Description if present */}
                                            {skill.description && (
                                                <p className="text-[11px] text-slate-500 line-clamp-2 mb-3 leading-relaxed">
                                                    {skill.description}
                                                </p>
                                            )}
                                        </div>

                                        {/* Footer Metadata: Confidence & Source */}
                                        <div className="pt-3 border-t border-slate-100 flex items-center justify-between gap-2 text-[11px]">
                                            {/* Confidence Score Pill */}
                                            {skill.confidence_percentage !== null ? (
                                                <div className="flex items-center gap-1.5 text-indigo-700">
                                                    <div className="w-1.5 h-1.5 rounded-full bg-indigo-600" />
                                                    <span className="font-semibold font-mono">
                                                        {skill.confidence_percentage}%
                                                    </span>
                                                    <span className="text-[10px] text-slate-400">
                                                        confidence
                                                    </span>
                                                </div>
                                            ) : (
                                                <span className="text-[10px] text-slate-400">
                                                    Unscored
                                                </span>
                                            )}

                                            {/* Source Badge */}
                                            <span
                                                className={`text-[10px] font-medium px-2 py-0.5 rounded-full ${
                                                    skill.source === 'cv_ai'
                                                        ? 'bg-blue-50 text-blue-700 border border-blue-100'
                                                        : 'bg-slate-100 text-slate-600 border border-slate-200'
                                                }`}
                                            >
                                                {skill.source_label}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {/* Footer Re-Extraction Action Prompt */}
                        <div className="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div className="flex items-center gap-2">
                                <div className="p-1.5 rounded-lg bg-blue-100 text-blue-700">
                                    <CheckIcon className="w-4 h-4" />
                                </div>
                                <div>
                                    <p className="font-semibold text-slate-900">
                                        Need to update or add new competencies?
                                    </p>
                                    <p className="text-slate-500 text-[11px]">
                                        Upload an updated resume in CV Management to refresh your skill extraction profile.
                                    </p>
                                </div>
                            </div>
                            <Link
                                href="/student/cv"
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-colors shadow-xs shrink-0 self-start sm:self-auto"
                            >
                                Manage CV
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </StudentLayout>
    );
}
