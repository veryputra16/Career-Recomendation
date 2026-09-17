import { useState, useRef, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function UserDropdown() {
    const { auth } = usePage().props;
    const [open, setOpen] = useState(false);
    const dropdownRef = useRef(null);

    const user = auth?.user;
    const role = user?.role || 'guest';
    const name = user?.name || 'User';
    const email = user?.email || '';
    const student = user?.student;

    // Close when clicking outside
    useEffect(() => {
        function handleClickOutside(event) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                setOpen(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    // Initials for avatar
    const initials = name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();

    const isStudent = role === 'student';

    return (
        <div className="relative" ref={dropdownRef}>
            <button
                type="button"
                onClick={() => setOpen(!open)}
                className="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 transition-colors focus:outline-hidden focus:ring-2 focus:ring-blue-500/20"
                aria-expanded={open}
                aria-haspopup="true"
                id="user-menu-button"
            >
                <div
                    className={`w-9 h-9 rounded-lg flex items-center justify-center text-xs font-bold text-white shadow-xs ${
                        isStudent
                            ? 'bg-gradient-to-br from-blue-600 to-cyan-600'
                            : 'bg-gradient-to-br from-purple-600 to-indigo-600'
                    }`}
                >
                    {initials || 'U'}
                </div>

                <div className="hidden md:flex flex-col text-left">
                    <span className="text-xs font-semibold text-slate-800 leading-tight truncate max-w-[140px]">
                        {name}
                    </span>
                    <span className="text-[11px] text-slate-500 capitalize leading-tight">
                        {role} {student?.student_number ? `• ${student.student_number}` : ''}
                    </span>
                </div>

                <svg
                    className={`w-4 h-4 text-slate-400 transition-transform duration-150 ${
                        open ? 'rotate-180' : ''
                    }`}
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </button>

            {open && (
                <div className="absolute right-0 mt-2 w-64 rounded-xl bg-white border border-slate-200 shadow-xl py-2 z-50 animate-in fade-in zoom-in-95 duration-100">
                    <div className="px-4 py-3 border-b border-slate-100">
                        <p className="text-xs font-semibold text-slate-800 truncate">{name}</p>
                        <p className="text-[11px] text-slate-500 truncate">{email}</p>
                        {student && (
                            <div className="mt-2 pt-2 border-t border-slate-100/80 text-[11px] text-slate-500 space-y-0.5">
                                {student.major && <p className="truncate">Major: <span className="font-medium text-slate-700">{student.major}</span></p>}
                                {student.student_number && <p className="font-mono text-slate-600">NIM: {student.student_number}</p>}
                            </div>
                        )}
                        <div className="mt-2 flex items-center">
                            <span
                                className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full tracking-wider ${
                                    isStudent
                                        ? 'bg-blue-50 text-blue-700 border border-blue-200'
                                        : 'bg-purple-50 text-purple-700 border border-purple-200'
                                }`}
                            >
                                {role} Account
                            </span>
                        </div>
                    </div>

                    <div className="pt-1">
                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="w-full text-left px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 flex items-center gap-2 transition-colors"
                        >
                            <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Sign Out
                        </Link>
                    </div>
                </div>
            )}
        </div>
    );
}
