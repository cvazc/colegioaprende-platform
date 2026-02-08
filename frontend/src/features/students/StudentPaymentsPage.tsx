import { useQuery } from '@tanstack/react-query';
import { listStudentPayments } from '@/shared/api/student-api';
import type { StudentPayment } from '@/shared/types/student';
import { Card } from '@/shared/ui/Card';

function formatDate(value: string) {
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return date.toLocaleDateString('es-MX', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  });
}

function formatMoney(amount: number, currency = 'MXN') {
  return new Intl.NumberFormat('es-MX', {
    style: 'currency',
    currency,
    maximumFractionDigits: 2,
  }).format(amount);
}

export function StudentPaymentsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['student-payments'],
    queryFn: listStudentPayments,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Pagos realizados</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      {!isLoading && (data ?? []).length === 0 && (
        <p className="mt-3 text-sm text-slate-600">Aun no hay pagos registrados.</p>
      )}
      <ul className="mt-4 grid gap-3">
        {(data ?? []).map((item: StudentPayment) => (
          <li key={item.id} className="rounded-lg border border-slate-200 p-3 text-sm">
            <p className="font-semibold text-slate-800">
              {item.provider} | {item.status}
            </p>
            <p className="text-slate-600">Monto: {formatMoney(item.amount, item.currency ?? 'MXN')}</p>
            <p className="text-slate-500">Fecha: {formatDate(item.created_at)}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
