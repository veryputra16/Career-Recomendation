import { useState, useRef } from 'react';
import { Head, useForm } from '@inertiajs/react';
import StudentLayout from '@/Layouts/StudentLayout';
import PageHeader from '@/Components/UI/PageHeader';
import EmptyState from '@/Components/UI/EmptyState';

export default function CVIndex({ currentCv = null, history = [] }) {
    const [dragOver, setDragOver] = useState(false);
    const [copied, setCopied] = useState(false);
    const fileInputRef = useRef(null);

    const { data, setData, post, processing, errors, reset, progress } = useForm({
        cv: null,
    });

    const handleFileChange = (file) => {
        if (file && file.type === 'application/pdf') {
            setData('cv', file);
        } else if (file) {
            alert('Please select a valid PDF document.');
        }
    };

    const handleDrop = (e) => {
        e.preventDefault();
        setDragOver(false);
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            handleFileChange(e.dataTransfer.files[0]);
        }
    };

    const handleDragOver = (e) => {
        e.preventDefault();
        setDragOver(true);
    };

    const handleDragLeave = (e) => {
        e.preventDefault();
        setDragOver(false);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!data.cv) return;

        post('/student/cv', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                reset('cv');
                if (fileInputRef.current) {
                    fileInputRef.current.value = '';
                }
            },
        });
    };

    const copyTextToClipboard = () => {
        if (currentCv?.extracted_text) {
            navigator.clipboard.writeText(currentCv.extracted_text);
            setCopied(true);
            setTimeout(() => setCopied(false), 2500);
        }
    };

    // Helper for formatting file size
    const formatBytes = (bytes) => {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    };

    // Calculate word count
    const wordCount = currentCv?.extracted_text
        ? currentCv.extracted_text.trim().split(/\s+/).filter(Boolean).length
        : 0;

    return (
        <StudentLayout title="CV Document & Text Extraction">
            <PageHeader
                title="CV Document & Text Extraction"
                subtitle="Upload your resume in PDF format to parse and extract your skills and career background."
                breadcrumbs={[
                    { label: 'Student', href: '/student' },
                    { label: 'CV Management' },
                ]}
            />

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
                {/* Left Column: Upload Form & Current Status (5 Cols) */}
                <div className="lg:col-span-5 space-y-6">
                    {/* Upload Card */}
                    <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                        <div className="flex items-center gap-3 pb-4 border-b border-slate-100 mb-5">
                            <div className="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                            </div>
                            <div>
                                <h2 className="text-base font-bold text-slate-900">
                                    {currentCv ? 'Upload New CV / Replace' : 'Upload Curriculum Vitae'}
                                </h2>
                                <p className="text-xs text-slate-500">PDF documents only • Max 5MB</p>
                            </div>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            {/* Drag & Drop Area */}
                            <div
                                onDrop={handleDrop}
                                onDragOver={handleDragOver}
                                onDragLeave={handleDragLeave}
                                onClick={() => fileInputRef.current?.click()}
                                className={`border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-colors ${
                                    dragOver
                                        ? 'border-blue-500 bg-blue-50/50'
                                        : data.cv
                                        ? 'border-emerald-300 bg-emerald-50/30'
                                        : 'border-slate-300 hover:border-slate-400 bg-slate-50/50'
                                }`}
                            >
                                <input
                                    ref={fileInputRef}
                                    type="file"
                                    accept=".pdf,application/pdf"
                                    onChange={(e) => handleFileChange(e.target.files?.[0])}
                                    className="hidden"
                                    id="cv-upload-input"
                                />

                                {data.cv ? (
                                    <div className="space-y-2">
                                        <div className="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center">
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p className="text-xs font-semibold text-slate-800 truncate max-w-xs mx-auto">
                                            {data.cv.name}
                                        </p>
                                        <p className="text-[11px] text-slate-500">
                                            {formatBytes(data.cv.size)} • Click or drop another to replace
                                        </p>
                                    </div>
                                ) : (
                                    <div className="space-y-2">
                                        <div className="w-10 h-10 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center">
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                        <p className="text-xs font-semibold text-slate-700">
                                            Click to select or drag and drop your PDF
                                        </p>
                                        <p className="text-[11px] text-slate-400">
                                            Adobe PDF document up to 5MB
                                        </p>
                                    </div>
                                )}
                            </div>

                            {progress && (
                                <div className="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div
                                        className="bg-blue-600 h-2 rounded-full transition-all duration-200"
                                        style={{ width: `${progress.percentage}%` }}
                                    />
                                </div>
                            )}

                            {errors.cv && (
                                <p className="text-xs text-red-600 font-medium">{errors.cv}</p>
                            )}

                            <button
                                type="submit"
                                disabled={!data.cv || processing}
                                id="upload-cv-button"
                                className="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-colors focus:outline-hidden disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Uploading & Extracting Text...
                                    </>
                                ) : (
                                    <>
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        {currentCv ? 'Upload & Replace CV' : 'Upload CV Document'}
                                    </>
                                )}
                            </button>
                        </form>
                    </div>

                    {/* Current CV Status Card */}
                    {currentCv ? (
                        <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                            <div className="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                                <h3 className="text-sm font-bold text-slate-900">Current Resume</h3>
                                <span
                                    className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full ${
                                        currentCv.status === 'processed'
                                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                            : currentCv.status === 'failed'
                                            ? 'bg-red-50 text-red-700 border border-red-200'
                                            : 'bg-blue-50 text-blue-700 border border-blue-200'
                                    }`}
                                >
                                    {currentCv.status === 'processed' ? 'Text Extracted' : currentCv.status}
                                </span>
                            </div>

                            <div className="space-y-3 text-xs">
                                <div className="flex items-center justify-between py-1 border-b border-slate-100">
                                    <span className="text-slate-500">File Name</span>
                                    <span className="font-semibold text-slate-800 truncate max-w-[180px]" title={currentCv.original_filename}>
                                        {currentCv.original_filename}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between py-1 border-b border-slate-100">
                                    <span className="text-slate-500">File Size</span>
                                    <span className="font-mono text-slate-700">{formatBytes(currentCv.file_size)}</span>
                                </div>
                                <div className="flex items-center justify-between py-1 border-b border-slate-100">
                                    <span className="text-slate-500">Uploaded On</span>
                                    <span className="text-slate-700">{currentCv.uploaded_at || 'N/A'}</span>
                                </div>
                                <div className="flex items-center justify-between py-1 border-b border-slate-100">
                                    <span className="text-slate-500">Word Count</span>
                                    <span className="font-semibold text-slate-800">{wordCount} words</span>
                                </div>
                            </div>

                            <div className="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a
                                    href={`/student/cv/${currentCv.id}/download`}
                                    className="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors"
                                >
                                    <svg className="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download File
                                </a>

                                <span className="text-[11px] text-slate-400">
                                    Stored securely
                                </span>
                            </div>
                        </div>
                    ) : (
                        <div className="bg-slate-50 rounded-2xl border border-slate-200 p-5 text-center text-xs text-slate-500">
                            No CV currently uploaded. Upload a document to start skill extraction.
                        </div>
                    )}

                    {/* Requirements Information Notice */}
                    <div className="bg-blue-50/60 rounded-2xl border border-blue-100 p-5 text-xs text-blue-900 space-y-2">
                        <div className="flex items-center gap-2 font-bold text-blue-950">
                            <svg className="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Extraction Tips</span>
                        </div>
                        <ul className="list-disc list-inside text-blue-800 text-[11px] space-y-1">
                            <li>Use clean, digital PDFs exported from Word, Google Docs, or Canva.</li>
                            <li>Scanned image PDFs without selectable text cannot be parsed.</li>
                            <li>Ensure document is not password protected.</li>
                            <li>The extracted text will feed directly into Phase 07 (AI Skill Extraction).</li>
                        </ul>
                    </div>
                </div>

                {/* Right Column: Extracted Text Viewer (7 Cols) */}
                <div className="lg:col-span-7">
                    <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col h-full min-h-[500px]">
                        <div className="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                            <div>
                                <h3 className="text-sm font-bold text-slate-900">Extracted Plain Text</h3>
                                <p className="text-xs text-slate-500">
                                    {currentCv?.extracted_text
                                        ? `${wordCount} words • ${currentCv.extracted_text.length} characters parsed`
                                        : 'Awaiting readable PDF extraction'}
                                </p>
                            </div>

                            {currentCv?.extracted_text && (
                                <button
                                    type="button"
                                    onClick={copyTextToClipboard}
                                    className="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors"
                                >
                                    {copied ? (
                                        <>
                                            <svg className="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span className="text-emerald-700">Copied!</span>
                                        </>
                                    ) : (
                                        <>
                                            <svg className="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                            Copy Text
                                        </>
                                    )}
                                </button>
                            )}
                        </div>

                        {currentCv?.extracted_text ? (
                            <div className="flex-1 bg-slate-50 rounded-xl border border-slate-200/80 p-4 font-mono text-xs text-slate-800 leading-relaxed overflow-y-auto max-h-[560px] whitespace-pre-wrap select-text">
                                {currentCv.extracted_text}
                            </div>
                        ) : currentCv && currentCv.status === 'failed' ? (
                            <div className="flex-1 flex flex-col items-center justify-center p-8 text-center bg-amber-50/30 rounded-xl border border-amber-200">
                                <div className="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-3">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h4 className="text-sm font-bold text-amber-900 mb-1">
                                    No Readable Text Found in Document
                                </h4>
                                <p className="text-xs text-amber-800 max-w-md leading-relaxed">
                                    The document was uploaded successfully, but our text extractor could not parse any character streams. This usually means the PDF consists of scanned images rather than selectable text.
                                </p>
                            </div>
                        ) : (
                            <div className="flex-1 flex items-center justify-center">
                                <EmptyState
                                    title="No Extracted Content"
                                    description="Upload your CV document using the form on the left to extract readable text content."
                                    status="Awaiting Document"
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Upload History Table */}
            {history.length > 0 && (
                <div className="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <h3 className="text-sm font-bold text-slate-900 mb-1">Upload History</h3>
                    <p className="text-xs text-slate-500 mb-4">Historical record of all curriculum vitae documents uploaded to your profile</p>

                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-xs">
                            <thead className="bg-slate-50 text-slate-500 font-semibold border-y border-slate-100 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th className="py-2.5 px-3">File Name</th>
                                    <th className="py-2.5 px-3">Uploaded Date</th>
                                    <th className="py-2.5 px-3">File Size</th>
                                    <th className="py-2.5 px-3">Status</th>
                                    <th className="py-2.5 px-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {history.map((item) => (
                                    <tr key={item.id} className="hover:bg-slate-50/50 transition-colors">
                                        <td className="py-3 px-3 font-semibold text-slate-800 flex items-center gap-2">
                                            <svg className="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <span className="truncate max-w-xs">{item.original_filename}</span>
                                        </td>
                                        <td className="py-3 px-3 text-slate-600">{item.uploaded_at}</td>
                                        <td className="py-3 px-3 font-mono text-slate-600">{formatBytes(item.file_size)}</td>
                                        <td className="py-3 px-3">
                                            <span
                                                className={`text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full ${
                                                    item.status === 'processed'
                                                        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                        : item.status === 'failed'
                                                        ? 'bg-red-50 text-red-700 border border-red-200'
                                                        : 'bg-blue-50 text-blue-700 border border-blue-200'
                                                }`}
                                            >
                                                {item.status}
                                            </span>
                                        </td>
                                        <td className="py-3 px-3 text-right">
                                            <a
                                                href={`/student/cv/${item.id}/download`}
                                                className="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors"
                                            >
                                                Download
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </StudentLayout>
    );
}
