import { FormEvent, useState } from 'react';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { createAdminCalendar, listAdminCalendars, updateAdminCalendar } from '@/shared/api/admin-api';
import type { CalendarTemplate } from '@/shared/types/admin';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';
import { Textarea } from '@/shared/ui/Textarea';

type EventForm = {
  title: string;
  due_date: string;
  is_payment: boolean;
};

type CalendarForm = {
  id?: number;
  name: string;
  description: string;
  is_active: boolean;
  events: EventForm[];
};

const initialForm: CalendarForm = {
  name: '',
  description: '',
  is_active: true,
  events: [{ title: '', due_date: '', is_payment: true }],
};

export function AdminCalendarsPage() {
  const queryClient = useQueryClient();
  const [form, setForm] = useState<CalendarForm>(initialForm);
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState('');
  const [error, setError] = useState('');

  const { data: calendars, isLoading } = useQuery({
    queryKey: ['admin-calendars'],
    queryFn: listAdminCalendars,
  });

  const isEdit = Boolean(form.id);

  function hydrate(item: CalendarTemplate) {
    setForm({
      id: item.id,
      name: item.name,
      description: item.description ?? '',
      is_active: item.is_active,
      events: (item.events ?? []).map((event) => ({
        title: event.title,
        due_date: event.due_date?.slice(0, 10) ?? '',
        is_payment: event.is_payment,
      })),
    });
    setFeedback('');
    setError('');
  }

  function resetForm() {
    setForm(initialForm);
  }

  function updateEvent(index: number, patch: Partial<EventForm>) {
    setForm((current) => ({
      ...current,
      events: current.events.map((event, i) => (i === index ? { ...event, ...patch } : event)),
    }));
  }

  function addEvent() {
    setForm((current) => ({
      ...current,
      events: [...current.events, { title: '', due_date: '', is_payment: true }],
    }));
  }

  function removeEvent(index: number) {
    setForm((current) => ({
      ...current,
      events: current.events.filter((_, i) => i !== index),
    }));
  }

  async function onSubmit(event: FormEvent) {
    event.preventDefault();
    setSaving(true);
    setFeedback('');
    setError('');

    const payload = {
      name: form.name,
      description: form.description || undefined,
      is_active: form.is_active,
      events: form.events
        .filter((item) => item.title && item.due_date)
        .map((item) => ({
          title: item.title,
          due_date: item.due_date,
          is_payment: item.is_payment,
          is_active: true,
        })),
    };

    try {
      if (form.id) {
        await updateAdminCalendar(form.id, payload);
        setFeedback('Plantilla actualizada correctamente.');
      } else {
        await createAdminCalendar(payload);
        setFeedback('Plantilla creada correctamente.');
      }
      await queryClient.invalidateQueries({ queryKey: ['admin-calendars'] });
      resetForm();
    } catch {
      setError('No fue posible guardar la plantilla.');
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="grid gap-4 lg:grid-cols-5">
      <Card>
        <h1 className="text-lg font-bold text-slate-900">Plantillas de calendario</h1>
        {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
        <ul className="mt-4 grid gap-3">
          {(calendars ?? []).map((item) => (
            <li key={item.id} className="rounded-lg border border-slate-200 p-3 text-sm">
              <p className="font-semibold text-slate-800">{item.name}</p>
              <p className="text-slate-600">Estado: {item.is_active ? 'Activa' : 'Inactiva'}</p>
              <p className="text-slate-500">Eventos: {item.events?.length ?? 0}</p>
              <div className="mt-2">
                <Button variant="secondary" onClick={() => hydrate(item)}>Editar</Button>
              </div>
            </li>
          ))}
        </ul>
      </Card>

      <Card>
        <h2 className="text-lg font-bold text-slate-900">{isEdit ? 'Editar plantilla' : 'Nueva plantilla'}</h2>
        <form className="mt-4 grid gap-3" onSubmit={onSubmit}>
          <label className="grid gap-1 text-sm">
            Nombre
            <Input value={form.name} onChange={(e) => setForm((s) => ({ ...s, name: e.target.value }))} required />
          </label>
          <label className="grid gap-1 text-sm">
            Descripción
            <Textarea rows={3} value={form.description} onChange={(e) => setForm((s) => ({ ...s, description: e.target.value }))} />
          </label>
          <label className="flex items-center gap-2 text-sm">
            <input type="checkbox" checked={form.is_active} onChange={(e) => setForm((s) => ({ ...s, is_active: e.target.checked }))} />
            Activa
          </label>

          <div className="rounded-lg border border-slate-200 p-3">
            <div className="mb-2 flex items-center justify-between">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Eventos</p>
              <Button type="button" variant="secondary" onClick={addEvent}>Agregar evento</Button>
            </div>
            <div className="grid gap-2">
              {form.events.map((eventItem, index) => (
                <div key={`${index}-${eventItem.title}`} className="grid gap-2 rounded-md border border-slate-200 p-2">
                  <label className="grid gap-1 text-xs">
                    Título
                    <Input value={eventItem.title} onChange={(e) => updateEvent(index, { title: e.target.value })} />
                  </label>
                  <label className="grid gap-1 text-xs">
                    Fecha
                    <Input type="date" value={eventItem.due_date} onChange={(e) => updateEvent(index, { due_date: e.target.value })} />
                  </label>
                  <label className="flex items-center gap-2 text-xs">
                    <input type="checkbox" checked={eventItem.is_payment} onChange={(e) => updateEvent(index, { is_payment: e.target.checked })} />
                    Es pago
                  </label>
                  {form.events.length > 1 && (
                    <Button type="button" variant="danger" onClick={() => removeEvent(index)}>Eliminar evento</Button>
                  )}
                </div>
              ))}
            </div>
          </div>

          <div className="flex items-center gap-2">
            <Button type="submit" disabled={saving}>{saving ? 'Guardando...' : isEdit ? 'Actualizar' : 'Crear plantilla'}</Button>
            {isEdit && <Button type="button" variant="secondary" onClick={resetForm}>Cancelar edición</Button>}
          </div>

          {feedback && <p className="text-sm text-emerald-700">{feedback}</p>}
          {error && <p className="text-sm text-rose-700">{error}</p>}
        </form>
      </Card>
    </div>
  );
}
