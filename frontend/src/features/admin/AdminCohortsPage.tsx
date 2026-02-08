import { FormEvent, useMemo, useState } from 'react';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { createAdminCohort, listAdminCalendars, listAdminCohorts, updateAdminCohort } from '@/shared/api/admin-api';
import { COURSE_TYPES, ENROLLMENT_TYPES } from '@/shared/config/catalogs';
import type { Cohort, CohortRuleInput } from '@/shared/types/admin';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';
import { Select } from '@/shared/ui/Select';

type CohortForm = {
  id?: number;
  name: string;
  course_type: string;
  enrollment_type: string;
  calendar_template_id: string;
  capacity: string;
  starts_at: string;
  ends_at: string;
  auto_assign: boolean;
  is_active: boolean;
  rule_field: string;
  rule_operator: 'equals' | 'not_equals' | 'in' | 'not_in';
  rule_value: string;
};

const initialForm: CohortForm = {
  name: '',
  course_type: '',
  enrollment_type: '',
  calendar_template_id: '',
  capacity: '',
  starts_at: '',
  ends_at: '',
  auto_assign: true,
  is_active: true,
  rule_field: '',
  rule_operator: 'equals',
  rule_value: '',
};

export function AdminCohortsPage() {
  const queryClient = useQueryClient();
  const [form, setForm] = useState<CohortForm>(initialForm);
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState('');
  const [error, setError] = useState('');

  const { data: cohorts, isLoading } = useQuery({
    queryKey: ['admin-cohorts'],
    queryFn: listAdminCohorts,
  });

  const { data: calendars } = useQuery({
    queryKey: ['admin-calendars'],
    queryFn: listAdminCalendars,
  });

  const isEdit = Boolean(form.id);

  const calendarOptions = useMemo(() => calendars ?? [], [calendars]);

  function hydrateFromCohort(item: Cohort) {
    const firstRule = item.rules?.[0];

    setForm({
      id: item.id,
      name: item.name,
      course_type: item.course_type ?? '',
      enrollment_type: item.enrollment_type ?? '',
      calendar_template_id: item.calendar_template_id ? String(item.calendar_template_id) : '',
      capacity: item.capacity ? String(item.capacity) : '',
      starts_at: item.starts_at?.slice(0, 10) ?? '',
      ends_at: item.ends_at?.slice(0, 10) ?? '',
      auto_assign: item.auto_assign,
      is_active: item.is_active,
      rule_field: firstRule?.field ?? '',
      rule_operator: (firstRule?.operator as CohortForm['rule_operator']) ?? 'equals',
      rule_value: firstRule?.value ?? '',
    });
    setFeedback('');
    setError('');
  }

  function resetForm() {
    setForm(initialForm);
  }

  async function onSubmit(event: FormEvent) {
    event.preventDefault();
    setSaving(true);
    setFeedback('');
    setError('');

    const rules: CohortRuleInput[] = [];
    if (form.rule_field && form.rule_value) {
      rules.push({
        field: form.rule_field,
        operator: form.rule_operator,
        value: form.rule_value,
        is_active: true,
      });
    }

    const payload = {
      name: form.name,
      course_type: form.course_type || undefined,
      enrollment_type: form.enrollment_type || undefined,
      calendar_template_id: form.calendar_template_id ? Number(form.calendar_template_id) : null,
      capacity: form.capacity ? Number(form.capacity) : null,
      starts_at: form.starts_at || undefined,
      ends_at: form.ends_at || undefined,
      auto_assign: form.auto_assign,
      is_active: form.is_active,
      rules,
    };

    try {
      if (form.id) {
        await updateAdminCohort(form.id, payload);
        setFeedback('Cohorte actualizada correctamente.');
      } else {
        await createAdminCohort(payload);
        setFeedback('Cohorte creada correctamente.');
      }

      await queryClient.invalidateQueries({ queryKey: ['admin-cohorts'] });
      resetForm();
    } catch {
      setError('No fue posible guardar la cohorte.');
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="grid gap-4 lg:grid-cols-5">
      <Card>
        <h1 className="text-lg font-bold text-slate-900">Cohortes</h1>
        {isLoading && <p className="mt-3 text-sm text-slate-500">Cargando...</p>}
        <ul className="mt-4 grid gap-3">
          {(cohorts ?? []).map((cohort) => (
            <li key={cohort.id} className="rounded-lg border border-slate-200 p-3 text-sm">
              <p className="font-semibold text-slate-800">{cohort.name}</p>
              <p className="text-slate-600">Curso: {cohort.course_type ?? 'Todos'}</p>
              <p className="text-slate-600">Inscripción: {cohort.enrollment_type ?? 'Todos'}</p>
              <p className="text-slate-500">
                Activos: {cohort.active_members_count ?? 0} | Waitlist: {cohort.waitlist_members_count ?? 0}
              </p>
              <div className="mt-2">
                <Button variant="secondary" onClick={() => hydrateFromCohort(cohort)}>
                  Editar
                </Button>
              </div>
            </li>
          ))}
        </ul>
      </Card>

      <Card>
        <h2 className="text-lg font-bold text-slate-900">{isEdit ? 'Editar cohorte' : 'Nueva cohorte'}</h2>
        <form className="mt-4 grid gap-3" onSubmit={onSubmit}>
          <label className="grid gap-1 text-sm">
            Nombre
            <Input value={form.name} onChange={(e) => setForm((s) => ({ ...s, name: e.target.value }))} required />
          </label>
          <label className="grid gap-1 text-sm">
            Curso
            <Select value={form.course_type} onChange={(e) => setForm((s) => ({ ...s, course_type: e.target.value }))}>
              <option value="">Todos</option>
              {COURSE_TYPES.map((item) => (
                <option key={item} value={item}>{item}</option>
              ))}
            </Select>
          </label>
          <label className="grid gap-1 text-sm">
            Tipo de inscripción
            <Select value={form.enrollment_type} onChange={(e) => setForm((s) => ({ ...s, enrollment_type: e.target.value }))}>
              <option value="">Todos</option>
              {ENROLLMENT_TYPES.map((item) => (
                <option key={item} value={item}>{item}</option>
              ))}
            </Select>
          </label>
          <label className="grid gap-1 text-sm">
            Plantilla calendario
            <Select value={form.calendar_template_id} onChange={(e) => setForm((s) => ({ ...s, calendar_template_id: e.target.value }))}>
              <option value="">Sin plantilla</option>
              {calendarOptions.map((calendar) => (
                <option key={calendar.id} value={calendar.id}>{calendar.name}</option>
              ))}
            </Select>
          </label>
          <label className="grid gap-1 text-sm">
            Capacidad
            <Input value={form.capacity} onChange={(e) => setForm((s) => ({ ...s, capacity: e.target.value }))} type="number" min={1} />
          </label>
          <div className="grid grid-cols-2 gap-3">
            <label className="grid gap-1 text-sm">
              Inicio
              <Input value={form.starts_at} onChange={(e) => setForm((s) => ({ ...s, starts_at: e.target.value }))} type="date" />
            </label>
            <label className="grid gap-1 text-sm">
              Fin
              <Input value={form.ends_at} onChange={(e) => setForm((s) => ({ ...s, ends_at: e.target.value }))} type="date" />
            </label>
          </div>

          <div className="rounded-lg border border-slate-200 p-3">
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Regla opcional</p>
            <div className="mt-2 grid grid-cols-3 gap-2">
              <Input placeholder="field (ej. course_type)" value={form.rule_field} onChange={(e) => setForm((s) => ({ ...s, rule_field: e.target.value }))} />
              <Select value={form.rule_operator} onChange={(e) => setForm((s) => ({ ...s, rule_operator: e.target.value as CohortForm['rule_operator'] }))}>
                <option value="equals">equals</option>
                <option value="not_equals">not_equals</option>
                <option value="in">in</option>
                <option value="not_in">not_in</option>
              </Select>
              <Input placeholder="value (csv para in/not_in)" value={form.rule_value} onChange={(e) => setForm((s) => ({ ...s, rule_value: e.target.value }))} />
            </div>
          </div>

          <label className="flex items-center gap-2 text-sm">
            <input type="checkbox" checked={form.auto_assign} onChange={(e) => setForm((s) => ({ ...s, auto_assign: e.target.checked }))} />
            Auto asignar
          </label>
          <label className="flex items-center gap-2 text-sm">
            <input type="checkbox" checked={form.is_active} onChange={(e) => setForm((s) => ({ ...s, is_active: e.target.checked }))} />
            Activa
          </label>

          <div className="flex items-center gap-2">
            <Button type="submit" disabled={saving}>{saving ? 'Guardando...' : isEdit ? 'Actualizar' : 'Crear cohorte'}</Button>
            {isEdit && <Button type="button" variant="secondary" onClick={resetForm}>Cancelar edición</Button>}
          </div>

          {feedback && <p className="text-sm text-emerald-700">{feedback}</p>}
          {error && <p className="text-sm text-rose-700">{error}</p>}
        </form>
      </Card>
    </div>
  );
}
