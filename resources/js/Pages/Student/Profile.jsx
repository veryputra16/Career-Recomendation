import { Head, useForm, usePage } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import PageHeader from '@/Components/UI/PageHeader';

export default function StudentProfile({ student = {}, user = {} }) {
    const { data, setData, put, processing, errors, recentlySuccessful } = useForm({
        full_name: student?.full_name || '',
        email: user?.email || '',
        student_number: student?.student_number || '',
        university: student?.university || '',
        faculty: student?.faculty || '',
        major: student?.major || '',
        semester: student?.semester ?? '',
        graduation_year: student?.graduation_year ?? '',
        bio: student?.bio || '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put('/student/profile', {
            preserveScroll: true,
        });
    };

    // Calculate profile completeness percentage
    const profileFields = [
        data.full_name,
        data.email,
        data.student_number,
        data.university,
        data.faculty,
        data.major,
        data.semester,
        data.graduation_year,
        data.bio,
    ];
    const filledFieldsCount = profileFields.filter((val) => val !== '' && val !== null && val !== undefined).length;
    const completenessPercent = Math.round((filledFieldsCount / profileFields.length) * 100);

    return (
        <StudentLayout title="Student Profile">
            <PageHeader
                title="Student Profile"
                subtitle="View and update your personal and academic information for accurate career guidance."
                breadcrumbs={[
                    { label: 'Student', href: '/student' },
                    { label: 'Profile' },
                ]}
            />

            <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Main Form Fields (2 Cols) */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Section 1: Personal Details */}
                        <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <div className="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
                                <div className="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 className="text-base font-bold text-slate-900">Personal Information</h2>
                                    <p className="text-xs text-slate-500">Essential identity and contact data</p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {/* Full Name */}
                                <div className="sm:col-span-2">
                                    <label htmlFor="full_name" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Full Name <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="full_name"
                                        type="text"
                                        value={data.full_name}
                                        onChange={(e) => setData('full_name', e.target.value)}
                                        placeholder="e.g. Budi Santoso"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.full_name ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                        required
                                    />
                                    {errors.full_name && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.full_name}</p>
                                    )}
                                </div>

                                {/* Email Address */}
                                <div>
                                    <label htmlFor="email" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Email Address <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="student@example.com"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.email ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                        required
                                    />
                                    {errors.email && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.email}</p>
                                    )}
                                </div>

                                {/* Student ID Number (NIM) */}
                                <div>
                                    <label htmlFor="student_number" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Student ID Number (NIM)
                                    </label>
                                    <input
                                        id="student_number"
                                        type="text"
                                        value={data.student_number}
                                        onChange={(e) => setData('student_number', e.target.value)}
                                        placeholder="e.g. 210101234"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.student_number ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    />
                                    {errors.student_number && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.student_number}</p>
                                    )}
                                </div>

                                {/* Bio / Professional Summary */}
                                <div className="sm:col-span-2">
                                    <label htmlFor="bio" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Professional Summary / Bio
                                    </label>
                                    <textarea
                                        id="bio"
                                        rows={4}
                                        value={data.bio}
                                        onChange={(e) => setData('bio', e.target.value)}
                                        placeholder="Brief overview of your academic interests, career goals, or technical passions..."
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.bio ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    />
                                    <div className="flex items-center justify-between mt-1">
                                        <p className="text-[11px] text-slate-400">Brief summary displayed on your career profile</p>
                                        <p className="text-[11px] text-slate-400">{data.bio.length}/1000 characters</p>
                                    </div>
                                    {errors.bio && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.bio}</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Section 2: Academic Details */}
                        <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <div className="flex items-center gap-3 pb-4 border-b border-slate-100 mb-6">
                                <div className="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 className="text-base font-bold text-slate-900">Academic Background</h2>
                                    <p className="text-xs text-slate-500">University, faculty, major, and graduation roadmap</p>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {/* University */}
                                <div className="sm:col-span-2">
                                    <label htmlFor="university" className="block text-xs font-semibold text-slate-700 mb-1">
                                        University / Institution
                                    </label>
                                    <input
                                        id="university"
                                        type="text"
                                        value={data.university}
                                        onChange={(e) => setData('university', e.target.value)}
                                        placeholder="e.g. Universitas Indonesia"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.university ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    />
                                    {errors.university && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.university}</p>
                                    )}
                                </div>

                                {/* Faculty */}
                                <div>
                                    <label htmlFor="faculty" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Faculty
                                    </label>
                                    <input
                                        id="faculty"
                                        type="text"
                                        value={data.faculty}
                                        onChange={(e) => setData('faculty', e.target.value)}
                                        placeholder="e.g. Faculty of Computer Science"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.faculty ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    />
                                    {errors.faculty && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.faculty}</p>
                                    )}
                                </div>

                                {/* Major / Study Program */}
                                <div>
                                    <label htmlFor="major" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Major / Study Program
                                    </label>
                                    <input
                                        id="major"
                                        type="text"
                                        value={data.major}
                                        onChange={(e) => setData('major', e.target.value)}
                                        placeholder="e.g. Informatics / Computer Science"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.major ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    />
                                    {errors.major && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.major}</p>
                                    )}
                                </div>

                                {/* Current Semester */}
                                <div>
                                    <label htmlFor="semester" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Current Semester
                                    </label>
                                    <select
                                        id="semester"
                                        value={data.semester}
                                        onChange={(e) => setData('semester', e.target.value)}
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.semester ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    >
                                        <option value="">Select Semester</option>
                                        {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14].map((sem) => (
                                            <option key={sem} value={sem}>
                                                Semester {sem}
                                            </option>
                                        ))}
                                    </select>
                                    {errors.semester && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.semester}</p>
                                    )}
                                </div>

                                {/* Expected Graduation Year */}
                                <div>
                                    <label htmlFor="graduation_year" className="block text-xs font-semibold text-slate-700 mb-1">
                                        Expected Graduation Year
                                    </label>
                                    <input
                                        id="graduation_year"
                                        type="number"
                                        min="2000"
                                        max="2100"
                                        value={data.graduation_year}
                                        onChange={(e) => setData('graduation_year', e.target.value)}
                                        placeholder="e.g. 2026"
                                        className={`w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 transition-colors ${
                                            errors.graduation_year ? 'border-red-400 bg-red-50/20' : 'border-slate-200 bg-white'
                                        }`}
                                    />
                                    {errors.graduation_year && (
                                        <p className="text-xs text-red-600 mt-1 font-medium">{errors.graduation_year}</p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Save Action Banner */}
                        <div className="flex items-center justify-between pt-2">
                            <div className="flex items-center gap-2">
                                {recentlySuccessful && (
                                    <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200 animate-in fade-in duration-200">
                                        <svg className="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Changes saved successfully
                                    </span>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                id="save-profile-button"
                                className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-semibold shadow-md shadow-blue-500/20 transition-colors focus:outline-hidden focus:ring-2 focus:ring-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Saving...
                                    </>
                                ) : (
                                    <>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save Changes
                                    </>
                                )}
                            </button>
                        </div>
                    </div>

                    {/* Sidebar Overview / Summary (1 Col) */}
                    <div className="space-y-6">
                        {/* Profile Completeness Card */}
                        <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <h3 className="text-sm font-bold text-slate-900 mb-1">Profile Completeness</h3>
                            <p className="text-xs text-slate-500 mb-4">Complete your profile to maximize career matching precision</p>

                            <div className="space-y-2">
                                <div className="flex items-center justify-between text-xs font-semibold">
                                    <span className="text-slate-700">Completion</span>
                                    <span className="text-blue-600 font-bold">{completenessPercent}%</span>
                                </div>
                                <div className="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div
                                        className="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                        style={{ width: `${completenessPercent}%` }}
                                    />
                                </div>
                            </div>

                            <div className="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-600 space-y-2">
                                <div className="flex items-center justify-between">
                                    <span>Personal details</span>
                                    <span className={data.full_name && data.email ? 'text-emerald-600 font-bold' : 'text-slate-400'}>
                                        {data.full_name && data.email ? '✓ Done' : 'Pending'}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Student ID (NIM)</span>
                                    <span className={data.student_number ? 'text-emerald-600 font-bold' : 'text-slate-400'}>
                                        {data.student_number ? '✓ Done' : 'Optional'}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Academic Program</span>
                                    <span className={data.university && data.major ? 'text-emerald-600 font-bold' : 'text-slate-400'}>
                                        {data.university && data.major ? '✓ Done' : 'Pending'}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Graduation Roadmap</span>
                                    <span className={data.semester && data.graduation_year ? 'text-emerald-600 font-bold' : 'text-slate-400'}>
                                        {data.semester && data.graduation_year ? '✓ Done' : 'Pending'}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* Account Metadata Card */}
                        <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <h3 className="text-sm font-bold text-slate-900 mb-3">Account Security</h3>
                            <div className="space-y-3 text-xs">
                                <div className="flex items-center justify-between py-1 border-b border-slate-100">
                                    <span className="text-slate-500">Account Type</span>
                                    <span className="font-semibold text-blue-700 uppercase bg-blue-50 px-2 py-0.5 rounded text-[10px]">
                                        Student
                                    </span>
                                </div>
                                <div className="flex items-center justify-between py-1 border-b border-slate-100">
                                    <span className="text-slate-500">Account ID</span>
                                    <span className="font-mono text-slate-700">#{user?.id}</span>
                                </div>
                                <div className="flex items-center justify-between py-1">
                                    <span className="text-slate-500">Member Since</span>
                                    <span className="text-slate-700">{user?.created_at || 'Active'}</span>
                                </div>
                            </div>
                        </div>

                        {/* AI & Career Calibration Notice */}
                        <div className="bg-blue-50/60 rounded-2xl border border-blue-100 p-5 text-xs text-blue-900 space-y-2">
                            <div className="flex items-center gap-2 font-bold text-blue-950">
                                <svg className="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Why this data matters</span>
                            </div>
                            <p className="text-blue-800 leading-relaxed text-[11px]">
                                In subsequent phases (CV parsing and AI career matching), your university, major, and graduation year serve as baseline context to recommend relevant internship tracks and align skill gap analyses.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </StudentLayout>
    );
}
