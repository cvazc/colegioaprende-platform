import { FormEvent, useState } from 'react';
import { http } from '@/shared/api/http';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';

type LookupResponse = {
  data: {
    student: {
      id: number;
      first_name: string;
      surnames: string;
      email: string;
    };
  };
};

export function AdminStudentsPage() {
  const [email, setEmail] = useState('');
  const [result, setResult] = useState<LookupResponse['data']['student'] | null>(null);
  const [error, setError] = useState('');

  async function onSubmit(event: FormEvent) {
    event.preventDefault();
    setError('');
    setResult(null);

    try {
      const response = await http.get<LookupResponse>(`/admin/students/by-email?email=${encodeURIComponent(email)}`);
      setResult(response.data.data.student);
    } catch {
      setError('No se encontro estudiante con ese correo.');
    }
  }

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Buscar estudiante</h1>
      <form className="mt-4 flex gap-2" onSubmit={onSubmit}>
        <Input value={email} onChange={(event) => setEmail(event.target.value)} placeholder="student@demo.com" required />
        <Button type="submit">Buscar</Button>
      </form>
      {result && (
        <div className="mt-4 rounded-lg border border-slate-200 p-3 text-sm">
          <p className="font-semibold text-slate-800">{result.first_name} {result.surnames}</p>
          <p className="text-slate-600">{result.email}</p>
          <p className="text-slate-500">ID: {result.id}</p>
        </div>
      )}
      {error && <p className="mt-3 text-sm text-rose-700">{error}</p>}
    </Card>
  );
}
