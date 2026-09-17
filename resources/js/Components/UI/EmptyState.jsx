export default function EmptyState({
    title,
    description,
    icon: Icon = null,
    status = null,
    action = null,
    className = '',
}) {
    return (
        <div
            className={`p-6 sm:p-8 rounded-2xl bg-white border border-slate-200 text-center flex flex-col items-center justify-center shadow-xs ${className}`}
        >
            {Icon ? (
                <div className="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center mb-3">
                    <Icon className="w-6 h-6" />
                </div>
            ) : (
                <div className="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
            )}

            {status && (
                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200 mb-2">
                    {status}
                </span>
            )}

            <h3 className="text-sm font-semibold text-slate-800 mb-1">
                {title}
            </h3>

            <p className="text-xs text-slate-500 max-w-sm mb-4 leading-relaxed">
                {description}
            </p>

            {action && (
                <div className="mt-1">
                    {action}
                </div>
            )}
        </div>
    );
}
