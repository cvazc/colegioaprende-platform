import { useQuery } from '@tanstack/react-query';
import { listStudentSubjects } from '@/shared/api/student-api';
import { Card } from '@/shared/ui/Card';

function statusClass(status: string, value?: number | null) {
  if (value === 2) {
    return 'bg-emerald-50 text-emerald-700 border-emerald-300';
  }

  if (value === 1) {
    return 'bg-blue-50 text-blue-700 border-blue-300';
  }

  const normalized = status.toLowerCase();

  if (normalized.includes('aprob')) {
    return 'bg-emerald-50 text-emerald-700 border-emerald-300';
  }

  if (normalized.includes('habil') || normalized.includes('enabled')) {
    return 'bg-blue-50 text-blue-700 border-blue-300';
  }

  return 'bg-slate-100 text-slate-700 border-slate-300';
}

export function StudentSubjectsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['student-subjects'],
    queryFn: listStudentSubjects,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Materias</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      {!isLoading && (data ?? []).length === 0 && (
        <p className="mt-3 text-sm text-slate-600">No hay materias disponibles.</p>
      )}
      <ul className="mt-4 grid gap-3">
        {(data ?? []).map((item) => (
          <li key={item.subject_id} className="rounded-lg border border-slate-200 p-3">
            <div className="flex flex-wrap items-center justify-between gap-2">
              <p className="font-semibold text-slate-800">{item.subject_name}</p>
              <span className={`rounded-full border px-2 py-1 text-xs font-semibold ${statusClass(item.status, item.status_value)}`}>
                {item.status}
              </span>
            </div>
            <p className="mt-2 text-xs text-slate-600">Calificacion: {item.score ?? 0}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
