import { FormEvent, useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { http } from '@/shared/api/http';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';

type Prospect = {
  id: number;
  first_name: string;
  surnames: string;
  email: string;
  status?: string;
};

type Response = {
  data: Prospect[];
};

export function AdminProspectsPage() {
  const [emailFilter, setEmailFilter] = useState('');
  const { data, refetch, isFetching } = useQuery({
    queryKey: ['admin-prospects', emailFilter],
    queryFn: async () => {
      const query = emailFilter ? `?email=${encodeURIComponent(emailFilter)}` : '';
      return (await http.get<Response>(`/admin/prospects${query}`)).data;
    },
  });

  function onSubmit(event: FormEvent) {
    event.preventDefault();
    refetch();
  }

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Prospectos</h1>
      <form className="mt-4 flex gap-2" onSubmit={onSubmit}>
        <Input value={emailFilter} onChange={(event) => setEmailFilter(event.target.value)} placeholder="Buscar por email" />
        <Button type="submit" variant="secondary">Buscar</Button>
      </form>
      {isFetching && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
      <ul className="mt-4 grid gap-3">
        {(data?.data ?? []).map((item) => (
          <li key={item.id} className="rounded-lg border border-slate-200 p-3 text-sm">
            <p className="font-semibold text-slate-800">{item.first_name} {item.surnames}</p>
            <p className="text-slate-600">{item.email}</p>
            <p className="text-slate-500">Estado: {item.status ?? 'N/A'}</p>
          </li>
        ))}
      </ul>
    </Card>
  );
}
