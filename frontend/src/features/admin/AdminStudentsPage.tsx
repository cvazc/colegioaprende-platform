import { FormEvent, useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { assignStudentCalendar, assignStudentToCohort, findStudentByEmail, listAdminCalendars, listAdminCohorts } from '@/shared/api/admin-api';
import type { StudentLookup } from '@/shared/types/admin';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';
import { Select } from '@/shared/ui/Select';
import { Textarea } from '@/shared/ui/Textarea';

export function AdminStudentsPage() {
  const [email, setEmail] = useState('');
  const [student, setStudent] = useState<StudentLookup | null>(null);
  const [error, setError] = useState('');
  const [feedback, setFeedback] = useState('');
  const [cohortId, setCohortId] = useState('');
  const [calendarId, setCalendarId] = useState('');
  const [reason, setReason] = useState('');
  const [saving, setSaving] = useState(false);

  const { data: cohorts } = useQuery({
    queryKey: ['admin-cohorts'],
    queryFn: listAdminCohorts,
  });

  const { data: calendars } = useQuery({
    queryKey: ['admin-calendars'],
    queryFn: listAdminCalendars,
  });

  async function onSearch(event: FormEvent) {
    event.preventDefault();
    setError('');
    setFeedback('');
    setStudent(null);

    try {
      const result = await findStudentByEmail(email);
      setStudent(result);
    } catch {
      setError('No se encontro estudiante con ese correo.');
    }
  }

  async function onAssignCohort() {
    if (!student || !cohortId) {
      return;
    }

    setSaving(true);
    setError('');
    setFeedback('');

    try {
      await assignStudentToCohort(student.id, {
        cohort_id: Number(cohortId),
        reason: reason || undefined,
        force: false,
      });
      setFeedback('Cohorte asignada correctamente.');
    } catch {
      setError('No fue posible asignar la cohorte.');
    } finally {
      setSaving(false);
    }
  }

  async function onAssignCalendar() {
    if (!student || !calendarId) {
      return;
    }

    setSaving(true);
    setError('');
    setFeedback('');

    try {
      await assignStudentCalendar(student.id, {
        calendar_template_id: Number(calendarId),
      });
      setFeedback('Calendario asignado correctamente.');
    } catch {
      setError('No fue posible asignar el calendario.');
    } finally {
      setSaving(false);
    }
  }

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Buscar estudiante</h1>
      <form className="mt-4 flex gap-2" onSubmit={onSearch}>
        <Input value={email} onChange={(event) => setEmail(event.target.value)} placeholder="student@demo.com" required />
        <Button type="submit">Buscar</Button>
      </form>

      {student && (
        <div className="mt-5 grid gap-4 rounded-lg border border-slate-200 p-4 text-sm">
          <div>
            <p className="font-semibold text-slate-800">{student.first_name} {student.surnames}</p>
            <p className="text-slate-600">{student.email}</p>
            <p className="text-slate-500">ID: {student.id}</p>
          </div>

          <div className="grid gap-3 md:grid-cols-2">
            <div className="grid gap-2">
              <label className="grid gap-1 text-sm">
                Cohorte
                <Select value={cohortId} onChange={(e) => setCohortId(e.target.value)}>
                  <option value="">Selecciona cohorte</option>
                  {(cohorts ?? []).map((item) => (
                    <option key={item.id} value={item.id}>{item.name}</option>
                  ))}
                </Select>
              </label>
              <label className="grid gap-1 text-sm">
                Razón
                <Textarea rows={2} value={reason} onChange={(e) => setReason(e.target.value)} placeholder="Opcional" />
              </label>
              <Button onClick={onAssignCohort} disabled={saving || !cohortId}>
                Asignar cohorte
              </Button>
            </div>

            <div className="grid gap-2">
              <label className="grid gap-1 text-sm">
                Calendario
                <Select value={calendarId} onChange={(e) => setCalendarId(e.target.value)}>
                  <option value="">Selecciona calendario</option>
                  {(calendars ?? []).map((item) => (
                    <option key={item.id} value={item.id}>{item.name}</option>
                  ))}
                </Select>
              </label>
              <Button variant="secondary" onClick={onAssignCalendar} disabled={saving || !calendarId}>
                Asignar calendario
              </Button>
            </div>
          </div>
        </div>
      )}

      {feedback && <p className="mt-3 text-sm text-emerald-700">{feedback}</p>}
      {error && <p className="mt-3 text-sm text-rose-700">{error}</p>}
    </Card>
  );
}
