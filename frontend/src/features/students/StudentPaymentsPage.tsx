import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type Payment = {
  id: number;
  provider: string;
  status: string;
  amount: number;
  created_at: string;
};

type Response = { data: Payment[] };

export function StudentPaymentsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['student-payments'],
    queryFn: async () => (await http.get<Response>('/student/payments')).data,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Pagos realizados</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      <ul className="mt-4 grid gap-3">
        {(data?.data ?? []).map((item) => (
          <li key={item.id} className="rounded-lg border border-slate-200 p-3 text-sm">
            <p className="font-semibold text-slate-800">
              {item.provider} | {item.status}
            </p>
            <p className="text-slate-600">Monto: ${item.amount}</p>
            <p className="text-slate-500">Fecha: {item.created_at}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
