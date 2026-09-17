export default function StatCard({
    title,
    value,
    description = null,
    icon: Icon = null,
    badge = null,
    variant = 'default',
    className = '',
}) {
    const variantStyles = {
        default: {
            iconBg: 'bg-slate-100 text-slate-700',
            badgeBg: 'bg-slate-100 text-slate-600',
            border: 'border-slate-200',
        },
        blue: {
            iconBg: 'bg-blue-50 text-blue-600',
            badgeBg: 'bg-blue-50 text-blue-700 border border-blue-100',
            border: 'border-slate-200',
        },
        indigo: {
            iconBg: 'bg-indigo-50 text-indigo-600',
            badgeBg: 'bg-indigo-50 text-indigo-700 border border-indigo-100',
            border: 'border-slate-200',
        },
        emerald: {
            iconBg: 'bg-emerald-50 text-emerald-600',
            badgeBg: 'bg-emerald-50 text-emerald-700 border border-emerald-100',
            border: 'border-slate-200',
        },
        purple: {
            iconBg: 'bg-purple-50 text-purple-600',
            badgeBg: 'bg-purple-50 text-purple-700 border border-purple-100',
            border: 'border-slate-200',
        },
        amber: {
            iconBg: 'bg-amber-50 text-amber-600',
            badgeBg: 'bg-amber-50 text-amber-700 border border-amber-100',
            border: 'border-slate-200',
        },
    };

    const currentStyle = variantStyles[variant] || variantStyles.default;

    return (
        <div
            className={`bg-white rounded-2xl border ${currentStyle.border} p-5 shadow-xs transition-shadow duration-150 hover:shadow-md ${className}`}
        >
            <div className="flex items-start justify-between">
                <div className="space-y-1">
                    <p className="text-xs font-medium uppercase tracking-wider text-slate-500">
                        {title}
                    </p>
                    <div className="flex items-baseline gap-2">
                        <span className="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                            {value}
                        </span>
                        {badge && (
                            <span
                                className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full tracking-wide ${currentStyle.badgeBg}`}
                            >
                                {badge}
                            </span>
                        )}
                    </div>
                </div>

                {Icon && (
                    <div className={`p-2.5 rounded-xl ${currentStyle.iconBg} shrink-0`}>
                        <Icon className="w-5 h-5" />
                    </div>
                )}
            </div>

            {description && (
                <p className="mt-3 text-xs text-slate-500 border-t border-slate-100 pt-2.5 flex items-center gap-1.5">
                    {description}
                </p>
            )}
        </div>
    );
}
