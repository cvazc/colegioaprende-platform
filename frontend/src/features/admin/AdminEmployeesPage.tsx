import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Card } from '@/shared/ui/Card';

type Employee = {
  id: number;
  first_name: string;
  surnames: string;
  email: string;
  role_label: 'admin' | 'staff';
};

type Response = { data: Employee[] };

export function AdminEmployeesPage() {
  const { data, isLoading } = useQuery({
    queryKey: ['admin-employees'],
    queryFn: async () => (await http.get<Response>('/admin/employees')).data,
  });

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Empleados</h1>
      {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      <ul className="mt-4 grid gap-3">
        {(data?.data ?? []).map((item) => (
          <li key={item.id} className="rounded-lg border border-slate-200 p-3 text-sm">
            <p className="font-semibold text-slate-800">{item.first_name} {item.surnames}</p>
            <p className="text-slate-600">{item.email}</p>
            <p className="text-slate-500">Rol: {item.role_label}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
