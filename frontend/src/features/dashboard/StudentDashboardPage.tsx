import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type ProgressResponse = {
  data: {
    overall: {
      percentage: number;
      approved_count: number;
      enabled_count: number;
      disabled_count: number;
      total_count: number;
    };
  };
};

export function StudentDashboardPage() {
  const { data } = useQuery({
    queryKey: ['student-progress'],
    queryFn: async () => {
      const response = await http.get<ProgressResponse>('/student/progress');
      return response.data;
    },
  });

  const progress = data?.data?.overall;

  return (
    <div className="grid gap-4 md:grid-cols-2">
      <Card>
        <h1 className="text-lg font-bold text-slate-900">Mi avance general</h1>
        <p className="mt-3 text-4xl font-extrabold text-brand-700">{progress?.percentage ?? 0}%</p>
        <p className="mt-1 text-sm text-slate-600">Basado en materias aprobadas.</p>
      </Card>
      <Card>
        <h2 className="text-lg font-bold text-slate-900">Resumen</h2>
        <ul className="mt-3 space-y-1 text-sm text-slate-700">
          <li>Aprobadas: {progress?.approved_count ?? 0}</li>
          <li>Habilitadas: {progress?.enabled_count ?? 0}</li>
          <li>Inhabilitadas: {progress?.disabled_count ?? 0}</li>
          <li>Total: {progress?.total_count ?? 0}</li>
        </ul>
      </Card>
    </div>
  );
}
