import { useQuery } from '@tanstack/react-query';
import { getStudentCalendar } from '@/shared/api/student-api';
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

export function StudentCalendarPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['student-calendar'],
    queryFn: getStudentCalendar,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Mi calendario</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      {!isLoading && !data?.data && (
        <p className="mt-3 text-sm text-amber-700">{data?.message ?? 'Sin calendario asignado.'}</p>
      )}
      {data?.data && (
        <>
          <p className="mt-3 text-sm text-slate-600">{data.data.calendar_name}</p>
          <ul className="mt-4 grid gap-3">
            {data.data.events.map((event) => (
              <li key={event.id} className="rounded-lg border border-slate-200 p-3 text-sm">
                <p className="font-semibold text-slate-800">{event.title}</p>
                <p className="text-slate-600">Fecha: {formatDate(event.due_date)}</p>
                <p className="text-slate-500">{event.is_payment ? 'Pago' : 'Actividad'}</p>
              </li>
            ))}
          </ul>
        </>
      )}
    </Card>
  );
}
