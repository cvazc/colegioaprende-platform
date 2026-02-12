import { FormEvent, useMemo, useState } from 'react';
import { http } from '@/shared/api/http';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';

const COURSE_TYPES = [
  'Preparatoria 4 meses 16 sesiones',
  'Secundaria 4 meses 16 sesiones',
  'Idioma Frances',
  'Licenciatura en Administracion',
];

const ENROLLMENT_TYPES = [
  'Estudiante General',
  'Estudiante Preferencial',
  'Estudiante Exclusivo',
];

export function RegisterPage() {
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState<string>('');
  const [error, setError] = useState<string>('');

  const defaultPayload = useMemo(
    () => ({
      first_name: '',
      surnames: '',
      actual_state: '',
      email: '',
      course_type: COURSE_TYPES[0],
      enrollment_type: ENROLLMENT_TYPES[0],
      recaptcha_token: '',
    }),
    []
  );

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError('');
    setResult('');
    setLoading(true);

    const formData = new FormData(event.currentTarget);
    const payload = {
      first_name: String(formData.get('first_name') ?? ''),
      surnames: String(formData.get('surnames') ?? ''),
      actual_state: String(formData.get('actual_state') ?? ''),
      email: String(formData.get('email') ?? ''),
      course_type: String(formData.get('course_type') ?? ''),
      enrollment_type: String(formData.get('enrollment_type') ?? ''),
      recaptcha_token: String(formData.get('recaptcha_token') ?? ''),
    };

    try {
      const { data } = await http.post('/prospects', payload);
      setResult(`Registro exitoso. Prospecto #${data?.data?.id ?? 'N/A'}`);
      event.currentTarget.reset();
    } catch {
      setError('No se pudo completar el registro. Verifica los datos.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="mx-auto max-w-3xl">
      <Card>
        <h1 className="text-2xl font-bold text-slate-900">Registro de prospecto</h1>
        <p className="mt-1 text-sm text-slate-600">Completa tus datos para iniciar el proceso de ingreso.</p>

        <form className="mt-6 grid gap-4 md:grid-cols-2" onSubmit={onSubmit}>
          <label className="grid gap-1 text-sm">
            Nombre
            <Input name="first_name" defaultValue={defaultPayload.first_name} required />
          </label>
          <label className="grid gap-1 text-sm">
            Apellidos
            <Input name="surnames" defaultValue={defaultPayload.surnames} required />
          </label>
          <label className="grid gap-1 text-sm">
            Estado actual
            <Input name="actual_state" defaultValue={defaultPayload.actual_state} required />
          </label>
          <label className="grid gap-1 text-sm">
            Correo
            <Input name="email" type="email" defaultValue={defaultPayload.email} required />
          </label>
          <label className="grid gap-1 text-sm">
            Tipo de curso
            <select
              name="course_type"
              defaultValue={defaultPayload.course_type}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
            >
              {COURSE_TYPES.map((item) => (
                <option key={item} value={item}>
                  {item}
                </option>
              ))}
            </select>
          </label>
          <label className="grid gap-1 text-sm">
            Tipo de inscripcion
            <select
              name="enrollment_type"
              defaultValue={defaultPayload.enrollment_type}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
            >
              {ENROLLMENT_TYPES.map((item) => (
                <option key={item} value={item}>
                  {item}
                </option>
              ))}
            </select>
          </label>
          <label className="grid gap-1 text-sm md:col-span-2">
            reCAPTCHA token (solo local/dev)
            <Input name="recaptcha_token" defaultValue={defaultPayload.recaptcha_token} placeholder="Opcional si no esta habilitado" />
          </label>
          <div className="md:col-span-2 flex items-center gap-3">
            <Button type="submit" disabled={loading}>
              {loading ? 'Enviando...' : 'Registrar'}
            </Button>
            {result && <span className="text-sm text-emerald-700">{result}</span>}
            {error && <span className="text-sm text-rose-700">{error}</span>}
          </div>
        </form>
      </Card>
    </div>
  );
}
