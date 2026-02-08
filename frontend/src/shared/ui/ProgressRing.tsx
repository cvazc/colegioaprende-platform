type Props = {
  value: number;
  size?: number;
  stroke?: number;
};

export function ProgressRing({ value, size = 170, stroke = 12 }: Props) {
  const normalized = Number.isFinite(value) ? Math.max(0, Math.min(100, value)) : 0;
  const radius = (size - stroke) / 2;
  const circumference = 2 * Math.PI * radius;
  const dashOffset = circumference - (normalized / 100) * circumference;

  return (
    <div className="relative inline-flex items-center justify-center" style={{ width: size, height: size }}>
      <svg width={size} height={size} className="-rotate-90">
        <circle
          cx={size / 2}
          cy={size / 2}
          r={radius}
          stroke="currentColor"
          strokeWidth={stroke}
          className="text-slate-200"
          fill="none"
        />
        <circle
          cx={size / 2}
          cy={size / 2}
          r={radius}
          stroke="currentColor"
          strokeWidth={stroke}
          className="text-brand-600"
          fill="none"
          strokeLinecap="round"
          strokeDasharray={circumference}
          strokeDashoffset={dashOffset}
        />
      </svg>
      <div className="absolute text-center">
        <p className="text-3xl font-extrabold text-slate-900">{Math.round(normalized)}%</p>
        <p className="text-xs uppercase tracking-wide text-slate-500">Completado</p>
      </div>
    </div>
  );
}
