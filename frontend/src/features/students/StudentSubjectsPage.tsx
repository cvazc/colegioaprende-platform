import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type Subject = {
  subject_id: number;
  subject_name: string;
  status: string;
  score: number;
};

type Response = { data: Subject[] };

export function StudentSubjectsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['student-subjects'],
    queryFn: async () => (await http.get<Response>('/student/subjects')).data,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Materias</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      <ul className="mt-4 grid gap-3">
        {(data?.data ?? []).map((item) => (
          <li key={item.subject_id} className="rounded-lg border border-slate-200 p-3">
            <p className="font-semibold text-slate-800">{item.subject_name}</p>
            <p className="text-xs text-slate-600">Estado: {item.status} | Score: {item.score ?? 0}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
