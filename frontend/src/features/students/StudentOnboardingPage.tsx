import { FormEvent, useState } from 'react';
import { http } from '@/shared/api/http';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';

export function StudentOnboardingPage() {
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setMessage('');
    setError('');

    const formData = new FormData(event.currentTarget);

    try {
      await http.put('/student/onboarding', {
        movil_phone: String(formData.get('movil_phone') ?? ''),
        birth_date: String(formData.get('birth_date') ?? ''),
        actual_address: String(formData.get('actual_address') ?? ''),
        city: String(formData.get('city') ?? ''),
        actual_state: String(formData.get('actual_state') ?? ''),
        occupation: String(formData.get('occupation') ?? ''),
        curp: String(formData.get('curp') ?? ''),
        emergency_contact: String(formData.get('emergency_contact') ?? ''),
      });
      setMessage('Perfil completado correctamente.');
    } catch {
      setError('No fue posible actualizar el perfil.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Completar perfil</h1>
      <p className="mt-1 text-sm text-slate-600">Este paso es obligatorio para usar todo el dashboard.</p>
      <form className="mt-6 grid gap-4 md:grid-cols-2" onSubmit={onSubmit}>
        <label className="grid gap-1 text-sm">
          Telefono movil
          <Input name="movil_phone" required />
        </label>
        <label className="grid gap-1 text-sm">
          Fecha de nacimiento
          <Input name="birth_date" type="date" required />
        </label>
        <label className="grid gap-1 text-sm md:col-span-2">
          Direccion
          <Input name="actual_address" required />
        </label>
        <label className="grid gap-1 text-sm">
          Ciudad
          <Input name="city" required />
        </label>
        <label className="grid gap-1 text-sm">
          Estado
          <Input name="actual_state" required />
        </label>
        <label className="grid gap-1 text-sm">
          Ocupacion
          <Input name="occupation" />
        </label>
        <label className="grid gap-1 text-sm">
          CURP
          <Input name="curp" />
        </label>
        <label className="grid gap-1 text-sm md:col-span-2">
          Contacto de emergencia
          <Input name="emergency_contact" />
        </label>
        <div className="md:col-span-2 flex items-center gap-3">
          <Button type="submit" disabled={loading}>{loading ? 'Guardando...' : 'Guardar perfil'}</Button>
          {message && <span className="text-sm text-emerald-700">{message}</span>}
          {error && <span className="text-sm text-rose-700">{error}</span>}
        </div>
      </form>
    </Card>
  );
}
