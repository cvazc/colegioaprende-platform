import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type CalendarTemplate = {
  id: number;
  name: string;
  is_active: boolean;
  events: { id: number; title: string; due_date: string }[];
};

type Response = { data: CalendarTemplate[] };

export function AdminCalendarsPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['admin-calendars'],
    queryFn: async () => (await http.get<Response>('/admin/calendar-templates')).data,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Plantillas de calendario</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      <ul className="mt-4 grid gap-3">
        {(data?.data ?? []).map((item) => (
          <li key={item.id} className="rounded-lg border border-slate-200 p-3 text-sm">
            <p className="font-semibold text-slate-800">{item.name}</p>
            <p className="text-slate-600">Estado: {item.is_active ? 'Activa' : 'Inactiva'}</p>
            <p className="text-slate-500">Eventos: {item.events?.length ?? 0}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
