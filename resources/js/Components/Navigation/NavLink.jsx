import { Link, usePage } from '@inertiajs/react';

export default function NavLink({
    href,
    active = null,
    children,
    icon: Icon = null,
    badge = null,
    className = '',
    disabled = false,
    ...props
}) {
    const { url } = usePage();

    // Determine active state: if explicitly provided, use it; otherwise match path
    const isActive = active !== null
        ? active
        : (href === url || (href !== '/' && url.startsWith(href)));

    if (disabled) {
        return (
            <div
                className={`flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg text-slate-400 cursor-not-allowed opacity-60 select-none ${className}`}
                title="Coming in a future phase"
            >
                <div className="flex items-center gap-3">
                    {Icon && <Icon className="w-5 h-5 text-slate-400 shrink-0" />}
                    <span>{children}</span>
                </div>
                {badge && (
                    <span className="text-[10px] font-semibold tracking-wide uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-500">
                        {badge}
                    </span>
                )}
            </div>
        );
    }

    return (
        <Link
            href={href}
            className={`group flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-all duration-150 ${
                isActive
                    ? 'bg-blue-50 text-blue-700 font-semibold shadow-xs'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
            } ${className}`}
            {...props}
        >
            <div className="flex items-center gap-3 min-w-0">
                {Icon && (
                    <Icon
                        className={`w-5 h-5 shrink-0 transition-colors ${
                            isActive
                                ? 'text-blue-600'
                                : 'text-slate-400 group-hover:text-slate-600'
                        }`}
                    />
                )}
                <span className="truncate">{children}</span>
            </div>
            {badge && (
                <span
                    className={`text-[10px] font-semibold tracking-wide uppercase px-2 py-0.5 rounded-full shrink-0 ${
                        isActive
                            ? 'bg-blue-200 text-blue-800'
                            : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'
                    }`}
                >
                    {badge}
                </span>
            )}
        </Link>
    );
}
