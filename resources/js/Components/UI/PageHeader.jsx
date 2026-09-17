import { Link } from '@inertiajs/react';

export default function PageHeader({
    title,
    subtitle = null,
    breadcrumbs = [],
    actions = null,
    className = '',
}) {
    return (
        <div className={`mb-8 ${className}`}>
            {breadcrumbs.length > 0 && (
                <nav className="flex items-center gap-1.5 text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
                    {breadcrumbs.map((crumb, index) => (
                        <div key={index} className="flex items-center gap-1.5">
                            {index > 0 && <span className="text-slate-300">/</span>}
                            {crumb.href ? (
                                <Link
                                    href={crumb.href}
                                    className="hover:text-slate-900 transition-colors"
                                >
                                    {crumb.label}
                                </Link>
                            ) : (
                                <span className="font-medium text-slate-800">{crumb.label}</span>
                            )}
                        </div>
                    ))}
                </nav>
            )}

            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                        {title}
                    </h1>
                    {subtitle && (
                        <p className="mt-1 text-sm text-slate-500">
                            {subtitle}
                        </p>
                    )}
                </div>

                {actions && (
                    <div className="flex items-center gap-3 shrink-0">
                        {actions}
                    </div>
                )}
            </div>
        </div>
    );
}
