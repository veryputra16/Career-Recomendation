import { usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function FlashMessage() {
    const { flash } = usePage().props;
    const [visible, setVisible] = useState(false);
    const [message, setMessage] = useState({ type: '', text: '' });

    useEffect(() => {
        if (flash?.success) {
            setMessage({ type: 'success', text: flash.success });
            setVisible(true);
        } else if (flash?.error) {
            setMessage({ type: 'error', text: flash.error });
            setVisible(true);
        } else if (flash?.status) {
            setMessage({ type: 'status', text: flash.status });
            setVisible(true);
        } else {
            setVisible(false);
        }
    }, [flash]);

    if (!visible || !message.text) return null;

    const styles = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        status: 'bg-blue-50 border-blue-200 text-blue-800',
    };

    const icons = {
        success: (
            <svg className="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        ),
        error: (
            <svg className="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        ),
        status: (
            <svg className="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        ),
    };

    return (
        <div
            className={`mb-6 p-4 rounded-xl border flex items-center justify-between text-sm shadow-xs animate-in fade-in slide-in-from-top-2 duration-200 ${
                styles[message.type] || styles.status
            }`}
            role="alert"
        >
            <div className="flex items-center gap-3">
                {icons[message.type] || icons.status}
                <span className="font-medium">{message.text}</span>
            </div>

            <button
                type="button"
                onClick={() => setVisible(false)}
                className="text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors"
                aria-label="Dismiss message"
            >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    );
}
