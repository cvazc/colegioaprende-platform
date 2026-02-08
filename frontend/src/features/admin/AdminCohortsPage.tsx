import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type Cohort = {
  id: number;
  name: string;
  course_type: string | null;
  enrollment_type: string | null;
  active_members_count?: number;
  waitlist_members_count?: number;
};

type Response = { data: Cohort[] };

export function AdminCohortsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['admin-cohorts'],
    queryFn: async () => (await http.get<Response>('/admin/cohorts')).data,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Cohortes</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      <ul className="mt-4 grid gap-3">
        {(data?.data ?? []).map((cohort) => (
          <li key={cohort.id} className="rounded-lg border border-slate-200 p-3 text-sm">
            <p className="font-semibold text-slate-800">{cohort.name}</p>
            <p className="text-slate-600">Curso: {cohort.course_type ?? 'Todos'}</p>
            <p className="text-slate-600">Inscripcion: {cohort.enrollment_type ?? 'Todos'}</p>
            <p className="text-slate-500">
              Activos: {cohort.active_members_count ?? 0} | Waitlist: {cohort.waitlist_members_count ?? 0}
            </p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
