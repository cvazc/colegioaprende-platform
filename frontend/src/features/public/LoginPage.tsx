import { FormEvent, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '@/features/auth/AuthProvider';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';

export function LoginPage() {
  const navigate = useNavigate();
  const location = useLocation();
  const { login } = useAuth();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setError('');
    setLoading(true);

    const formData = new FormData(event.currentTarget);

    try {
      await login({
        email: String(formData.get('email') ?? ''),
        password: String(formData.get('password') ?? ''),
        recaptcha_token: String(formData.get('recaptcha_token') ?? ''),
      });

      const nextPath = (location.state as { from?: { pathname?: string } } | null)?.from?.pathname;
      navigate(nextPath ?? '/app', { replace: true });
    } catch {
      setError('Credenciales incorrectas o token de recaptcha invalido.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="mx-auto max-w-xl">
      <Card>
        <h1 className="text-2xl font-bold text-slate-900">Iniciar sesion</h1>
        <p className="mt-1 text-sm text-slate-600">Accede a tu panel de estudiante o administrativo.</p>

        <form className="mt-6 grid gap-4" onSubmit={onSubmit}>
          <label className="grid gap-1 text-sm">
            Correo
            <Input name="email" type="email" required />
          </label>
          <label className="grid gap-1 text-sm">
            Contrasena
            <Input name="password" type="password" required />
          </label>
          <label className="grid gap-1 text-sm">
            reCAPTCHA token (solo local/dev)
            <Input name="recaptcha_token" placeholder="Opcional si no esta habilitado" />
          </label>
          <div className="flex items-center gap-3">
            <Button type="submit" disabled={loading}>
              {loading ? 'Entrando...' : 'Entrar'}
            </Button>
            {error && <span className="text-sm text-rose-700">{error}</span>}
          </div>
        </form>
      </Card>
    </div>
  );
}
