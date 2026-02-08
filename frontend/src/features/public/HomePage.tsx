import { Link } from 'react-router-dom';
import { Card } from '@/shared/ui/Card';

export function HomePage() {
  return (
    <div className="grid gap-6 md:grid-cols-2">
      <Card>
        <h1 className="text-3xl font-extrabold tracking-tight text-slate-900">Colegio Aprende</h1>
        <p className="mt-3 text-sm text-slate-600">
          Plataforma educativa para secundaria y preparatoria en linea.
        </p>
        <div className="mt-6 flex gap-3">
          <Link to="/register" className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white">
            Registrarme
          </Link>
          <Link to="/login" className="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">
            Ya tengo cuenta
          </Link>
        </div>
      </Card>
      <Card>
        <h2 className="text-lg font-bold text-slate-900">Flujo actual</h2>
        <ul className="mt-3 list-disc space-y-1 pl-5 text-sm text-slate-600">
          <li>Registro de prospecto</li>
          <li>Pago por pasarela</li>
          <li>Conversion a estudiante</li>
          <li>Onboarding obligatorio</li>
          <li>Calendario y cohorte</li>
        </ul>
      </Card>
    </div>
  );
}
