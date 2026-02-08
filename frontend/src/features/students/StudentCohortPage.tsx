import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type Response = {
  data: {
    cohort_id: number;
    cohort_name: string;
    status: string;
    source: string;
    assigned_at: string;
  } | null;
  message?: string;
};

export function StudentCohortPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['student-cohort'],
    queryFn: async () => (await http.get<Response>('/student/cohort')).data,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Mi cohorte</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      {!isLoading && !data?.data && <p className="mt-3 text-sm text-amber-700">{data?.message ?? 'Sin cohorte asignada.'}</p>}
      {data?.data && (
        <div className="mt-4 space-y-1 text-sm text-slate-700">
          <p>Cohorte: {data.data.cohort_name}</p>
          <p>Estado: {data.data.status}</p>
          <p>Origen: {data.data.source}</p>
          <p>Asignado: {data.data.assigned_at}</p>
        </div>
      )}
    </Card>
  );
}
