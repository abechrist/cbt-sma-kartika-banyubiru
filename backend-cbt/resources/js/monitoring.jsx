import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import React, { useEffect, useReducer, useState } from 'react';
import { createRoot } from 'react-dom/client';

function bootEcho() {
    const cfg = window.__REVERB__ || window.__ECHO__ || {};

    window.Pusher = Pusher;

    // Mode Pusher (production split domain)
    if (window.__PUSHER_APP_KEY__) {
        return new Echo({
            broadcaster: 'pusher',
            key: window.__PUSHER_APP_KEY__,
            cluster: window.__PUSHER_APP_CLUSTER__ ?? 'ap1',
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
            },
        });
    }

    // Fallback Reverb (local development)
    if (!cfg) return null;

    return new Echo({
        broadcaster: 'reverb',
        key: cfg.appKey,
        wsHost: cfg.host,
        wsPort: cfg.port,
        wssPort: cfg.tlsPort ?? cfg.port,
        forceTLS: cfg.scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            },
        },
    });
}

const OFFLINE_AFTER_MS = 65_000;

const STATUS_LABELS = {
    not_started: 'Belum mulai',
    in_progress: 'Mengerjakan',
    submitted: 'Selesai',
    auto_submitted: 'Auto-submit',
    expired: 'Daluwarsa',
    cancelled: 'Dibatalkan',
};

const STATUS_COLORS = {
    not_started: 'bg-slate-100 text-slate-700 border border-slate-200',
    in_progress: 'bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold',
    submitted: 'bg-brand-100 text-brand-900 border border-brand-200 font-semibold',
    auto_submitted: 'bg-amber-100 text-amber-900 border border-amber-200',
    expired: 'bg-rose-100 text-rose-800 border border-rose-200',
    cancelled: 'bg-slate-200 text-slate-600',
};

function participantsReducer(state, action) {
    switch (action.type) {
        case 'REPLACE':
            return [...action.participants];
        case 'UPSERT': {
            const next = [...state];
            const idx = next.findIndex((p) => p.attempt_id === action.data.attempt_id);
            const entry = {
                attempt_id: action.data.attempt_id,
                user_id: action.data.user_id,
                student_name: action.data.student_name,
                status: action.data.status,
                progress: action.data.progress ?? 0,
                answers_count: action.data.answers_count ?? 0,
                last_seen_at: action.data.last_seen_at,
                submitted_at: action.data.submitted_at,
                suspicious_flags: action.data.suspicious_flags ?? 0,
                countdownTo: action.data.time_remaining != null
                    ? Date.now() + action.data.time_remaining * 1000
                    : null,
            };
            if (idx >= 0) next[idx] = entry;
            else next.push(entry);
            return next;
        }
        case 'HEARTBEAT': {
            const next = state.map((p) =>
                p.attempt_id === action.data.attempt_id
                    ? {
                          ...p,
                          status: action.data.status ?? p.status,
                          last_seen_at: action.data.last_seen_at ?? p.last_seen_at,
                          countdownTo: action.data.time_remaining != null
                              ? Date.now() + action.data.time_remaining * 1000
                              : p.countdownTo,
                      }
                    : p
            );
            return next;
        }
        case 'FLAG': {
            return state.map((p) =>
                p.attempt_id === action.data.attempt_id
                    ? { ...p, suspicious_flags: action.data.suspicious_flags ?? p.suspicious_flags }
                    : p
            );
        }
        default:
            return state;
    }
}

function logsReducer(state, action) {
    if (action.type === 'PREPEND') {
        return [action.log, ...state].slice(0, 100);
    }
    return state;
}

function useNow(intervalMs) {
    const [now, setNow] = useState(Date.now());
    useEffect(() => {
        const id = setInterval(() => setNow(Date.now()), intervalMs);
        return () => clearInterval(id);
    }, [intervalMs]);
    return now;
}

function LiveMonitoring({ initialParticipants, sessionId, examQuestionsCount }) {
    const [participants, dispatch] = useReducer(participantsReducer, null, () =>
        initialParticipants.map((p) => ({
            ...p,
            countdownTo: p.time_remaining != null ? Date.now() + p.time_remaining * 1000 : null,
        }))
    );
    const [logs, dispatchLogs] = useReducer(logsReducer, []);
    const [connected, setConnected] = useState(false);
    const now = useNow(5000);

    useEffect(() => {
        const echo = bootEcho();
        if (!echo) {
            setConnected(false);
            return;
        }

        const channel = echo.private(`monitoring.${sessionId}`);

        channel
            .subscribed(() => setConnected(true))
            .error(() => setConnected(false))
            .listen('.attempts.status.updated', (data) => dispatch({ type: 'UPSERT', data }))
            .listen('.attempts.heartbeat', (data) => dispatch({ type: 'HEARTBEAT', data }))
            .listen('.activity.logged', (data) => dispatchLogs({ type: 'PREPEND', log: data }));

        return () => {
            echo.leaveChannel(`private-monitoring.${sessionId}`);
            echo.disconnect();
        };
    }, [sessionId]);

    const isOnline = (p) => {
        if (p.status !== 'in_progress') return false;
        const lastSeen = p.last_seen_at ? Date.parse(p.last_seen_at) : 0;
        return now - lastSeen < OFFLINE_AFTER_MS;
    };

    const statusCount = (status) =>
        participants.filter((p) => p.status === status || (status === 'done' && ['submitted', 'auto_submitted'].includes(p.status))).length;

    const timeLeft = (p) => {
        if (!p.countdownTo || p.status !== 'in_progress') return null;
        const seconds = Math.max(0, Math.floor((p.countdownTo - now) / 1000));
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    };

    const total = participants.length;
    const online = participants.filter(isOnline).length;
    const inProgress = participants.filter((p) => p.status === 'in_progress').length;
    const done = participants.filter((p) => ['submitted', 'auto_submitted'].includes(p.status)).length;
    const suspicious = participants.filter((p) => (p.suspicious_flags ?? 0) > 0).length;

    return (
        <>
            {!connected && (
                <div className="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm rounded-lg">
                    Mode siaga — menunggu koneksi realtime (mulai <span className="font-mono">php artisan reverb:start</span>).
                </div>
            )}

            <div className="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
                <div className="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/80 flex flex-wrap items-center justify-between gap-4">
                    <div className="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-6 w-full sm:w-auto">
                        <div className="text-center p-2 sm:p-0 bg-white sm:bg-transparent rounded-xl border sm:border-0 border-slate-200/80">
                            <p className="text-2xl font-extrabold font-mono text-slate-900 leading-tight">{total}</p>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-0.5">Total</p>
                        </div>
                        <div className="text-center p-2 sm:p-0 bg-white sm:bg-transparent rounded-xl border sm:border-0 border-slate-200/80">
                            <p className="text-2xl font-extrabold font-mono text-emerald-600 leading-tight">{online}</p>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-0.5">Online</p>
                        </div>
                        <div className="text-center p-2 sm:p-0 bg-white sm:bg-transparent rounded-xl border sm:border-0 border-slate-200/80">
                            <p className="text-2xl font-extrabold font-mono text-brand-800 leading-tight">{inProgress}</p>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-0.5">Mengerjakan</p>
                        </div>
                        <div className="text-center p-2 sm:p-0 bg-white sm:bg-transparent rounded-xl border sm:border-0 border-slate-200/80">
                            <p className="text-2xl font-extrabold font-mono text-blue-600 leading-tight">{done}</p>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-0.5">Selesai</p>
                        </div>
                        <div className="col-span-2 sm:col-span-1 text-center p-2 sm:p-0 bg-white sm:bg-transparent rounded-xl border sm:border-0 border-slate-200/80">
                            <p className={`text-2xl font-extrabold font-mono leading-tight ${suspicious > 0 ? 'text-rose-600' : 'text-slate-400'}`}>{suspicious}</p>
                            <p className="text-[11px] font-bold uppercase tracking-wider text-slate-500 mt-0.5">⚠ Indikator</p>
                        </div>
                    </div>
                    <span className={`px-3 py-1 text-xs font-bold rounded-full border flex items-center gap-1.5 ${connected ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-amber-100 text-amber-800 border-amber-200'}`}>
                        <span className={`w-2 h-2 rounded-full ${connected ? 'bg-emerald-500 animate-ping' : 'bg-amber-500'}`}></span>
                        {connected ? 'REAL-TIME AKTIF' : 'SIAGA LOKAL'}
                    </span>
                </div>

                {/* Mobile Participant Cards (Stacked & responsive on 360px) */}
                <div className="block md:hidden divide-y divide-slate-100 p-3">
                    {participants.length === 0 ? (
                        <div className="p-6 text-center text-slate-400 text-xs">
                            Belum ada peserta. Menunggu siswa masuk ke sesi ini…
                        </div>
                    ) : (
                        participants.map((p, i) => (
                            <div key={p.attempt_id} className={`p-3.5 rounded-2xl mb-2.5 border transition ${isOnline(p) ? 'bg-white border-slate-200 shadow-2xs' : 'bg-rose-50/50 border-rose-200'}`}>
                                <div className="flex items-start justify-between gap-2 mb-2">
                                    <div className="min-w-0">
                                        <div className="flex items-center gap-1.5 flex-wrap">
                                            <span className="text-xs font-mono font-bold text-slate-400">#{i + 1}</span>
                                            <span className="font-bold text-sm text-slate-900 truncate">{p.student_name}</span>
                                            {(p.suspicious_flags ?? 0) > 0 && (
                                                <span className="px-2 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-800 rounded-full border border-rose-200">
                                                    ⚠ {p.suspicious_flags}
                                                </span>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-2 mt-1 text-[11px] text-slate-500 font-mono">
                                            <span>Mulai: {p.started_at ? new Date(p.started_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}</span>
                                            <span>•</span>
                                            <span>Sisa: <strong className="text-slate-800 font-bold">{timeLeft(p) ?? '-'}</strong></span>
                                        </div>
                                    </div>
                                    <div className="flex flex-col items-end gap-1 shrink-0">
                                        <span className={`px-2 py-0.5 text-xs rounded-full ${STATUS_COLORS[p.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                            {STATUS_LABELS[p.status] ?? p.status}
                                        </span>
                                        {p.status === 'in_progress' && (
                                            <span className={`text-[10px] font-bold px-1.5 py-0.2 rounded ${isOnline(p) ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50'}`}>
                                                {isOnline(p) ? '● Online' : '○ Terputus'}
                                            </span>
                                        )}
                                    </div>
                                </div>
                                <div className="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <div className="flex-1 flex items-center gap-2">
                                        <div className="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                            <div className="bg-brand-700 h-2 rounded-full" style={{ width: `${p.progress}%` }} />
                                        </div>
                                        <span className="text-xs font-mono font-bold text-slate-700">{Math.round(p.progress)}%</span>
                                        <span className="text-[11px] font-mono text-slate-400">({p.answers_count}/{examQuestionsCount})</span>
                                    </div>
                                    {p.status !== 'not_started' && (
                                        <form action={`/monitoring/${p.attempt_id}/reset`} method="POST" onSubmit={() => confirm('Reset percobaan ini?')}>
                                            <input type="hidden" name="_token" value={window.__CSRF__} />
                                            <button type="submit" className="min-h-[36px] px-3 py-1 text-rose-600 hover:text-rose-800 font-bold text-xs rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">Reset</button>
                                        </form>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>

                {/* Desktop Table View */}
                <div className="hidden md:block overflow-x-auto">
                    <table className="min-w-full divide-y divide-slate-200 text-sm">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Online</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jawaban</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Waktu</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {participants.length === 0 ? (
                                <tr>
                                    <td colSpan="9" className="px-6 py-8 text-center text-gray-500">
                                        Belum ada peserta. Menunggu siswa masuk ke sesi ini…
                                    </td>
                                </tr>
                            ) : (
                                participants.map((p, i) => (
                                    <tr key={p.attempt_id} className={isOnline(p) ? '' : 'bg-red-50/40'}>
                                        <td className="px-6 py-4">{i + 1}</td>
                                        <td className="px-6 py-4 font-medium">
                                            {p.student_name}
                                            {(p.suspicious_flags ?? 0) > 0 && (
                                                <span className="ml-2 px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded-full">
                                                    ⚠ {p.suspicious_flags}
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className={`px-2 py-1 text-xs rounded-full ${STATUS_COLORS[p.status] ?? 'bg-gray-100 text-gray-800'}`}>
                                                {STATUS_LABELS[p.status] ?? p.status}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            {p.status === 'in_progress' ? (
                                                isOnline(p) ? (
                                                    <span className="text-green-600 font-medium">Online</span>
                                                ) : (
                                                    <span className="text-red-600 font-medium">Terputus</span>
                                                )
                                            ) : (
                                                <span className="text-gray-400">-</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-2">
                                                <div className="w-24 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                                    <div className="bg-brand-700 h-2 rounded-full" style={{ width: `${p.progress}%` }} />
                                                </div>
                                                <span className="text-xs font-mono font-semibold text-slate-600">{Math.round(p.progress)}%</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-xs font-mono">{p.answers_count} / {examQuestionsCount}</td>
                                        <td className="px-6 py-4 text-xs font-mono font-bold text-slate-800">{timeLeft(p) ?? '-'}</td>
                                        <td className="px-6 py-4 text-xs font-mono text-slate-500">
                                            {p.started_at ? new Date(p.started_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'}
                                        </td>
                                        <td className="px-6 py-4">
                                            {p.status !== 'not_started' ? (
                                                <form action={`/monitoring/${p.attempt_id}/reset`} method="POST" onSubmit={() => confirm('Reset percobaan ini?')}>
                                                    <input type="hidden" name="_token" value={window.__CSRF__} />
                                                    <button type="submit" className="text-rose-600 hover:text-rose-800 font-semibold text-xs px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 border border-rose-200 transition">Reset</button>
                                                </form>
                                            ) : (
                                                <span className="text-slate-300 text-xs">-</span>
                                            )}
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <div className="mt-6 bg-white rounded-lg shadow overflow-hidden">
                <div className="p-4 border-b bg-gray-50 flex items-center justify-between">
                    <h3 className="font-bold text-gray-800">Log Aktivitas &amp; Mencurigakan</h3>
                    <span className="text-xs text-gray-500">{logs.length} entri real-time</span>
                </div>
                <div className="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    {logs.length === 0 ? (
                        <p className="px-4 py-8 text-center text-gray-500 text-sm">Belum ada aktivitas real-time baru.</p>
                    ) : (
                        logs.map((l, i) => (
                            <div key={i} className="px-4 py-3 flex items-start gap-3 text-sm">
                                <span
                                    className={`mt-1 w-2 h-2 rounded-full flex-shrink-0 ${l.suspicious ? 'bg-red-500' : 'bg-blue-400'}`}
                                />
                                <div className="flex-1">
                                    <p className="text-gray-800">
                                        <span className="font-medium">{l.student_name ?? 'Sistem'}</span>
                                        <span className="text-gray-500">— {l.description}</span>
                                        <span className="ml-2 px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-600">{l.action}</span>
                                    </p>
                                    <p className="text-xs text-gray-400 mt-0.5">
                                        {l.created_at ? new Date(l.created_at).toLocaleTimeString('id-ID') : ''}
                                    </p>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </>
    );
}

const rootEl = document.getElementById('monitorRoot');
if (rootEl) {
    createRoot(rootEl).render(
        <LiveMonitoring
            initialParticipants={window.__MONITOR_DATA__ ?? []}
            sessionId={window.__SESSION_ID__}
            examQuestionsCount={window.__EXAM_QUESTIONS_COUNT__ ?? 0}
        />
    );
}